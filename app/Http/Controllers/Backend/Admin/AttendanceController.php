<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Attendance;
use App\Models\AttendanceMonthlyStaff;
use App\Models\AttendanceMonthlyStudent;
use App\Models\AttendanceMonthlyTeacher;
use App\Models\AttendanceStaff;
use App\Models\AttendanceStudent;
use App\Models\AttendanceTeacher;
use App\Models\Section;
use App\Models\StdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Helper\AttendanceHelper;
use View;
use DB;
use Excel;

class AttendanceController extends Controller
{
   // ===== Start Student attendance   =====//

   public function importStdattendances()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.attendance.student_attendance.import_daily', compact('stdclass'));
   }


   public function importStdattendancesProcess(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('student-attendance-import');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required',
              'class_id' => 'required',
              'section_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'danger',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               if ($request->hasFile('excel_upload')) {
                  $extension = Input::file('excel_upload')->getClientOriginalExtension();;
                  if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                     $destinationPath = 'assets/uploads/student_attendance_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/student_attendance_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path


                     Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) use ($request) {

                        $atten_date = $request->input('atten_date');

                        DB::table('attendance_students')->where('attendance_date', $atten_date)->delete();

                        $allDataInSheet = $reader->getActiveSheet()->toArray(null, true, true, true);
                        $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

                        DB::beginTransaction();
                        try {

                           $bulk_data = [];

                           for ($i = 2; $i <= $arrayCount; $i++) {


                              if ($allDataInSheet[$i]['J'] != '') {

                                 $bulk_data[] = [
                                   "attendance_date" => $atten_date,
                                   "student_code" => trim($allDataInSheet[$i]["B"]),
                                   "class_id" => trim($allDataInSheet[$i]["D"]),
                                   "section_id" => trim($allDataInSheet[$i]["E"]),
                                   "in_time" => trim($allDataInSheet[$i]["J"]),
                                   "out_time" => trim($allDataInSheet[$i]["H"]),
                                   "late" => trim($allDataInSheet[$i]["I"]),
                                   "status" => trim($allDataInSheet[$i]["J"]),
                                   "remarks" => trim($allDataInSheet[$i]["K"]),
                                   "year" => config('running_session'),
                                   "created_at" => Carbon::now(),
                                   "updated_at" => Carbon::now(),
                                 ];

                              }
                           }

                           $insert = DB::table('attendance_students')->insert($bulk_data);

                           if ($insert) {
                              AttendanceHelper::updateStudentDailyAttendance($atten_date);
                           }
                           DB::commit();
                        } catch (\Exception $e) {
                           DB::rollback();
                           return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
                        }

                     });

                     return response()->json(['type' => 'success', 'message' => "Successfully Imported"]);

                  } else {
                     return response()->json([
                       'type' => 'danger',
                       'message' => "Error! File type is not valid"
                     ]);
                  }
               }
            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function studentDailyAttendanceReport()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.attendance.student_attendance.daily_report', compact('stdclass'));
   }


   public function getStudentDailyAttendanceReport(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $class_name = $request->input('class_name');
         $section_name = $request->input('section_name');
         $atten_date = $request->input('atten_date');
         $year = config('running_session');

         $data = DB::table('enrolls')
           ->leftJoin('students', 'students.id', '=', 'enrolls.student_id')
           ->leftJoin('attendance_students as attendances', function ($join) use ($atten_date) {
              $join->on('attendances.student_code', '=', 'students.std_code');
              $join->where('attendances.attendance_date', '=', $atten_date);
           })
           ->select('students.name as std_name', 'students.id as std_id', 'students.std_code',
             'attendances.status as attn_status', 'attendances.id as attend_id', 'attendances.in_time',
             'attendances.out_time', 'attendances.late', 'attendances.remarks')
           ->where('enrolls.class_id', $class_id)
           ->where('enrolls.section_id', $section_id)
           ->where('enrolls.year', $year)
           ->orderBy('students.std_code', 'asc')
           ->get();
         $view = View::make('backend.admin.attendance.student_attendance.daily_report_details', compact('class_name', 'section_name', 'atten_date', 'data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function importStdAttendanceMonthly()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.attendance.student_attendance.import_monthly', compact('stdclass'));
   }


   public function importStdAttendanceMonthlyProcess(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('student-attendance-import');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required',
              'class_id' => 'required',
              'section_id' => 'required',
              'month' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'danger',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               if ($request->hasFile('excel_upload')) {
                  $extension = Input::file('excel_upload')->getClientOriginalExtension();;
                  if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                     $destinationPath = 'assets/uploads/student_attendance_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/student_attendance_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path


                     Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) use ($request) {

                        $month = $request->input('month');
                        $year = config('running_session');

                        DB::table('attendance_monthly_students')->where('month', $month)->where('year', $year)->delete();

                        $allDataInSheet = $reader->getActiveSheet()->toArray(null, true, true, true);
                        $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

                        DB::beginTransaction();
                        try {

                           $bulk_data = [];

                           for ($i = 3; $i <= $arrayCount; $i++) {

                              if ($allDataInSheet[$i]['B'] != '') {

                                 $bulk_data[] = [
                                   "student_id" => trim($allDataInSheet[$i]["A"]),
                                   "student_name" => trim($allDataInSheet[$i]["B"]),
                                   "section_id" => trim($allDataInSheet[$i]["C"]),
                                   "class_id" => trim($allDataInSheet[$i]["D"]),
                                   "year" => $year,
                                   "month" => $month,
                                   "one" => trim($allDataInSheet[$i]["E"]),
                                   "two" => trim($allDataInSheet[$i]["F"]),
                                   "three" => trim($allDataInSheet[$i]["G"]),
                                   "four" => trim($allDataInSheet[$i]["H"]),
                                   "five" => trim($allDataInSheet[$i]["I"]),
                                   "six" => trim($allDataInSheet[$i]["J"]),
                                   "seven" => trim($allDataInSheet[$i]["K"]),
                                   "eight" => trim($allDataInSheet[$i]["L"]),
                                   "nine" => trim($allDataInSheet[$i]["M"]),
                                   "ten" => trim($allDataInSheet[$i]["N"]),
                                   "eleven" => trim($allDataInSheet[$i]["O"]),
                                   "twelve" => trim($allDataInSheet[$i]["P"]),
                                   "thirteen" => trim($allDataInSheet[$i]["Q"]),
                                   "fourteen" => trim($allDataInSheet[$i]["R"]),
                                   "fifteen" => trim($allDataInSheet[$i]["S"]),
                                   "sixteen" => trim($allDataInSheet[$i]["T"]),
                                   "seventeen" => trim($allDataInSheet[$i]["U"]),
                                   "eightteen" => trim($allDataInSheet[$i]["V"]),
                                   "nineteen" => trim($allDataInSheet[$i]["W"]),
                                   "twenty" => trim($allDataInSheet[$i]["X"]),
                                   "twentyone" => trim($allDataInSheet[$i]["Y"]),
                                   "twentytwo" => trim($allDataInSheet[$i]["Z"]),
                                   "twentythree" => trim($allDataInSheet[$i]["AA"]),
                                   "twentyfour" => trim($allDataInSheet[$i]["AB"]),
                                   "twentyfive" => trim($allDataInSheet[$i]["AC"]),
                                   "twentysix" => trim($allDataInSheet[$i]["AD"]),
                                   "twentyseven" => trim($allDataInSheet[$i]["AE"]),
                                   "twentyeight" => trim($allDataInSheet[$i]["AF"]),
                                   "twentynine" => trim($allDataInSheet[$i]["AG"]),
                                   "thirty" => trim($allDataInSheet[$i]["AH"]),
                                   "thirtyone" => trim($allDataInSheet[$i]["AI"]),
                                   "total_present" => trim($allDataInSheet[$i]["AJ"]),
                                   "total_late" => trim($allDataInSheet[$i]["AK"]),
                                   "total_leave" => trim($allDataInSheet[$i]["AL"]),
                                   "total_absent" => trim($allDataInSheet[$i]["AM"]),
                                   "created_at" => Carbon::now(),
                                   "updated_at" => Carbon::now(),
                                 ];

                              }
                           }

                           $insert = DB::table('attendance_monthly_students')->insert($bulk_data);

                           if ($insert) {
                              AttendanceHelper::updateStudentMontlyAttendance($month, $year);
                           }

                           DB::commit();
                        } catch (\Exception $e) {
                           DB::rollback();
                           return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
                        }

                     });

                     return response()->json(['type' => 'success', 'message' => "Successfully Imported"]);


                  } else {
                     return response()->json([
                       'type' => 'danger',
                       'message' => "Error! File type is not valid"
                     ]);
                  }
               }
            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function studentMonthlyAttendanceReport()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.attendance.student_attendance.monthly_report', compact('stdclass'));
   }

   public function getstudentMonthlyAttendanceReport(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $month = $request->input('month');
         $class_name = $request->input('class_name');
         $section_name = $request->input('section_name');
         $month_name = $request->input('month_name');

         $year = config('running_session');

         $data = AttendanceMonthlyStudent::where('month', $month)
           ->where('year', $year)
           ->orderBy('student_id', 'asc')
           ->get();
         $view = View::make('backend.admin.attendance.student_attendance.monthly_report_details', compact('month', 'month_name', 'class_name', 'section_name', 'data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   // ===== End Student attendance   =====//


   // Start Teacher Attendance

   public function importTeacherattendances()
   {
      return view('backend.admin.attendance.teacher_attendance.import_attendance');
   }


   public function importTeacherattendancesProcess(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('teacher-attendance-import');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required',
              'atten_date' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'danger',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               if ($request->hasFile('excel_upload')) {
                  $extension = Input::file('excel_upload')->getClientOriginalExtension();;
                  if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                     $destinationPath = 'assets/uploads/teacher_attendance_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/teacher_attendance_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path

                     Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) use ($request) {

                        $atten_date = $request->input('atten_date');
                        $year = config('running_session');

                        DB::table('attendance_teachers')->where('attendance_date', $atten_date)->where('year', $year)->delete();

                        $allDataInSheet = $reader->getActiveSheet()->toArray(null, true, true, true);
                        $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

                        DB::beginTransaction();
                        try {

                           $bulk_data = [];

                           for ($i = 2; $i <= $arrayCount; $i++) {

                              if ($allDataInSheet[$i]['J'] != '') {

                                 $bulk_data[] = [
                                   "attendance_date" => $atten_date,
                                   "teacher_id" => trim($allDataInSheet[$i]["B"]),
                                   "teacher_name" => trim($allDataInSheet[$i]["C"]),
                                   "mobile" => trim($allDataInSheet[$i]["D"]),
                                   "post" => trim($allDataInSheet[$i]["E"]),
                                   "designation" => trim($allDataInSheet[$i]["F"]),
                                   "in_time" => trim($allDataInSheet[$i]["G"]),
                                   "out_time" => trim($allDataInSheet[$i]["H"]),
                                   "late" => trim($allDataInSheet[$i]["I"]),
                                   "status" => trim($allDataInSheet[$i]["J"]),
                                   "remarks" => trim($allDataInSheet[$i]["K"]),
                                   "year" => config('running_session'),
                                   "created_at" => Carbon::now(),
                                   "updated_at" => Carbon::now(),
                                 ];

                              }
                           }

                           DB::table('attendance_teachers')->insert($bulk_data);
                           DB::commit();
                        } catch (\Exception $e) {
                           DB::rollback();
                           return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
                        }

                     });

                     return response()->json(['type' => 'success', 'message' => "Successfully Imported"]);

                  } else {
                     return response()->json([
                       'type' => 'danger',
                       'message' => "Error! File type is not valid"
                     ]);
                  }
               }
            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function teacherDailyAttendanceReport()
   {
      return view('backend.admin.attendance.teacher_attendance.daily_report');
   }


   public function getTeacherDailyAttendanceReport(Request $request)
   {
      if ($request->ajax()) {

         $atten_date = $request->input('atten_date');
         $year = config('running_session');

         $data = AttendanceTeacher::where('attendance_date', $atten_date)
           ->where('year', $year)
           ->orderBy('teacher_id', 'asc')
           ->get();
         $view = View::make('backend.admin.attendance.teacher_attendance.daily_report_details', compact('atten_date', 'data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function importTeacherAttendanceMonthly()
   {
      return view('backend.admin.attendance.teacher_attendance.import_monthly');
   }


   public function importTeacherAttendanceMonthlyProcess(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('teacher-attendance-import');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required',
              'month' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'danger',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               if ($request->hasFile('excel_upload')) {
                  $extension = Input::file('excel_upload')->getClientOriginalExtension();;
                  if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                     $destinationPath = 'assets/uploads/teacher_attendance_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/teacher_attendance_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path


                     Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) use ($request) {

                        $month = $request->input('month');
                        $year = config('running_session');

                        DB::table('attendance_monthly_teachers')->where('month', $month)->where('year', $year)->delete();

                        $allDataInSheet = $reader->getActiveSheet()->toArray(null, true, true, true);
                        $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

                        DB::beginTransaction();
                        try {

                           $bulk_data = [];

                           for ($i = 3; $i <= $arrayCount; $i++) {

                              if ($allDataInSheet[$i]['J'] != '') {

                                 $bulk_data[] = [
                                   "teacher_id" => trim($allDataInSheet[$i]["A"]),
                                   "teacher_name" => trim($allDataInSheet[$i]["B"]),
                                   "department" => trim($allDataInSheet[$i]["C"]),
                                   "designation" => trim($allDataInSheet[$i]["D"]),
                                   "year" => $year,
                                   "month" => $month,
                                   "one" => trim($allDataInSheet[$i]["E"]),
                                   "two" => trim($allDataInSheet[$i]["F"]),
                                   "three" => trim($allDataInSheet[$i]["G"]),
                                   "four" => trim($allDataInSheet[$i]["H"]),
                                   "five" => trim($allDataInSheet[$i]["I"]),
                                   "six" => trim($allDataInSheet[$i]["J"]),
                                   "seven" => trim($allDataInSheet[$i]["K"]),
                                   "eight" => trim($allDataInSheet[$i]["L"]),
                                   "nine" => trim($allDataInSheet[$i]["M"]),
                                   "ten" => trim($allDataInSheet[$i]["N"]),
                                   "eleven" => trim($allDataInSheet[$i]["O"]),
                                   "twelve" => trim($allDataInSheet[$i]["P"]),
                                   "thirteen" => trim($allDataInSheet[$i]["Q"]),
                                   "fourteen" => trim($allDataInSheet[$i]["R"]),
                                   "fifteen" => trim($allDataInSheet[$i]["S"]),
                                   "sixteen" => trim($allDataInSheet[$i]["T"]),
                                   "seventeen" => trim($allDataInSheet[$i]["U"]),
                                   "eightteen" => trim($allDataInSheet[$i]["V"]),
                                   "nineteen" => trim($allDataInSheet[$i]["W"]),
                                   "twenty" => trim($allDataInSheet[$i]["X"]),
                                   "twentyone" => trim($allDataInSheet[$i]["Y"]),
                                   "twentytwo" => trim($allDataInSheet[$i]["Z"]),
                                   "twentythree" => trim($allDataInSheet[$i]["AA"]),
                                   "twentyfour" => trim($allDataInSheet[$i]["AB"]),
                                   "twentyfive" => trim($allDataInSheet[$i]["AC"]),
                                   "twentysix" => trim($allDataInSheet[$i]["AD"]),
                                   "twentyseven" => trim($allDataInSheet[$i]["AE"]),
                                   "twentyeight" => trim($allDataInSheet[$i]["AF"]),
                                   "twentynine" => trim($allDataInSheet[$i]["AG"]),
                                   "thirty" => trim($allDataInSheet[$i]["AH"]),
                                   "thirtyone" => trim($allDataInSheet[$i]["AI"]),
                                   "total_present" => trim($allDataInSheet[$i]["AJ"]),
                                   "total_late" => trim($allDataInSheet[$i]["AK"]),
                                   "total_leave" => trim($allDataInSheet[$i]["AL"]),
                                   "total_absent" => trim($allDataInSheet[$i]["AM"]),
                                   "created_at" => Carbon::now(),
                                   "updated_at" => Carbon::now(),
                                 ];

                              }
                           }

                           DB::table('attendance_monthly_teachers')->insert($bulk_data);
                           DB::commit();
                        } catch (\Exception $e) {
                           DB::rollback();
                           return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
                        }

                     });

                     return response()->json(['type' => 'success', 'message' => "Successfully Imported"]);


                  } else {
                     return response()->json([
                       'type' => 'danger',
                       'message' => "Error! File type is not valid"
                     ]);
                  }
               }
            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function teacherMonthlyAttendanceReport()
   {
      return view('backend.admin.attendance.teacher_attendance.monthly_report');
   }

   public function getTeacherMonthlyAttendanceReport(Request $request)
   {
      if ($request->ajax()) {

         $month = $request->input('month');
         $year = config('running_session');

         $data = AttendanceMonthlyTeacher::where('month', $month)
           ->where('year', $year)
           ->orderBy('teacher_id', 'asc')
           ->get();
         $view = View::make('backend.admin.attendance.teacher_attendance.monthly_report_details', compact('month', 'data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   // End Teacher Attendacne


   // Start Staff Attendance

   public function importStaffattendances()
   {
      return view('backend.admin.attendance.staff_attendance.import_attendance');
   }


   public function importStaffattendancesProcess(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('staff-attendance-import');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required',
              'atten_date' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'danger',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               if ($request->hasFile('excel_upload')) {
                  $extension = Input::file('excel_upload')->getClientOriginalExtension();;
                  if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                     $destinationPath = 'assets/uploads/staff_attendance_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/staff_attendance_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path


                     Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) use ($request) {

                        $atten_date = $request->input('atten_date');

                        DB::table('attendance_staffs')->where('attendance_date', $atten_date)->delete();
                        $allDataInSheet = $reader->getActiveSheet()->toArray(null, true, true, true);
                        $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

                        DB::beginTransaction();
                        try {

                           $bulk_data = [];

                           for ($i = 2; $i <= $arrayCount; $i++) {

                              if ($allDataInSheet[$i]['J'] != '') {

                                 $bulk_data[] = [
                                   "attendance_date" => $atten_date,
                                   "staff_id" => trim($allDataInSheet[$i]["B"]),
                                   "staff_name" => trim($allDataInSheet[$i]["C"]),
                                   "mobile" => trim($allDataInSheet[$i]["D"]),
                                   "post" => trim($allDataInSheet[$i]["E"]),
                                   "designation" => trim($allDataInSheet[$i]["F"]),
                                   "in_time" => trim($allDataInSheet[$i]["G"]),
                                   "out_time" => trim($allDataInSheet[$i]["H"]),
                                   "late" => trim($allDataInSheet[$i]["I"]),
                                   "status" => trim($allDataInSheet[$i]["J"]),
                                   "remarks" => trim($allDataInSheet[$i]["K"]),
                                   "year" => config('running_session'),
                                   "created_at" => Carbon::now(),
                                   "updated_at" => Carbon::now(),
                                 ];

                              }
                           }

                           DB::table('attendance_staffs')->insert($bulk_data);
                           DB::commit();
                        } catch (\Exception $e) {
                           DB::rollback();
                           return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
                        }

                     });

                     return response()->json(['type' => 'success', 'message' => "Successfully Imported"]);

                  } else {
                     return response()->json([
                       'type' => 'danger',
                       'message' => "Error! File type is not valid"
                     ]);
                  }
               }
            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function staffDailyAttendanceReport()
   {
      return view('backend.admin.attendance.staff_attendance.daily_report');
   }


   public function getstaffDailyAttendanceReport(Request $request)
   {
      if ($request->ajax()) {

         $atten_date = $request->input('atten_date');
         $year = config('running_session');

         $data = AttendanceStaff::where('attendance_date', $atten_date)
           ->where('year', $year)
           ->orderBy('staff_id', 'asc')
           ->get();
         $view = View::make('backend.admin.attendance.staff_attendance.daily_report_details', compact('atten_date', 'data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function importStaffAttendanceMonthly()
   {
      return view('backend.admin.attendance.staff_attendance.import_monthly');
   }


   public function importStaffAttendanceMonthlyProcess(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('staff-attendance-import');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required',
              'month' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'danger',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               if ($request->hasFile('excel_upload')) {
                  $extension = Input::file('excel_upload')->getClientOriginalExtension();;
                  if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                     $destinationPath = 'assets/uploads/staff_attendance_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/staff_attendance_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path

                     Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) use ($request) {

                        $month = $request->input('month');
                        $year = config('running_session');

                        DB::table('attendance_monthly_staffs')->where('month', $month)->where('year', $year)->delete();

                        $allDataInSheet = $reader->getActiveSheet()->toArray(null, true, true, true);
                        $arrayCount = count($allDataInSheet);  // Here get total count of row in that Excel sheet

                        DB::beginTransaction();
                        try {

                           $bulk_data = [];

                           for ($i = 3; $i <= $arrayCount; $i++) {

                              if ($allDataInSheet[$i]['J'] != '') {

                                 $bulk_data[] = [
                                   "staff_id" => trim($allDataInSheet[$i]["A"]),
                                   "staff_name" => trim($allDataInSheet[$i]["B"]),
                                   "department" => trim($allDataInSheet[$i]["C"]),
                                   "designation" => trim($allDataInSheet[$i]["D"]),
                                   "year" => $year,
                                   "month" => $month,
                                   "one" => trim($allDataInSheet[$i]["E"]),
                                   "two" => trim($allDataInSheet[$i]["F"]),
                                   "three" => trim($allDataInSheet[$i]["G"]),
                                   "four" => trim($allDataInSheet[$i]["H"]),
                                   "five" => trim($allDataInSheet[$i]["I"]),
                                   "six" => trim($allDataInSheet[$i]["J"]),
                                   "seven" => trim($allDataInSheet[$i]["K"]),
                                   "eight" => trim($allDataInSheet[$i]["L"]),
                                   "nine" => trim($allDataInSheet[$i]["M"]),
                                   "ten" => trim($allDataInSheet[$i]["N"]),
                                   "eleven" => trim($allDataInSheet[$i]["O"]),
                                   "twelve" => trim($allDataInSheet[$i]["P"]),
                                   "thirteen" => trim($allDataInSheet[$i]["Q"]),
                                   "fourteen" => trim($allDataInSheet[$i]["R"]),
                                   "fifteen" => trim($allDataInSheet[$i]["S"]),
                                   "sixteen" => trim($allDataInSheet[$i]["T"]),
                                   "seventeen" => trim($allDataInSheet[$i]["U"]),
                                   "eightteen" => trim($allDataInSheet[$i]["V"]),
                                   "nineteen" => trim($allDataInSheet[$i]["W"]),
                                   "twenty" => trim($allDataInSheet[$i]["X"]),
                                   "twentyone" => trim($allDataInSheet[$i]["Y"]),
                                   "twentytwo" => trim($allDataInSheet[$i]["Z"]),
                                   "twentythree" => trim($allDataInSheet[$i]["AA"]),
                                   "twentyfour" => trim($allDataInSheet[$i]["AB"]),
                                   "twentyfive" => trim($allDataInSheet[$i]["AC"]),
                                   "twentysix" => trim($allDataInSheet[$i]["AD"]),
                                   "twentyseven" => trim($allDataInSheet[$i]["AE"]),
                                   "twentyeight" => trim($allDataInSheet[$i]["AF"]),
                                   "twentynine" => trim($allDataInSheet[$i]["AG"]),
                                   "thirty" => trim($allDataInSheet[$i]["AH"]),
                                   "thirtyone" => trim($allDataInSheet[$i]["AI"]),
                                   "total_present" => trim($allDataInSheet[$i]["AJ"]),
                                   "total_late" => trim($allDataInSheet[$i]["AK"]),
                                   "total_leave" => trim($allDataInSheet[$i]["AL"]),
                                   "total_absent" => trim($allDataInSheet[$i]["AM"]),
                                   "created_at" => Carbon::now(),
                                   "updated_at" => Carbon::now(),
                                 ];

                              }
                           }

                           DB::table('attendance_monthly_staffs')->insert($bulk_data);
                           DB::commit();
                        } catch (\Exception $e) {
                           DB::rollback();
                           return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
                        }

                     });

                     return response()->json(['type' => 'success', 'message' => "Successfully Imported"]);


                  } else {
                     return response()->json([
                       'type' => 'danger',
                       'message' => "Error! File type is not valid"
                     ]);
                  }
               }
            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function staffMonthlyAttendanceReport()
   {
      return view('backend.admin.attendance.staff_attendance.monthly_report');
   }

   public function getstaffMonthlyAttendanceReport(Request $request)
   {
      if ($request->ajax()) {

         $month = $request->input('month');
         $year = config('running_session');

         $data = AttendanceMonthlyStaff::where('month', $month)
           ->where('year', $year)
           ->orderBy('staff_id', 'asc')
           ->get();
         $view = View::make('backend.admin.attendance.staff_attendance.monthly_report_details', compact('month', 'data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
   // End Teacher Attendacne
}

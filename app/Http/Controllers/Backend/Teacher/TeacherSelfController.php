<?php

namespace App\Http\Controllers\Backend\Teacher;

use App\Helper\Academic;
use App\Models\Exam;
use App\Models\Section;
use App\Models\StdClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use View;
use DB;
use Excel;

class TeacherSelfController extends Controller
{

   public function index()
   {
      $teacher = Auth::user();
      return view('backend.teacher.home', compact('teacher'));
   }

   public function profile()
   {
      $teacher = Auth::user();
      return view('backend.teacher.profile', compact('teacher'));
   }

   public function edit()
   {
      $teacher = Auth::user();
      return view('backend.teacher.edit_profile', compact('teacher'));
   }

   public function update(Request $request)
   {
      if ($request->ajax()) {

         $teacher = Teacher::findOrFail(Auth::user()->id);

         $rules = [
           'name' => 'required',
           'teacher_code' => 'required|unique:teachers,teacher_code,' . $teacher->id,
           'email' => 'required|email|unique:teachers,email,' . $teacher->id,
           'phone' => 'required|unique:teachers,phone,' . $teacher->id,
           'photo' => 'image|max:2024|mimes:jpeg,jpg,gif,png'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {

            $upload_ok = 1;
            $file_path = $request->input('SelectedFileName');

            if ($request->hasFile('photo')) {

               if (Input::file('photo')->isValid()) {
                  File::delete($teacher->file_path);
                  $destinationPath = 'assets/images/teacher_image'; // upload path
                  $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                  $fileName = time() . '.' . $extension; // renameing image
                  $file_path = 'assets/images/teacher_image/' . $fileName;
                  Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                  $upload_ok = 1;

               } else {
                  $upload_ok = 0;
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Please! File is not valid</div>"
                  ]);
               }
            }
            if ($upload_ok == 0) {
               return response()->json([
                 'type' => 'error',
                 'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
               ]);
            } else {

               $teacher->name = $request->input('name');
               $teacher->teacher_code = $request->input('teacher_code');
               $teacher->dob = $request->input('dob');
               $teacher->doj = $request->input('doj');
               $teacher->gender = $request->input('gender');
               $teacher->religion = $request->input('religion');
               $teacher->blood_group = $request->input('blood_group');
               $teacher->address = $request->input('address');
               $teacher->phone = $request->input('phone');
               $teacher->email = $request->input('email');
               $teacher->designation = $request->input('designation');
               $teacher->file_path = $file_path;
               $teacher->save(); //
               return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);

            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function change_password()
   {
      return view('backend.teacher.change_password');
   }

   public function update_password(Request $request)
   {
      if ($request->ajax()) {

         $teacher = Teacher::findOrFail(Auth::user()->id);

         $rules = [
           'password' => 'required'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {
            $teacher->password = Hash::make($request->input('password'));
            $teacher->save(); //
            return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getClassroutines(Request $request)
   {
      $teacher_id = auth()->id();

      $routines = DB::table('class_routines')
        ->join('subjects', 'subjects.id', '=', 'class_routines.subject_id')
        ->join('sections', 'sections.id', '=', 'class_routines.section_id')
        ->join('class_rooms', 'class_rooms.id', '=', 'class_routines.class_room_id')
        ->join('teachers', 'teachers.id', '=', 'class_routines.teacher_id')
        ->select('class_routines.*', 'subjects.name as subject_name', 'sections.name as section_name',
          'teachers.name as teacher_name', 'class_rooms.name as class_room')
        ->where('class_routines.teacher_id', $teacher_id)
        ->where('class_routines.year', config('running_session'))
        ->orderby('class_routines.time_start', 'asc')->get();

      return view('backend.teacher.class_routine', compact('routines'));
   }


   public function getAttendance()
   {
      return view('backend.teacher.attendance');
   }

   public function attendanceReport(Request $request)
   {
      if ($request->ajax()) {

         $teacher = Teacher::findOrFail(Auth::user()->id);
         $year = config('running_session');

         $data = array();
         $data['month'] = $request->input('month');
         $data['year'] = $year;
         $month = $request->input('month');


         $data['result'] = DB::select("SELECT * FROM attendance_monthly_teachers WHERE teacher_id = '$teacher->teacher_code' AND month = $month AND year = '$year'");

         if (count($data['result']) != 0) {
            $view = View::make('backend.teacher.attendance_report', compact('data'))->render();
            return response()->json(['html' => $view]);
         } else {
            return response()->json(['status' => 'error', 'html' => "<div class='alert alert-danger'> Sorry No record have found </div>"]);
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function getSubjects(Request $request)
   {
      if ($request->ajax()) {

         $teacher_id = auth()->id();
         $year = config('running_session');

         $exam_id = $request->input('exam_id');
         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');


         $subjects = DB::select("SELECT  subjects.id, subjects.NAME AS subject_name
            FROM assign_examinees AS examinee
            JOIN subjects ON subjects.id = examinee.subject_id
            WHERE examinee.YEAR = '$year' and examinee.exam_id = $exam_id AND 
            examinee.class_id = $class_id AND examinee.section_id = $section_id and examinee.teacher_id = $teacher_id ");

         if ($subjects) {
            echo "<option value='' selected disabled> Select a subject</option>";
            foreach ($subjects as $subject) {
               echo "<option  value='$subject->id'> $subject->subject_name</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getSections(Request $request, $class_id)
   {
      if ($request->ajax()) {

         $class = StdClass::findOrFail($class_id);
         $sections = $class->sections;
         if ($sections) {
            echo "<option value='' selected disabled> Select a section</option>";
            foreach ($sections as $section) {
               echo "<option  value='$section->id'> $section->name</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function import()
   {
      $exams = Exam::where('year', config('running_session'))
        ->where('result_modification_last_date', '>=', date('Y-m-d'))
        ->orderBy('created_at', 'desc')->get();
      $stdclass = StdClass::all();
      return view('backend.teacher.mark.import', compact('exams', 'stdclass'));
   }

   public function importStore(Request $request)
   {
      if ($request->ajax()) {
         $rules = [
           'exam_id' => 'required',
           'class_id' => 'required',
           'section_id' => 'required',
           'subject_id' => 'required',
           'excel_upload' => 'required',
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

                  $destinationPath = 'assets/uploads/marks_excel_uploads'; // upload path
                  $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                  $file_path = 'assets/uploads/marks_excel_uploads/' . $fileName;
                  Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path

                  $data = Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) {
                  })->get();

                  if (!empty($data) && $data->count()) {

                     DB::beginTransaction();
                     try {

                        $exam_id = $request->input('exam_id');
                        $class_id = $request->input('class_id');
                        $section_id = $request->input('section_id');
                        $subject_id = $request->input('subject_id');
                        $teacher_id = auth()->id();
                        $uploader_id = auth()->id();


                        $bulk_data = [];

                        DB::table('marks')
                          ->where('exam_id', $exam_id)
                          ->where('class_id', $class_id)
                          ->where('section_id', $section_id)
                          ->where('subject_id', $subject_id)
                          ->delete();


                        foreach ($data as $key => $value) {
                           if ("$value->student_code" != '') {

                              $theory_marks = "$value->theory_marks" != '' ? "$value->theory_marks" : 0;
                              $mcq_marks = "$value->mcq_marks" != '' ? "$value->mcq_marks" : 0;
                              $practical_marks = "$value->practical_marks" != '' ? "$value->practical_marks" : 0;
                              $ct_marks = "$value->ct_marks" != '' ? "$value->ct_marks" : 0;
                              $total_marks = $theory_marks + $mcq_marks + $practical_marks;

                              $bulk_data[] = [
                                'exam_id' => $exam_id,
                                'class_id' => $class_id,
                                'section_id' => $section_id,
                                'subject_id' => $subject_id,
                                'student_code' => "$value->student_code",
                                'total_marks' => $total_marks,
                                'theory_marks' => $theory_marks,
                                'mcq_marks' => $mcq_marks,
                                'practical_marks' => $practical_marks,
                                'ct_marks' => $ct_marks,
                                'teacher_id' => $teacher_id,
                                'uploader_id' => $uploader_id,
                                'year' => config('running_session'),
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                              ];

                           }
                        }

                        DB::table('marks')->insert($bulk_data);
                        DB::commit();
                     } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
                     }
                     return response()->json(['type' => 'success', 'message' => "Successfully Imported"]);

                  } else {
                     return response()->json([
                       'type' => 'danger',
                       'message' => "Error! No records in file"
                     ]);
                  }


               } else {
                  return response()->json([
                    'type' => 'danger',
                    'message' => "Error! File type is not valid"
                  ]);
               }
            }
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function manageMarks()
   {
      $exams = Exam::where('year', config('running_session'))
        ->where('result_modification_last_date', '>=', date('Y-m-d'))
        ->orderBy('created_at', 'desc')->get();
      $stdclass = StdClass::all();
      return view('backend.teacher.mark.index', compact('stdclass', 'exams'));
   }

   public function getMarks(Request $request)
   {

      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $exam_id = $request->input('exam_id');
         $subject_id = $request->input('subject_id');
         $year = config('running_session');

         $data = Academic::getSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year);
         $view = View::make('backend.teacher.mark.view', compact('std_class', 'section', 'exam', 'subject', 'data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function updateMarks(Request $request)
   {
      if ($request->ajax()) {

         $exam_id = $request->input('exam_id');
         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $subject_id = $request->input('subject_id');
         $student_code = $request->input('student_code');
         $teacher_id = auth()->id();
         $uploader_id = auth()->id();

         DB::beginTransaction();
         try {

            $bulk_data = [];

            DB::table('marks')
              ->where('exam_id', $exam_id)
              ->where('class_id', $class_id)
              ->where('section_id', $section_id)
              ->where('subject_id', $subject_id)
              ->delete();


            foreach ($student_code as $std_code) {
               $theory_marks = $request->input('theory_' . $std_code) ? $request->input('theory_' . $std_code) : 0;
               $mcq_marks = $request->input('mcq_' . $std_code) ? $request->input('mcq_' . $std_code) : 0;
               $practical_marks = $request->input('practical_' . $std_code) ? $request->input('practical_' . $std_code) : 0;
               $ct_marks = $request->input('ct_' . $std_code) ? $request->input('ct_' . $std_code) : 0;
               $total_marks = $theory_marks + $mcq_marks + $practical_marks;

               $bulk_data[] = [
                 'exam_id' => $exam_id,
                 'student_code' => $std_code,
                 'subject_id' => $subject_id,
                 'class_id' => $class_id,
                 'section_id' => $section_id,
                 'total_marks' => $total_marks,
                 'theory_marks' => $theory_marks,
                 'mcq_marks' => $mcq_marks,
                 'practical_marks' => $practical_marks,
                 'ct_marks' => $ct_marks,
                 'teacher_id' => $teacher_id,
                 'uploader_id' => $uploader_id,
                 'year' => config('running_session'),
                 'created_at' => Carbon::now(),
                 'updated_at' => Carbon::now(),
               ];

            }

            DB::table('marks')->insert($bulk_data);
            DB::commit();

         } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
         }

         return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

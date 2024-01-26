<?php

namespace App\Http\Controllers\Backend\Parent;

use App\Helper\Academic;
use App\Helper\Accounts;
use App\Helper\GenerateMarksheet;
use App\Models\Enroll;
use App\Models\Exam;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use PDF;
use View;
use DB;
use Yajra\DataTables\DataTables;

class ParentSelfController extends Controller
{

   public function index()
   {
      return view('backend.parent.home');
   }

   public function profile()
   {
      $parent = Auth::user();
      $student = Student::where('std_code', $parent->parent_code)->first();
      $enroll = Enroll::where('student_id', $student->id)
        ->where('year', config('running_session'))
        ->first();
      return view('backend.parent.profile', compact('parent', 'student', 'enroll'));
   }

   public function edit()
   {
      $parent = Auth::user();
      return view('backend.parent.edit_profile', compact('parent'));
   }

   public function update(Request $request)
   {
      if ($request->ajax()) {

         $parent = Auth::user();

         $rules = [
           'father_name' => 'required',
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
                  File::delete($parent->file_path);
                  $destinationPath = 'assets/images/parent_image'; // upload path
                  $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                  $fileName = time() . '.' . $extension; // renameing image
                  $file_path = 'assets/images/parent_image/' . $fileName;
                  Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                  $upload_ok = 1;

               } else {
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

               $parent->father_name = $request->input('father_name');
               $parent->mother_name = $request->input('mother_name');
               $parent->gender = $request->input('gender');
               $parent->profession = $request->input('profession');
               $parent->blood_group = $request->input('blood_group');
               $parent->address = $request->input('address');
               $parent->phone = $request->input('phone');
               $parent->email = $request->input('email');
               $parent->file_path = $file_path;
               $parent->save(); //
               return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);

            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function change_password()
   {
      return view('backend.parent.change_password');
   }

   public function update_password(Request $request)
   {
      if ($request->ajax()) {

         $parent = Auth::user();

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
            $parent->password = Hash::make($request->input('password'));
            $parent->save(); //
            return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getClassroutines()
   {
      $parent = Auth::user();
      $student = Student::where('parent_id', $parent->parent_code)->first();
      $year = config('running_session');
      $students = DB::table('enrolls')
        ->join('students', 'students.id', '=', 'enrolls.student_id')
        ->join('sections', 'sections.id', '=', 'enrolls.section_id')
        ->join('std_classes', 'std_classes.id', '=', 'enrolls.class_id')
        ->select('enrolls.*', 'students.std_code', 'students.name as student_name',
          'std_classes.name as class_name', 'sections.name as section_name')
        ->where('enrolls.student_id', $student->id)
        ->where('enrolls.year', $year)->get();


      foreach ($students as $student) {
         $class_id = $student->class_id;
         $section_id = $student->section_id;
         $class_name = $student->class_name;
         $section_name = $student->section_name;
      }


      $data = array();
      $data['class_id'] = $class_id;
      $data['section_id'] = $section_id;
      $data['class_name'] = $class_name;
      $data['section_name'] = $section_name;
      $data['year'] = $year;

      $data['routines'] = Academic::generateClassRoutine($class_id, $section_id);

      return view('backend.parent.class_routine', compact('data'));
   }


   public function getAttendance()
   {
      return view('backend.parent.attendance');
   }

   public function attendanceReport(Request $request)
   {
      if ($request->ajax()) {

         $parent = Auth::user();
         $student = Student::where('parent_id', $parent->parent_code)->first();
         $year = config('running_session');
         $students = DB::table('enrolls')
           ->join('students', 'students.id', '=', 'enrolls.student_id')
           ->join('sections', 'sections.id', '=', 'enrolls.section_id')
           ->join('std_classes', 'std_classes.id', '=', 'enrolls.class_id')
           ->select('enrolls.*', 'students.name as student_name',
             'std_classes.name as class_name', 'sections.name as section_name')
           ->where('enrolls.student_id', $student->id)
           ->where('enrolls.year', $year)->get();


         foreach ($students as $student) {
            $class_id = $student->class_id;
            $section_id = $student->section_id;
            $class_name = $student->class_name;
            $section_name = $student->section_name;
            $std_code = $student->student_code;
            $year = $student->year;
         }


         $data = array();
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['class_name'] = $class_name;
         $data['section_name'] = $section_name;
         $data['std_code'] = $std_code;
         $data['month'] = $request->input('month');
         $data['year'] = $year;
         $month = $request->input('month');


         $data['result'] = DB::select("SELECT * FROM attendance_monthly_students WHERE student_id = '$std_code' AND month = $month AND year = '$year'");

         if (count($data['result']) != 0) {
            $view = View::make('backend.parent.attendance_report', compact('data'))->render();
            return response()->json(['html' => $view]);
         } else {
            return response()->json(['status' => 'error', 'html' => "<div class='alert alert-danger'> Sorry No record have found </div>"]);
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getAcademicResult()
   {
      $year = config('running_session');
      $exams = Exam::where('year', $year)->orderBy('created_at', 'desc')->get();
      return view('backend.parent.result', compact('exams'));
   }

   public function generateMarksheet(Request $request)
   {

      $parent = Auth::user();
      $student = Student::where('parent_id', $parent->parent_code)->first();
      $year = config('running_session');
      $students = DB::table('enrolls')
        ->join('students', 'students.id', '=', 'enrolls.student_id')
        ->join('sections', 'sections.id', '=', 'enrolls.section_id')
        ->join('std_classes', 'std_classes.id', '=', 'enrolls.class_id')
        ->select('enrolls.*', 'students.std_code', 'students.name as student_name',
          'std_classes.name as class_name', 'sections.name as section_name')
        ->where('enrolls.student_id', $student->id)
        ->where('enrolls.year', $year)->get();


      foreach ($students as $student) {
         $class_id = $student->class_id;
         $section_id = $student->section_id;
         $student_code = $student->std_code;
         $student_name = $student->student_name;
         $roll = $student->roll;
         $class_name = $student->class_name;
         $section_name = $student->section_name;
      }


      $exam_id = $request->input('exam_id');


      $exam = Exam::where('id', $exam_id)->first();

      $data = array();
      $data['total_student'] = Enroll::where('class_id', $class_id)->where('year', $year)->count();
      $data['student_code'] = $student_code;
      $data['student_name'] = $student_name;
      $data['std_roll'] = $roll;
      $data['class_id'] = $class_id;
      $data['section_id'] = $section_id;
      $data['exam_id'] = $exam_id;
      $data['class_name'] = $class_name;
      $data['section_name'] = $section_name;
      $data['exam_name'] = $exam->name;
      $data['year'] = $year;
      $data['has_ct'] = $exam->ct_marks_percentage;
      $data['mmp'] = $exam->main_marks_percentage;
      $data['result'] = GenerateMarksheet::generateMarksheetResult($exam_id, $class_id, $section_id, $student_code, $year);
      $view = View::make('backend.admin.tabulation_sheet.marksheet', compact('data'))->render();
      return response()->json(['html' => $view]);

   }


   public function feeBooks()
   {
      return view('backend.parent.accounts.fee_book');
   }

   public function allFeeBooks()
   {
      $year = config('running_session');
      $parent = Auth::user();
      $student = Student::where('parent_id', $parent->parent_code)->first();
      $enroll = Enroll::where('student_id', $student->id)
        ->where('year', config('running_session'))
        ->first();

      $class_id = $enroll->class_id;
      $section_id = $enroll->section_id;
      $student_id = $enroll->student_code;
      $months = 'null';

      $feecategory = Accounts::studentFeeBooks($class_id, $section_id, $months, $student_id, $year);
      return Datatables::of($feecategory)
        ->addColumn('month', function ($feecategory) {
           $monthName = date("F", mktime(0, 0, 0, $feecategory->month, 10));
           return $monthName;
        })
        ->addColumn('action', function ($feecategory) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" href=' . '/parent/printFeeBook/' . $feecategory->month . '  class="btn btn-xs btn-success margin-r-5 print" title="Print Fee Book"><i class="fa fa-print fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['status', 'action'])
        ->addIndexColumn()
        ->make(true);
   }


   public function printFeeBook($month)
   {
      $year = config('running_session');
      $parent = Auth::user();
      $student = Student::where('parent_id', $parent->parent_code)->first();
      $enroll = Enroll::where('student_id', $student->id)
        ->where('year', config('running_session'))
        ->first();

      $class_id = $enroll->class_id;
      $section_id = $enroll->section_id;
      $student_id = $enroll->student_code;


      $data = Accounts::printfeeBook($class_id, $section_id, $month, $student_id, $year);

      if ($data) {

         foreach ($data as $element) {
            $students[$element->std_code][] = $element;
         }

         foreach ($students as $std) {

            foreach ($std as $monthly) {
               $allmonths[$monthly->month][] = $monthly;
            }


            $html = '<!DOCTYPE html><html lang="en">';
            //  dd($allmonths);
            foreach ($allmonths as $monthly) {
               $view = view('backend.admin.accounts.accounts_print_pdf.printFeeBook', compact('monthly'));
               $html .= $view->render();
            }

            unset($allmonths);
            $html .= '</html>';

         }

         $pdf = PDF::loadHTML($html);
         $pdf = $pdf->setPaper('a4', 'landscape');
         return $pdf->download('Monthly_Fee_' . date("F_Y", mktime(0, 0, 0, $month, 10)) . '_' . $student->std_code . '.pdf');


      } else {
         return response()->json(['type' => 'error', 'message' => "No data found"]);
      }
   }


   public function paymentHistory()
   {
      return view('backend.parent.accounts.payment');
   }

   public function paymentDetails(Request $request)
   {
      if ($request->ajax()) {

         $parent = Auth::user();
         $student = Student::where('parent_id', $parent->parent_code)->first();

         $year = config('running_session');


         $payments = DB::table('accounts_payments')
           ->join('students', 'accounts_payments.student_id', '=', 'students.std_code')
           ->join('std_classes', 'std_classes.id', '=', 'accounts_payments.class_id')
           ->join('sections', 'sections.id', '=', 'accounts_payments.section_id')
           ->select('accounts_payments.id as payment_id', 'accounts_payments.barcode', 'accounts_payments.student_roll as roll',
             'accounts_payments.fee_month as month', 'accounts_payments.amount',
             'students.name', 'students.std_code as std_code',
             'std_classes.name as class_name',
             'sections.name as section_name')
           ->where('accounts_payments.student_id', $student->std_code)
           ->where('accounts_payments.year', $year)->get();
         return Datatables::of($payments)
           ->addColumn('month', function ($payments) {
              return date("F", mktime(0, 0, 0, $payments->month, 10));
           })
           ->addColumn('status', function ($payments) {
              return '<span class="label label-success">Paid</span>';
           })
           ->rawColumns(['action', 'status'])
           ->addIndexColumn()
           ->make(true);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

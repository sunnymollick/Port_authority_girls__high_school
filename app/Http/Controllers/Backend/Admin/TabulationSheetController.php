<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\GenerateMarksheet;
use App\Models\Enroll;
use App\Models\Exam;
use App\Models\Section;
use App\Models\StdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use View;
use DB;
use PDF;

class TabulationSheetController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.tabulation_sheet.index', compact('stdclass'));
   }

   public function summeryResult(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $exam_id = $request->input('exam_id');
         $year = config('running_session');

         $data = array();
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['exam_id'] = $exam_id;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name'] = $request->input('exam_name');
         $data['year'] = $year;

         $data['result'] = GenerateMarksheet::generateSummeryResult($exam_id, $class_id, $section_id, $year);

         $view = View::make('backend.admin.tabulation_sheet.summery', compact('data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function viewMarksheet(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $exam_id = $request->input('exam_id');
         $student_code = $request->input('student_code');
         $year = config('running_session');

         $exam = Exam::where('id', $exam_id)->first();

         $data = array();
         $data['total_student'] = Enroll::where('class_id', $class_id)->where('year', $year)->count();
         $data['student_code'] = $student_code;
         $data['student_name'] = $request->input('student_name');
         $data['std_roll'] = $request->input('std_roll');
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['exam_id'] = $exam_id;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name'] = $request->input('exam_name');
         $data['year'] = config('running_session');
         $data['has_ct'] = $exam->ct_marks_percentage;
         $data['mmp'] = $exam->main_marks_percentage;

         $data['result'] = GenerateMarksheet::generateMarksheetResult($exam_id, $class_id, $section_id, $student_code, $year);

         // dd($data['result']);

         $view = View::make('backend.admin.tabulation_sheet.marksheet', compact('data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

   public function printMarksheet(Request $request)
   {

      $class_id = Input::get('class_id');
      $section_id = Input::get('section_id');
      $exam_id = Input::get('exam_id');
      $student_code = Input::get('student_code');
      $year = config('running_session');

      $exam = Exam::where('id', $exam_id)->first();

      $data = array();
      $data['student_code'] = $student_code;
      $data['student_name'] = $request->input('student_name');
      $data['std_roll'] = $request->input('std_roll');
      $data['class_id'] = $class_id;
      $data['section_id'] = $section_id;
      $data['exam_id'] = $exam_id;
      $data['class_name'] = $request->input('class_name');
      $data['section_name'] = $request->input('section_name');
      $data['exam_name'] = $request->input('exam_name');
      $data['year'] = config('running_session');
      $data['has_ct'] = $exam->ct_marks_percentage;
      $data['mmp'] = $exam->main_marks_percentage;

      $data['total_std'] = Input::get('total_std');
      $data['total_atd'] = Input::get('total_atd');
      $data['total_wd'] = Input::get('total_wd');
      $data['position'] = Input::get('position');

      $data['result'] = GenerateMarksheet::generateMarksheetResult($exam_id, $class_id, $section_id, $student_code, $year);
      $view = View::make('backend.admin.tabulation_sheet.printMarksheet', compact('data'));

      $html = '<!DOCTYPE html><html lang="en">';
      $html .= $view->render();
      $html .= '</html>';
      $pdf = PDF::loadHTML($html);
      $sheet = $pdf->setPaper('a4', 'landscape');
      return $sheet->download('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');

   }


   public function fullMarksheet()
   {
      $stdclass = StdClass::whereIn('in_digit', [06, 07])->get();
      return view('backend.admin.tabulation_sheet.full_marksheet.index', compact('stdclass'));
   }

   public function summeryStudent(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $year = config('running_session');

         $exam = Exam::where('class_id', $class_id)->where('year', config('running_session'))->get();

         $first_term = "";
         $second_term = "";
         if (isset($exam[0])) {
            $first_term = $exam[0]->id;
         }
         if (isset($exam[1])) {
            $second_term = $exam[1]->id;
         }

         if (!empty($first_term) and !empty($second_term)) {
            $data = array();
            $data['class_id'] = $class_id;
            $data['section_id'] = $section_id;
            $data['first_term'] = $first_term;
            $data['second_term'] = $second_term;
            $data['class_name'] = $request->input('class_name');
            $data['section_name'] = $request->input('section_name');
            $data['exam_name'] = $request->input('exam_name');
            $data['year'] = $year;

            DB::statement(DB::raw("set @rownum=0, @class_id='$class_id', @section_id='$section_id',  @year='$year'"));


            $data['result'] = DB::select("SELECT @rownum  := @rownum  + 1 AS rownum,std.name AS std_name, std.std_code,std_classes.name AS class_name, sections.name AS section_name, enrolls.roll from enrolls
            LEFT JOIN students AS std ON std.id = enrolls.student_id 
            LEFT JOIN std_classes ON std_classes.id = enrolls.class_id 
            LEFT JOIN sections ON sections.id = enrolls.section_id 
            WHERE enrolls.class_id = @class_id AND enrolls.section_id = @section_id AND enrolls.year = @year");

            $view = View::make('backend.admin.tabulation_sheet.full_marksheet.summery', compact('data'))->render();
            return response()->json(['html' => $view]);
         } else {
            return response()->json(['html' => "<div class='alert alert-danger'>Please set half yearly and annual exam first and import marks.Be sure that no more than 2 exams in both classes</div>"]);
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function viewFullMarksheet(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $first_term = $request->input('first_term');
         $second_term = $request->input('second_term');
         $student_code = $request->input('student_code');
         $year = config('running_session');

         $first_term_exam = Exam::where('id', $first_term)->first();
         $second_term_exam = Exam::where('id', $second_term)->first();

         $data = array();
         $data['total_student'] = Enroll::where('class_id', $class_id)->where('year', $year)->count();
         $data['student_code'] = $student_code;
         $data['student_name'] = $request->input('student_name');
         $data['std_roll'] = $request->input('std_roll');
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name'] = $request->input('exam_name');
         $data['year'] = config('running_session');
         $data['ft_has_ct'] = $first_term_exam->ct_marks_percentage;
         $data['ft_mmp'] = $first_term_exam->main_marks_percentage;
         $data['st_has_ct'] = $second_term_exam->ct_marks_percentage;
         $data['st_mmp'] = $second_term_exam->main_marks_percentage;

         $data['result'] = GenerateMarksheet::generateMarksheetResult($first_term, $second_term, $class_id, $section_id, $student_code, $year);

         // dd($data['result']);

         $view = View::make('backend.admin.tabulation_sheet.marksheet', compact('data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

}

<?php

namespace App\Http\Controllers\Backend\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\GenerateMarksheet;
use App\Models\StdClass;
use App\Models\Enroll;
use App\Models\Exam;
use App\Models\Section;
use App\Models\SessionYear;
use View;
use DB;
use PDF;

class MarkSheetController extends Controller
{

   public function jrhalfYearlyExamSummery()
   {
      $stdclass = StdClass::whereIn('in_digit', ['06', '07', '08'])->get();
      return view('backend.admin.exam.junior_result.half_yearly_exam', compact('stdclass'));
   }

   public function jrhalfSummeryResult(Request $request)
   {
      if ($request->ajax()) {

            $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $exam_id = $request->input('exam_id');
         $year = config('running_session');
         $session_year = SessionYear::select()->where('running_year',$year)->first();

         $data = array();
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['exam_id'] = $exam_id;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name'] = $request->input('exam_name');
         $data['year'] = $year;

        if(!$session_year->special){
            $data['result'] = $data['result'] = GenerateMarksheet::jrhalfSummeryResult($exam_id, $class_id, $section_id, $year);
        }
        else{
            $data['result'] = $data['result'] = GenerateMarksheet::jrhalfSummeryResultSpecial($exam_id, $class_id, $section_id, $year);
        }
        // dd($data);
        // dd($session_year);


        //=================================$merit start=====================================
        if (count($data['result']) != 0) {
            $merit = array(array("data_id"=>0,"gpa"=>0,"total_marks"=>0));
             $mindx=0;
             // dd($merit);
    
             foreach($data['result'] as $key => $res){
                // dd($res);
                // $merit[$mindx]->
                $merit[$mindx]["data_id"] = $key;
                $merit[$mindx]["gpa"] = round(($res->mainSubPoint/$res->totalSubject),2);
                $merit[$mindx]["total_marks"] = $res->totalMarks;
                $mindx++;
            }
    
            for($i=0;$i<$mindx-1;$i++){
                for($j=0;$j<$mindx-$i-1;$j++){
                    if($merit[$j]["gpa"] == $merit[$j+1]["gpa"]){
                        if($merit[$j]["total_marks"] < $merit[$j+1]["total_marks"]){
                            $temp = $merit[$j];
                            $merit[$j] = $merit[$j+1];
                            $merit[$j+1] = $temp;
                        }
                    }
                    elseif($merit[$j]["gpa"] < $merit[$j+1]["gpa"]){
                        $temp = $merit[$j];
                        $merit[$j] = $merit[$j+1];
                        $merit[$j+1] = $temp;
                    }
                }
            }
    
            foreach($merit as $key=>$m){
                $data_id = $m["data_id"];
                $rank =  $key+1;
                
                
                $rank_mod_10 = $rank % 10;
                $rank_mod_100 = $rank % 100;
                if ($rank_mod_10 == 1 && $rank_mod_100 != 11) $rank = (string)$rank . "st";
                elseif ($rank_mod_10 == 2 && $rank_mod_100 != 12) $rank = (string)$rank . "nd";
                elseif ($rank_mod_10 == 3 && $rank_mod_100 != 13) $rank = (string)$rank . "rd";
                else $rank = (string)$rank . "th";
    
                $data['result'][$data_id]->rank = $rank;
    
                // $data['result'][]
            }
        }
        //=================================$merit end=====================================
        // dd($data);

         $view = View::make('backend.admin.exam.junior_result.half_yearly_summery', compact('data','session_year'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function jrhalfExamMarksheet(Request $request)
   {
    //    dd($request);
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
         $data['rank'] = $request->input('rank');
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['exam_id'] = $exam_id;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name'] = $request->input('exam_name');
         $data['year'] = config('running_session');
         $data['has_ct'] = $exam->ct_marks_percentage;
         $data['mmp'] = $exam->main_marks_percentage;
        // dd($data);
         $session_year = SessionYear::select()->where('running_year',$year)->first();

        //  dd($session_year);

         if(!$session_year->special){
            $data['result'] = GenerateMarksheet::jrhalfExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
         }
         else{
            $data['result'] = GenerateMarksheet::jrhalfExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
         }
        //  dd($data);
         // dd($data['result']);

         $view = View::make('backend.admin.exam.junior_result.half_yearly_marksheet', compact('data','session_year'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function jrHalfyearlyprintMarksheet(Request $request)
   {

      $class_id = $request->input('class_id');
      $section_id = $request->input('section_id');
      $exam_id = $request->input('exam_id');
      $student_code = $request->input('student_code');
      $year = config('running_session');

      $exam = Exam::where('id', $exam_id)->first();

      $data = array();
      $data['student_code'] = $student_code;
      $data['student_name'] = $request->input('student_name');
      $data['std_roll'] = $request->input('std_roll');
      $data['rank'] = $request->input('rank');
      $data['class_id'] = $class_id;
      $data['section_id'] = $section_id;
      $data['exam_id'] = $exam_id;
      $data['class_name'] = $request->input('class_name');
      $data['section_name'] = $request->input('section_name');
      $data['exam_name'] = $request->input('exam_name');
      $data['year'] = config('running_session');
      $data['has_ct'] = $exam->ct_marks_percentage;
      $data['mmp'] = $exam->main_marks_percentage;

      $data['total_std'] = $request->input('total_std');
      $data['total_atd'] = $request->input('total_atd');
      $data['total_wd'] = $request->input('total_wd');
      $data['position'] = $request->input('position');

      $session_year = SessionYear::select()->where('running_year',$year)->first();
      if(!$session_year->special){
        $data['result'] = GenerateMarksheet::jrhalfExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
      }
      else{
        $data['result'] = GenerateMarksheet::jrhalfExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
      }
    //   dd($data);
      $view = View::make('backend.admin.exam.junior_result.half_yearly_printMarksheet', compact('data','session_year'));

      $html = '<!DOCTYPE html><html lang="en">';
      $html .= $view->render();
      $html .= '</html>';
      $pdf = PDF::loadHTML($html);
      $sheet = $pdf->setPaper('a4', 'landscape');
      return $sheet->download('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');

   }


   public function jrfullMarksheet()
   {
      $stdclass = StdClass::whereIn('in_digit', ['06', '07', '08'])->get();
      return view('backend.admin.exam.junior_result.final_exam', compact('stdclass'));
   }

   public function jrfinalSummeryResult(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $exam_id_half = $request->input('exam_id_half');
         $exam_id_final = $request->input('exam_id_final');
         $year = config('running_session');

         $data = array();
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['exam_id_half'] = $exam_id_half;
         $data['exam_id_final'] = $exam_id_final;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name_half'] = $request->input('exam_name_half');
         $data['exam_name_final'] = $request->input('exam_name_final');
         $data['year'] = $year;

         $data['result'] = $data['result'] = GenerateMarksheet::jrfinalSummeryResult($exam_id_half, $exam_id_final, $class_id, $section_id, $year);

         $view = View::make('backend.admin.exam.junior_result.final_summery', compact('data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function jrfinalMarksheet(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $exam_id_half = $request->input('exam_id_half');
         $exam_id_final = $request->input('exam_id_final');
         $student_code = $request->input('student_code');
         $year = config('running_session');


         $data = array();
         $data['total_student'] = Enroll::where('class_id', $class_id)->where('year', $year)->count();
         $data['student_code'] = $student_code;
         $data['student_name'] = $request->input('student_name');
         $data['std_roll'] = $request->input('std_roll');
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['exam_id_half'] = $exam_id_half;
         $data['exam_id_final'] = $exam_id_final;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name_half'] = $request->input('exam_name_half');
         $data['exam_name_final'] = $request->input('exam_name_final');
         $data['year'] = config('running_session');


         $data['result'] = GenerateMarksheet::jrfinalMarksheet($exam_id_half, $exam_id_final, $class_id, $section_id, $student_code, $year);

         // dd($data['result']);

         $view = View::make('backend.admin.exam.junior_result.final_marksheet', compact('data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function jrfinalyprintMarksheet(Request $request)
   {

      $class_id = $request->input('class_id');
      $section_id = $request->input('section_id');
      $exam_id_half = $request->input('exam_id_half');
      $exam_id_final = $request->input('exam_id_final');
      $student_code = $request->input('student_code');
      $year = config('running_session');

      //dd($exam_id_half,$exam_id_final,$student_code);
      $data = array();
      $data['student_code'] = $student_code;
      $data['student_name'] = $request->input('student_name');
      $data['std_roll'] = $request->input('std_roll');
      $data['class_id'] = $class_id;
      $data['section_id'] = $section_id;
      $data['exam_id_half'] = $exam_id_half;
      $data['exam_id_final'] = $exam_id_final;
      $data['class_name'] = $request->input('class_name');
      $data['section_name'] = $request->input('section_name');
      $data['exam_name_half'] = $request->input('exam_name_half');
      $data['exam_name_final'] = $request->input('exam_name_final');
      $data['year'] = config('running_session');


      $data['total_std'] = $request->input('total_std');
      $data['total_atd'] = $request->input('total_atd');
      $data['total_wd'] = $request->input('total_wd');
      $data['position'] = $request->input('position');

      $data['result'] = GenerateMarksheet::jrfinalMarksheet($exam_id_half, $exam_id_final, $class_id, $section_id, $student_code, $year);
      $view = View::make('backend.admin.exam.junior_result.final_printMarksheet', compact('data'));

      $html = '<!DOCTYPE html><html lang="en">';
      $html .= $view->render();
      $html .= '</html>';
      $pdf = PDF::loadHTML($html);
      $sheet = $pdf->setPaper('a4', 'landscape');
      return $sheet->download('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');

   }

   public function srResult()
   {
      $stdclass = StdClass::whereIn('in_digit', ['09', '10'])->get();
      return view('backend.admin.exam.senior_result.index', compact('stdclass'));
   }

   public function srSummeryResult(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $exam_id = $request->input('exam_id');
         $year = config('running_session');
         $session_year = SessionYear::select()->where('running_year',$year)->first();
        // dd($session_year);
         $data = array();
         $data['class_id'] = $class_id;
         $data['section_id'] = $section_id;
         $data['exam_id'] = $exam_id;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name'] = $request->input('exam_name');
         $data['year'] = $year;
        if(!$session_year->special){
            $data['result'] = $data['result'] = GenerateMarksheet::srSummeryResult($exam_id, $class_id, $section_id, $year);
        }
        else{
            $data['result'] = $data['result'] = GenerateMarksheet::srSummeryResultSpecial($exam_id, $class_id, $section_id, $year);
        }


        //=================================$merit start=====================================
        if (count($data['result']) != 0) {
            $merit = array(array("data_id"=>0,"gpa"=>0,"total_marks"=>0));
             $mindx=0;
             // dd($merit);
    
             foreach($data['result'] as $key => $res){
                // dd($res);
                // $merit[$mindx]->
                $merit[$mindx]["data_id"] = $key;
                $merit[$mindx]["gpa"] = round(($res->mainSubPoint/$res->totalSubject),2);
                $merit[$mindx]["total_marks"] = $res->totalMarks;
                $mindx++;
            }
    
            for($i=0;$i<$mindx-1;$i++){
                for($j=0;$j<$mindx-$i-1;$j++){
                    if($merit[$j]["gpa"] == $merit[$j+1]["gpa"]){
                        if($merit[$j]["total_marks"] < $merit[$j+1]["total_marks"]){
                            $temp = $merit[$j];
                            $merit[$j] = $merit[$j+1];
                            $merit[$j+1] = $temp;
                        }
                    }
                    elseif($merit[$j]["gpa"] < $merit[$j+1]["gpa"]){
                        $temp = $merit[$j];
                        $merit[$j] = $merit[$j+1];
                        $merit[$j+1] = $temp;
                    }
                }
            }
    
            foreach($merit as $key=>$m){
                $data_id = $m["data_id"];
                $rank =  $key+1;
                
                $rank_mod_10 = $rank % 10;
                $rank_mod_100 = $rank % 100;
                if ($rank_mod_10 == 1 && $rank_mod_100 != 11) $rank = (string)$rank . "st";
                elseif ($rank_mod_10 == 2 && $rank_mod_100 != 12) $rank = (string)$rank . "nd";
                elseif ($rank_mod_10 == 3 && $rank_mod_100 != 13) $rank = (string)$rank . "rd";
                else $rank = (string)$rank . "th";
    
                $data['result'][$data_id]->rank = $rank;
    
                // $data['result'][]
            }
        }
        //=================================$merit end=====================================
        // dd($data);
        // dd($data['result']);
         $view = View::make('backend.admin.exam.senior_result.summery', compact('data','session_year'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function srMarksheet(Request $request)
   {
    //    dd($request);
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $rank = $request->input('rank');
         $section_id = $request->input('section_id');
         $exam_id = $request->input('exam_id');
         $student_code = $request->input('student_code');
         $year = config('running_session');
         $session_year = SessionYear::select()->where('running_year',$year)->first();
        //  dd($session_year);

         $exam = Exam::where('id', $exam_id)->first();

         $data = array();
         $data['total_student'] = Enroll::where('class_id', $class_id)->where('year', $year)->count();
         $data['student_code'] = $student_code;
         $data['student_name'] = $request->input('student_name');
         $data['std_roll'] = $request->input('std_roll');
         $data['class_id'] = $class_id;
         $data['rank'] = $rank;
         $data['section_id'] = $section_id;
         $data['exam_id'] = $exam_id;
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');
         $data['exam_name'] = $request->input('exam_name');
         $data['year'] = config('running_session');
         $data['has_ct'] = $exam->ct_marks_percentage;
         $data['mmp'] = $exam->main_marks_percentage;


        if(!$session_year->special){
            $data['result'] = GenerateMarksheet::srExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
        }
        else{
            $data['result'] = GenerateMarksheet::srExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
        }

        //  dd($data['result']);

         $view = View::make('backend.admin.exam.senior_result.marksheet', compact('data','session_year'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function srPrintMarksheet(Request $request)
   {


      $class_id = $request->input('class_id');
      $section_id = $request->input('section_id');
      $exam_id = $request->input('exam_id');
      $student_code = $request->input('student_code');
      $year = config('running_session');

      $exam = Exam::where('id', $exam_id)->first();

      $data = array();
      $data['student_code'] = $student_code;
      $data['student_name'] = $request->input('student_name');
      $data['std_roll'] = $request->input('std_roll');
      $data['rank'] = $request->input('rank');
      $data['class_id'] = $class_id;
      $data['section_id'] = $section_id;
      $data['exam_id'] = $exam_id;
      $data['class_name'] = $request->input('class_name');
      $data['section_name'] = $request->input('section_name');
      $data['exam_name'] = $request->input('exam_name');
      $data['year'] = config('running_session');
      $data['has_ct'] = $exam->ct_marks_percentage;
      $data['mmp'] = $exam->main_marks_percentage;

      $data['total_std'] = $request->input('total_std');
      $data['total_atd'] = $request->input('total_atd');
      $data['total_wd'] = $request->input('total_wd');
      $data['position'] = $request->input('position');

        $session_year = SessionYear::select()->where('running_year',$year)->first();
        if(!$session_year->special){
            $data['result'] = GenerateMarksheet::srExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
        }
        else{
            $data['result'] = GenerateMarksheet::srExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
        }
        // dd($data);
      $view = View::make('backend.admin.exam.senior_result.printMarksheet', compact('data','session_year'));

      $html = '<!DOCTYPE html><html lang="en">';
      $html .= $view->render();
      $html .= '</html>';
      $pdf = PDF::loadHTML($html);
      $sheet = $pdf->setPaper('a4', 'landscape');
      return $sheet->download('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');

   }
}

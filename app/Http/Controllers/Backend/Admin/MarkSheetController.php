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

    public function jrhalfSummeryResultForClassRank($exam_id, $class_id, $year)
    {
        $data['result'] = GenerateMarksheet::jrhalfSummeryResult($exam_id, $class_id, "null", $year);

        // dd($data);
        // dd($session_year);


        //=================================$merit start=====================================
        if (count($data['result']) != 0) {
            $merit = array(array("data_id" => 0, "gpa" => 0, "total_marks" => 0));
            $class_rank = array();
            $mindx = 0;
            // dd($merit);

            foreach ($data['result'] as $key => $res) {
                // dd($res);
                // $merit[$mindx]->
                $merit[$mindx]["data_id"] = $key;

                /**gpa count start*/
                $full_marksheet = GenerateMarksheet::jrhalfExamMarksheet($exam_id, $class_id, "null", $res->stdCode, $year);
                // dd($full_marksheet);
                $total_marks = 0;
                $cgpa_status = 1;
                $total_gpa = 0;
                $total_cgpa = 0;
                $optional_sub_marks = 0;
                $total_subjects = 0;

                $total_subjects = count($full_marksheet);

                foreach ($full_marksheet as $row) {
                    $total_marks += $row->obtainedMark;

                    if ($row->grade === 'F') {
                        $cgpa_status = 0;
                    }

                    if ($cgpa_status != 0) {
                        $total_cgpa = round($total_cgpa + $row->CGPA, 2);
                    }

                    if ($row->subject_id == $row->optional_subject) {
                        $total_subjects = count($full_marksheet) - 1; // Optional subject not count on average point so less
                        $total_cgpa = $total_cgpa - $row->CGPA;

                        if ($row->CGPA > 2.0) {
                            $optional_sub_marks = $row->CGPA - 2.0;
                            $total_cgpa = $total_cgpa + $optional_sub_marks;
                        }
                    }
                }

                // $cgpa = sprintf('%0.2f', $total_cgpa / $total_subjects);
                $cgpa = $cgpa_status ? sprintf('%0.2f', $total_cgpa / $total_subjects) : 0;
                // if($cgpa_status == 0) $cgpa = 0;
                $cgpa = $cgpa > 5 ? '5.00' : $cgpa;
                /**gpa count end*/
                // $merit[$mindx]["gpa"] = round(($res->mainSubPoint / $res->totalSubject), 2);
                $merit[$mindx]["name"] = $row->name;
                $merit[$mindx]["std_code"] = $row->std_code;
                $merit[$mindx]["gpa"] = $cgpa;
                $merit[$mindx]["total_marks"] = $res->totalMarks;
                $mindx++;
            }
            // dd($merit);
            for ($i = 0; $i < $mindx - 1; $i++) {
                for ($j = 0; $j < $mindx - $i - 1; $j++) {
                    if ($merit[$j]["gpa"] == $merit[$j + 1]["gpa"]) {
                        if ($merit[$j]["total_marks"] < $merit[$j + 1]["total_marks"]) {
                            $temp = $merit[$j];
                            $merit[$j] = $merit[$j + 1];
                            $merit[$j + 1] = $temp;
                        }
                    } elseif ($merit[$j]["gpa"] < $merit[$j + 1]["gpa"]) {
                        $temp = $merit[$j];
                        $merit[$j] = $merit[$j + 1];
                        $merit[$j + 1] = $temp;
                    }
                }
            }

            foreach ($merit as $key => $m) {
                $data_id = $m["data_id"];
                $rank =  $key + 1;


                $rank_mod_10 = $rank % 10;
                $rank_mod_100 = $rank % 100;
                if ($rank_mod_10 == 1 && $rank_mod_100 != 11) $rank = (string)$rank . "st";
                elseif ($rank_mod_10 == 2 && $rank_mod_100 != 12) $rank = (string)$rank . "nd";
                elseif ($rank_mod_10 == 3 && $rank_mod_100 != 13) $rank = (string)$rank . "rd";
                else $rank = (string)$rank . "th";

                // $data['result'][$data_id]->rank = $rank;
                $class_rank[$m["std_code"]] = $rank;

                // $data['result'][]
            }
            // dd($class_rank);
            return $class_rank;
        }
        //=================================$merit end=====================================
    }
    public function jrhalfSummeryResult(Request $request)
    {
        if ($request->ajax()) {

            $class_id = $request->input('class_id');
            $section_id = $request->input('section_id');
            $exam_id = $request->input('exam_id');
            $year = config('running_session');
            $session_year = SessionYear::select()->where('running_year', $year)->first();

            $data = array();
            $data['class_id'] = $class_id;
            $data['section_id'] = $section_id;
            $data['exam_id'] = $exam_id;
            $data['class_name'] = $request->input('class_name');
            $data['section_name'] = $request->input('section_name');
            $data['exam_name'] = $request->input('exam_name');
            $data['year'] = $year;

            if (!$session_year->special) {
                $data['result'] = $data['result'] = GenerateMarksheet::jrhalfSummeryResult($exam_id, $class_id, $section_id, $year);
            } else {
                $data['result'] = $data['result'] = GenerateMarksheet::jrhalfSummeryResultSpecial($exam_id, $class_id, $section_id, $year);
            }
            // dd($data);
            // dd($session_year);

            $class_rank = $this::jrhalfSummeryResultForClassRank($exam_id, $class_id, $year);
            //=================================$merit start=====================================
            if (count($data['result']) != 0) {
                $merit = array(array("data_id" => 0, "gpa" => 0, "total_marks" => 0));
                $mindx = 0;
                // dd($merit);

                foreach ($data['result'] as $key => $res) {
                    // dd($res);
                    // $merit[$mindx]->
                    $merit[$mindx]["data_id"] = $key;

                    /**gpa count start*/
                    $full_marksheet = GenerateMarksheet::jrhalfExamMarksheet($exam_id, $class_id, $section_id, $res->stdCode, $year);
                    // dd($full_marksheet);
                    $total_marks = 0;
                    $cgpa_status = 1;
                    $total_gpa = 0;
                    $total_cgpa = 0;
                    $optional_sub_marks = 0;
                    $total_subjects = 0;

                    $total_subjects = count($full_marksheet);

                    foreach ($full_marksheet as $row) {
                        // dd($full_marksheet);
                        $total_marks += $row->obtainedMark;

                        if ($row->grade === 'F') {
                            $cgpa_status = 0;
                        }

                        if ($cgpa_status != 0) {
                            $total_cgpa = round($total_cgpa + $row->CGPA, 2);
                        }

                        if ($row->subject_id == $row->optional_subject) {
                            $total_subjects = count($full_marksheet) - 1; // Optional subject not count on average point so less
                            $total_cgpa = $total_cgpa - $row->CGPA;

                            if ($row->CGPA > 2.0) {
                                $optional_sub_marks = $row->CGPA - 2.0;
                                $total_cgpa = $total_cgpa + $optional_sub_marks;
                            }
                        }
                    }

                    // $cgpa = sprintf('%0.2f', $total_cgpa / $total_subjects);
                    $cgpa = $cgpa_status ? sprintf('%0.2f', $total_cgpa / $total_subjects) : 0;
                    // dd($cgpa);
                    // if($cgpa_status == 0) $cgpa = 0;
                    $cgpa = $cgpa > 5 ? '5.00' : $cgpa;
                    /**gpa count end*/
                    // $merit[$mindx]["gpa"] = round(($res->mainSubPoint / $res->totalSubject), 2);
                    $merit[$mindx]["name"] = $row->name;
                    $merit[$mindx]["std_code"] = $row->std_code;
                    $merit[$mindx]["gpa"] = $cgpa;
                    $merit[$mindx]["total_marks"] = $res->totalMarks;
                    $mindx++;
                }
                // dd($merit);
                for ($i = 0; $i < $mindx - 1; $i++) {
                    for ($j = 0; $j < $mindx - $i - 1; $j++) {
                        if ($merit[$j]["gpa"] == $merit[$j + 1]["gpa"]) {
                            if ($merit[$j]["total_marks"] < $merit[$j + 1]["total_marks"]) {
                                $temp = $merit[$j];
                                $merit[$j] = $merit[$j + 1];
                                $merit[$j + 1] = $temp;
                            }
                        } elseif ($merit[$j]["gpa"] < $merit[$j + 1]["gpa"]) {
                            $temp = $merit[$j];
                            $merit[$j] = $merit[$j + 1];
                            $merit[$j + 1] = $temp;
                        }
                    }
                }

                foreach ($merit as $key => $m) {
                    $data_id = $m["data_id"];
                    $rank =  $key + 1;


                    $rank_mod_10 = $rank % 10;
                    $rank_mod_100 = $rank % 100;
                    if ($rank_mod_10 == 1 && $rank_mod_100 != 11) $rank = (string)$rank . "st";
                    elseif ($rank_mod_10 == 2 && $rank_mod_100 != 12) $rank = (string)$rank . "nd";
                    elseif ($rank_mod_10 == 3 && $rank_mod_100 != 13) $rank = (string)$rank . "rd";
                    else $rank = (string)$rank . "th";

                    $data['result'][$data_id]->rank = $rank;
                    $data['result'][$data_id]->class_rank = $class_rank[$m["std_code"]];

                    // $data['result'][]
                }
            }
            //=================================$merit end=====================================
            // dd($data);

            $view = View::make('backend.admin.exam.junior_result.half_yearly_summery', compact('data', 'session_year'))->render();
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
            $data['total_student'] = Enroll::where('class_id', $class_id)->where('section_id', $section_id)->where('year', $year)->count();
            $data['student_code'] = $student_code;
            $data['student_name'] = $request->input('student_name');
            $data['std_roll'] = $request->input('std_roll');
            $data['rank'] = $request->input('rank');
            $data['class_rank'] = $request->input('class_rank');
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
            $session_year = SessionYear::select()->where('running_year', $year)->first();

            //  dd($session_year);

            if (!$session_year->special) {
                $data['result'] = GenerateMarksheet::jrhalfExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
            } else {
                $data['result'] = GenerateMarksheet::jrhalfExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
            }
            //  dd($data);
            // dd($data['result']);

            $view = View::make('backend.admin.exam.junior_result.half_yearly_marksheet', compact('data', 'session_year'))->render();
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
        $data['class_rank'] = $request->input('class_rank');
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

        $session_year = SessionYear::select()->where('running_year', $year)->first();
        if (!$session_year->special) {
            $data['result'] = GenerateMarksheet::jrhalfExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
        } else {
            $data['result'] = GenerateMarksheet::jrhalfExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
        }
        //   dd($data);
        $view = View::make('backend.admin.exam.junior_result.half_yearly_printMarksheet', compact('data', 'session_year'));

        $html = '<!DOCTYPE html><html lang="en">';
        $html .= $view->render();
        $html .= '</html>';
        $pdf = PDF::loadHTML($html);
        $sheet = $pdf->setPaper('legal', 'landscape');
        return $sheet->stream('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');
    }

    //extra print
    public function jrHalfyearlyMarksheetPrint(Request $request)
    {
        // dd($request);
        $class_id = $request->input('class_id');
        $section_id = $request->input('section_id');
        $exam_id = $request->input('exam_id');
        $student_code = $request->input('std_code');
        $year = config('running_session');

        $exam = Exam::where('id', $exam_id)->first();

        $data = array();
        $data['student_code'] = $student_code;
        $data['student_name'] = $request->input('std_name');
        $data['std_roll'] = $request->input('std_roll');
        $data['rank'] = $request->input('rank');
        $data['class_rank'] = $request->input('class_rank');
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['exam_id'] = $exam_id;
        $data['class_name'] = $request->input('class_name');
        $data['section_name'] = $request->input('section_name');
        $data['exam_name'] = $request->input('exam_name');
        $data['year'] = config('running_session');
        $data['has_ct'] = $exam->ct_marks_percentage;
        $data['mmp'] = $exam->main_marks_percentage;
        $data['total_std'] = Enroll::where('class_id', $class_id)->where('section_id', $section_id)->where('year', $year)->count();
        $data['total_atd'] = $request->input('total_atd');
        $data['total_wd'] = $request->input('total_wd');
        $data['position'] = $request->input('position');

        $session_year = SessionYear::select()->where('running_year', $year)->first();
        if (!$session_year->special) {
            $data['result'] = GenerateMarksheet::jrhalfExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
        } else {
            $data['result'] = GenerateMarksheet::jrhalfExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
        }
        // dd($data);
        $view = View::make('backend.admin.exam.junior_result.half_yearly_printMarksheet', compact('data', 'session_year'));

        $html = '<!DOCTYPE html><html lang="en">';
        $html .= $view->render();
        $html .= '</html>';
        $pdf = PDF::loadHTML($html);
        $sheet = $pdf->setPaper('legal', 'landscape');
        return $sheet->stream('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');
    }

    public function jrhalfSummeryResultGradeSummaryPrint(Request $request){
        $class_id = $request->input('class_id');
        $section_id = $request->input('section_id');
        $exam_id = $request->input('exam_id');
        $year = config('running_session');

        $exam = Exam::where('id', $exam_id)->first();

        $data = array();
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['exam_id'] = $exam_id;
        $data['class_name'] = $request->input('class_name');
        $data['section_name'] = $request->input('section_name');
        $data['exam_name'] = $request->input('exam_name');
        $data['year'] = config('running_session');


        $session_year = SessionYear::select()->where('running_year', $year)->first();

        $data['result'] = GenerateMarksheet::jrhalfSummeryResult($exam_id, $class_id, "null", $year);

        if (count($data['result']) != 0) {

            $grade_summary = array("Golap" => array("A+"=>0,"A"=>0,"A-"=>0,"B"=>0,"C"=>0,"D"=>0,"F"=>0),
                                  "Shapla" => array("A+"=>0,"A"=>0,"A-"=>0,"B"=>0,"C"=>0,"D"=>0,"F"=>0));

            foreach ($data['result'] as $key => $res) {
                $section_name = Section::where('id', $res->stdSection)->first()->name;

                /**gpa count start*/
                $full_marksheet = GenerateMarksheet::jrhalfExamMarksheet($exam_id, $class_id, "null", $res->stdCode, $year);
                // dd($full_marksheet);
                $total_marks = 0;
                $cgpa_status = 1;
                $total_gpa = 0;
                $total_cgpa = 0;
                $optional_sub_marks = 0;
                $total_subjects = 0;

                $total_subjects = count($full_marksheet);

                foreach ($full_marksheet as $row) {
                    $total_marks += $row->obtainedMark;

                    if ($row->grade === 'F') {
                        $cgpa_status = 0;
                    }

                    if ($cgpa_status != 0) {
                        $total_cgpa = round($total_cgpa + $row->CGPA, 2);
                    }

                    if ($row->subject_id == $row->optional_subject) {
                        $total_subjects = count($full_marksheet) - 1; // Optional subject not count on average point so less
                        $total_cgpa = $total_cgpa - $row->CGPA;

                        if ($row->CGPA > 2.0) {
                            $optional_sub_marks = $row->CGPA - 2.0;
                            $total_cgpa = $total_cgpa + $optional_sub_marks;
                        }
                    }
                }

                $cgpa = $cgpa_status ? sprintf('%0.2f', $total_cgpa / $total_subjects) : 0;
                // if($cgpa_status == 0) $cgpa = 0;
                $cgpa = $cgpa > 5 ? '5.00' : $cgpa;
                $gpa = "F";

                if ($cgpa_status != 0) {

                    if ($cgpa >= 5) {
                        $gpa = "A+";
                    } else if ($cgpa >= 4 and $cgpa <= 4.99) {
                        $gpa = "A";
                    } else if ($cgpa >= 3.50 and $cgpa <= 3.99) {
                        $gpa = "A-";
                    } else if ($cgpa >= 3 and $cgpa <= 3.49) {
                        $gpa = "B";
                    } else if ($cgpa >= 2 and $cgpa <= 2.99) {
                        $gpa = "C";
                    } else if ($cgpa >= 1 and $cgpa <= 1.99) {
                        $gpa = "D";
                    } else {
                        $gpa = "F";
                    }
                } else {
                    $gpa = "F";
                }
                $grade_summary[$section_name][$gpa] = $grade_summary[$section_name][$gpa] + 1;
            }

            // dd($grade_summary);
            $view = View::make('backend.admin.exam.junior_result.half_yearly_grade_summary_print', compact('data', 'session_year','grade_summary'));

            $html = '<!DOCTYPE html><html lang="en">';
            $html .= $view->render();
            $html .= '</html>';
            $pdf = PDF::loadHTML($html);
            $sheet = $pdf->setPaper('legal', 'landscape');
            return $sheet->stream('Summary_' .  '_' . $data['class_name'] . '.pdf');
        }
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
        $sheet = $pdf->setPaper('legal', 'landscape');
        return $sheet->stream('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');
    }

    public function srResult()
    {
        $stdclass = StdClass::whereIn('in_digit', ['09', '10'])->get();
        return view('backend.admin.exam.senior_result.index', compact('stdclass'));
    }

    public function srSummeryResultForClassRank($exam_id, $class_id, $year){

        $data['result'] = GenerateMarksheet::srSummeryResult($exam_id, $class_id, "null", $year);
        //=================================$merit start=====================================
        if (count($data['result']) != 0) {
            $merit = array(array("data_id" => 0, "gpa" => 0, "total_marks" => 0));
            $class_rank = array();
            $mindx = 0;
            // dd($merit);

            foreach ($data['result'] as $key => $res) {
                // dd($res);
                // $merit[$mindx]->
                /** */
                $merit[$mindx]["data_id"] = $key;

                /**gpa count start*/
                $full_marksheet = GenerateMarksheet::srExamMarksheet($exam_id, $class_id, "null", $res->stdCode, $year);
                $total_marks = 0;
                $cgpa_status = 1;
                $total_gpa = 0;
                $total_cgpa = 0;
                $optional_sub_marks = 0;
                $total_subjects = 0;

                $bangla_marks = 0;
                $combined_bangla = 0;
                $combined_bangla_marks = 0;
                $bangla_both_theory = 0;
                $bangla_both_mcq = 0;

                $ban_cgpa = $ban_grade = 0;

                $eng_marks = 0;
                $combined_eng = 0;
                $combined_eng_marks = 0;

                $eng_cgpa = $eng_grade = 0;

                $total_subjects = count($full_marksheet);

                foreach ($full_marksheet as $row) {
                    $total_marks += $row->obtainedMark;

                    if ($row->grade === 'F' && $row->subject_code != 101 && $row->subject_code != 102 && $row->subject_code != 107 && $row->subject_code != 108 && $row->subject_id != $row->optional_subject) {
                        $cgpa_status = 0;
                    }

                    if (
                        $row->grade === 'F' && $row->subject_code != 101 && $row->subject_code != 102 && $row->subject_code != 107 && $row->subject_code != 108 && $row->subject_id != $row->optional_subject
                    ) {
                        $cgpa_status = 0;
                    }

                    if ($cgpa_status != 0) {
                        $total_cgpa = round($total_cgpa + $row->CGPA, 2);
                    }

                    //special check
                    {
                        // Bangla combined
                        if ($row->subject_code == 101 || $row->subject_code == 102) {
                            $combined_bangla = $combined_bangla + 1;
                            $combined_bangla_marks = $combined_bangla_marks + $row->obtainedMark;
                            $total_cgpa = $total_cgpa - $row->CGPA;
                            $bangla_both_theory = $bangla_both_theory + $row->theory_marks;
                            $bangla_both_mcq = $bangla_both_mcq + $row->mcq_marks;
                        }

                        // English combined
                        if ($row->subject_code == 107 || $row->subject_code == 108) {
                            $combined_eng = $combined_eng + 1;
                            $combined_eng_marks = $combined_eng_marks + $row->obtainedMark;
                            $total_cgpa = $total_cgpa - $row->CGPA;
                        }

                        // carrer and physical education combined
                        if ($row->subject_code == 156 || $row->subject_code == 133) {
                            $total_subjects = $total_subjects - 1; //  subject not count on average point so less
                            $total_cgpa = $total_cgpa - $row->CGPA;
                        }

                        // Optional subject calculation
                        if ($row->subject_id == $row->optional_subject) {
                            $total_subjects = $total_subjects - 1; // Optional subject not count on average point so less
                            $total_cgpa = $total_cgpa - $row->CGPA;

                            if ($row->CGPA > 2.0) {
                                $optional_sub_marks = $row->CGPA - 2.0;
                                $total_cgpa = $total_cgpa + $optional_sub_marks;
                            }
                        }
                    }
                }

                //dd($combined_bangla_marks);

                if ($combined_bangla == 2) {
                    $total_subjects = $total_subjects - 1; // both are now 1 subject so 1 minus from total subject
                    $bangla_marks = round($combined_bangla_marks / 2, 2);

                    // need total 66 to pass the subject
                    if ($combined_bangla_marks >= 66 && $bangla_both_theory >= 46 && $bangla_both_mcq >= 20) {
                        if ($bangla_marks >= 80) {
                            $ban_cgpa = 5.0;
                            $ban_grade = 'A+';
                            $total_cgpa = $total_cgpa + $ban_cgpa;
                        } elseif ($bangla_marks >= 70 and $bangla_marks <= 79) {
                            $ban_cgpa = 4.0;
                            $ban_grade = 'A';
                            $total_cgpa = $total_cgpa + $ban_cgpa;
                        } elseif ($bangla_marks >= 60 and $bangla_marks <= 69) {
                            $ban_cgpa = 3.5;
                            $ban_grade = 'A-';
                            $total_cgpa = $total_cgpa + $ban_cgpa;
                        } elseif ($bangla_marks >= 50 and $bangla_marks <= 59) {
                            $ban_cgpa = 3.0;
                            $ban_grade = 'B';
                            $total_cgpa = $total_cgpa + $ban_cgpa;
                        } elseif ($bangla_marks >= 40 and $bangla_marks <= 49) {
                            $ban_cgpa = 2.0;
                            $ban_grade = 'C';
                            $total_cgpa = $total_cgpa + $ban_cgpa;
                        } elseif ($bangla_marks >= 33 and $bangla_marks <= 39) {
                            $ban_cgpa = 1.0;
                            $ban_grade = 'D';
                            $total_cgpa = $total_cgpa + $ban_cgpa;
                        } else {
                            $ban_cgpa = 3.5;
                            $ban_grade = 'A-';
                            $total_cgpa = $total_cgpa + $ban_cgpa;
                        }
                    } else {
                        $ban_cgpa = 0.0;
                        $ban_grade = 'F';
                        $cgpa_status = 0;
                    }
                }

                if ($combined_eng == 2) {
                    $total_subjects = $total_subjects - 1; // both are now 1 subject so 1 minus from total subject
                    $eng_marks = round($combined_eng_marks / 2, 2);

                    if ($combined_eng_marks >= 66) {
                        if ($eng_marks >= 80) {
                            $eng_cgpa = 5.0;
                            $eng_grade = 'A+';
                            $total_cgpa = $total_cgpa + $eng_cgpa;
                        } elseif ($eng_marks >= 70 and $eng_marks <= 79) {
                            $eng_cgpa = 4.0;
                            $eng_grade = 'A';
                            $total_cgpa = $total_cgpa + $eng_cgpa;
                        } elseif ($eng_marks >= 60 and $eng_marks <= 69) {
                            $eng_cgpa = 3.5;
                            $eng_grade = 'A-';
                            $total_cgpa = $total_cgpa + $eng_cgpa;
                        } elseif ($eng_marks >= 50 and $eng_marks <= 59) {
                            $eng_cgpa = 3.0;
                            $eng_grade = 'B';
                            $total_cgpa = $total_cgpa + $eng_cgpa;
                        } elseif ($eng_marks >= 40 and $eng_marks <= 49) {
                            $eng_cgpa = 2.0;
                            $eng_grade = 'C';
                            $total_cgpa = $total_cgpa + $eng_cgpa;
                        } elseif ($eng_marks >= 33 and $eng_marks <= 39) {
                            $eng_cgpa = 1.0;
                            $eng_grade = 'D';
                            $total_cgpa = $total_cgpa + $eng_cgpa;
                        } else {
                            $eng_cgpa = 3.5;
                            $eng_grade = 'A-';
                            $total_cgpa = $total_cgpa + $eng_cgpa;
                        }
                    } else {
                        $eng_cgpa = 0.0;
                        $eng_grade = 'F';
                        $cgpa_status = 0;
                    }
                }

                // $cgpa = sprintf('%0.2f', $total_cgpa / $total_subjects);
                $cgpa = $cgpa_status ? sprintf('%0.2f', $total_cgpa / $total_subjects) : 0;
                // if($cgpa_status == 0) $cgpa = 0;
                $cgpa = $cgpa > 5 ? '5.00' : $cgpa;


                /**gpa count end*/
                $merit[$mindx]["name"] = $row->name;
                $merit[$mindx]["std_code"] = $row->std_code;
                $merit[$mindx]["gpa"] = $cgpa;
                $merit[$mindx]["total_marks"] = $res->totalMarks;
                $mindx++;
            }

            for ($i = 0; $i < $mindx - 1; $i++) {
                for ($j = 0; $j < $mindx - $i - 1; $j++) {
                    if ($merit[$j]["gpa"] == $merit[$j + 1]["gpa"]) {
                        if ($merit[$j]["total_marks"] < $merit[$j + 1]["total_marks"]) {
                            $temp = $merit[$j];
                            $merit[$j] = $merit[$j + 1];
                            $merit[$j + 1] = $temp;
                        }
                    } elseif ($merit[$j]["gpa"] < $merit[$j + 1]["gpa"]) {
                        $temp = $merit[$j];
                        $merit[$j] = $merit[$j + 1];
                        $merit[$j + 1] = $temp;
                    }
                }
            }

            foreach ($merit as $key => $m) {
                $data_id = $m["data_id"];
                $rank =  $key + 1;

                $rank_mod_10 = $rank % 10;
                $rank_mod_100 = $rank % 100;
                if ($rank_mod_10 == 1 && $rank_mod_100 != 11) $rank = (string)$rank . "st";
                elseif ($rank_mod_10 == 2 && $rank_mod_100 != 12) $rank = (string)$rank . "nd";
                elseif ($rank_mod_10 == 3 && $rank_mod_100 != 13) $rank = (string)$rank . "rd";
                else $rank = (string)$rank . "th";

                // $data['result'][$data_id]->rank = $rank;
                $class_rank[$m["std_code"]] = $rank;

                // $data['result'][]
            }
            return $class_rank;
        }
        //=================================$merit end=====================================
    }
    public function srSummeryResult(Request $request)
    {
        if ($request->ajax()) {

            $class_id = $request->input('class_id');
            $section_id = $request->input('section_id');
            $exam_id = $request->input('exam_id');
            $year = config('running_session');
            $session_year = SessionYear::select()->where('running_year', $year)->first();
            // dd($session_year);
            $data = array();
            $data['class_id'] = $class_id;
            $data['section_id'] = $section_id;
            $data['exam_id'] = $exam_id;
            $data['class_name'] = $request->input('class_name');
            $data['section_name'] = $request->input('section_name');
            $data['exam_name'] = $request->input('exam_name');
            $data['year'] = $year;
            if (!$session_year->special) {
                $data['result'] = $data['result'] = GenerateMarksheet::srSummeryResult($exam_id, $class_id, $section_id, $year);
            } else {
                $data['result'] = $data['result'] = GenerateMarksheet::srSummeryResultSpecial($exam_id, $class_id, $section_id, $year);
            }


            $class_rank = $this::srSummeryResultForClassRank($exam_id, $class_id, $year);
            //=================================$merit start=====================================
            if (count($data['result']) != 0) {
                $merit = array(array("data_id" => 0, "gpa" => 0, "total_marks" => 0));
                $mindx = 0;
                // dd($merit);

                foreach ($data['result'] as $key => $res) {
                    // dd($res);
                    // $merit[$mindx]->
                    /** */
                    $merit[$mindx]["data_id"] = $key;

                    /**gpa count start*/
                    $full_marksheet = GenerateMarksheet::srExamMarksheet($exam_id, $class_id, $section_id, $res->stdCode, $year);
                    $total_marks = 0;
                    $cgpa_status = 1;
                    $total_gpa = 0;
                    $total_cgpa = 0;
                    $optional_sub_marks = 0;
                    $total_subjects = 0;

                    $bangla_marks = 0;
                    $combined_bangla = 0;
                    $combined_bangla_marks = 0;
                    $bangla_both_theory = 0;
                    $bangla_both_mcq = 0;

                    $ban_cgpa = $ban_grade = 0;

                    $eng_marks = 0;
                    $combined_eng = 0;
                    $combined_eng_marks = 0;

                    $eng_cgpa = $eng_grade = 0;

                    $total_subjects = count($full_marksheet);

                    foreach ($full_marksheet as $row) {
                        $total_marks += $row->obtainedMark;

                        if ($row->grade === 'F' && $row->subject_code != 101 && $row->subject_code != 102 && $row->subject_code != 107 && $row->subject_code != 108 && $row->subject_id != $row->optional_subject) {
                            $cgpa_status = 0;
                        }

                        if (
                            $row->grade === 'F' && $row->subject_code != 101 && $row->subject_code != 102 && $row->subject_code != 107 && $row->subject_code != 108 && $row->subject_id != $row->optional_subject
                        ) {
                            $cgpa_status = 0;
                        }

                        if ($cgpa_status != 0) {
                            $total_cgpa = round($total_cgpa + $row->CGPA, 2);
                        }

                        //special check
                        if (!$session_year->special) {
                            // Bangla combined
                            if ($row->subject_code == 101 || $row->subject_code == 102) {
                                $combined_bangla = $combined_bangla + 1;
                                $combined_bangla_marks = $combined_bangla_marks + $row->obtainedMark;
                                $total_cgpa = $total_cgpa - $row->CGPA;
                                $bangla_both_theory = $bangla_both_theory + $row->theory_marks;
                                $bangla_both_mcq = $bangla_both_mcq + $row->mcq_marks;
                            }

                            // English combined
                            if ($row->subject_code == 107 || $row->subject_code == 108) {
                                $combined_eng = $combined_eng + 1;
                                $combined_eng_marks = $combined_eng_marks + $row->obtainedMark;
                                $total_cgpa = $total_cgpa - $row->CGPA;
                            }

                            // carrer and physical education combined
                            if ($row->subject_code == 156 || $row->subject_code == 133) {
                                $total_subjects = $total_subjects - 1; //  subject not count on average point so less
                                $total_cgpa = $total_cgpa - $row->CGPA;
                            }

                            // Optional subject calculation
                            if ($row->subject_id == $row->optional_subject) {
                                $total_subjects = $total_subjects - 1; // Optional subject not count on average point so less
                                $total_cgpa = $total_cgpa - $row->CGPA;

                                if ($row->CGPA > 2.0) {
                                    $optional_sub_marks = $row->CGPA - 2.0;
                                    $total_cgpa = $total_cgpa + $optional_sub_marks;
                                }
                            }
                        }
                    }

                    //dd($combined_bangla_marks);

                    if ($combined_bangla == 2) {
                        $total_subjects = $total_subjects - 1; // both are now 1 subject so 1 minus from total subject
                        $bangla_marks = round($combined_bangla_marks / 2, 2);

                        // need total 66 to pass the subject
                        if ($combined_bangla_marks >= 66 && $bangla_both_theory >= 46 && $bangla_both_mcq >= 20) {
                            if ($bangla_marks >= 80) {
                                $ban_cgpa = 5.0;
                                $ban_grade = 'A+';
                                $total_cgpa = $total_cgpa + $ban_cgpa;
                            } elseif ($bangla_marks >= 70 and $bangla_marks <= 79) {
                                $ban_cgpa = 4.0;
                                $ban_grade = 'A';
                                $total_cgpa = $total_cgpa + $ban_cgpa;
                            } elseif ($bangla_marks >= 60 and $bangla_marks <= 69) {
                                $ban_cgpa = 3.5;
                                $ban_grade = 'A-';
                                $total_cgpa = $total_cgpa + $ban_cgpa;
                            } elseif ($bangla_marks >= 50 and $bangla_marks <= 59) {
                                $ban_cgpa = 3.0;
                                $ban_grade = 'B';
                                $total_cgpa = $total_cgpa + $ban_cgpa;
                            } elseif ($bangla_marks >= 40 and $bangla_marks <= 49) {
                                $ban_cgpa = 2.0;
                                $ban_grade = 'C';
                                $total_cgpa = $total_cgpa + $ban_cgpa;
                            } elseif ($bangla_marks >= 33 and $bangla_marks <= 39) {
                                $ban_cgpa = 1.0;
                                $ban_grade = 'D';
                                $total_cgpa = $total_cgpa + $ban_cgpa;
                            } else {
                                $ban_cgpa = 3.5;
                                $ban_grade = 'A-';
                                $total_cgpa = $total_cgpa + $ban_cgpa;
                            }
                        } else {
                            $ban_cgpa = 0.0;
                            $ban_grade = 'F';
                            $cgpa_status = 0;
                        }
                    }

                    if ($combined_eng == 2) {
                        $total_subjects = $total_subjects - 1; // both are now 1 subject so 1 minus from total subject
                        $eng_marks = round($combined_eng_marks / 2, 2);

                        if ($combined_eng_marks >= 66) {
                            if ($eng_marks >= 80) {
                                $eng_cgpa = 5.0;
                                $eng_grade = 'A+';
                                $total_cgpa = $total_cgpa + $eng_cgpa;
                            } elseif ($eng_marks >= 70 and $eng_marks <= 79) {
                                $eng_cgpa = 4.0;
                                $eng_grade = 'A';
                                $total_cgpa = $total_cgpa + $eng_cgpa;
                            } elseif ($eng_marks >= 60 and $eng_marks <= 69) {
                                $eng_cgpa = 3.5;
                                $eng_grade = 'A-';
                                $total_cgpa = $total_cgpa + $eng_cgpa;
                            } elseif ($eng_marks >= 50 and $eng_marks <= 59) {
                                $eng_cgpa = 3.0;
                                $eng_grade = 'B';
                                $total_cgpa = $total_cgpa + $eng_cgpa;
                            } elseif ($eng_marks >= 40 and $eng_marks <= 49) {
                                $eng_cgpa = 2.0;
                                $eng_grade = 'C';
                                $total_cgpa = $total_cgpa + $eng_cgpa;
                            } elseif ($eng_marks >= 33 and $eng_marks <= 39) {
                                $eng_cgpa = 1.0;
                                $eng_grade = 'D';
                                $total_cgpa = $total_cgpa + $eng_cgpa;
                            } else {
                                $eng_cgpa = 3.5;
                                $eng_grade = 'A-';
                                $total_cgpa = $total_cgpa + $eng_cgpa;
                            }
                        } else {
                            $eng_cgpa = 0.0;
                            $eng_grade = 'F';
                            $cgpa_status = 0;
                        }
                    }

                    // $cgpa = sprintf('%0.2f', $total_cgpa / $total_subjects);
                    $cgpa = $cgpa_status ? sprintf('%0.2f', $total_cgpa / $total_subjects) : 0;
                    // if($cgpa_status == 0) $cgpa = 0;
                    $cgpa = $cgpa > 5 ? '5.00' : $cgpa;


                    /**gpa count end*/
                    $merit[$mindx]["name"] = $row->name;
                    $merit[$mindx]["std_code"] = $row->std_code;
                    $merit[$mindx]["gpa"] = $cgpa;
                    $merit[$mindx]["total_marks"] = $res->totalMarks;
                    $mindx++;
                }

                for ($i = 0; $i < $mindx - 1; $i++) {
                    for ($j = 0; $j < $mindx - $i - 1; $j++) {
                        if ($merit[$j]["gpa"] == $merit[$j + 1]["gpa"]) {
                            if ($merit[$j]["total_marks"] < $merit[$j + 1]["total_marks"]) {
                                $temp = $merit[$j];
                                $merit[$j] = $merit[$j + 1];
                                $merit[$j + 1] = $temp;
                            }
                        } elseif ($merit[$j]["gpa"] < $merit[$j + 1]["gpa"]) {
                            $temp = $merit[$j];
                            $merit[$j] = $merit[$j + 1];
                            $merit[$j + 1] = $temp;
                        }
                    }
                }

                foreach ($merit as $key => $m) {
                    $data_id = $m["data_id"];
                    $rank =  $key + 1;

                    $rank_mod_10 = $rank % 10;
                    $rank_mod_100 = $rank % 100;
                    if ($rank_mod_10 == 1 && $rank_mod_100 != 11) $rank = (string)$rank . "st";
                    elseif ($rank_mod_10 == 2 && $rank_mod_100 != 12) $rank = (string)$rank . "nd";
                    elseif ($rank_mod_10 == 3 && $rank_mod_100 != 13) $rank = (string)$rank . "rd";
                    else $rank = (string)$rank . "th";

                    $data['result'][$data_id]->rank = $rank;
                    $data['result'][$data_id]->class_rank = $class_rank[$m["std_code"]];

                    // $data['result'][]
                }
            }
            //=================================$merit end=====================================
            // dd($data);
            // dd($data['result']);
            $view = View::make('backend.admin.exam.senior_result.summery', compact('data', 'session_year'))->render();
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
            $class_rank = $request->input('class_rank');
            $section_id = $request->input('section_id');
            $exam_id = $request->input('exam_id');
            $student_code = $request->input('student_code');
            $year = config('running_session');
            $session_year = SessionYear::select()->where('running_year', $year)->first();
            //  dd($session_year);

            $exam = Exam::where('id', $exam_id)->first();

            $data = array();
            $data['total_student'] = Enroll::where('class_id', $class_id)->where('section_id', $section_id)->where('year', $year)->count();
            $data['student_code'] = $student_code;
            $data['student_name'] = $request->input('student_name');
            $data['std_roll'] = $request->input('std_roll');
            $data['class_id'] = $class_id;
            $data['rank'] = $rank;
            $data['class_rank'] = $class_rank;
            $data['section_id'] = $section_id;
            $data['exam_id'] = $exam_id;
            $data['class_name'] = $request->input('class_name');
            $temp_section_name_split =  explode(" ", $request->input('section_name'));

            $data['section'] = $temp_section_name_split[0];
            $data['group'] = $temp_section_name_split[1];
            $data['section_name'] = $request->input('section_name');
            $data['exam_name'] = $request->input('exam_name');
            $data['year'] = config('running_session');
            $data['has_ct'] = $exam->ct_marks_percentage;
            $data['mmp'] = $exam->main_marks_percentage;


            if (!$session_year->special) {
                $data['result'] = GenerateMarksheet::srExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
            } else {
                $data['result'] = GenerateMarksheet::srExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
            }

            //  dd($data['result']);

            $view = View::make('backend.admin.exam.senior_result.marksheet', compact('data', 'session_year'))->render();
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
        $data['class_rank'] = $request->input('class_rank');
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['exam_id'] = $exam_id;
        $data['class_name'] = $request->input('class_name');
        $temp_section_name_split =  explode(" ", $request->input('section_name'));

        $data['section'] = $temp_section_name_split[0];
        $data['group'] = $temp_section_name_split[1];
        $data['section_name'] = $request->input('section_name');
        $data['exam_name'] = $request->input('exam_name');
        $data['year'] = config('running_session');
        $data['has_ct'] = $exam->ct_marks_percentage;
        $data['mmp'] = $exam->main_marks_percentage;

        $data['total_std'] = $request->input('total_std');
        $data['total_atd'] = $request->input('total_atd');
        $data['total_wd'] = $request->input('total_wd');
        $data['position'] = $request->input('position');

        $session_year = SessionYear::select()->where('running_year', $year)->first();
        if (!$session_year->special) {
            $data['result'] = GenerateMarksheet::srExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
        } else {
            $data['result'] = GenerateMarksheet::srExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
        }
        // dd($data);
        $view = View::make('backend.admin.exam.senior_result.printMarksheet', compact('data', 'session_year'));

        $html = '<!DOCTYPE html><html lang="en">';
        $html .= $view->render();
        $html .= '</html>';
        $pdf = PDF::loadHTML($html);
        $sheet = $pdf->setPaper('legal', 'landscape');
        return $sheet->stream('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');
    }
    //extra print for summary
    public function srMarksheetPrint(Request $request)
    {
        $class_id = $request->input('class_id');
        $section_id = $request->input('section_id');
        $exam_id = $request->input('exam_id');
        $student_code = $request->input('std_code');
        $year = config('running_session');

        $exam = Exam::where('id', $exam_id)->first();

        $data = array();
        $data['student_code'] = $student_code;
        $data['student_name'] = $request->input('std_name');
        $data['std_roll'] = $request->input('std_roll');
        $data['rank'] = $request->input('rank');
        $data['class_rank'] = $request->input('class_rank');
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['exam_id'] = $exam_id;
        $data['class_name'] = $request->input('class_name');
        $temp_section_name_split =  explode(" ", $request->input('section_name'));

        $data['section'] = $temp_section_name_split[0];
        $data['group'] = $temp_section_name_split[1];
        $data['section_name'] = $request->input('section_name');
        $data['exam_name'] = $request->input('exam_name');
        $data['year'] = config('running_session');
        $data['has_ct'] = $exam->ct_marks_percentage;
        $data['mmp'] = $exam->main_marks_percentage;

        $data['total_std'] = Enroll::where('class_id', $class_id)->where('section_id', $section_id)->where('year', $year)->count();
        $data['total_atd'] = $request->input('total_atd');
        $data['total_wd'] = $request->input('total_wd');
        $data['position'] = $request->input('position');

        $session_year = SessionYear::select()->where('running_year', $year)->first();
        if (!$session_year->special) {
            $data['result'] = GenerateMarksheet::srExamMarksheet($exam_id, $class_id, $section_id, $student_code, $year);
        } else {
            $data['result'] = GenerateMarksheet::srExamMarksheetSpecial($exam_id, $class_id, $section_id, $student_code, $year);
        }
        // dd($data);
        $view = View::make('backend.admin.exam.senior_result.printMarksheet', compact('data', 'session_year'));

        $html = '<!DOCTYPE html><html lang="en">';
        $html .= $view->render();
        $html .= '</html>';
        $pdf = PDF::loadHTML($html);
        $sheet = $pdf->setPaper('legal', 'landscape');
        return $sheet->stream('Marksheet_' . $data['student_code'] . '_' . $data['class_name'] . '.pdf');
    }
    
    public function srSummeryResultGradeSummaryPrint(Request $request){
        $class_id = $request->input('class_id');
        $section_id = $request->input('section_id');
        $exam_id = $request->input('exam_id');
        $year = config('running_session');

        $exam = Exam::where('id', $exam_id)->first();

        $data = array();
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        $data['exam_id'] = $exam_id;
        $data['class_name'] = $request->input('class_name');
        $data['section_name'] = $request->input('section_name');
        $data['exam_name'] = $request->input('exam_name');
        $data['year'] = config('running_session');


        $session_year = SessionYear::select()->where('running_year', $year)->first();

        $data['result'] = GenerateMarksheet::srSummeryResult($exam_id, $class_id, "null", $year);

        if (count($data['result']) != 0) {

            $grade_summary = array(
                                  "Golap Science" => array("A+"=>0,"A"=>0,"A-"=>0,"B"=>0,"C"=>0,"D"=>0,"F"=>0),
                                  "Shapla Science" => array("A+"=>0,"A"=>0,"A-"=>0,"B"=>0,"C"=>0,"D"=>0,"F"=>0),
                                  "Golap Business" => array("A+"=>0,"A"=>0,"A-"=>0,"B"=>0,"C"=>0,"D"=>0,"F"=>0),
                                  "Shapla Business" => array("A+"=>0,"A"=>0,"A-"=>0,"B"=>0,"C"=>0,"D"=>0,"F"=>0),
                                  "Golap Humanities" => array("A+"=>0,"A"=>0,"A-"=>0,"B"=>0,"C"=>0,"D"=>0,"F"=>0),
                                  "Shapla Humanities" => array("A+"=>0,"A"=>0,"A-"=>0,"B"=>0,"C"=>0,"D"=>0,"F"=>0)

                                );
                                foreach ($data['result'] as $key => $res) {
                                    $section_name = Section::where('id', $res->stdSection)->first()->name;

                                    /**gpa count start*/
                                    $full_marksheet = GenerateMarksheet::srExamMarksheet($exam_id, $class_id, "null", $res->stdCode, $year);
                                    $total_marks = 0;
                                    $cgpa_status = 1;
                                    $total_gpa = 0;
                                    $total_cgpa = 0;
                                    $optional_sub_marks = 0;
                                    $total_subjects = 0;

                                    $bangla_marks = 0;
                                    $combined_bangla = 0;
                                    $combined_bangla_marks = 0;
                                    $bangla_both_theory = 0;
                                    $bangla_both_mcq = 0;

                                    $ban_cgpa = $ban_grade = 0;

                                    $eng_marks = 0;
                                    $combined_eng = 0;
                                    $combined_eng_marks = 0;

                                    $eng_cgpa = $eng_grade = 0;

                                    $total_subjects = count($full_marksheet);

                                    foreach ($full_marksheet as $row) {
                                        $total_marks += $row->obtainedMark;

                                        if ($row->grade === 'F' && $row->subject_code != 101 && $row->subject_code != 102 && $row->subject_code != 107 && $row->subject_code != 108 && $row->subject_id != $row->optional_subject) {
                                            $cgpa_status = 0;
                                        }

                                        if (
                                            $row->grade === 'F' && $row->subject_code != 101 && $row->subject_code != 102 && $row->subject_code != 107 && $row->subject_code != 108 && $row->subject_id != $row->optional_subject
                                        ) {
                                            $cgpa_status = 0;
                                        }

                                        if ($cgpa_status != 0) {
                                            $total_cgpa = round($total_cgpa + $row->CGPA, 2);
                                        }

                                        //special check
                                        {
                                            // Bangla combined
                                            if ($row->subject_code == 101 || $row->subject_code == 102) {
                                                $combined_bangla = $combined_bangla + 1;
                                                $combined_bangla_marks = $combined_bangla_marks + $row->obtainedMark;
                                                $total_cgpa = $total_cgpa - $row->CGPA;
                                                $bangla_both_theory = $bangla_both_theory + $row->theory_marks;
                                                $bangla_both_mcq = $bangla_both_mcq + $row->mcq_marks;
                                            }

                                            // English combined
                                            if ($row->subject_code == 107 || $row->subject_code == 108) {
                                                $combined_eng = $combined_eng + 1;
                                                $combined_eng_marks = $combined_eng_marks + $row->obtainedMark;
                                                $total_cgpa = $total_cgpa - $row->CGPA;
                                            }

                                            // carrer and physical education combined
                                            if ($row->subject_code == 156 || $row->subject_code == 133) {
                                                $total_subjects = $total_subjects - 1; //  subject not count on average point so less
                                                $total_cgpa = $total_cgpa - $row->CGPA;
                                            }

                                            // Optional subject calculation
                                            if ($row->subject_id == $row->optional_subject) {
                                                $total_subjects = $total_subjects - 1; // Optional subject not count on average point so less
                                                $total_cgpa = $total_cgpa - $row->CGPA;

                                                if ($row->CGPA > 2.0) {
                                                    $optional_sub_marks = $row->CGPA - 2.0;
                                                    $total_cgpa = $total_cgpa + $optional_sub_marks;
                                                }
                                            }
                                        }
                                    }

                                    //dd($combined_bangla_marks);

                                    if ($combined_bangla == 2) {
                                        $total_subjects = $total_subjects - 1; // both are now 1 subject so 1 minus from total subject
                                        $bangla_marks = ceil($combined_bangla_marks / 2);

                                        // need total 66 to pass the subject
                                        if ($combined_bangla_marks >= 66 && $bangla_both_theory >= 46 && $bangla_both_mcq >= 20) {
                                            if ($bangla_marks >= 80) {
                                                $ban_cgpa = 5.0;
                                                $ban_grade = 'A+';
                                                $total_cgpa = $total_cgpa + $ban_cgpa;
                                            } elseif ($bangla_marks >= 70 and $bangla_marks <= 79) {
                                                $ban_cgpa = 4.0;
                                                $ban_grade = 'A';
                                                $total_cgpa = $total_cgpa + $ban_cgpa;
                                            } elseif ($bangla_marks >= 60 and $bangla_marks <= 69) {
                                                $ban_cgpa = 3.5;
                                                $ban_grade = 'A-';
                                                $total_cgpa = $total_cgpa + $ban_cgpa;
                                            } elseif ($bangla_marks >= 50 and $bangla_marks <= 59) {
                                                $ban_cgpa = 3.0;
                                                $ban_grade = 'B';
                                                $total_cgpa = $total_cgpa + $ban_cgpa;
                                            } elseif ($bangla_marks >= 40 and $bangla_marks <= 49) {
                                                $ban_cgpa = 2.0;
                                                $ban_grade = 'C';
                                                $total_cgpa = $total_cgpa + $ban_cgpa;
                                            } elseif ($bangla_marks >= 33 and $bangla_marks <= 39) {
                                                $ban_cgpa = 1.0;
                                                $ban_grade = 'D';
                                                $total_cgpa = $total_cgpa + $ban_cgpa;
                                            } else {
                                                $ban_cgpa = 3.5;
                                                $ban_grade = 'A-';
                                                $total_cgpa = $total_cgpa + $ban_cgpa;
                                            }
                                        } else {
                                            $ban_cgpa = 0.0;
                                            $ban_grade = 'F';
                                            $cgpa_status = 0;
                                        }
                                    }

                                    if ($combined_eng == 2) {
                                        $total_subjects = $total_subjects - 1; // both are now 1 subject so 1 minus from total subject
                                        $eng_marks = ceil($combined_eng_marks / 2);

                                        if ($combined_eng_marks >= 66) {
                                            if ($eng_marks >= 80) {
                                                $eng_cgpa = 5.0;
                                                $eng_grade = 'A+';
                                                $total_cgpa = $total_cgpa + $eng_cgpa;
                                            } elseif ($eng_marks >= 70 and $eng_marks <= 79) {
                                                $eng_cgpa = 4.0;
                                                $eng_grade = 'A';
                                                $total_cgpa = $total_cgpa + $eng_cgpa;
                                            } elseif ($eng_marks >= 60 and $eng_marks <= 69) {
                                                $eng_cgpa = 3.5;
                                                $eng_grade = 'A-';
                                                $total_cgpa = $total_cgpa + $eng_cgpa;
                                            } elseif ($eng_marks >= 50 and $eng_marks <= 59) {
                                                $eng_cgpa = 3.0;
                                                $eng_grade = 'B';
                                                $total_cgpa = $total_cgpa + $eng_cgpa;
                                            } elseif ($eng_marks >= 40 and $eng_marks <= 49) {
                                                $eng_cgpa = 2.0;
                                                $eng_grade = 'C';
                                                $total_cgpa = $total_cgpa + $eng_cgpa;
                                            } elseif ($eng_marks >= 33 and $eng_marks <= 39) {
                                                $eng_cgpa = 1.0;
                                                $eng_grade = 'D';
                                                $total_cgpa = $total_cgpa + $eng_cgpa;
                                            } else {
                                                $eng_cgpa = 3.5;
                                                $eng_grade = 'A-';
                                                $total_cgpa = $total_cgpa + $eng_cgpa;
                                            }
                                        } else {
                                            $eng_cgpa = 0.0;
                                            $eng_grade = 'F';
                                            $cgpa_status = 0;
                                        }
                                    }



                                    // $cgpa = sprintf('%0.2f', $total_cgpa / $total_subjects);
                                    $cgpa = $cgpa_status ? sprintf('%0.2f', $total_cgpa / $total_subjects) : 0;
                                    // if($cgpa_status == 0) $cgpa = 0;
                                    $cgpa = $cgpa > 5 ? '5.00' : $cgpa;

                                    $gpa = "F";

                                    if ($cgpa_status != 0) {

                                        if ($cgpa >= 5) {
                                            $gpa = "A+";
                                        } else if ($cgpa >= 4 and $cgpa <= 4.99) {
                                            $gpa = "A";
                                        } else if ($cgpa >= 3.50 and $cgpa <= 3.99) {
                                            $gpa = "A-";
                                        } else if ($cgpa >= 3 and $cgpa <= 3.49) {
                                            $gpa = "B";
                                        } else if ($cgpa >= 2 and $cgpa <= 2.99) {
                                            $gpa = "C";
                                        } else if ($cgpa >= 1 and $cgpa <= 1.99) {
                                            $gpa = "D";
                                        } else {
                                            $gpa = "F";
                                        }
                                    } else {
                                        $gpa = "F";
                                    }

                                    $grade_summary[$section_name][$gpa] = $grade_summary[$section_name][$gpa] + 1;

                }



            // dd($grade_summary);
            $view = View::make('backend.admin.exam.senior_result.half_yearly_grade_summary_print', compact('data', 'session_year','grade_summary'));

            $html = '<!DOCTYPE html><html lang="en">';
            $html .= $view->render();
            $html .= '</html>';
            $pdf = PDF::loadHTML($html);
            $sheet = $pdf->setPaper('legal', 'landscape');
            return $sheet->stream('Summary_' .  '_' . $data['class_name'] . '.pdf');
        }
    }
}

<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\Academic;
use App\Models\Exam;
use App\Models\StdClass;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SessionYear;
use App\Models\SpecialSessionSubject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

use View;
use DB;
use Excel;
use PDF;

class MarkController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.mark.index', compact('stdclass'));
   }

   public function getMarks(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $exam_id = $request->input('exam_id');
         $subject_id = $request->input('subject_id');
         $year = config('running_session');
         $session_year = SessionYear::select()->where('running_year',$year)->first();

         if ($section_id == 'all') {
            $section_id = 'null';
         }
         if(!$session_year->special){
            $data = Academic::getSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year);
         }
         else{
            $data = Academic::getSpecialSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year);
         }
        //  dd($data);
        //  $view = View::make('backend.admin.mark.view', compact('std_class', 'section', 'exam', 'subject', 'data','session_year'))->render();
         $view = View::make('backend.admin.mark.view', compact(  'data','session_year'))->render();

         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {

    // dd($request);
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('marks-create');
         if ($haspermision) {
            $exam_id = $request->input('exam_id');
            $class_id = $request->input('class_id');
            $section_id = $request->input('section_id');
            $subject_id = $request->input('subject_id');
            $student_code = $request->input('student_code');
            $teacher_id = $request->input('teacher_id');
            $uploader_id = auth()->user()->id;

            $year = config('running_session');
            $session_year = SessionYear::select()->where('running_year',$year)->first();

            DB::beginTransaction();
            try {

                if(!$session_year->special){
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
                }
                else{
                    $bulk_data = [];

                    DB::table('special_marks')
                        ->where('exam_id', $exam_id)
                        ->where('class_id', $class_id)
                        ->where('section_id', $section_id)
                        ->where('subject_id', $subject_id)
                        ->delete();


                    foreach ($student_code as $std_code) {
                        $written_marks = $request->input('written_' . $std_code) ? $request->input('written_' . $std_code) : 0;
                        $assignment_marks = $request->input('assignment_' . $std_code) ? $request->input('assignment_' . $std_code) : 0;
                        $other_marks = $request->input('other_' . $std_code) ? $request->input('other_' . $std_code) : 0;
                        // $ct_marks = $request->input('ct_' . $std_code) ? $request->input('ct_' . $std_code) : 0;
                        $total_marks = $written_marks + $assignment_marks + $other_marks;

                        $bulk_data[] = [
                            'exam_id' => $exam_id,
                            'student_code' => $std_code,
                            'subject_id' => $subject_id,
                            'class_id' => $class_id,
                            'section_id' => $section_id,
                            'total_marks' => $total_marks,
                            'written_marks' => $written_marks,
                            'assignment_marks' => $assignment_marks,
                            'other_marks' => $other_marks,
                            'teacher_id' => $teacher_id,
                            'uploader_id' => $uploader_id,
                            'year' => config('running_session'),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ];

                    }

                    DB::table('special_marks')->insert($bulk_data);
                }
               DB::commit();

            } catch (\Exception $e) {
               DB::rollback();
               return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
            }


            return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);

         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function import()
   {
      $haspermision = auth()->user()->can('marks-import');
      if ($haspermision) {
         $stdclass = StdClass::all();
         $year = config('running_session');
         $session_year = SessionYear::select()->where('running_year',$year)->first();
        //  dd($session_year);
         return view('backend.admin.mark.import', compact('stdclass','session_year'));
      } else {
         abort(403, 'Sorry, you are not authorized to access the page');
      }
   }

   public function importStore(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('marks-import');
         if ($haspermision) {
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


                            $year = config('running_session');
                            $session_year = SessionYear::select()->where('running_year',$year)->first();

                            if(!$session_year->special){
                            $exam_id = $request->input('exam_id');
                            $class_id = $request->input('class_id');
                            $section_id = $request->input('section_id');
                            $subject_id = $request->input('subject_id');
                            $uploader_id = auth()->user()->id;
                            $subject = Subject::where('id', $subject_id)->first();
                            $teacher_id = $subject->teacher_id;


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

                                    //6,7,8 theory marks start
                                    if($class_id == 8 || $class_id == 9 || $class_id == 10){
                                        $theory_marks = $theory_marks + $mcq_marks;
                                        $mcq_marks = 0;
                                    }
                                    //6,7,8 theory marks end
                                    
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
                        }
                        else{
                            $exam_id = $request->input('exam_id');
                            $class_id = $request->input('class_id');
                            $section_id = $request->input('section_id');
                            $subject_id = $request->input('subject_id');
                            $uploader_id = auth()->user()->id;
                            // $subject = Subject::where('id', $subject_id)->first();
                            // $teacher_id = $subject->teacher_id;
                            $subject = SpecialSessionSubject::where('id', $subject_id)->first();
                            $teacher_id = $subject->teacher_id;


                            $bulk_data = [];

                            DB::table('special_marks')
                                ->where('exam_id', $exam_id)
                                ->where('class_id', $class_id)
                                ->where('section_id', $section_id)
                                ->where('subject_id', $subject_id)
                                ->delete();


                            foreach ($data as $key => $value) {
                                if ("$value->student_code" != '') {

                                    $written_marks = "$value->written_marks" != '' ? "$value->written_marks" : 0;
                                    $assignment_marks = "$value->assignment_marks" != '' ? "$value->assignment_marks" : 0;
                                    $other_marks = "$value->other_marks" != '' ? "$value->other_marks" : 0;
                                    $total_marks = $written_marks + $assignment_marks + $other_marks;

                                    $bulk_data[] = [
                                    'exam_id' => $exam_id,
                                    'class_id' => $class_id,
                                    'section_id' => $section_id,
                                    'subject_id' => $subject_id,
                                    'student_code' => "$value->student_code",
                                    'total_marks' => $total_marks,
                                    'written_marks' => $written_marks,
                                    'assignment_marks' => $assignment_marks,
                                    'other_marks' => $other_marks,
                                    'teacher_id' => $teacher_id,
                                    'uploader_id' => $uploader_id,
                                    'year' => config('running_session'),
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now(),
                                    ];

                                }
                            }

                            DB::table('special_marks')->insert($bulk_data);
                        }
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
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function exportExcelMarks()
   {

        $class_id = Input::get('class_id');
        $section_id = Input::get('section_id');
        $exam_id = Input::get('exam_id');
        $subject_id = Input::get('subject_id');

        $year = config('running_session');
        $session_year = SessionYear::select()->where('running_year', $year)->first();
        if (!$session_year->special) {
            $data = Academic::getSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year);
        } else {
            $data = Academic::getSpecialSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year);
        }

        // dd($data);
        $payload = array();
        $class = null;
        $section = null;

        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                if (!$session_year->special) {
                    $payload[] = array(
                        'Student Id' => $value->std_code,
                        'Name' => $value->std_name,
                        'Class' => $value->class_name,
                        'Section' => $value->section,
                        'Roll' => $value->std_roll,
                        'Subject' => $value->sub_name,
                        'Theory Marks' => $value->theory_marks,
                        'MCQ Marks' => $value->mcq_marks,
                        'Practical Marks' => $value->practical_marks,
                        'CT Marks' => $value->ct_marks
                    );
                } else {
                    $payload[] = array(
                        'Student Id' => $value->std_code,
                        'Name' => $value->std_name,
                        'Class' => $value->class_name,
                        'Section' => $value->section,
                        'Roll' => $value->std_roll,
                        'Subject' => $value->sub_name,
                        'Written Marks' => $value->written_marks,
                        'Assignment Marks' => $value->assignment_marks,
                        'Other Marks' => $value->other_marks,
                    );
                }

                // dd($payload);
                $class = $value->class_name;
                $section = $value->section;
            }
        }

        return Excel::create('Marks_' . $class . '_' . $section, function ($excel) use ($payload) {
            $excel->sheet('Marks', function ($sheet) use ($payload) {
                $sheet->fromArray($payload);
            });
        })->download('xls');
    }

   public function exportPdfMarks()
   {

        $class_id = Input::get('class_id');
        $section_id = Input::get('section_id');
        $exam_id = Input::get('exam_id');
        $subject_id = Input::get('subject_id');

        $year = config('running_session');

        $session_year = SessionYear::select()->where('running_year', $year)->first();
        // dd($session_year);
        if ($section_id == 'all') {
            $section_id = 'null';
        }
        if (!$session_year->special) {
            $data = Academic::getSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year);
        } else {
            $data = Academic::getSpecialSubjectMarks($class_id, $section_id, $subject_id, $exam_id, $year);
        }
        // dd($data);
        $class = $section = $sub = "";

        if (count($data) > 0) {
            $class = $data[0]->class_name;
            $section = $data[0]->section;
            $sub = $data[0]->sub_name;
            $view = view('backend.admin.mark.export_pdf', compact('data', 'session_year'));
            $html = $view->render();
        } else {
            $html = "<html><body><p> Sorry!! no records have found</p></body></html>";
        }


        $pdf = PDF::loadHTML($html);
        $sheet = $pdf->setPaper('a4', 'portrait');
        return $sheet->download('Marks_' . $class . '_' . $section . '_' . $sub . '.pdf');
    }

}

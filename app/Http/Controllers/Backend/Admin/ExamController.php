<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\Academic;
use App\Http\Controllers\Controller;
use App\Models\AssignExaminee;
use App\Models\Exam;
use App\Models\Section;
use App\Models\StdClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use PDF;
use View;
use DB;

class ExamController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.exam.all', compact('stdclass'));
   }

   public function allExams(Request $request)
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('exam-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('exam-delete')) {
         $can_delete = "style='display:none;'";
      }

      $class_id = $request->input('class_id');


      $exams = Exam::where('class_id', $class_id)->where('year', config('running_session'))->get();
      // dd($exams);
      return DataTables::of($exams)
        ->addColumn('file_path', function ($exam) {
           return $exam->file_path ? "<a class='btn btn-primary' href='" . asset($exam->file_path) . "' target='_blank'>Download</a>" : '';
        })
        ->addColumn('main_marks_percentage', function ($exam) {
           return $exam->main_marks_percentage ? $exam->main_marks_percentage . '%' : '';
        })
        ->addColumn('ct_marks_percentage', function ($exam) {
           return $exam->ct_marks_percentage ? $exam->ct_marks_percentage . '%' : '0%';
        })
//        ->addColumn('status', function ($exam) {
//           return $exam->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
//        })
        ->addColumn('action', function ($exams) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . ' id="' . $exams->id . '" class="btn btn-xs btn-info margin-r-5 view" title="View"><i class="fa fa-eye fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $exams->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $exams->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'file_path', 'status', 'marks_percentage'])
        ->addIndexColumn()
        ->make(true);
   }


   public function getExams(Request $request, $class_id)
   {
      if ($request->ajax()) {

         $exams = Exam::where('class_id', $class_id)->where('year', config('running_session'))->get();
         if ($exams) {
            echo "<option value='' selected disabled> Select a exam</option>";
            foreach ($exams as $exam) {
               echo "<option  value='$exam->id'>$exam->name</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('exam-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.exam.create', compact('stdclass'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
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
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('exam-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required',
              'class_id' => 'required',
              'main_marks_percentage' => 'required',
              'ct_marks_percentage' => 'required',
              'start_date' => 'required',
              'end_date' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $upload_ok = 1;


               if ($request->hasFile('photo')) {
                  $extension = Input::file('photo')->getClientOriginalExtension();;
                  if ($extension == "doc" || $extension == "docx" || $extension == "pdf" || $extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/uploads/exam_routine'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/exam_routine/' . $fileName;
                        Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                        $upload_ok = 1;

                     } else {
                        return response()->json([
                          'type' => 'error',
                          'message' => "<div class='alert alert-warning'>File is not valid</div>"
                        ]);
                     }
                  } else {
                     return response()->json([
                       'type' => 'error',
                       'message' => "<div class='alert alert-warning'>Error! File type is not valid</div>"
                     ]);
                  }
               } else {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Error! File not selected</div>"
                  ]);
               }


               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {
                  $name = $request->input('name');
                  $rows = DB::table('exams')
                    ->where('start_date', $request->input('start_date'))
                    ->where('name', $request->input('name'))
                    ->where('class_id', $request->input('class_id'))
                    ->count();
                  if ($rows == 0) {
                     $exam = new Exam;
                     $exam->name = $request->input('name');
                     $exam->class_id = $request->input('class_id');
                     $exam->description = $request->input('description');
                     $exam->start_date = $request->input('start_date');
                     $exam->end_date = $request->input('end_date');
                     $exam->result_modification_last_date = $request->input('result_modification_last_date');
                     $exam->main_marks_percentage = $request->input('main_marks_percentage');
                     $exam->ct_marks_percentage = $request->input('ct_marks_percentage');
                     $exam->file_path = $file_path;
                     $exam->year = config('running_session');
                     $exam->save(); //
                     return response()->json(['type' => 'success', 'message' => "Successfully Created"]);
                  } else {
                     return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Exam Name $name  already exist in same class</div>"]);

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

   /**
    * Display the specified resource.
    *
    * @param  \App\Models\Exam $exam
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Exam $exam)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('exam-view');
         if ($haspermision) {
            $view = View::make('backend.admin.exam.view', compact('exam'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\Exam $exam
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Exam $exam)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('exam-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.exam.edit', compact('exam', 'stdclass'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  \App\Models\Exam $exam
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Exam $exam)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('exam-edit');
         if ($haspermision) {

            Exam::findOrFail($exam->id);

            $rules = [
              'name' => 'required',
              'class_id' => 'required',
              'main_marks_percentage' => 'required',
              'ct_marks_percentage' => 'required',
              'start_date' => 'required',
              'end_date' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $upload_ok = 1;

               if ($request->hasFile('photo')) {
                  $extension = Input::file('photo')->getClientOriginalExtension();;
                  if ($extension == "doc" || $extension == "docx" || $extension == "pdf" || $extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/uploads/exam_routine'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/exam_routine/' . $fileName;
                        Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                        $upload_ok = 1;

                     } else {
                        return response()->json([
                          'type' => 'error',
                          'message' => "<div class='alert alert-warning'>File is not valid</div>"
                        ]);
                     }
                  } else {
                     return response()->json([
                       'type' => 'error',
                       'message' => "<div class='alert alert-warning'>Error! File type is not valid</div>"
                     ]);
                  }
               } else {
                  $upload_ok = 1;
                  $file_path = $request->input('SelectedFileName');
               }


               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {
                  $name = $request->input('name');
                  $rows = DB::table('exams')
                    ->where('start_date', $request->input('start_date'))
                    ->where('name', $request->input('name'))
                    ->where('class_id', $request->input('class_id'))
                    ->whereNotIn('id', [$exam->id])
                    ->count();
                  if ($rows == 0) {
                     $exam->name = $request->input('name');
                     $exam->class_id = $request->input('class_id');
                     $exam->description = $request->input('description');
                     $exam->start_date = $request->input('start_date');
                     $exam->end_date = $request->input('end_date');
                     $exam->result_modification_last_date = $request->input('result_modification_last_date');
                     $exam->main_marks_percentage = $request->input('main_marks_percentage');
                     $exam->ct_marks_percentage = $request->input('ct_marks_percentage');
                     $exam->file_path = $file_path;
                     $exam->status = $request->input('status');
                     $exam->save(); //
                     return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);

                  } else {
                     return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Exam Name $name  already exist in same date</div>"]);

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

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\Exam $exam
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Exam $exam)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('exam-delete');
         if ($haspermision) {
            $exam->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function assignExaminee()
   {
      return view('backend.admin.exam.assign_examinee.all');
   }

   public function allAssignedExaminee()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('examinee-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('examinee-delete')) {
         $can_delete = "style='display:none;'";
      }

      $year = config('running_session');

      $examinee = DB::select("SELECT examinee.id, exams.name AS exam_name,std_classes.name AS class_name,sections.name AS section_name,subjects.name AS subject_name,
         teachers.name AS examinee, examinee.status  FROM assign_examinees AS examinee
         LEFT JOIN exams ON exams.id = examinee.exam_id
         JOIN std_classes ON std_classes.id = examinee.class_id
         JOIN sections ON sections.id = examinee.section_id
         JOIN subjects ON subjects.id = examinee.subject_id
         JOIN teachers ON teachers.id = examinee.teacher_id
         WHERE examinee.year = '$year' and exams.result_modification_last_date >= NOW()");

      return DataTables::of($examinee)
        ->addColumn('status', function ($examinee) {
           return $examinee->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('action', function ($examinee) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $examinee->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $examinee->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'status'])
        ->addIndexColumn()
        ->make(true);
   }

   public function createExaminee(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('examinee-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $teacher = Teacher::all();
            $view = View::make('backend.admin.exam.assign_examinee.create', compact('exams', 'stdclass', 'teacher'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function storeExaminee(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('examinee-create');
         if ($haspermision) {

            $rules = [
              'exam_id' => 'required',
              'class_id' => 'required',
              'teacher_id' => 'required',
              'subject_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $year = config('running_session');

               $rows = DB::table('assign_examinees')
                 ->where('exam_id', $request->input('exam_id'))
                 ->where('subject_id', $request->input('subject_id'))
                 ->where('year', $year)
                 ->count();
               if ($rows == 0) {
                  $assignexaminee = new AssignExaminee();
                  $assignexaminee->exam_id = $request->input('exam_id');
                  $assignexaminee->class_id = $request->input('class_id');
                  $assignexaminee->section_id = $request->input('section_id');
                  $assignexaminee->subject_id = $request->input('subject_id');
                  $assignexaminee->teacher_id = $request->input('teacher_id');
                  $assignexaminee->year = $year;
                  $assignexaminee->save(); //
                  return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Created</div>"]);

               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Teacher already assigned in that subject </div>"]);

               }
            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function editExaminee(Request $request, $id)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('examinee-edit');
         if ($haspermision) {
            $exams = Exam::where('year', config('running_session'))
              ->where('result_modification_last_date', '>=', date('Y-m-d'))
              ->orderBy('created_at', 'desc')->get();
            $stdclass = StdClass::all();
            $section = Section::all();
            $teacher = Teacher::all();
            $subjects = Subject::all();
            $examinee = AssignExaminee::where('id', $id)->first();
            $view = View::make('backend.admin.exam.assign_examinee.edit', compact('examinee', 'exams', 'stdclass', 'section', 'teacher', 'subjects'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function updateExaminee(Request $request, $id)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('examinee-edit');
         if ($haspermision) {

            $rules = [
              'exam_id' => 'required',
              'class_id' => 'required',
              'teacher_id' => 'required',
              'subject_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $year = config('running_session');

               $rows = DB::table('assign_examinees')
                 ->where('exam_id', $request->input('exam_id'))
                 ->where('subject_id', $request->input('subject_id'))
                 ->where('year', $year)
                 ->whereNotIn('id', [$id])
                 ->count();
               if ($rows == 0) {
                  $assignexaminee = AssignExaminee::where('id', $id)->first();
                  $assignexaminee->exam_id = $request->input('exam_id');
                  $assignexaminee->class_id = $request->input('class_id');
                  $assignexaminee->section_id = $request->input('section_id');
                  $assignexaminee->subject_id = $request->input('subject_id');
                  $assignexaminee->teacher_id = $request->input('teacher_id');
                  $assignexaminee->status = $request->input('status');
                  $assignexaminee->year = $year;
                  $assignexaminee->save(); //
                  return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Updated</div>"]);

               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Teacher already assigned in that subject </div>"]);

               }
            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function deleteExaminee(Request $request, $id)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('exam-delete');
         if ($haspermision) {
            AssignExaminee::where('id', $id)->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function admitCard()
   {

      $haspermision = auth()->user()->can('admit-card-print');
      if ($haspermision) {
         $stdclass = StdClass::all();
         return view('backend.admin.exam.admit_card.index', compact('stdclass'));
      } else {
         abort(403, 'Sorry, you are not authorized to access the page');
      }

   }

   public function generateAdmitCard(Request $request)
   {

      $class_id = Input::get('class_id');
      $section_id = Input::get('section_id');
      $exam_id = Input::get('exam_id');
      $student_id = Input::get('student_id');

      $class_name = Input::get('class_name');
      $section_name = Input::get('section_name');

      $year = config('running_session');

      if ($student_id == '') {
         $student_id = 'Null';
      }

      // dd($student_id);


      $data = Academic::generateAdmitCard($exam_id, $class_id, $section_id, $student_id, $year);


      if ($data) {

         $html = '<!DOCTYPE html><html lang="en">';
         foreach ($data as $std) {
            $view = view('backend.admin.exam.admit_card.print_admin_card', compact('std'));
            $html .= $view->render();
         }
         $html .= '</html>';
         $pdf = PDF::loadHTML($html);
         $sheet = $pdf->setPaper('a4', 'portrait');
         return $sheet->download('admin_card_' . $class_name . '_' . $section_name . '.pdf');


      } else {
         return response()->json(['type' => 'error', 'message' => "No data found"]);
      }

   }


}

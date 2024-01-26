<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Subject;
use App\Models\StdClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SessionYear;
use App\Models\SpecialSessionSubject;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;


class SubjectController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
        $stdclass = StdClass::all();
        $year = config('running_session');
        $session_year = SessionYear::select()->where('running_year',$year)->first();
        return view('backend.admin.subject.all', compact('stdclass','session_year'));
   }

   public function allSubjects(Request $request)
   {

      if ($request->ajax()) {
         $can_edit = $can_delete = '';
         if (!auth()->user()->can('subject-edit')) {
            $can_edit = "style='display:none;'";
         }
         if (!auth()->user()->can('subject-delete')) {
            $can_delete = "style='display:none;'";
         }

         $class_id = $request->input('class_id');
         $year = config('running_session');
         $session_year = SessionYear::select()->where('running_year',$year)->first();
        //  dd($session_year);
         if($session_year->special == 0){
            $subjects = Subject::where('class_id', $class_id)->orderBy('subject_order', 'asc')->get();
         }
         else{
            $subjects = SpecialSessionSubject::where('class_id', $class_id)->where('year', $year)->orderBy('subject_order', 'asc')->get();
            $can_edit = "style='display:none;'";
            $can_delete = "style='display:none;'";
         }
         return Datatables::of($subjects)
         ->addColumn('subject_code', function ($subjects) {
             $sc = $subjects->subject_code;
            return $sc != 0 ? $sc : '';
         })
           ->addColumn('class_id', function ($subjects) {
              $class = $subjects->stdclass;
              return $class ? $class->name : '';
           })
           ->addColumn('action', function ($subjects) use ($can_edit, $can_delete) {
              $html = '<div class="btn-group">';
              $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $subjects->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
              $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $subjects->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
              $html .= '</div>';
              return $html;
           })
           ->rawColumns(['action'])
           ->addIndexColumn()
           ->make(true);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getSubjects(Request $request, $class_id)
   {
      if ($request->ajax()) {

        $year = config('running_session');
        $session_year = SessionYear::select()->where('running_year',$year)->first();
        // dd($session_year);
         $class = StdClass::findOrFail($class_id);
        //  dd($class->id);
         if(!$session_year->special){
            $subjects = $class->subjects;
         }
         else{
             $subjects = SpecialSessionSubject::select()->where('class_id',$class->id) ->where('year',$year)->get();
         }
         if ($subjects) {
            echo "<option value='' selected disabled> Select a subject</option>";
            foreach ($subjects as $subject) {
               echo "<option  value='$subject->id'> $subject->name</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getOptionalSubjects(Request $request, $class_id)
   {
      if ($request->ajax()) {
         $subjects = Subject::where('class_id', $class_id)->where('subject_type', 0)->get();
         if ($subjects) {
            echo "<option value=''> Select Optional Subject</option>";
            foreach ($subjects as $subject) {
               echo "<option  value='$subject->id'> $subject->name</option>";
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
         $haspermision = auth()->user()->can('subject-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            //  $teachers = Teacher::all();
            $view = View::make('backend.admin.subject.create', compact('stdclass'))->render();
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
         $haspermision = auth()->user()->can('subject-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required',
              'class_id' => 'required',
               // 'teacher_id' => 'required',
              'subject_marks' => 'required',
              'pass_marks' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $name = $request->input('name');
               $rows = DB::table('subjects')
                 ->where('class_id', $request->input('class_id'))
                 ->where('name', $request->input('name'))
                 ->count();
               if ($rows == 0) {
                  $subject = new Subject;
                  $subject->name = $request->input('name');
                  $subject->class_id = $request->input('class_id');
                  $subject->teacher_id = 0; //$request->input('teacher_id');
                  $subject->subject_code = $request->input('subject_code') ? $request->input('subject_code') : 0;
                  $subject->subject_order = $request->input('subject_order') ? $request->input('subject_order') : 0;
                  $subject->subject_marks = $request->input('subject_marks') ? $request->input('subject_marks') : 0;
                  $subject->pass_marks = $request->input('pass_marks') ? $request->input('pass_marks') : 0;
                  $subject->theory_marks = $request->input('theory_marks') ? $request->input('theory_marks') : 0;
                  $subject->theory_pass_marks = $request->input('theory_pass_marks') ? $request->input('theory_pass_marks') : 0;
                  $subject->mcq_marks = $request->input('mcq_marks') ? $request->input('mcq_marks') : 0;
                  $subject->mcq_pass_marks = $request->input('mcq_pass_marks') ? $request->input('mcq_pass_marks') : 0;
                  $subject->practical_marks = $request->input('practical_marks') ? $request->input('practical_marks') : 0;
                  $subject->practical_pass_marks = $request->input('practical_pass_marks') ? $request->input('practical_pass_marks') : 0;
                  $subject->ct_marks = $request->input('ct_marks') ? $request->input('ct_marks') : 0;
                  $subject->year = config('running_session');
                  $subject->save(); //
                  return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Created</div>"]);

               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Subject Name $name  already exist in same class</div>"]);

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
    * @param  \App\Models\Subject $subject
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Subject $subject)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('subject-view');
         if ($haspermision) {
            $view = View::make('backend.admin.subject.view', compact('subject'))->render();
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
    * @param  \App\Models\Subject $subject
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Subject $subject)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('subject-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            // $teachers = Teacher::all();
            $view = View::make('backend.admin.subject.edit', compact('stdclass', 'subject'))->render();
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
    * @param  \App\Models\Subject $subject
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Subject $subject)
   {
      if ($request->ajax()) {
         // Setup the validator
         Subject::findOrFail($subject->id);

         $rules = [
           'name' => 'required',
           'class_id' => 'required',
            //  'teacher_id' => 'required',
           'subject_marks' => 'required',
           'pass_marks' => 'required'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {

            $name = $request->input('name');
            $rows = DB::table('sections')
              ->where('class_id', $request->input('class_id'))
              ->where('name', $request->input('name'))
              ->whereNotIn('id', [$subject->id])
              ->count();
            if ($rows == 0) {
               $subject->name = $request->input('name');
               $subject->class_id = $request->input('class_id');
               $subject->teacher_id = 0; //$request->input('teacher_id');
               $subject->subject_code = $request->input('subject_code') ? $request->input('subject_code') : 0;
               $subject->subject_order = $request->input('subject_order') ? $request->input('subject_order') : 0;
               $subject->subject_marks = $request->input('subject_marks') ? $request->input('subject_marks') : 0;
               $subject->pass_marks = $request->input('pass_marks') ? $request->input('pass_marks') : 0;
               $subject->theory_marks = $request->input('theory_marks') ? $request->input('theory_marks') : 0;
               $subject->theory_pass_marks = $request->input('theory_pass_marks') ? $request->input('theory_pass_marks') : 0;
               $subject->mcq_marks = $request->input('mcq_marks') ? $request->input('mcq_marks') : 0;
               $subject->mcq_pass_marks = $request->input('mcq_pass_marks') ? $request->input('mcq_pass_marks') : 0;
               $subject->practical_marks = $request->input('practical_marks') ? $request->input('practical_marks') : 0;
               $subject->practical_pass_marks = $request->input('practical_pass_marks') ? $request->input('practical_pass_marks') : 0;
               $subject->ct_marks = $request->input('ct_marks') ? $request->input('ct_marks') : 0;
               $subject->year = config('running_session');
               $subject->save(); //
               return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Created</div>"]);

            } else {
               return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Section Name $name  already exist in same class</div>"]);

            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\Subject $subject
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Subject $subject)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('subject-delete');
         if ($haspermision) {
            $subject->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\ClassRoom;
use App\Models\ClassRoutine;
use App\Models\StdClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Helper\Academic;

use View;
use DB;

class ClassRoutineController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.class_routine.index', compact('stdclass'));
   }

   public function getClassroutines(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');

         $data['routines'] = Academic::generateClassRoutine($class_id, $section_id);
         $view = View::make('backend.admin.class_routine.view', compact('data'))->render();
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
   public function create(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('classroutine-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $classrooms = ClassRoom::all();
            $teachers = Teacher::all();
            $view = View::make('backend.admin.class_routine.create', compact('stdclass', 'teachers', 'classrooms'))->render();
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
         $haspermision = auth()->user()->can('classroutine-create');
         if ($haspermision) {

            $rules = [
              'teacher_id' => 'required',
              'class_id' => 'required',
              'section_id' => 'required',
              'subject_id' => 'required',
              'time_start' => 'required',
              'time_start_min' => 'required',
              'time_end' => 'required',
              'time_end_min' => 'required',
              'day' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $class_id = $request->input('class_id');
               $section_id = $request->input('class_id');
               $subject_id = $request->input('subject_id');
               $time_start = $request->input('time_start');
               $time_start_min = $request->input('time_start_min');
               $time_end = $request->input('time_end');
               $time_end_min = $request->input('time_end_min');
               $start_hour = $time_start . ':' . $time_start_min;
               $end_hour = $time_end . ':' . $time_end_min;
               $day = $request->input('day');
               $year = config('running_session');

               // $end_min = date('i', strtotime('-1 minutes', strtotime($end_hour)));

               DB::statement(DB::raw("SET @class_id = '$class_id', @section_id = '$section_id', @subject_id = '$subject_id',
               @start_hour = '$start_hour', @end_hour =  '$end_hour' ,@year = '$year', @day =  '$day' "));

               $rows = DB::select("SELECT class_id, section_id, DAY, year FROM class_routines
                     WHERE class_id = @class_id AND section_id = @section_id
                     AND DAY = @day AND YEAR=@year
                     AND  TIME_FORMAT(CONCAT_WS(':', `time_end`, `time_end_min`), '%H %i') 
                     BETWEEN  TIME_FORMAT(@start_hour, '%H %i')  AND  TIME_FORMAT(@end_hour, '%H %i')");

               if (count($rows) == 0) {

                  $classroutine = new ClassRoutine();
                  $classroutine->class_id = $class_id;
                  $classroutine->section_id = $section_id;
                  $classroutine->subject_id = $subject_id;
                  $classroutine->teacher_id = $request->input('teacher_id');
                  $classroutine->class_room_id = $request->input('class_room_id');
                  $classroutine->time_start = $time_start;
                  $classroutine->time_start_min = $time_start_min;
                  $classroutine->time_end = $time_end;
                  $classroutine->time_end_min = $time_end_min;
                  $classroutine->day = $day;
                  $classroutine->year = $year;
                  $classroutine->save();

                  return response()->json(['type' => 'success', 'message' => "Successfully Created"]);
               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Class Routine  already exist in same selected requirements</div>"]);

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
    * @param  \App\Models\ClassRoutine $classroutine
    * @return \Illuminate\Http\Response
    */
   public function show(ClassRoutine $classroutine)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\ClassRoutine $classroutine
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, ClassRoutine $classroutine)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('classroutine-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $classrooms = ClassRoom::all();
            $teachers = Teacher::all();
            $view = View::make('backend.admin.class_routine.edit', compact('stdclass', 'teachers', 'classrooms', 'classroutine'))->render();
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
    * @param  \App\Models\ClassRoutine $classroutine
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, ClassRoutine $classroutine)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('classroutine-edit');
         if ($haspermision) {

            $classroutine = ClassRoutine::findOrFail($classroutine->id);

            $rules = [
              'teacher_id' => 'required',
              'class_id' => 'required',
              'section_id' => 'required',
              'subject_id' => 'required',
              'time_start' => 'required',
              'time_start_min' => 'required',
              'time_end' => 'required',
              'time_end_min' => 'required',
              'day' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $class_id = $request->input('class_id');
               $section_id = $request->input('class_id');
               $subject_id = $request->input('subject_id');
               $time_start = $request->input('time_start');
               $time_start_min = $request->input('time_start_min');
               $time_end = $request->input('time_end');
               $time_end_min = $request->input('time_end_min');
               $start_hour = $time_start . ':' . $time_start_min;
               $end_hour = $time_end . ':' . $time_end_min;
               $day = $request->input('day');
               $year = config('running_session');

               // $end_min = date('i', strtotime('-1 minutes', strtotime($end_hour)));

               DB::statement(DB::raw("SET @id = '$classroutine->id', @class_id = '$class_id', @section_id = '$section_id', @subject_id = '$subject_id',
               @start_hour = '$start_hour', @end_hour =  '$end_hour' ,@year = '$year', @day =  '$day' "));

               $rows = DB::select("SELECT class_id, section_id, DAY, year FROM class_routines
                     WHERE class_id = @class_id AND section_id = @section_id
                     AND DAY = @day AND YEAR=@year
                     AND id NOT IN (@id)
                     AND  TIME_FORMAT(CONCAT_WS(':', `time_end`, `time_end_min`), '%H %i') 
                     BETWEEN  TIME_FORMAT(@start_hour, '%H %i')  AND  TIME_FORMAT(@end_hour, '%H %i')");

               if (count($rows) == 0) {

                  $classroutine->class_id = $class_id;
                  $classroutine->section_id = $section_id;
                  $classroutine->subject_id = $subject_id;
                  $classroutine->teacher_id = $request->input('teacher_id');
                  $classroutine->class_room_id = $request->input('class_room_id');
                  $classroutine->time_start = $time_start;
                  $classroutine->time_start_min = $time_start_min;
                  $classroutine->time_end = $time_end;
                  $classroutine->time_end_min = $time_end_min;
                  $classroutine->day = $day;
                  $classroutine->year = $year;
                  $classroutine->save();
                  return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Class Routine  already exist in same selected requirements</div>"]);

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
    * @param  \App\Models\ClassRoutine $classroutine
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, ClassRoutine $classroutine)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('classroutine-delete');
         if ($haspermision) {
            $classroutine->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

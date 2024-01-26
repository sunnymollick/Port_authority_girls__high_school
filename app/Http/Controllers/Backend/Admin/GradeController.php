<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;


class GradeController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.grade.all');
   }

   public function allGrades()
   {
      DB::statement(DB::raw('set @rownum=0'));
      $grades = Grade::get(['grades.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      return DataTables::of($grades)
        ->addColumn('action', 'backend.admin.grade.action')
        ->make(true);
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('grade-create');
         if ($haspermision) {
            $view = View::make('backend.admin.grade.create')->render();
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
         $haspermision = auth()->user()->can('grade-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required|unique:grades,name',
              'grade_point' => 'required|unique:grades,grade_point',
              'mark_from' => 'required',
              'mark_upto' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $grade = new Grade;
               $grade->name = $request->input('name');
               $grade->grade_point = $request->input('grade_point');
               $grade->mark_from = $request->input('mark_from');
               $grade->mark_upto = $request->input('mark_upto');
               $grade->comment = $request->input('comment');
               $grade->save(); //
               return response()->json(['type' => 'success', 'message' => "Successfully Created"]);
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
    * @param  \App\Models\Grade $grade
    * @return \Illuminate\Http\Response
    */
   public function show(Grade $grade)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\Grade $grade
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Grade $grade)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('grade-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.grade.edit', compact('grade'))->render();
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
    * @param  \App\Models\Grade $grade
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Grade $grade)
   {

      if ($request->ajax()) {
         $haspermision = auth()->user()->can('grade-edit');
         if ($haspermision) {
            Grade::findOrFail($grade->id);
            $rules = [
              'name' => 'required|unique:grades,name,' . $grade->id,
              'grade_point' => 'required|unique:grades,grade_point,' . $grade->id,
              'mark_from' => 'required',
              'mark_upto' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $grade->name = $request->input('name');
               $grade->grade_point = $request->input('grade_point');
               $grade->mark_from = $request->input('mark_from');
               $grade->mark_upto = $request->input('mark_upto');
               $grade->comment = $request->input('comment');
               $grade->save();  //
               return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
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
    * @param  \App\Models\Grade $grade
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Grade $grade)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('grade-delete');
         if ($haspermision) {
            $grade->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Section;

use App\Models\StdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;

class SectionController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.section.all');
   }

   public function allSections()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('section-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('section-delete')) {
         $can_delete = "style='display:none;'";
      }

      $sections = Section::with('stdclass')->orderBy('class_id', 'asc')->orderBy('name', 'ASC')->get();
      return Datatables::of($sections)
        ->addColumn('status', function ($section) {
           return $section->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('class_id', function ($section) {
           $class = $section->stdclass;
           return $class ? $class->name : '';
        })
        ->addColumn('action', function ($sections) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $sections->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $sections->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'status'])
        ->addIndexColumn()
        ->make(true);
   }


   public function getSections(Request $request, $class_id)
   {
      if ($request->ajax()) {

         $class = StdClass::findOrFail($class_id);
         $sections = $class->sections;
         if ($sections) {
            echo "<option value='' selected disabled> Select a section</option>";
            foreach ($sections as $section) {
               echo "<option  value='$section->id'>$section->name</option>";
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
         $haspermision = auth()->user()->can('section-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.section.create', compact('stdclass'))->render();
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
         $haspermision = auth()->user()->can('section-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required',
              'class_id' => 'required'
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
                 ->count();
               if ($rows == 0) {
                  $section = new Section;
                  $section->name = $request->input('name');
                  $section->class_id = $request->input('class_id');
                  $section->save(); //
                  return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Created</div>"]);

               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Section Name $name  already exist in same class</div>"]);

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
    * @param  \App\Models\Section $section
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Section $section)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('section-view');
         if ($haspermision) {
            $view = View::make('backend.admin.section.view', compact('section'))->render();
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
    * @param  \App\Models\Section $section
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Section $section)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('section-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.section.edit', compact('stdclass', 'section'))->render();
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
    * @param  \App\Models\Section $section
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Section $section)
   {
      if ($request->ajax()) {
         // Setup the validator
         Section::findOrFail($section->id);

         $rules = [
           'name' => 'required',
           'class_id' => 'required'
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
              ->whereNotIn('id', [$section->id])
              ->count();
            if ($rows == 0) {

               $section->name = $request->input('name');
               $section->class_id = $request->input('class_id');
               $section->status = $request->input('status');
               $section->save(); //
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
    * @param  \App\Models\Section $section
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Section $section)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('section-delete');
         if ($haspermision) {
            $section->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

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

class StdClassController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.stdclass.all');
   }

   public function allClasses()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('stdclass-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('stdclass-delete')) {
         $can_delete = "style='display:none;'";
      }

      $stdclass = StdClass::with('sections')->orderBy('in_digit', 'asc')->get();
      return Datatables::of($stdclass)
        ->addColumn('section', function ($stdclass) {
           $sections = $stdclass->sections;
           $data = array();
           foreach ($sections as $section) {
              $data[] = '<span class="label label-success">' . $section->name . '</span>';
           }
           return implode(" ", $data);
        })
        ->addColumn('status', function ($stdclass) {
           return $stdclass->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('action', function ($stdclass) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $stdclass->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $stdclass->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'status', 'section'])
        ->addIndexColumn()
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
         $haspermision = auth()->user()->can('stdclass-create');
         if ($haspermision) {
            $view = View::make('backend.admin.stdclass.create')->render();
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
         $haspermision = auth()->user()->can('stdclass-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required|unique:std_classes,name',
              'in_digit' => 'required|unique:std_classes'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $stdclass = new StdClass;
               $stdclass->name = $request->input('name');
               $stdclass->in_digit = $request->input('in_digit');
               $stdclass->save(); //
               return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Created</div>"]);
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
    * @param  \App\Models\StdClass $stdClass
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, StdClass $stdclass)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('stdclass-view');
         if ($haspermision) {
            $view = View::make('backend.admin.stdclass.view', compact('stdclass'))->render();
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
    * @param  \App\Models\StdClass $stdClass
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, StdClass $stdclass)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('stdclass-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.stdclass.edit', compact('stdclass'))->render();
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
    * @param  \App\Models\StdClass $stdClass
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, StdClass $stdclass)
   {
      if ($request->ajax()) {
         // Setup the validator
         StdClass::findOrFail($stdclass->id);

         $rules = [
           'name' => 'required|unique:std_classes,name,' . $stdclass->id,
           'in_digit' => 'required|unique:std_classes,in_digit,' . $stdclass->id,
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {

            $stdclass->name = $request->input('name');
            $stdclass->in_digit = $request->input('in_digit');
            $stdclass->status = $request->input('status');
            $stdclass->save();

            return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Updated</div>"]);
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\StdClass $stdClass
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, StdClass $stdclass)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('stdclass-delete');
         if ($haspermision) {
            $stdclass->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

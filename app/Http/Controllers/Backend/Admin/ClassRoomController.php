<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\ClassRoom;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;

class ClassRoomController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.class_room.all');
   }

   public function allClassrooms()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('classroom-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('classroom-delete')) {
         $can_delete = "style='display:none;'";
      }

      $rooms = ClassRoom::orderBy('id', 'asc')->get();
      return Datatables::of($rooms)
        ->addColumn('action', function ($rooms) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $rooms->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $rooms->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action'])
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
         $haspermision = auth()->user()->can('classroom-create');
         if ($haspermision) {
            $view = View::make('backend.admin.class_room.create')->render();
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
         $haspermision = auth()->user()->can('classroom-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required|unique:class_rooms,name',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $classroom = new ClassRoom;
               $classroom->name = $request->input('name');
               $classroom->capacity = $request->input('capacity');
               $classroom->save(); //
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
    * @param  \App\Models\ClassRoom $classroom
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, ClassRoom $classroom)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\ClassRoom $classroom
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, ClassRoom $classroom)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('classroom-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.class_room.edit', compact('classroom'))->render();
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
    * @param  \App\Models\ClassRoom $classroom
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, ClassRoom $classroom)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('classroom-edit');
         if ($haspermision) {
            ClassRoom::findOrFail($classroom->id);
            $rules = [
              'name' => 'required|unique:class_rooms,name,' . $classroom->id
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $classroom->name = $request->input('name');
               $classroom->capacity = $request->input('capacity');
               $classroom->save(); //
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
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\ClassRoom $classroom
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, ClassRoom $classroom)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('classroom-delete');
         if ($haspermision) {
            $classroom->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

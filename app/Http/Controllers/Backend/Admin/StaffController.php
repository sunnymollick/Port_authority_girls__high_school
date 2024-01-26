<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;
use View;
use DB;

class StaffController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.staff.all');
   }

   public function allStaffs()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('staff-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('staff-delete')) {
         $can_delete = "style='display:none;'";
      }

      $staffs = Staff::orderBy('id', 'asc')->get();
      return Datatables::of($staffs)
        ->addColumn('status', function ($staff) {
           return $staff->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('file_path', function ($staff) {
           return "<img src='" . asset($staff->file_path) . "' class='img-thumbnail' width='60px'>";
        })
        ->addColumn('action', function ($staffs) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" id="' . $staffs->id . '" class="btn btn-xs btn-info margin-r-5 view" title="View"><i class="fa fa-eye fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $staffs->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $staffs->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'file_path', 'status'])
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
         $haspermision = auth()->user()->can('staff-create');
         if ($haspermision) {
            $view = View::make('backend.admin.staff.create')->render();
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
         $haspermision = auth()->user()->can('staff-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required',
              'phone' => 'required|unique:staffs,phone',
              'photo' => 'image|max:2024|mimes:jpeg,jpg,gif,png'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $upload_ok = 1;
               $file_path = 'assets/images/staff_image/default.png';

               if ($request->hasFile('photo')) {
                  if (Input::file('photo')->isValid()) {
                     $destinationPath = 'assets/images/staff_image'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = $request->input('staff_code') . '.' . $extension; // renameing image
                     $file_path = 'assets/images/staff_image/' . $fileName;
                     Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                     $upload_ok = 1;

                  } else {
                     return response()->json([
                       'type' => 'error',
                       'message' => "<div class='alert alert-warning'>Please! File is not valid</div>"
                     ]);
                  }
               }
               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {

                  $staff = new Staff;
                  $staff->name = $request->input('name');
                  $staff->staff_code = $request->input('staff_code');
                  $staff->qualification = $request->input('qualification');
                  $staff->doj = $request->input('doj');
                  $staff->gender = $request->input('gender');
                  $staff->religion = $request->input('religion');
                  $staff->address = $request->input('address');
                  $staff->phone = $request->input('phone');
                  $staff->email = $request->input('email');
                  $staff->designation = $request->input('designation');
                  $staff->file_path = $file_path;
                  $staff->save(); //
                  return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

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
    * @param  \App\Models\Staff $staff
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Staff $staff)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('staff-view');
         if ($haspermision) {
            $view = View::make('backend.admin.staff.view', compact('staff'))->render();
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
    * @param  \App\Models\Staff $staff
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Staff $staff)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('staff-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.staff.edit', compact('staff'))->render();
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
    * @param  \App\Models\Staff $staff
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Staff $staff)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('staff-edit');
         if ($haspermision) {

            Staff::findOrFail($staff->id);

            $rules = [
              'name' => 'required',
              'phone' => 'required|unique:staffs,phone,' . $staff->id,
              'photo' => 'image|max:2024|mimes:jpeg,jpg,gif,png'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $upload_ok = 1;
               $file_path = $request->input('SelectedFileName');

               if ($request->hasFile('photo')) {
                  if (Input::file('photo')->isValid()) {
                     $destinationPath = 'assets/images/staff_image'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = $request->input('staff_code') . '.' . $extension; // renameing image
                     $file_path = 'assets/images/staff_image/' . $fileName;
                     Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                     $upload_ok = 1;

                  } else {
                     return response()->json([
                       'type' => 'error',
                       'message' => "<div class='alert alert-warning'>Please! File is not valid</div>"
                     ]);
                  }
               }
               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {

                  $staff->name = $request->input('name');
                  $staff->qualification = $request->input('qualification');
                  $staff->staff_code = $request->input('staff_code');
                  $staff->doj = $request->input('doj');
                  $staff->gender = $request->input('gender');
                  $staff->religion = $request->input('religion');
                  $staff->address = $request->input('address');
                  $staff->phone = $request->input('phone');
                  $staff->email = $request->input('email');
                  $staff->designation = $request->input('designation');
                  $staff->file_path = $file_path;
                  $staff->status = $request->input('status');
                  $staff->save(); //
                  return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);

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
    * @param  \App\Models\Staff $staff
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Staff $staff)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('staff-delete');
         if ($haspermision) {
            $staff->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

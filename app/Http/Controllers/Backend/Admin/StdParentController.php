<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\StdParent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;

use View;
use DB;
use Excel;

class StdParentController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.parent.all');
   }

   public function allParents()
   {
      $can_edit = $can_delete = '';
      if (!auth()->user()->can('parent-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('parent-delete')) {
         $can_delete = "style='display:none;'";
      }
      $parents = StdParent::orderBy('parent_code', 'desc')->get();
      return Datatables::of($parents)
        ->addColumn('action', function ($parents) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" id="' . $parents->id . '" class="btn btn-xs btn-info margin-r-5 view" title="View"><i class="fa fa-eye fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $parents->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $parents->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . ' id="' . $parents->id . '" class="btn btn-xs btn-success margin-r-5 password" title="Change Password"><i class="fa fa-lock fa-fw"></i> </a>';
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
         $haspermision = auth()->user()->can('parent-create');
         if ($haspermision) {
            $view = View::make('backend.admin.parent.create')->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function import()
   {
      $haspermision = auth()->user()->can('parent-import');
      if ($haspermision) {
         return view('backend.admin.parent.import');
      } else {
         abort(403, 'Sorry, you are not authorized to access the page');
      }
   }

   public function importStore(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('parent-import');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required'
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

                     $destinationPath = 'assets/uploads/parents_excel_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/parents_excel_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path

                     $data = Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) {
                     })->get();

                     if (!empty($data) && $data->count()) {

                        foreach ($data as $key => $value) {
                           $insert = [
                             'parent_code' => "$value->parent_code",
                             'father_name' => "$value->father_name",
                             'mother_name' => "$value->mother_name",
                             'email' => "$value->email",
                             'phone' => "$value->phone",
                             'password' => Hash::make(123456),
                             'gender' => "$value->gender",
                             'blood_group' => "$value->blood_group",
                             'address' => "$value->address",
                             'profession' => "$value->profession",
                             'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now(),
                           ];

                           DB::table('parents')->updateOrInsert(['parent_code' => "$value->parent_code"], $insert);
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

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('parent-create');
         if ($haspermision) {

            $rules = [
              'father_name' => 'required',
              'mother_name' => 'required',
              'parent_code' => 'required|unique:parents,parent_code',
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
               $file_path = 'assets/images/parent_image/default.png';


               if ($request->hasFile('photo')) {
                  if (Input::file('photo')->isValid()) {
                     $destinationPath = 'assets/images/parent_image'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = 'assets/images/parent_image/' . $fileName;
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

                  $parent = new StdParent();
                  $parent->father_name = $request->input('father_name');
                  $parent->mother_name = $request->input('mother_name');
                  $parent->parent_code = $request->input('parent_code');
                  $parent->email = $request->input('email');
                  $parent->gender = $request->input('gender');
                  $parent->blood_group = $request->input('blood_group');
                  $parent->phone = $request->input('phone');
                  $parent->password = Hash::make(123456);
                  $parent->address = $request->input('address');
                  $parent->profession = $request->input('profession');
                  $parent->file_path = $file_path;
                  $parent->save(); //
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
    * @param  \App\Models\StdParent $parent
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, StdParent $parent)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('parent-view');
         if ($haspermision) {
            $view = View::make('backend.admin.parent.view', compact('parent'))->render();
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
    * @param  \App\Models\StdParent $parent
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, StdParent $parent)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('parent-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.parent.edit', compact('parent'))->render();
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
    * @param  \App\Models\StdParent $parent
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, StdParent $parent)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('parent-create');
         if ($haspermision) {

            StdParent::findOrFail($parent->id);

            $rules = [
              'father_name' => 'required',
              'mother_name' => 'required',
              'parent_code' => 'required|unique:parents,parent_code,' . $parent->id,
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
                     $destinationPath = 'assets/images/parent_image'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = 'assets/images/parent_image/' . $fileName;
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
                  $parent->father_name = $request->input('father_name');
                  $parent->mother_name = $request->input('mother_name');
                  $parent->parent_code = $request->input('parent_code');
                  $parent->email = $request->input('email');
                  $parent->gender = $request->input('gender');
                  $parent->blood_group = $request->input('blood_group');
                  $parent->phone = $request->input('phone');
                  $parent->address = $request->input('address');
                  $parent->profession = $request->input('profession');
                  $parent->file_path = $file_path;
                  $parent->save(); //
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
    * @param  \App\Models\StdParent $parent
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, StdParent $parent)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('parent-delete');
         if ($haspermision) {
            $parent->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function change_password(Request $request, $parent_id)
   {
      if ($request->ajax()) {
         $view = View::make('backend.admin.parent.change_password', compact('parent_id'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function update_password(Request $request)
   {
      if ($request->ajax()) {
         $parent = StdParent::findOrFail($request->input('parent_id'));

         $rules = [
           'password' => 'required'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {
            $parent->password = Hash::make($request->input('password'));
            $parent->save(); //
            return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

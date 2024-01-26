<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;

use View;
use DB;
use Excel;

class TeacherController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.teacher.all');
   }

   public function allTeachers()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('teacher-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('teacher-delete')) {
         $can_delete = "style='display:none;'";
      }

      $teachers = Teacher::orderBy('order', 'asc')->get();
      return Datatables::of($teachers)
        ->addColumn('status', function ($result) {
           return $result->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('file_path', function ($teachers) {
           return "<img src='" . asset($teachers->file_path) . "' class='img-thumbnail' width='50px'>";
        })
        ->addColumn('action', function ($teachers) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" id="' . $teachers->id . '" class="btn btn-xs btn-info margin-r-5 view" title="View"><i class="fa fa-eye fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $teachers->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $teachers->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . ' id="' . $teachers->id . '" class="btn btn-xs btn-success margin-r-5 password" title="Change Password"><i class="fa fa-lock fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'status', 'file_path'])
        ->addIndexColumn()
        ->make(true);
   }


   public function import()
   {
      $haspermision = auth()->user()->can('teacher-import');
      if ($haspermision) {
         return view('backend.admin.teacher.import');
      } else {
         abort(403, 'Sorry, you are not authorized to access the page');
      }
   }

   public function importStore(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('teacher-import');
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

                     $destinationPath = 'assets/uploads/teachers_excel_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/teachers_excel_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path

                     $data = Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) {
                     })->get();

                     $teacher_image_path = 'assets/images/teacher_image/';

                     if (!empty($data) && $data->count()) {

                        foreach ($data as $key => $value) {
                           $insert = [
                             'teacher_code' => "$value->teacher_id",
                             'name' => "$value->name",
                             'order' => "$value->teacher_serial",
                             'qualification' => "$value->qualification",
                             'subject' => "$value->subject",
                             'marital_status' => "$value->marital_status",
                             'dob' => "$value->date_of_birth",
                             'doj' => "$value->school_join_date",
                             'gender' => "$value->gender",
                             'religion' => "$value->religion",
                             'phone' => "$value->phone",
                             'email' => "$value->email",
                             'password' => Hash::make(123456),
                             'blood_group' => "$value->blood_group",
                             'address' => "$value->address",
                             'designation' => "$value->designation",
                             'file_path' => $teacher_image_path . "$value->teacher_id" . '.jpg',
                             'created_at' => Carbon::now(),
                             'updated_at' => Carbon::now(),
                           ];

                           DB::table('teachers')->updateOrInsert(['order' => "$value->teacher_serial"], $insert);
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
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('teacher-create');
         if ($haspermision) {
            $view = View::make('backend.admin.teacher.create')->render();
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
         $haspermision = auth()->user()->can('teacher-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required',
              'teacher_code' => 'required|unique:teachers,teacher_code',
              'email' => 'required|email|unique:teachers,email',
              'phone' => 'required|unique:teachers,phone',
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
               $file_path = 'assets/images/teacher_image/default.png';

               if ($request->hasFile('photo')) {
                  if (Input::file('photo')->isValid()) {
                     $destinationPath = 'assets/images/teacher_image'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = 'assets/images/teacher_image/' . $fileName;
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

                  $teacher = new Teacher;
                  $teacher->name = $request->input('name');
                  $teacher->teacher_code = $request->input('teacher_code');
                  $teacher->dob = $request->input('dob');
                  $teacher->doj = $request->input('doj');
                  $teacher->qualification = $request->input('qualification');
                  $teacher->subject = $request->input('subject');
                  $teacher->marital_status = $request->input('marital_status');
                  $teacher->gender = $request->input('gender');
                  $teacher->religion = $request->input('religion');
                  $teacher->blood_group = $request->input('blood_group');
                  $teacher->address = $request->input('address');
                  $teacher->phone = $request->input('phone');
                  $teacher->email = $request->input('email');
                  $teacher->password = Hash::make(123456);
                  $teacher->designation = $request->input('designation');
                  $teacher->order = $request->input('order');
                  $teacher->file_path = $file_path;
                  $teacher->save(); //
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
    * @param  \App\Models\Teacher $teacher
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Teacher $teacher)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('teacher-view');
         if ($haspermision) {
            $view = View::make('backend.admin.teacher.view', compact('teacher'))->render();
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
    * @param  \App\Models\Teacher $teacher
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Teacher $teacher)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('teacher-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.teacher.edit', compact('teacher'))->render();
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
    * @param  \App\Models\Teacher $teacher
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Teacher $teacher)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('teacher-edit');
         if ($haspermision) {

            Teacher::findOrFail($teacher->id);

            $rules = [
              'name' => 'required',
              'teacher_code' => 'required|unique:teachers,teacher_code,' . $teacher->id,
              'phone' => 'required|unique:teachers,phone,' . $teacher->id,
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
                     $destinationPath = 'assets/images/teacher_image'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = 'assets/images/teacher_image/' . $fileName;
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

                  $teacher->name = $request->input('name');
                  $teacher->teacher_code = $request->input('teacher_code');
                  $teacher->dob = $request->input('dob');
                  $teacher->doj = $request->input('doj');
                  $teacher->qualification = $request->input('qualification');
                  $teacher->subject = $request->input('subject');
                  $teacher->marital_status = $request->input('marital_status');
                  $teacher->gender = $request->input('gender');
                  $teacher->religion = $request->input('religion');
                  $teacher->blood_group = $request->input('blood_group');
                  $teacher->address = $request->input('address');
                  $teacher->phone = $request->input('phone');
                  $teacher->designation = $request->input('designation');
                  $teacher->order = $request->input('order');
                  $teacher->file_path = $file_path;
                  $teacher->status = $request->input('status');
                  //  $teacher->password = Hash::make(123456);
                  $teacher->save(); //
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
    * @param  \App\Models\Teacher $teacher
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Teacher $teacher)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('teacher-delete');
         if ($haspermision) {
            $teacher->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function change_password(Request $request, $teacher_id)
   {
      if ($request->ajax()) {
         $view = View::make('backend.admin.teacher.change_password', compact('teacher_id'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function update_password(Request $request)
   {
      if ($request->ajax()) {
         $teacher = Teacher::findOrFail($request->input('teacher_id'));

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
            $teacher->password = Hash::make($request->input('password'));
            $teacher->save(); //
            return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

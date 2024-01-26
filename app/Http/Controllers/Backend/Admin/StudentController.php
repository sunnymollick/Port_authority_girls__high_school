<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\Academic;
use App\Http\Controllers\Controller;
use App\Models\Enroll;
use App\Models\StdClass;
use App\Models\StdParent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use View;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

use DB;
use Excel;
use PDF;

class StudentController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.student.index', compact('stdclass'));
   }

   public function allStudents(Request $request)
   {
      if ($request->ajax()) {

         $can_edit = $can_delete = '';
         if (!auth()->user()->can('student-edit')) {
            $can_edit = "style='display:none;'";
         }
         if (!auth()->user()->can('student-delete')) {
            $can_delete = "style='display:none;'";
         }

         $class_id = $request->input('class_id');

         $class_name = $request->input('class_name');
         $section = $request->input('section');

         $section_id = $request->input('section_id');
         if ($section_id == 'all') {
            $section_id = 'null';
         }

         DB::statement(DB::raw("SET @section_id = $section_id"));

         $students = DB::table('enrolls')
           ->join('students', 'students.id', '=', 'enrolls.student_id')
           ->join('sections', 'sections.id', '=', 'enrolls.section_id')
           ->select('students.*', 'enrolls.roll', 'sections.name as section_name')
           ->where('enrolls.class_id', $class_id)
           ->where('enrolls.section_id', DB::raw('COALESCE(@section_id, enrolls.section_id)'))
           ->where('enrolls.year', config('running_session'))
           ->orderBy('enrolls.roll', 'asc')->get();

         return Datatables::of($students)
           ->addColumn('class_name', function () use ($class_name) {
              return $class_name;
           })
           ->addColumn('status', function ($student) {
              return $student->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
           })
           ->addColumn('action', function ($student) use ($can_edit, $can_delete) {
              $html = '<div class="btn-group">';
              $html .= '<a data-toggle="tooltip" id="' . $student->id . '" class="btn btn-xs btn-info margin-r-5 view" title="View"><i class="fa fa-eye fa-fw"></i> </a>';
              $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $student->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
              $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $student->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
              $html .= '<a data-toggle="tooltip" ' . $can_edit . ' id="' . $student->id . '" class="btn btn-xs btn-success margin-r-5 password" title="Change Password"><i class="fa fa-lock fa-fw"></i> </a>';
              $html .= '</div>';
              return $html;
           })
           ->rawColumns(['action', 'file_path', 'status'])
           ->addIndexColumn()
           ->make(true);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function getStudents(Request $request, $class_id)
   {
      if ($request->ajax()) {

         $students = DB::table('enrolls')
           ->join('students', 'students.id', '=', 'enrolls.student_id')
           ->select('students.*')
           ->where('enrolls.class_id', $class_id)
           ->where('enrolls.year', config('running_session'))->get();

         if ($students) {
            echo "<option value='' selected disabled> Choose Student</option>";
            foreach ($students as $std) {
               echo "<option  value='$std->std_code'> $std->name ($std->std_code)</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getAllStudents(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');

         $students = DB::table('enrolls')
           ->join('students', 'students.id', '=', 'enrolls.student_id')
           ->select('students.*')
           ->where('enrolls.class_id', $class_id)
           ->where('enrolls.section_id', $section_id)
           ->where('enrolls.year', config('running_session'))->get();

         if ($students) {
            echo "<option value='' selected disabled> Choose Student</option>";
            foreach ($students as $std) {
               echo "<option  value='$std->std_code'> $std->name ($std->std_code)</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function import()
   {
      $haspermision = auth()->user()->can('student-import');
      if ($haspermision) {
         $stdclass = StdClass::all();
         return view('backend.admin.student.import', compact('stdclass'));
      } else {
         abort(403, 'Sorry, you are not authorized to access the page');
      }
   }

   public function importStore(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('student-import');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required',
              'class_id' => 'required',
              'section_id' => 'required'
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
                  if ($extension == "xlsx" || $extension == "xls") {

                     $destinationPath = 'assets/uploads/students_excel_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/students_excel_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path


                     $year = config('running_session');
                     $class_name = $request->input('class_name');
                     $section_name = $request->input('section_name');


                     $img_path = 'assets/uploads/student_images/' . $year . '/' . $class_name . '/' . $section_name . '/';
                     if (!File::isDirectory($img_path)) {
                        File::makeDirectory(trim($img_path), 0777, true, true);
                     }


                     $data = Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) {
                     })->get();
                     // dd($data);

                     if (!empty($data) && $data->count()) {


                        DB::beginTransaction();
                        try {

                           foreach ($data as $key => $value) {
                              if ($value->name != '' && "$value->student_code" != '') {

                                 $student = Student::where('std_code', "$value->student_code")->first();
                                 if (empty($student)) {
                                    $student = new Student();
                                    $student->name = $value->name;
                                    $student->std_code = "$value->student_code";
                                    $student->email = $value->email;
                                    $student->gender = $value->gender;
                                    $student->dob = $value->dob;
                                    $student->religion = $value->religion;
                                    $student->blood_group = $value->blood_group;
                                    $student->phone = "$value->phone";
                                    $student->password = Hash::make(123456);
                                    $student->address = $value->address;
                                    $student->parent_id = "$value->student_code";
                                    $student->file_path = $img_path . "$value->student_code" . '.jpg';
                                    $student->save(); //

                                    if ($student->id) {
                                       $enroll = new Enroll();
                                       $enroll->student_code = "$value->student_code";
                                       $enroll->subject_id = "$value->optional_subject_id";
                                       $enroll->student_id = $student->id;
                                       $enroll->class_id = $request->input('class_id');
                                       $enroll->section_id = $request->input('section_id');
                                       $enroll->subject_id = "$value->optional_subject_id" ? "$value->optional_subject_id" : 0;
                                       $enroll->roll = "$value->roll" ? "$value->roll" : NULL;
                                       $enroll->date_added = date('Y-m-d');
                                       $enroll->year = config('running_session');
                                       $enroll->save();
                                    }
                                 } else {
                                    // update
                                    $student->name = $value->name;
                                    $student->email = $value->email;
                                    $student->gender = $value->gender;
                                    $student->dob = $value->dob;
                                    $student->religion = $value->religion;
                                    $student->blood_group = $value->blood_group;
                                    $student->phone = "$value->phone";
                                    $student->password = Hash::make(123456);
                                    $student->address = $value->address;
                                    $student->parent_id = "$value->student_code";
                                    $student->file_path = $img_path . "$value->student_code" . '.jpg';
                                    $student->save(); //
                                    
                                    //**enroll update
                                    $enroll = Enroll::where('student_code', $student->std_code)->where('year', config('running_session'))->first();
                                    $enroll->subject_id = "$value->optional_subject_id";
                                    $enroll->subject_id = "$value->optional_subject_id" ? "$value->optional_subject_id" : 0;
                                    $enroll->date_added = date('Y-m-d');
                                    $enroll->save();
                                    //**enroll update

                                 }
                              }

                           }

                           DB::commit();
                           return response()->json(['type' => 'success', 'message' => "Successfully Imported"]);

                        } catch (\Exception $e) {
                           DB::rollback();
                           return response()->json(['type' => 'error', 'message' => "Insert Failed"]);
                        }

                     } else {
                        return response()->json([
                          'type' => 'error',
                          'message' => "Error! No records in file"
                        ]);
                     }


                  } else {
                     return response()->json([
                       'type' => 'error',
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
         $haspermision = auth()->user()->can('student-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.student.create', compact('stdclass'))->render();
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
         $haspermision = auth()->user()->can('student-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required',
              'std_code' => 'required|unique:students,std_code',
              'photo' => 'image|max:2024|mimes:jpeg,jpg,gif,png'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {


               $year = config('running_session');
               $class_name = $request->input('class_name');
               $section_name = $request->input('section_name');
               $img_path = 'assets/uploads/student_images/' . $year . '/' . $class_name . '/' . $section_name . '/';
               if (!File::isDirectory($img_path)) {
                  File::makeDirectory(trim($img_path), 0777, true, true);
               }

               $upload_ok = 1;
               $file_path = "assets/images/default/student.png";

               if ($request->hasFile('photo')) {
                  if (Input::file('photo')->isValid()) {
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = $img_path . $fileName;
                     Input::file('photo')->move($img_path, $fileName); // uploading file to given path
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

                  DB::beginTransaction();
                  try {

                     $student = new Student();
                     $student->name = $request->input('name');
                     $student->std_code = $request->input('std_code');
                     $student->email = $request->input('email');
                     $student->gender = $request->input('gender');
                     $student->dob = $request->input('dob');
                     $student->religion = $request->input('religion');
                     $student->blood_group = $request->input('blood_group');
                     $student->phone = $request->input('phone');
                     $student->password = Hash::make(123456);
                     $student->address = $request->input('address');
                     $student->parent_id = $request->input('std_code');
                     $student->file_path = $file_path;
                     $student->save(); //

                     if ($student->id) {
                        $enroll = new Enroll();
                        $enroll->student_code = $request->input('std_code');
                        $enroll->student_id = $student->id;
                        $enroll->class_id = $request->input('class_id');
                        $enroll->section_id = $request->input('section_id');
                        $enroll->subject_id = $request->input('subject_id') ? $request->input('subject_id') : 0;
                        $enroll->roll = $request->input('roll');
                        $enroll->date_added = date('Y-m-d');
                        $enroll->year = config('running_session');
                        $enroll->save();
                     }
                     DB::commit();
                     return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

                  } catch (\Exception $e) {
                     DB::rollback();
                     return response()->json(['type' => 'error', 'message' => "Insert Failed"]);
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
    * @param  \App\Models\Student $student
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Student $student)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('student-view');
         if ($haspermision) {
            $enroll = Enroll::where('student_id', $student->id)
              ->where('year', config('running_session'))
              ->first();
            $parent = StdParent::where('parent_code', $student->parent_id)->first();
            $view = View::make('backend.admin.student.view', compact('student', 'enroll', 'parent'))->render();
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
    * @param  \App\Models\Student $student
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Student $student)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('student-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $enroll = Enroll::where('student_id', $student->id)
              ->where('year', config('running_session'))
              ->first();
            $parent = StdParent::where('parent_code', $student->parent_id)->first();
            $view = View::make('backend.admin.student.edit', compact('student', 'enroll', 'parent', 'stdclass'))->render();
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
    * @param  \App\Models\Student $student
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Student $student)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('student-edit');
         if ($haspermision) {

            Student::findOrFail($student->id);

            $rules = [
              'name' => 'required',
              'std_code' => 'required|unique:students,std_code,' . $student->id,
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
               $year = config('running_session');
               $class_name = $request->input('class_name');
               $section_name = $request->input('section_name');
               $img_path = 'assets/uploads/student_images/' . $year . '/' . $class_name . '/' . $section_name . '/';
               if (!File::isDirectory($img_path)) {
                  File::makeDirectory(trim($img_path), 0777, true, true);
               }


               if ($request->hasFile('photo')) {
                  if (Input::file('photo')->isValid()) {
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = $img_path . $fileName;
                     Input::file('photo')->move($img_path, $fileName); // uploading file to given path
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

                  DB::beginTransaction();
                  try {
                     $student->name = $request->input('name');
                     $student->std_code = $request->input('std_code');
                     $student->email = $request->input('email');
                     $student->gender = $request->input('gender');
                     $student->dob = $request->input('dob');
                     $student->religion = $request->input('religion');
                     $student->blood_group = $request->input('blood_group');
                     $student->phone = $request->input('phone');
                     $student->address = $request->input('address');
                     $student->parent_id = $request->input('std_code');
                     $student->status = $request->input('status');
                     $student->file_path = $file_path;
                     $student->save(); //

                     if ($student) {
                        $enroll = Enroll::findOrFail($request->input('enroll_id'));
                        $enroll->student_code = $request->input('std_code');
                        $enroll->class_id = $request->input('class_id');
                        $enroll->section_id = $request->input('section_id');
                        $enroll->subject_id = $request->input('subject_id') ? $request->input('subject_id') : 0;
                        $enroll->roll = $request->input('roll');
                        $enroll->date_added = date('Y-m-d');
                        $enroll->save();
                     }
                     DB::commit();
                     return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

                  } catch (\Exception $e) {
                     DB::rollback();
                     return response()->json(['type' => 'error', 'message' => "Insert Failed"]);
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
    * @param  \App\Models\Student $student
    * @return \Illuminate\Http\Response
    */

   public function destroy(Request $request, Student $student)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('student-delete');
         if ($haspermision) {
            $student->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function promotion()
   {
      $haspermision = auth()->user()->can('student-promotion');
      if ($haspermision) {
         $stdclass = StdClass::all();
         return view('backend.admin.student.promotion', compact('stdclass'));
      } else {
         abort(403, 'Sorry, you are not authorized to access the page');
      }
   }

   public function importPromotion(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('student-promotion');
         if ($haspermision) {
            $rules = [
              'excel_upload' => 'required',
              'class_id' => 'required',
              'section_id' => 'required'
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

                     $destinationPath = 'assets/uploads/students_excel_uploads'; // upload path
                     $fileName = date('d_m_Y_h_i_s_') . time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/students_excel_uploads/' . $fileName;
                     Input::file('excel_upload')->move($destinationPath, $fileName); // uploading file to given path

                     $data = Excel::selectSheetsByIndex(0)->load($file_path, function ($reader) {
                     })->get();

                     if (!empty($data) && $data->count()) {

                        DB::beginTransaction();
                        try {

                           foreach ($data as $key => $value) {
                              if ("$value->student_code" != '') {
                                 $student = Student::where('std_code', "$value->student_code")->first();
                                 if ($student) {
                                    $enroll = Enroll::where('student_id', $student->id)
                                      ->where('year', config('running_session'))
                                      ->first();
                                    if ($enroll == null) {
                                       $enroll = new Enroll();
                                       $enroll->student_code = "$value->student_code";
                                       $enroll->student_id = $student->id;
                                       $enroll->class_id = $request->input('class_id');
                                       $enroll->section_id = $request->input('section_id');
                                       $enroll->subject_id = "$value->optional_subject_id" ? "$value->optional_subject_id" : 0;
                                       $enroll->roll = "$value->roll" ? "$value->roll" : NULL;
                                       $enroll->date_added = date('Y-m-d');
                                       $enroll->year = config('running_session');
                                       $enroll->save();
                                    }
                                 }
                              }
                           }
                           DB::commit();
                           return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

                        } catch (\Exception $e) {
                           DB::rollback();
                           return response()->json(['type' => 'error', 'message' => "Insert Failed"]);
                        }

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


   public function change_password(Request $request, $student_id)
   {
      if ($request->ajax()) {
         $view = View::make('backend.admin.student.change_password', compact('student_id'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function update_password(Request $request)
   {
      if ($request->ajax()) {
         $student = Student::findOrFail($request->input('student_id'));

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
            $student->password = Hash::make($request->input('password'));
            $student->save(); //
            return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function exportStudentExcel($class_id, $section_id)
   {

      $students = Academic::exportStudent($class_id, $section_id);

      $payload = array();
      $class = null;
      $section = null;

      if ($students->count() > 0) {
         foreach ($students as $key => $value) {
            $payload[] = array(
              'std_code' => $value->std_code,
              'name' => $value->name,
              'class' => $value->class_name,
              'section' => $value->section,
              'roll' => $value->roll
            );


            $class = $value->class_name;
            $section = $value->section;
         }

      }

      return Excel::create('Students_' . $class . '_' . $section, function ($excel) use ($payload) {
         $excel->sheet('Students', function ($sheet) use ($payload) {
            $sheet->fromArray($payload);
         });
      })->download('xls');

   }

   public function exportStudentPdf($class_id, $section_id)
   {

      $students = Academic::exportStudent($class_id, $section_id);

      $class = null;
      $section = null;
      $view = view('backend.admin.student.export_pdf', compact('students'));
      $html = $view->render();

      $pdf = PDF::loadHTML($html);
      $sheet = $pdf->setPaper('a4', 'portrait');
      return $sheet->download('Students.pdf');

   }

}

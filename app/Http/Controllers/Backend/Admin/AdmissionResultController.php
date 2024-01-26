<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\AdmissionResult;
use App\Models\StdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

use View;
use DB;

class AdmissionResultController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

   public function index()
   {
      return view('backend.admin.exam.admission_result.all');
   }

   public function allAdmissionResult()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('admission-result-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('admission-result-delete')) {
         $can_delete = "style='display:none;'";
      }

      $result = AdmissionResult::with('stdclass', 'section')->where('year', config('running_session'))->orderBy('id', 'desc')->get();
      return Datatables::of($result)
        ->addColumn('status', function ($result) {
           return $result->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('file_path', function ($result) {
           return $result->file_path ? "<a class='btn btn-primary' href='" . asset($result->file_path) . "' target='_blank'>Download</a>" : '';
        })
        ->addColumn('class_name', function ($result) {
           $class = $result->stdclass;
           return $class ? $class->name : '';
        })
        ->addColumn('section_name', function ($result) {
           $section = $result->section;
           return $section ? $section->name : '';
        })
        ->addColumn('action', function ($result) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $result->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $result->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'status', 'file_path'])
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
         $haspermision = auth()->user()->can('admission-result-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.exam.admission_result.create', compact('stdclass'))->render();
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
         $haspermision = auth()->user()->can('admission-result-create');
         if ($haspermision) {

            $rules = [
              'title' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $upload_ok = 1;

               if ($request->hasFile('result_file')) {

                  if (Input::file('result_file')->isValid()) {
                     $extension = Input::file('result_file')->getClientOriginalExtension();;
                     if ($extension == "doc" || $extension == "docx" || $extension == "pdf" || $extension == "jpg" || $extension == "jpeg" || $extension == "png") {

                        $destinationPath = 'assets/uploads/admission_result'; // upload path
                        $extension = Input::file('result_file')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/admission_result/' . $fileName;
                        Input::file('result_file')->move($destinationPath, $fileName); // uploading file to given path
                        $upload_ok = 1;

                     } else {
                        return response()->json([
                          'type' => 'error',
                          'message' => "<div class='alert alert-warning'>Error! File is not valid</div>"
                        ]);
                     }
                  }
               } else {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Error! No file selected</div>"
                  ]);
               }


               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {

                  $admissionResult = new AdmissionResult();
                  $admissionResult->title = $request->input('title');
                  $admissionResult->class_id = $request->input('class_id');
                  $admissionResult->section_id = $request->input('section_id');
                  $admissionResult->file_path = $file_path;
                  $admissionResult->year = config('running_session');
                  $admissionResult->save(); //
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
    * @param  \App\Models\AdmissionResult $admissionResult
    * @return \Illuminate\Http\Response
    */
   public function show(AdmissionResult $admissionResult)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\AdmissionResult $admissionResult
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, AdmissionResult $admissionResult)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('admission-result-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.exam.admission_result.edit', compact('admissionResult', 'stdclass'))->render();
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
    * @param  \App\Models\AdmissionResult $admissionResult
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, AdmissionResult $admissionResult)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('admission-result-edit');
         if ($haspermision) {

            AdmissionResult::findOrFail($admissionResult->id);

            $rules = [
              'title' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $upload_ok = 1;

               if ($request->hasFile('result_file')) {
                  $extension = Input::file('result_file')->getClientOriginalExtension();;
                  if ($extension == "doc" || $extension == "docx" || $extension == "pdf" || $extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                     if (Input::file('result_file')->isValid()) {
                        $destinationPath = 'assets/uploads/exam_routine'; // upload path
                        $extension = Input::file('result_file')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/exam_routine/' . $fileName;
                        Input::file('result_file')->move($destinationPath, $fileName); // uploading file to given path
                        $upload_ok = 1;

                     } else {
                        return response()->json([
                          'type' => 'error',
                          'message' => "<div class='alert alert-warning'>File is not valid</div>"
                        ]);
                     }
                  } else {
                     return response()->json([
                       'type' => 'error',
                       'message' => "<div class='alert alert-warning'>Error! File type is not valid</div>"
                     ]);
                  }
               } else {
                  $upload_ok = 1;
                  $file_path = $request->input('SelectedFileName');
               }


               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {

                  $admissionResult->title = $request->input('title');
                  $admissionResult->class_id = $request->input('class_id');
                  $admissionResult->section_id = $request->input('section_id');
                  $admissionResult->file_path = $file_path;
                  $admissionResult->status = $request->input('status');
                  $admissionResult->save(); //
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
    * @param  \App\Models\AdmissionResult $admissionResult
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, AdmissionResult $admissionResult)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('admission-result-delete');
         if ($haspermision) {
            $admissionResult->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

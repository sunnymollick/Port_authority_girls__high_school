<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\AcademicCalender;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;

use View;
use DB;

class AcademicCalenderController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.academic_calender.all');
   }

   public function allAcademicCalender()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('academic-calender-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('academic-calender-delete')) {
         $can_delete = "style='display:none;'";
      }

      $academic = AcademicCalender::where('year', config('running_session'))->get();
      return Datatables::of($academic)
        ->addColumn('status', function ($academic) {
           return $academic->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('file_path', function ($academic) {
           return $academic->file_path ? "<a class='btn btn-primary' href='" . asset($academic->file_path) . "' target='_blank'>Download</a>" : '';
        })
        ->addColumn('action', function ($academic) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $academic->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $academic->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
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
         $haspermision = auth()->user()->can('academic_calender-create');
         if ($haspermision) {
            $view = View::make('backend.admin.academic_calender.create')->render();
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
         $haspermision = auth()->user()->can('academic_calender-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               if ($request->hasFile('photo')) {
                  $extension = Input::file('photo')->getClientOriginalExtension();;
                  if ($extension == "doc" || $extension == "docx" || $extension == "pdf" || $extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/uploads/academic_calender'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/academic_calender/' . $fileName;
                        Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
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
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Error! File not selected</div>"
                  ]);
               }


               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {
                  $rows = DB::table('academic_calenders')
                    ->where('year', config('running_session'))
                    ->count();
                  if ($rows == 0) {
                     $academiccalender = new AcademicCalender;
                     $academiccalender->name = $request->input('name');
                     $academiccalender->file_path = $file_path;
                     $academiccalender->year = config('running_session');
                     $academiccalender->save(); //
                     return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

                  } else {
                     return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Academic Calender already created of this year</div>"]);

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
    * @param  \App\Models\AcademicCalender $academiccalender
    * @return \Illuminate\Http\Response
    */
   public function show(AcademicCalender $academiccalender)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\AcademicCalender $academiccalender
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, AcademicCalender $academiccalender)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('academic_calender-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.academic_calender.edit', compact('academiccalender'))->render();
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
    * @param  \App\Models\AcademicCalender $academiccalender
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, AcademicCalender $academiccalender)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('academic_calender-edit');
         if ($haspermision) {

            AcademicCalender::findOrFail($academiccalender->id);

            $rules = [
              'name' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               if ($request->hasFile('photo')) {
                  $extension = Input::file('photo')->getClientOriginalExtension();;
                  if ($extension == "doc" || $extension == "docx" || $extension == "pdf" || $extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/uploads/academic_calender'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/academic_calender/' . $fileName;
                        Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
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

                  $academiccalender->name = $request->input('name');
                  $academiccalender->file_path = $file_path;
                  $academiccalender->save(); //
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
    * @param  \App\Models\AcademicCalender $academiccalender
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, AcademicCalender $academiccalender)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('academic_calender-delete');
         if ($haspermision) {
            $academiccalender->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

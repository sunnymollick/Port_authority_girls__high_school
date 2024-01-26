<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\StdClass;
use App\Models\Syllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;

use View;
use DB;

class SyllabusController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.syllabus.index', compact('stdclass'));
   }

   public function allSyllabus(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');

         DB::statement(DB::raw('set @rownum=0'));
         $syllabus = Syllabus::where('class_id', $class_id)->where('section_id', $section_id)->where('year', config('running_session'))->orderby('created_at', 'desc')->get(['syllabus.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
         return Datatables::of($syllabus)
           ->addColumn('action', 'backend.admin.syllabus.action')
           ->addColumn('file_path', function ($syllabus) {
              return $syllabus->file_path ? "<a class='btn btn-primary' href='" . asset($syllabus->file_path) . "'>Download</a>" : '';
           })
           ->addColumn('subject', function ($syllabus) {
              $subject = $syllabus->subject;
              return $subject ? $subject->name : '';
           })
           ->rawColumns(['action', 'file_path'])
           ->make(true);
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
         $haspermision = auth()->user()->can('syllabus-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.syllabus.create', compact('stdclass'))->render();
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
         $haspermision = auth()->user()->can('syllabus-create');
         if ($haspermision) {

            $rules = [
              'title' => 'required',
              'class_id' => 'required',
              'section_id' => 'required',
              'subject_id' => 'required'
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
                  if ($extension == "doc" || $extension == "docx" || $extension == "pdf") {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/uploads/syllabus'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/syllabus/' . $fileName;
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

                  $rows = DB::table('syllabus')
                    ->where('class_id', $request->input('class_id'))
                    ->where('section_id', $request->input('section_id'))
                    ->where('subject_id', $request->input('subject_id'))
                    ->where('year', config('running_session'))
                    ->count();
                  if ($rows == 0) {

                     $syllabus = new Syllabus;
                     $syllabus->title = $request->input('title');
                     $syllabus->class_id = $request->input('class_id');
                     $syllabus->section_id = $request->input('section_id');
                     $syllabus->subject_id = $request->input('subject_id');
                     $syllabus->year = config('running_session');
                     $syllabus->uploaded_by = auth()->user()->id;
                     $syllabus->file_path = $file_path;
                     $syllabus->save(); //
                     return response()->json(['type' => 'success', 'message' => "Successfully Created"]);
                  } else {
                     return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Syllabus  already exist in same selected requirements</div>"]);

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
    * @param  \App\Models\Syllabus $syllabus
    * @return \Illuminate\Http\Response
    */
   public function show(Syllabus $syllabus)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\Syllabus $syllabus
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Syllabus $syllabus)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('syllabus-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.syllabus.edit', compact('stdclass', 'syllabus'))->render();
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
    * @param  \App\Models\Syllabus $syllabus
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Syllabus $syllabus)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('syllabus-edit');
         if ($haspermision) {

            $syllabus = Syllabus::findOrFail($syllabus->id);

            $rules = [
              'title' => 'required',
              'class_id' => 'required',
              'section_id' => 'required',
              'subject_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               if ($request->hasFile('photo')) {
                  $extension = Input::file('photo')->getClientOriginalExtension();
                  if ($extension == "doc" || $extension == "docx" || $extension == "pdf") {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/uploads/syllabus'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/syllabus/' . $fileName;
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

                  $rows = DB::table('syllabus')
                    ->where('class_id', $request->input('class_id'))
                    ->where('section_id', $request->input('section_id'))
                    ->where('subject_id', $request->input('subject_id'))
                    ->where('year', config('running_session'))
                    ->whereNotIn('id', [$syllabus->id])
                    ->count();
                  if ($rows == 0) {
                     $syllabus->title = $request->input('title');
                     $syllabus->class_id = $request->input('class_id');
                     $syllabus->section_id = $request->input('section_id');
                     $syllabus->subject_id = $request->input('subject_id');
                     $syllabus->year = config('running_session');
                     $syllabus->uploaded_by = auth()->user()->id;
                     $syllabus->file_path = $file_path;
                     $syllabus->save(); //
                     return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
                  } else {
                     return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Syllabus  already exist in same selected requirements</div>"]);
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
    * @param  \App\Models\Syllabus $syllabus
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Syllabus $syllabus)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('syllabus-delete');
         if ($haspermision) {
            $syllabus->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

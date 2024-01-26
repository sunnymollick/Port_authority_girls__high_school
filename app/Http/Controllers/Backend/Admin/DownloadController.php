<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Download;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use View;
use Yajra\DataTables\DataTables;

class DownloadController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.frontend.download.all');
   }

   public function allDownloads(Request $request)
   {
      if ($request->ajax()) {
         DB::statement(DB::raw('set @rownum=0'));
         $downloads = Download::orderby('created_at', 'desc')->get(['downloads.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
         return Datatables::of($downloads)
           ->addColumn('action', 'backend.admin.frontend.download.action')
           ->addColumn('file_path', function ($download) {
              return $download->file_path ? "<a class='btn btn-primary' href='" . asset($download->file_path) . "'>Download</a>" : '';
           })
           ->addColumn('status', function ($download) {
              return $download->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
           })
           ->rawColumns(['action', 'file_path', 'status'])
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
         $haspermision = auth()->user()->can('download-create');
         if ($haspermision) {
            $view = View::make('backend.admin.frontend.download.create')->render();
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
         $haspermision = auth()->user()->can('download-create');
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

               if ($request->hasFile('photo')) {
                  $extension = Input::file('photo')->getClientOriginalExtension();;
                  if ($extension == "jpg" || $extension == "jpeg" || $extension == "png"
                    || $extension == "doc" || $extension == "docx" || $extension == "pdf" || $extension == "pptx"
                  ) {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/uploads/download'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/uploads/download/' . $fileName;
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
                  $download = new Download;
                  $download->title = $request->input('title');
                  $download->uploaded_by = auth()->user()->id;
                  $download->file_path = $file_path;
                  $download->save(); //
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
    * @param  \App\Models\Download $download
    * @return \Illuminate\Http\Response
    */
   public function show(Download $download)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\Download $download
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Download $download)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('download-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.frontend.download.edit', compact('download'))->render();
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
    * @param  \App\Models\Download $download
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Download $download)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('download-edit');
         if ($haspermision) {

            $download = Download::findOrFail($download->id);

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

               if ($request->hasFile('photo')) {
                  $extension = Input::file('photo')->getClientOriginalExtension();;
                  if ($extension == "jpg" || $extension == "jpeg" || $extension == "png"
                    || $extension == "doc" || $extension == "docx" || $extension == "pdf" || $extension == "pptx"
                  ) {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/uploads/download'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/images/uploads/' . $fileName;
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
                  $download->title = $request->input('title');
                  $download->uploaded_by = auth()->user()->id;
                  $download->file_path = $file_path;
                  $download->status = $request->input('status');
                  $download->save(); //
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
    * @param  \App\Models\Download $download
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Download $download)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('download-delete');
         if ($haspermision) {
            $download->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

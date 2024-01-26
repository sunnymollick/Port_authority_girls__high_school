<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Gallery;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use View;
use Yajra\DataTables\DataTables;

class GalleryController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.frontend.gallery.all');
   }

   public function allGalleries(Request $request)
   {
      if ($request->ajax()) {
         DB::statement(DB::raw('set @rownum=0'));
         $galleries = Gallery::orderby('created_at', 'desc')->get(['galleries.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
         return Datatables::of($galleries)
           ->addColumn('action', 'backend.admin.frontend.gallery.action')
           ->addColumn('file_path', function ($gallery) {
              return "<img src='" . asset($gallery->file_path) . "' width='80px'>";
           })
           ->addColumn('status', function ($gallery) {
              return $gallery->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
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
         $haspermision = auth()->user()->can('gallery-create');
         if ($haspermision) {
            $view = View::make('backend.admin.frontend.gallery.create')->render();
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
         $haspermision = auth()->user()->can('gallery-create');
         if ($haspermision) {

            $rules = [
              'title' => 'required',
              'photo' => 'max:3048|dimensions:max_width=2000,max_height=1500', // 2mb
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
                  if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/images/gallery'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/images/gallery/' . $fileName;
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
                  $gallery = new Gallery;
                  $gallery->title = $request->input('title');
                  $gallery->uploaded_by = auth()->user()->id;
                  $gallery->file_path = $file_path;
                  $gallery->save(); //
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
    * @param  \App\Models\Gallery $gallery
    * @return \Illuminate\Http\Response
    */
   public function show(Gallery $gallery)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\Gallery $gallery
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Gallery $gallery)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('gallery-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.frontend.gallery.edit', compact('gallery'))->render();
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
    * @param  \App\Models\Gallery $gallery
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Gallery $gallery)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('gallery-edit');
         if ($haspermision) {

            $gallery = Gallery::findOrFail($gallery->id);

            $rules = [
              'title' => 'required',
              'photo' => 'max:3048|dimensions:max_width=2000,max_height=1500', // 2mb
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
                  if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                     if (Input::file('photo')->isValid()) {
                        $destinationPath = 'assets/images/gallery'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/images/gallery/' . $fileName;
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
                  $gallery->title = $request->input('title');
                  $gallery->uploaded_by = auth()->user()->id;
                  $gallery->file_path = $file_path;
                  $gallery->status = $request->input('status');
                  $gallery->save(); //
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
    * @param  \App\Models\Gallery $gallery
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Gallery $gallery)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('gallery-delete');
         if ($haspermision) {
            $gallery->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

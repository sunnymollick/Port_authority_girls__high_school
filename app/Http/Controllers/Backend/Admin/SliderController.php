<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Slider;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use View;
use Yajra\DataTables\DataTables;

class SliderController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.frontend.slider.all');
   }

   public function allSliders(Request $request)
   {
      if ($request->ajax()) {
         DB::statement(DB::raw('set @rownum=0'));
         $sliders = Slider::orderby('order', 'asc')->get(['sliders.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
         return Datatables::of($sliders)
           ->addColumn('action', 'backend.admin.frontend.slider.action')
           ->addColumn('file_path', function ($slider) {
              return "<img src='" . asset($slider->file_path) . "' width='120px'>";
           })
           ->addColumn('status', function ($slider) {
              return $slider->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
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
         $haspermision = auth()->user()->can('slider-create');
         if ($haspermision) {
            $view = View::make('backend.admin.frontend.slider.create')->render();
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
         $haspermision = auth()->user()->can('slider-create');
         if ($haspermision) {

            $rules = [
              'title' => 'required',
              'photo' => 'max:2048|dimensions:max_width=1920,max_height=800', // 2mb
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
                        $destinationPath = 'assets/images/slider'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/images/slider/' . $fileName;
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
                  $slider = new Slider;
                  $slider->title = $request->input('title');
                  $slider->sub_title = $request->input('sub_title');
                  $slider->description = $request->input('description');
                  $slider->order = $request->input('order');
                  $slider->uploaded_by = auth()->user()->id;
                  $slider->file_path = $file_path;
                  $slider->save(); //
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
    * @param  \App\Models\Slider $slider
    * @return \Illuminate\Http\Response
    */
   public function show(Slider $slider)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\Slider $slider
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Slider $slider)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('slider-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.frontend.slider.edit', compact('slider'))->render();
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
    * @param  \App\Models\Slider $slider
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Slider $slider)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('slider-create');
         if ($haspermision) {

            $slider = Slider::findOrFail($slider->id);

            $rules = [
              'title' => 'required',
              'photo' => 'max:2048|dimensions:max_width=1920,max_height=800', // 2mb
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
                        $destinationPath = 'assets/images/slider'; // upload path
                        $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                        $fileName = time() . '.' . $extension; // renameing image
                        $file_path = 'assets/images/slider/' . $fileName;
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
                  $slider->title = $request->input('title');
                  $slider->sub_title = $request->input('sub_title');
                  $slider->description = $request->input('description');
                  $slider->order = $request->input('order');
                  $slider->uploaded_by = auth()->user()->id;
                  $slider->file_path = $file_path;
                  $slider->status = $request->input('status');
                  $slider->save(); //
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
    * @param  \App\Models\Slider $slider
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Slider $slider)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('slider-delete');
         if ($haspermision) {
            $slider->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

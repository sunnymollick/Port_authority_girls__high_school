<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Sommelon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use PDF;

class SommelonController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $data = Sommelon::orderBy('id', 'desc')->first();
      $serial = 1001 + ($data->id ? $data->id : 0);
      return View::make('frontend.sommelon.sommelon_form', compact('serial'));
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {
      //
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

         $rules = [
           'std_name' => 'required',
           'std_father_name' => 'required',
           'std_mother_name' => 'required',
           'mobile' => 'required'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {

            if ($request->hasFile('photo')) {
               if ($request->file('photo')->isValid()) {
                  $destinationPath = public_path('assets/images/sommelon');
                  $extension = $request->file('photo')->getClientOriginalExtension();
                  $fileName = time() . '.' . $extension;
                  $photo = 'assets/images/sommelon/' . $fileName;
                  $request->file('photo')->move($destinationPath, $fileName);
               } else {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Please! File is not valid</div>"
                  ]);
               }
            }

            $sommelon = new Sommelon();
            $sommelon->sl = $request->input('sl');
            $sommelon->reg_no = $request->input('reg_no');
            $sommelon->reg_date = $request->input('reg_date');
            $sommelon->ssc_batch = $request->input('ssc_batch');
            $sommelon->std_name = $request->input('std_name');
            $sommelon->std_father_name = $request->input('std_father_name');
            $sommelon->std_mother_name = $request->input('std_mother_name');
            $sommelon->std_dob = $request->input('std_dob');
            $sommelon->blood_group = $request->input('blood_group');
            $sommelon->prs_address = $request->input('prs_address');
            $sommelon->prm_address = $request->input('prm_address');
            $sommelon->mobile = $request->input('mobile');
            $sommelon->email = $request->input('email');
            $sommelon->education = $request->input('education');
            $sommelon->session = $request->input('session');
            $sommelon->university_name = $request->input('university_name');
            $sommelon->profession = $request->input('profession');
            $sommelon->designation_work_place = $request->input('designation_work_place');
            $sommelon->bikash_trans_id = $request->input('bikash_trans_id');
            $sommelon->bikash_trans_date = $request->input('bikash_trans_date');
            $sommelon->running_year = config('running_session');
            $sommelon->file_path = $photo;
            $sommelon->save();
            return response()->json(['type' => 'success',
              'message' => "<div class='alert alert-success' style='color: #fff'>Successfully Registered.You must have to print this registration copy and submitted to authority. <a class='btn btn-danger' href='/alumniPrint/$sommelon->id'>Download</a></div>"]);
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function alumniPrint($id)
   {
      $data = Sommelon::where('id', $id)->first();
      $view = View::make('frontend.sommelon.sommelonPrint', compact('data'));

      $html = '<!DOCTYPE html><html lang="en">';
      $html .= $view->render();
      $html .= '</html>';
      $pdf = PDF::loadHTML($html);
      $sheet = $pdf->setPaper('a4', 'portrait');
      return $sheet->download('Alumni_application_' . $data->sl . '.pdf');

   }

   /**
    * Display the specified resource.
    *
    * @param  \App\Models\Sommelon $sommelon
    * @return \Illuminate\Http\Response
    */
   public function show(Sommelon $sommelon)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\Sommelon $sommelon
    * @return \Illuminate\Http\Response
    */
   public function edit(Sommelon $sommelon)
   {
      //
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  \App\Models\Sommelon $sommelon
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Sommelon $sommelon)
   {
      //
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\Sommelon $sommelon
    * @return \Illuminate\Http\Response
    */
   public function destroy(Sommelon $sommelon)
   {
      //
   }
}

<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use DB;
use View;

class DashboardController extends Controller
{
   /**
    * Create a new controller instance.
    *
    * @return void
    */
   public function __construct()
   {
      $this->middleware('auth');
   }

   /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
   public function index()
   {
      $year = config('running_session');
      $data = DB::select("SELECT 
                ( SELECT COUNT(*) FROM  enrolls WHERE year='$year') AS students,
                ( SELECT COUNT(*) FROM  parents) AS parents,
                ( SELECT COUNT(*) FROM  teachers ) AS teachers");

      return View::make('backend.admin.home', compact('data'));
   }


}

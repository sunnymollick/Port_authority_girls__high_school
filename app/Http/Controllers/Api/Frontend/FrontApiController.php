<?php

namespace App\Http\Controllers\Api\Frontend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Api\ResponseController;
use DB;

class FrontApiController extends ResponseController
{


   public function getStudent(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $students = DB::table('enrolls')
           ->join('students', 'students.id', '=', 'enrolls.student_id')
           ->select('students.*', 'enrolls.roll')
           ->where('enrolls.class_id', $class_id)
           ->where('enrolls.section_id', $section_id)
           ->where('enrolls.year', config('running_session'))->get();

         if ($students) {
            return $this->sendResponse($students, 'success');
         } else {
            return $this->sendError('No records have found');
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


}

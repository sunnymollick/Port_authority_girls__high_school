<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\AccountsExceptionalStudent;
use App\Http\Controllers\Controller;
use App\Models\AccountsHead;
use App\Models\StdClass;
use App\Models\StdParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;


class AccountsExceptionalStudentController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $stdclass = StdClass::all();
      $accountsHead = AccountsHead::where('status', 1)->get();
      return view('backend.admin.accounts.exceptional_student.index', compact('stdclass', 'accountsHead'));
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('accounts-exceptional-student-fee-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $accountsHead = AccountsHead::where('status', 1)->get();
            $view = View::make('backend.admin.accounts.exceptional_student.create', compact('stdclass', 'accountsHead'))->render();
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
         $haspermision = auth()->user()->can('accounts-exceptional-student-fee-create');
         if ($haspermision) {

            $rules = [
              'month' => 'required',
              'class_id' => 'required',
              'section_id' => 'required',
              'student_id' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $class_id = $request->input('class_id');
               $section_id = $request->input('section_id');
               $student_id = $request->input('student_id');
               $month = $request->input('month');
               $accounts_head_id = $request->input('accounts_head_id');
               $year = config('running_session');


               $rows = DB::table('accounts_exceptional_students')
                 ->where('class_id', $class_id)
                 ->where('section_id', $section_id)
                 ->where('accounts_head_id', $accounts_head_id)
                 ->where('month', $month)
                 ->where('year', $year)
                 ->count();
               if ($rows == 0) {

                  $accountsExceptionalStudent = new AccountsExceptionalStudent();
                  $accountsExceptionalStudent->student_id = $student_id;
                  $accountsExceptionalStudent->class_id = $class_id;
                  $accountsExceptionalStudent->section_id = $section_id;
                  $accountsExceptionalStudent->accounts_head_id = $accounts_head_id;
                  $accountsExceptionalStudent->amount = $request->input('amount');
                  $accountsExceptionalStudent->month = $month;
                  $accountsExceptionalStudent->year = $year;
                  $accountsExceptionalStudent->status = 1;
                  $accountsExceptionalStudent->save();

                  return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Created</div>"]);


               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-danger'>Accounts fee rules already exist in same month</div>"]);

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
    * @param  \App\Models\AccountsExceptionalStudent $accountsExceptionalStudent
    * @return \Illuminate\Http\Response
    */
   public function show(AccountsExceptionalStudent $accountsExceptionalStudent)
   {
      //
   }


   public function getExceptionalStudentfeeDetails(Request $request)
   {

      $class_id = $request->input('class_id');
      $section_id = $request->input('section_id');
      $student_id = $request->input('student_id');
      $month = $request->input('month');

      if ($month != '') {
         $data = DB::table('accounts_exceptional_students as ex')
           ->join('students', 'students.std_code', '=', 'ex.student_id')
           ->join('std_classes', 'std_classes.id', '=', 'ex.class_id')
           ->join('sections', 'sections.id', '=', 'ex.section_id')
           ->join('accounts_heads', 'accounts_heads.id', '=', 'ex.accounts_head_id')
           ->select('ex.*', 'students.std_code', 'students.name', 'std_classes.name as class_name', 'sections.name as section', 'accounts_heads.name as accounts_head')
           ->where('ex.class_id', $class_id)
           ->where('ex.section_id', $section_id)
           ->where('ex.student_id', $student_id)
           ->where('ex.month', $month)
           ->where('ex.year', config('running_session'))->get();
      } else {
         $data = DB::table('accounts_exceptional_students as ex')
           ->join('students', 'students.std_code', '=', 'ex.student_id')
           ->join('std_classes', 'std_classes.id', '=', 'ex.class_id')
           ->join('sections', 'sections.id', '=', 'ex.section_id')
           ->join('accounts_heads', 'accounts_heads.id', '=', 'ex.accounts_head_id')
           ->select('ex.*', 'students.std_code', 'students.name', 'std_classes.name as class_name', 'sections.name as section', 'accounts_heads.name as accounts_head')
           ->where('ex.class_id', $class_id)
           ->where('ex.section_id', $section_id)
           ->where('ex.student_id', $student_id)
           ->where('ex.year', config('running_session'))->get();
      }
      return Datatables::of($data)
        ->addColumn('status', function ($data) {
           return $data->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('month', function ($data) {
           $monthName = date("F", mktime(0, 0, 0, $data->month, 10));
           return $monthName;
        })
        ->addColumn('action', function ($data) {
           $html = '<div class="btn-group">';
           if (auth()->user()->can('accounts-exceptional-student-fee-delete')) {
              $html .= '<a data-toggle="tooltip" id="' . $data->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           }

           $html .= '</div>';

           return $html;
        })
        ->addIndexColumn()
        ->rawColumns(['status', 'action'])
        ->make(true);
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\AccountsExceptionalStudent $accountsExceptionalStudent
    * @return \Illuminate\Http\Response
    */
   public function edit(AccountsExceptionalStudent $accountsExceptionalStudent)
   {
      //
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  \App\Models\AccountsExceptionalStudent $accountsExceptionalStudent
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, AccountsExceptionalStudent $accountsExceptionalStudent)
   {
      //
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\AccountsExceptionalStudent $accountsExceptionalStudent
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, AccountsExceptionalStudent $accountsExceptionalStudent)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('accounts-exceptional-student-fee-delete');
         if ($haspermision) {
            $accountsExceptionalStudent->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

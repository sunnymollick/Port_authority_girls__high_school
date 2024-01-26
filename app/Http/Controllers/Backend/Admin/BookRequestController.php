<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Enroll;
use App\Models\StdClass;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

use View;
use DB;

class BookRequestController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.library.book_request.all');
   }

   public function allRequests(Request $request)
   {
      $can_edit = $can_delete = '';
      if (!auth()->user()->can('book-issue-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('book-issue-delete')) {
         $can_delete = "style='display:none;'";
      }


      // return date('F d Y', strtotime($bookrequest->issue_start_date));
      $bookrequests = '';
      $term = $request->input('reports_term');
      DB::statement(DB::raw('set @rownum=0'));
      if ($term === 'all_issued') {
         $bookrequests = BookRequest::with('book')->where('status', '=', 0)->orderBy('issue_start_date', 'desc')->get(['book_requests.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      }
      if ($term === 'all_returned') {
         $bookrequests = BookRequest::with('book')->where('status', '=', 1)->orderBy('issue_start_date', 'desc')->get(['book_requests.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      }
      if ($term === 'last_week') {
         $bookrequests = BookRequest::with('book')->where('issue_start_date', ">", DB::raw('NOW() - INTERVAL 1 WEEK'))->where('status', '=', 0)->get(['book_requests.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      }

      if ($term === 'this_month') {
         $currentMonth = date('m');
         $bookrequests = BookRequest::with('book')->whereRaw('MONTH(issue_start_date) = ?', [$currentMonth])->get(['book_requests.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      }
      if ($term === 'last_month') {
         $bookrequests = BookRequest::with('book')->whereMonth('issue_start_date', '=', Carbon::now()->subMonth()->month)->get(['book_requests.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      }

      return Datatables::of($bookrequests)
        ->addColumn('status', function ($bookrequest) {
           return $bookrequest->status ? '<span class="label label-success">Returned</span>' : '<span class="label label-danger">Issued</span>';
        })
        ->addColumn('book_id', function ($bookrequest) {
           $book = $bookrequest->book;
           return $book ? $book->name : '';
        })
        ->addColumn('student_code', function ($bookrequest) {
           return $bookrequest->student_code;
        })
        ->addColumn('action', function ($bookrequests) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $bookrequests->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $bookrequests->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action', 'status'])
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
         $haspermision = auth()->user()->can('bookrequest-create');
         if ($haspermision) {
            $books = Book::all();
            $view = View::make('backend.admin.library.book_request.create', compact('books'))->render();
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
         $haspermision = auth()->user()->can('bookrequest-create');
         if ($haspermision) {

            $rules = [
              'book_id' => 'required',
              'student_code' => 'required',
              'issue_start_date' => 'required',
              'issue_end_date' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $name = $request->input('name');
               $rows = DB::table('book_requests')
                 ->where('book_id', $request->input('book_id'))
                 ->where('student_code', $request->input('student_code'))
                 ->where('status', 0)
                 ->count();
               if ($rows == 0) {
                  $bookrequest = new BookRequest();
                  $bookrequest->book_id = $request->input('book_id');
                  $bookrequest->student_code = $request->input('student_code');
                  $bookrequest->issue_start_date = $request->input('issue_start_date');
                  $bookrequest->issue_end_date = $request->input('issue_end_date');
                  $bookrequest->year = config('running_session');
                  $bookrequest->save();
                  return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> This Book $name  already issued for same student</div>"]);

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
    * @param  \App\Models\BookRequest $bookrequest
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, BookRequest $bookrequest)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('bookrequest-view');
         if ($haspermision) {
            $student = Student::where('std_code', $bookrequest->student_code)->first();
            if ($student) {
               $enroll = Enroll::where('student_id', $student->id)->where('year', $bookrequest->year)->first();
            }
            $view = View::make('backend.admin.library.book_request.view', compact('bookrequest', 'student', 'enroll'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\BookRequest $bookrequest
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, BookRequest $bookrequest)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('bookrequest-edit');
         if ($haspermision) {
            $books = Book::all();
            $view = View::make('backend.admin.library.book_request.edit', compact('books', 'bookrequest'))->render();
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
    * @param  \App\Models\BookRequest $bookrequest
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, BookRequest $bookrequest)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('bookrequest-create');
         if ($haspermision) {

            BookRequest::findOrFail($bookrequest->id);


            $rules = [
              'book_id' => 'required',
              'student_code' => 'required',
              'issue_start_date' => 'required',
              'issue_end_date' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $name = $request->input('name');
               $rows = DB::table('book_requests')
                 ->where('book_id', $request->input('book_id'))
                 ->where('student_code', $request->input('student_code'))
                 ->where('status', 0)
                 ->whereNotIn('id', [$bookrequest->id])
                 ->count();
               if ($rows == 0) {

                  $bookrequest->book_id = $request->input('book_id');
                  $bookrequest->student_code = $request->input('student_code');
                  $bookrequest->issue_start_date = $request->input('issue_start_date');
                  $bookrequest->issue_end_date = $request->input('issue_end_date');
                  $bookrequest->returned_date = $request->input('returned_date');
                  $bookrequest->status = $request->input('status');
                  $bookrequest->save(); //
                  return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);

               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> This Book $name  already issued for same student</div>"]);

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
    * @param  \App\Models\BookRequest $bookrequest
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, BookRequest $bookrequest)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('bookrequest-delete');
         if ($haspermision) {
            $bookrequest->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

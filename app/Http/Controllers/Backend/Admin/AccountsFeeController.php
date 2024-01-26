<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\AccountsFee;
use App\Http\Controllers\Controller;
use App\Models\AccountsFeeItems;
use App\Models\AccountsHead;
use App\Models\Section;
use App\Models\StdClass;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Helper\Accounts;
use Illuminate\Support\Facades\File;

use View;
use DB;
use PDF;

class AccountsFeeController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

   public function index()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.accounts.accounts_fees.index', compact('stdclass'));
   }

   public function allAccountsFees()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('accounts-fee-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('accounts-fee-delete')) {
         $can_delete = "style='display:none;'";
      }

      $year = config('running_session');

      $accountsFee = AccountsFee::with('stdclass')->where('year', $year)->orderBy('id', 'desc')->get();
      return Datatables::of($accountsFee)
        ->addColumn('status', function ($accountsFee) {
           return $accountsFee->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('class', function ($accountsFee) {
           $class = $accountsFee->stdclass;
           return $class ? $class->name : '';
        })
        ->addColumn('month', function ($accountsFee) {
           $monthName = date("F", mktime(0, 0, 0, $accountsFee->month, 10));
           return $monthName;
        })
        ->addColumn('action', function ($accountsFee) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           // $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $accountsFee->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $accountsFee->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
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
         $haspermision = auth()->user()->can('accounts-fee-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $accountsHead = AccountsHead::where('status', 1)->get();
            $view = View::make('backend.admin.accounts.accounts_fees.create', compact('stdclass', 'accountsHead'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getAllSection(Request $request, $class_id)
   {
      if ($request->ajax()) {

         $class = StdClass::findOrFail($class_id);
         $sections = $class->sections;
         if ($sections) {
            echo "<option value='' selected disabled> Select a section</option>";
            echo "<option value='all'> All </option>";
            foreach ($sections as $section) {
               echo "<option  value='$section->id'> $section->name</option>";
            }
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
         $haspermision = auth()->user()->can('accounts-fee-create');
         if ($haspermision) {

            $rules = [
              'title' => 'required',
              'class_id' => 'required',
              'section_id' => 'required',
              'month' => 'required'
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
               $accounts_head_id = explode(",", $request->input('accounts_head_id'));
               $year = config('running_session');
               $month = $request->input('month');

               $rows = DB::table('accounts_fees')
                 ->where('class_id', $class_id)
                 ->where('month', $month)
                 ->where('year', $year)
                 ->count();
               if ($rows == 0) {

                  DB::beginTransaction();
                  try {

                     $accountsFee = new AccountsFee();
                     $accountsFee->title = $request->input('title');
                     $accountsFee->class_id = $class_id;
                     $accountsFee->month = $month;
                     $accountsFee->status = 1;
                     $accountsFee->year = $year;
                     $accountsFee->save(); //

                     if ($accountsFee->id) {

                        $accountsfee_id = $accountsFee->id;

                        $total_fee_items = count($accounts_head_id);

                        $sections = Section::where('class_id', $class_id)->get();

                        for ($i = 0; $i < $total_fee_items; $i++) {

                           $bulk_data = [];

                           if ($request->input('section_id') == 'all') {

                              foreach ($sections as $section) {
                                 $bulk_data[] = [
                                   "fee_master_id" => $accountsfee_id,
                                   "class_id" => $class_id,
                                   "section_id" => $section->id,
                                   "accounts_head_id" => $accounts_head_id[$i],
                                   "amount" => $request->input('amount_' . $accounts_head_id[$i]),
                                   "month" => $month,
                                   "year" => $year,
                                   "created_at" => Carbon::now(),
                                   "updated_at" => Carbon::now(),
                                 ];
                              }

                              DB::table('accounts_fee_items')->insert($bulk_data);

                           } else {

                              $accountsFeeItems = new AccountsFeeItems();
                              $accountsFeeItems->fee_master_id = $accountsfee_id;
                              $accountsFeeItems->class_id = $class_id;
                              $accountsFeeItems->section_id = $section_id;
                              $accountsFeeItems->accounts_head_id = $accounts_head_id[$i];
                              $accountsFeeItems->amount = $request->input('amount_' . $accounts_head_id[$i]);
                              $accountsFeeItems->month = $month;
                              $accountsFeeItems->year = $year;
                              $accountsFeeItems->save();
                           }

                        }


                     }

                     DB::commit();
                     return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

                  } catch (\Exception $e) {
                     DB::rollback();
                     return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
                  }

               } else {
                  return response()->json(['type' => 'error', 'message' => "Accounts fee rules already exist in the same month"]);

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
    * @param  \App\Models\AccountsFee $accountsFee
    * @return \Illuminate\Http\Response
    */
   public function show(AccountsFee $accountsFee)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\AccountsFee $accountsFee
    * @return \Illuminate\Http\Response
    */
   public function edit(AccountsFee $accountsFee)
   {
      //
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request $request
    * @param  \App\Models\AccountsFee $accountsFee
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, AccountsFee $accountsFee)
   {
      //
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\AccountsFee $accountsFee
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, AccountsFee $accountsFee)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('accounts-fee-delete');
         if ($haspermision) {
            $accountsFee->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function printFeePdf()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.accounts.accounts_print_pdf.index', compact('stdclass'));
   }

   public function generateFeePdf(Request $request)
   {

      $class_name = $request->input('class_name');
      $section_name = $request->input('section_name');


      $class_id = $request->input('class_id');
      $section_id = $request->input('section_id');

      $months = $request->input('months');
      $student_id = $request->input('student_id');

      $year = config('running_session');


      if ($months == 'all') {
         $months = 'Null';
      }
      if ($student_id == '') {
         $student_id = 'Null';
      }

      // dd('de');


      $data = Accounts::printfeeBook($class_id, $section_id, $months, $student_id, $year);


      if ($data) {

         foreach ($data as $element) {
            $students[$element->std_code][] = $element;
         }


         $path = public_path('assets/fee_books/' . $year . '/' . $class_name . '/' . $section_name . '/');
         if (!File::isDirectory($path)) {
            File::makeDirectory(trim($path), 0777, true, true);
         }


         foreach ($students as $std) {

            foreach ($std as $monthly) {
               $allmonths[$monthly->month][] = $monthly;
            }


            $html = '<!DOCTYPE html><html lang="en">';
            //  dd($allmonths);
            foreach ($allmonths as $monthly) {
               $view = view('backend.admin.accounts.accounts_print_pdf.printFeeBook', compact('monthly'));
               $html .= $view->render();
            }

            unset($allmonths);
            $html .= '</html>';

            $pdf = PDF::loadHTML($html);
            $sheet = $pdf->setPaper('a4', 'landscape');
            $sheet->save($path . 'fee_book_' . $class_name . '_' . $section_name . '_' . $monthly[0]->std_code . '.pdf');
         }

         return response()->json(['type' => 'success', 'message' => "Success"]);

      } else {
         return response()->json(['type' => 'error', 'message' => "No data found"]);
      }

   }

   public function downloadFeeZipped($class_name, $section_name)
   {
      $year = config('running_session');
      $path = public_path('assets/fee_books/' . $year . '/' . $class_name . '/' . $section_name . '/');

      $zip_file = 'fee_books_' . $class_name . '_' . $section_name . '.zip';
      $zip = new \ZipArchive();
      $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

      $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
      foreach ($files as $name => $file) {
         // We're skipping all subfolders
         if (!$file->isDir()) {

            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($path));
            $zip->addFile($filePath, $relativePath);
         }
      }
      $zip->close();

      return response()->download($zip_file)->deleteFileAfterSend(true);

   }

   public function deleteFeeBookFile()
   {
      $path = public_path('assets/fee_books/');
      $delete_file = new Filesystem;
      $delete_file->cleanDirectory($path);
      if ($delete_file) {
         return true;
      }
   }

}

<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\Accounts;
use App\Helper\PaymentHistory;
use App\Models\AccountsFeeBarcode;
use App\Models\AccountsPayment;
use App\Models\StdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use Excel;
use View;
use DB;
use PDF;


class AccountsPaymentController extends Controller
{

   public function studentPayment()
   {
      return view('backend.admin.accounts.accounts_fee_payment.add_payment');
   }

   public function accountsFeeDetails(Request $request)
   {

      $barcode = $request->input('barcode');

      $student_id = substr($barcode, 0, -4);
      $month = substr(substr($barcode, -4), 0, -2);
      $year = config('running_session');

      // dd($student_id, $month, $barcode);
      $data = Accounts::searchFeeDetailsBarode($barcode, $student_id, $month, $year);

      // dd($data);

      if ($data) {
         $view = View::make('backend.admin.accounts.accounts_fee_payment.accounts_payment_summery', compact('data'))->render();
         return response()->json(['html' => $view]);
      } else {
         echo 'false';
      }

   }

   public function confirmFeePayment(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('accounts-fee-payment-create');
         if ($haspermision) {

            $rules = [
              'barcode_data' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'message' => "Sorry! No data have sent"
               ]);
            } else {

               $year = config('running_session');


               DB::beginTransaction();
               try {

                  if ($request->input('barcode_data')) {
                     $barcode_data = array_filter($request->input('barcode_data'));
                     $total_item = count($barcode_data);
                  }

                  if ($total_item > 0) {

                     $bulk_data = [];
                     $bulk_barcode = [];

                     for ($i = 0; $i < $total_item; $i++) {

                        $bulk_barcode[] = [
                          "barcode" => $barcode_data[$i]
                        ];

                        $bulk_data[] = [
                          "student_id" => $request->input('std_code_' . $barcode_data[$i]),
                          "class_id" => $request->input('class_id_' . $barcode_data[$i]),
                          "section_id" => $request->input('section_id_' . $barcode_data[$i]),
                          "student_roll" => $request->input('roll_' . $barcode_data[$i]),
                          "barcode" => $barcode_data[$i],
                          "amount" => $request->input('total_amount_' . $barcode_data[$i]),
                          "fee_month" => $request->input('month_' . $barcode_data[$i]),
                          "payment_date" => $request->input('payment_date'),
                          "year" => $year,
                          "created_at" => Carbon::now(),
                          "updated_at" => Carbon::now(),
                        ];
                     }

                     $delete = DB::table('accounts_payments')->whereIn('barcode', $bulk_barcode)->delete();
                     if ($delete) {
                        DB::table('accounts_payments')->insert($bulk_data);
                     }

                  }

                  DB::commit();
                  return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

               } catch (\Exception $e) {
                  DB::rollback();
                  return response()->json(['type' => 'error', 'message' => "Insert Failed"]);
               }


            }
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function accountsFeePaymentHistory()
   {
      $stdclass = StdClass::all();
      return view('backend.admin.accounts.accounts_fee_payment.index', compact('stdclass'));
   }

   public function accountsFeePaymentHistoryReports(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $month = $request->input('month');
         $year = config('running_session');

         $students = Accounts::stdFeePaymentHistoryReports($class_id, $section_id, $month, $year);
         return Datatables::of($students)
           ->addColumn('status', function ($student) {
              return $student->status == 'Paid' ? '<span class="label label-success">Paid</span>' : '<span class="label label-danger">Not Paid</span>';
           })
           ->rawColumns(['status'])
           ->addIndexColumn()
           ->make(true);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function exportExcelPaymentHistory($class_id, $section_id, $month)
   {

      $year = config('running_session');

      $data = Accounts::stdFeePaymentHistoryReports($class_id, $section_id, $month, $year);

      $monthName = date("F", mktime(0, 0, 0, $month, 10));
      $payload = array();
      $class = null;
      $section = null;
      $month = null;
      if (count($data) > 0) {
         foreach ($data as $key => $value) {

            $class = $value->class_name;
            $section = $value->section_name;

            $payload[] = array(
              'std_id' => $value->std_code,
              'name' => $value->name,
              'class' => $value->class_name,
              'section' => $value->section_name,
              'roll' => $value->roll,
              'month' => $monthName,
              'barcode' => $value->barcode,
              'amount' => $value->amount,
              'status' => $value->status
            );
         }

      }

      return Excel::create('Payment_' . $class . '_' . $section . '_' . $monthName, function ($excel) use ($payload) {
         $excel->sheet('payment_report', function ($sheet) use ($payload) {
            $sheet->fromArray($payload);
         });
      })->download('xls');

   }

   public function exportPdfPaymentHistory($class_id, $section_id, $month)
   {

      $year = config('running_session');

      $data = Accounts::stdFeePaymentHistoryReports($class_id, $section_id, $month, $year);

      $monthName = date("F", mktime(0, 0, 0, $month, 10));

      $class = $section = $month = "";

      if (count($data) > 0) {
         $class = $data[0]->class_name;
         $section = $data[0]->section_name;
         $view = view('backend.admin.accounts.accounts_fee_payment.export_pdf', compact('data', 'monthName'));
         $html = $view->render();
      } else {
         $html = "<html><body><p> Sorry!! no records have found</p></body></html>";
      }


      $pdf = PDF::loadHTML($html);
      $sheet = $pdf->setPaper('a4', 'portrait');
      return $sheet->download('Accounts_fee_' . $class . '_' . $section . '_' . $monthName . '.pdf');

   }

}

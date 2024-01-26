<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Helper\Accounts;
use App\Models\AccountsHeadCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AccountsFeeBarcode;
use App\Models\StdClass;

use Excel;
use View;
use DB;
use PDF;

class AccountsStatementController extends Controller
{

   public function incomeStatement()
   {
      $stdclass = StdClass::all();
      $category_heads = AccountsHeadCategory::where('category_type', 'Incomes')->where('status', 1)->get();
      return view('backend.admin.accounts.reports.statement.income_statement', compact('stdclass', 'category_heads'));
   }

   public function expenseStatement()
   {
      $category_heads = AccountsHeadCategory::where('category_type', 'Expenses')->where('status', 1)->get();
      return view('backend.admin.accounts.reports.statement.expense_statement', compact('category_heads'));
   }

   public function studentFeeIncomeStatementReports(Request $request)
   {
      if ($request->ajax()) {

         $income_type = $request->input('income_type');
         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $from_date = $request->input('from_date');
         $to_date = $request->input('to_date');
         $report_format = $request->input('report_format');

         if ($income_type === 'student_fee') {

            if ($class_id == 'all') {
               $class_id = 'null';
            }
            if ($section_id == 'all') {
               $section_id = 'null';
            }


            if ($report_format === 'Summary') {
               $data = Accounts::studentFeeIncomeStatementSummeryReports($class_id, $section_id, $from_date, $to_date);
            }

            if ($report_format === 'Details') {
               $data = Accounts::studentFeeIncomeStatementDetailsReports($class_id, $section_id, $from_date, $to_date);
            }

            $view = View::make('backend.admin.accounts.reports.statement.student_fee_reports', compact('data', 'report_format', 'from_date', 'to_date'))->render();
            return response()->json(['html' => $view]);
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function allIncomeStatementReports(Request $request)
   {
      if ($request->ajax()) {

         $income_type = $request->input('income_type');
         $from_date = $request->input('from_date');
         $to_date = $request->input('to_date');
         $report_format = $request->input('report_format');
         $category_type = $request->input('category_type');


         if ($income_type === 'all') {

            if ($report_format === 'Summary') {
               $data = Accounts::allIncomeStatementSummaryReports($category_type, $from_date, $to_date);
            }

            $view = View::make('backend.admin.accounts.reports.statement.accounts_category_reports', compact('data', 'report_format', 'from_date', 'to_date', 'category_type'))->render();
            return response()->json(['html' => $view]);
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function accountsCategoryStatementReports(Request $request)
   {
      if ($request->ajax()) {

         $from_date = $request->input('from_date');
         $to_date = $request->input('to_date');
         $income_cat_id = $request->input('income_cat_id');
         $report_format = $request->input('report_format');
         $category_type = $request->input('category_type');

         if ($income_cat_id == 'all') {
            $income_cat_id = 'null';
         }

         if ($report_format === 'Summary') {
            $data = Accounts::accountsCategoryStatementSummaryReports($category_type, $income_cat_id, $from_date, $to_date);
         }

         if ($report_format === 'Details') {
            $data = Accounts::accountsCategoryStatementDetailsReports($category_type, $income_cat_id, $from_date, $to_date);
         }

         $view = View::make('backend.admin.accounts.reports.statement.accounts_category_reports', compact('data', 'report_format', 'from_date', 'to_date', 'category_type'))->render();
         return response()->json(['html' => $view]);


      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


}

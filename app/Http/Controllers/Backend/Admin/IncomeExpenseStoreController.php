<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\AccountsHeadCategory;
use App\Models\AccountsHeadCategoryItem;
use App\Models\BankAccountSetup;
use App\Models\BankList;
use App\Models\IncomeExpenseStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;

class IncomeExpenseStoreController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

   public function allIncomes()
   {
      return view('backend.admin.accounts.income_expense_store.income_history');
   }


   public function allIncomesHistory()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('incomes-expense-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('incomes-expense-delete')) {
         $can_delete = "style='display:none;'";
      }

      $year = config('running_session');
      $incomes = DB::table('income_expense_stores as ies')
        ->leftJoin('accounts_head_categories as ahc', 'ahc.id', '=', 'ies.category_id')
        ->leftJoin('accounts_head_category_items as ahci', 'ahci.id', '=', 'ies.item_id')
        ->select('ies.*', 'ahc.category_name', 'ahci.category_item_name')
        ->where('ies.store_type', 'Incomes')
        ->where('ies.store_year', $year)
        ->orderBy('ies.id', 'desc')
        ->get();
      return Datatables::of($incomes)
        ->addColumn('action', function ($incomes) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . '  id="' . $incomes->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->addIndexColumn()
        ->rawColumns(['status', 'action'])
        ->make(true);
   }


   public function allExpenses()
   {
      return view('backend.admin.accounts.income_expense_store.expense_history');
   }


   public function allExpensesHistory()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('incomes-expense-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('incomes-expense-delete')) {
         $can_delete = "style='display:none;'";
      }

      $year = config('running_session');
      $expenses = DB::table('income_expense_stores as ies')
        ->leftJoin('accounts_head_categories as ahc', 'ahc.id', '=', 'ies.category_id')
        ->leftJoin('accounts_head_category_items as ahci', 'ahci.id', '=', 'ies.item_id')
        ->select('ies.*', 'ahc.category_name', 'ahci.category_item_name')
        ->where('ies.store_type', 'Expenses')
        ->where('ies.store_year', $year)
        ->orderBy('ies.id', 'desc')
        ->get();
      return Datatables::of($expenses)
        ->addColumn('action', function ($expenses) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $expenses->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->addIndexColumn()
        ->rawColumns(['status', 'action'])
        ->make(true);
   }

   public function getAllStoreCategory(Request $request, $category_type)
   {
      if ($request->ajax()) {

         $category = AccountsHeadCategory::where('category_type', $category_type)->where('status', 1)->get();
         if ($category) {
            echo "<option value='' selected disabled> Select Category</option>";
            foreach ($category as $value) {
               echo "<option  value='$value->id'>$value->category_name</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getAllStoreCategoryItems(Request $request, $category_id)
   {
      if ($request->ajax()) {

         $items = AccountsHeadCategoryItem::where('category_id', $category_id)->get();
         if ($items) {
            echo "<option value='' selected disabled> Select Item </option>";
            foreach ($items as $value) {
               echo "<option  value='$value->id'>$value->category_item_name</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function getBankAccountNumber(Request $request, $bank_id)
   {
      if ($request->ajax()) {

         $bankAccounts = BankAccountSetup::where('bank_id', $bank_id)->where('status', 1)->get();
         if ($bankAccounts) {
            echo "<option value='' selected disabled> Select Account Number </option>";
            foreach ($bankAccounts as $account) {
               echo "<option  value='$account->id'>$account->account_number</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */


   public function create(Request $request, $Incomes)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('incomes-expense-create');
         if ($haspermision) {

            $store = IncomeExpenseStore:: whereMonth('created_at', Carbon::now()->month)->orderBy('id', 'DESC')->first();
            if ($store) {
               $sum = 10001 + $store->id;
            }
            $voucher_id = $store ? 'SVID' . date('ym') . $sum : 'SVID' . date('ym') . '10001';

            $banks = BankList::where('status', 1)->get();
            $store_type = $Incomes;
            $category = AccountsHeadCategory::where('category_type', $store_type)->where('status', 1)->get();
            $view = View::make('backend.admin.accounts.income_expense_store.create', compact('banks', 'voucher_id', 'store_type', 'category'))->render();
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
         $haspermision = auth()->user()->can('incomes-expense-create');
         if ($haspermision) {

            $rules = [
              'store_date' => 'required',
              'store_type' => 'required',
              'store_type' => 'required',
              'item_id' => 'required',
              'amount' => 'required'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $year = config('running_session');

               $incomesExpense = new IncomeExpenseStore();
               $incomesExpense->store_date = $request->input('store_date');
               $incomesExpense->store_type = $request->input('store_type');
               $incomesExpense->store_voucher = $request->input('store_voucher');
               $incomesExpense->category_id = $request->input('category_id');
               $incomesExpense->item_id = $request->input('item_id');
               $incomesExpense->name = $request->input('name');
               $incomesExpense->address = $request->input('address');
               $incomesExpense->amount = $request->input('amount');
               $incomesExpense->comment = $request->input('comment');
               $incomesExpense->payment_method = $request->input('payment_method');
               $incomesExpense->bank_name_id = $request->input('bank_name_id');
               $incomesExpense->bank_account_id = $request->input('bank_account_id');
               $incomesExpense->check_number = $request->input('check_number');
               $incomesExpense->check_date = $request->input('check_date');
               $incomesExpense->store_year = $year;
               $incomesExpense->status = 1;
               $incomesExpense->save(); //
               return response()->json(['type' => 'success', 'message' => "Successfully Created"]);
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
    * @param  \App\Models\IncomeExpenseStore $incomesExpense
    * @return \Illuminate\Http\Response
    */
   public function show(IncomeExpenseStore $incomesExpense)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\IncomeExpenseStore $incomesExpense
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, IncomeExpenseStore $incomesExpense)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('incomes-expense-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.accounts.income_expense_store.edit', compact('incomesExpense'))->render();
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
    * @param  \App\Models\IncomeExpenseStore $incomesExpense
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, IncomeExpenseStore $incomesExpense)
   {
      //
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\IncomeExpenseStore $incomesExpense
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, IncomeExpenseStore $incomesExpense)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('incomes-expense-delete');
         if ($haspermision) {
            $incomesExpense->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

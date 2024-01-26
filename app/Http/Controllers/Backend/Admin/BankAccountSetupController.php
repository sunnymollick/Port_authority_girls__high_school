<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccountSetup;
use App\Models\BankList;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use Yajra\DataTables\DataTables;

class BankAccountSetupController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

   public function index()
   {
      return view('backend.admin.accounts.bank_accounts_setup.index');
   }

   public function allBankAccounts()
   {

      $bankAccount = BankAccountSetup::orderBy('id', 'desc')->get();
      return Datatables::of($bankAccount)
        ->addColumn('status', function ($bankAccount) {
           return $bankAccount->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('bank_name', function ($bankAccount) {
           $bankAccount = $bankAccount->bank;
           return $bankAccount ? $bankAccount->bank_name : '';
        })
        ->addColumn('action', function ($bankAccount) {
           $html = '<div class="btn-group">';
           if (auth()->user()->can('bank-accounts-edit')) {
              $html .= '<a data-toggle="tooltip" id="' . $bankAccount->id . '" class="btn btn-xs btn-success margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           }
           if (auth()->user()->can('bank-accounts-delete')) {
              $html .= '<a data-toggle="tooltip" id="' . $bankAccount->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           }

           $html .= '</div>';

           return $html;
        })
        ->addIndexColumn()
        ->rawColumns(['status', 'action'])
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
         $haspermision = auth()->user()->can('bank-accounts-create');
         if ($haspermision) {
            $banks = BankList::all();
            $view = View::make('backend.admin.accounts.bank_accounts_setup.create', compact('banks'))->render();
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
         $haspermision = auth()->user()->can('bank-accounts-create');
         if ($haspermision) {

            $rules = [
              'account_number' => 'required|unique:bank_account_setups,account_number',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $bankAccount = new BankAccountSetup();
               $bankAccount->bank_id = $request->input('bank_id');
               $bankAccount->account_name = $request->input('account_name');
               $bankAccount->account_number = $request->input('account_number');
               $bankAccount->branch_name = $request->input('branch_name');
               $bankAccount->contact_person = $request->input('contact_person');
               $bankAccount->phone = $request->input('phone');
               $bankAccount->designition = $request->input('designition');
               $bankAccount->email = $request->input('email');
               $bankAccount->status = 1;
               $bankAccount->save(); //
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
    * @param  \App\Models\BankAccountSetup $bankAccount
    * @return \Illuminate\Http\Response
    */
   public function show(BankAccountSetup $bankAccount)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\BankAccountSetup $bankAccount
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, BankAccountSetup $bankAccount)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('bank-accounts-edit');
         if ($haspermision) {
            $banks = BankList::all();
            $view = View::make('backend.admin.accounts.bank_accounts_setup.edit', compact('bankAccount', 'banks'))->render();
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
    * @param  \App\Models\BankAccountSetup $bankAccount
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, BankAccountSetup $bankAccount)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('bank-accounts-edit');
         if ($haspermision) {
            BankAccountSetup::findOrFail($bankAccount->id);
            $rules = [
              'account_number' => 'required|unique:bank_account_setups,account_number,' . $bankAccount->id
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $bankAccount->bank_id = $request->input('bank_id');
               $bankAccount->account_name = $request->input('account_name');
               $bankAccount->account_number = $request->input('account_number');
               $bankAccount->branch_name = $request->input('branch_name');
               $bankAccount->contact_person = $request->input('contact_person');
               $bankAccount->phone = $request->input('phone');
               $bankAccount->designition = $request->input('designition');
               $bankAccount->email = $request->input('email');
               $bankAccount->status = $request->input('status');
               $bankAccount->save();
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
    * Remove the specified resource from storage.
    *
    * @param  \App\Models\BankAccountSetup $bankAccount
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, BankAccountSetup $bankAccount)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('bank-accounts-delete');
         if ($haspermision) {
            $bankAccount->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

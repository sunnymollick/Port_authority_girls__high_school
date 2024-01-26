<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\AccountsHead;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;

class AccountsHeadController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

   public function index()
   {
      return view('backend.admin.accounts.accounts_head.index');
   }

   public function allAccountsHead()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('accounts-head-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('accounts-head-delete')) {
         $can_delete = "style='display:none;'";
      }

      $accountsHead = AccountsHead::orderBy('order', 'asc')->get();
      return Datatables::of($accountsHead)
        ->addColumn('status', function ($accountsHead) {
           return $accountsHead->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('action', function ($accountsHead) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $accountsHead->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $accountsHead->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
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
         $haspermision = auth()->user()->can('accounts-head-create');
         if ($haspermision) {
            $view = View::make('backend.admin.accounts.accounts_head.create')->render();
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
         $haspermision = auth()->user()->can('accounts-head-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required|unique:accounts_heads,name',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $accountsHead = new AccountsHead();
               $accountsHead->name = $request->input('name');
               $accountsHead->order = $request->input('order');
               $accountsHead->status = 1;
               $accountsHead->year = config('running_session');
               $accountsHead->save(); //
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
    * @param  \App\Models\AccountsHead $accountsHead
    * @return \Illuminate\Http\Response
    */
   public function show(AccountsHead $accountsHead)
   {
      //
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\AccountsHead $accountsHead
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, AccountsHead $accountsHead)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('accounts-head-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.accounts.accounts_head.edit', compact('accountsHead'))->render();
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
    * @param  \App\Models\AccountsHead $accountsHead
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, AccountsHead $accountsHead)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('accounts-head-edit');
         if ($haspermision) {
            AccountsHead::findOrFail($accountsHead->id);
            $rules = [
              'name' => 'required|unique:accounts_heads,name,' . $accountsHead->id
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $accountsHead->name = $request->input('name');
               $accountsHead->order = $request->input('order');
               $accountsHead->status = $request->input('status');
               $accountsHead->save(); //
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
    * @param  \App\Models\AccountsHead $accountsHead
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, AccountsHead $accountsHead)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('accounts-head-delete');
         if ($haspermision) {
            $accountsHead->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

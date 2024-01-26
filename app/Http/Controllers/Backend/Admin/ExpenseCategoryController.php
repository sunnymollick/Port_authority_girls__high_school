<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Expense_category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
   {
      return view('backend.admin.accounts.expense_category.all');
   }

   public function allExpensecategory()
   {

      DB::statement(DB::raw('set @rownum=0'));
      $expensecategory = Expense_category::orderBy('id', 'asc')->get(['expense_categories.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      return Datatables::of($expensecategory)
        ->addColumn('status', function ($expensecategory) {
           return $expensecategory->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('action', 'backend.admin.accounts.expense_category.action')
        ->rawColumns(['status','action'])
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
         $haspermision = auth()->user()->can('expense-category-create');
         if ($haspermision) {
            $view = View::make('backend.admin.accounts.expense_category.create')->render();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('expense-category-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required|unique:expense_categories,name',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $expensecategory = new Expense_category();
               $expensecategory->name = $request->input('name');
               $expensecategory->save(); //
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
     * @param  \App\Models\Expense_category  $expensecategory
     * @return \Illuminate\Http\Response
     */
    public function show(Expense_category $expensecategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expense_category  $expensecategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Expense_category $expensecategory)
    {
       if ($request->ajax()) {
          $haspermision = auth()->user()->can('expense-category-edit');
          if ($haspermision) {
             $view = View::make('backend.admin.accounts.expense_category.edit', compact('expensecategory'))->render();
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense_category  $expensecategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense_category $expensecategory)
    {
       if ($request->ajax()) {
          $haspermision = auth()->user()->can('expense-category-edit');
          if ($haspermision) {
             Expense_category::findOrFail($expensecategory->id);
             $rules = [
               'name' => 'required|unique:expense_categories,name,' . $expensecategory->id
             ];
             $validator = Validator::make($request->all(), $rules);
             if ($validator->fails()) {
                return response()->json([
                  'type' => 'error',
                  'errors' => $validator->getMessageBag()->toArray()
                ]);
             } else {
                $expensecategory->name = $request->input('name');
                $expensecategory->status = $request->input('status');
                $expensecategory->save(); //
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
     * @param  \App\Models\Expense_category  $expensecategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Expense_category $expensecategory)
    {
       if ($request->ajax()) {
          $haspermision = auth()->user()->can('expense-category-delete');
          if ($haspermision) {
             $expensecategory->delete();
             return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
          } else {
             abort(403, 'Sorry, you are not authorized to access the page');
          }
       } else {
          return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
       }
    }
}

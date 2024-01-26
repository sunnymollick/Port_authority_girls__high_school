<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Enroll;
use App\Models\Fee_item;
use App\Models\Invoice;
use App\Models\Std_Fee_Category;
use App\Http\Controllers\Controller;
use App\Models\StdClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use View;
use DB;

class StdFeeCategoryController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

   public function index()
   {
      return view('backend.admin.accounts.std_fee_category.all');
   }

   public function allFeecategory()
   {

      DB::statement(DB::raw('set @rownum=0'));
      $feecategory = Std_Fee_Category::where('year', config('running_session'))->orderBy('id', 'asc')->get(['std_fee_categories.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      return Datatables::of($feecategory)
        ->addColumn('month', function ($feecategory) {
           $monthName = date("F", mktime(0, 0, 0, $feecategory->month, 10));
           return $monthName;
        })
        ->addColumn('action', 'backend.admin.accounts.std_fee_category.action')
        ->rawColumns(['status', 'action'])
        ->make(true);
   }

   public function getFeeRoles(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $month = $request->input('month');

         $fee_category = DB::table('std_fee_categories')
           ->select('std_fee_categories.*')
           ->where('class_id', $class_id)
           ->where('month', $month)
           ->where('year', config('running_session'))->get();

         if ($fee_category) {
            echo "<option value='' selected disabled> Choose Fee Roles</option>";
            foreach ($fee_category as $category) {
               echo "<option  value='$category->id'> $category->name </option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function getFeeItems(Request $request)
   {
      if ($request->ajax()) {

         $fee_category_id = $request->input('fee_category_id');

         $fee_items = DB::table('fee_items')
           ->select('fee_items.*')
           ->where('fee_category_id', $fee_category_id)->get();

         if ($fee_items) {
            echo "<table class='table table-striped table-hover'>";
            echo "<thead><tr><th>Item's Name</th> <th>Amount</th></tr></thead>";
            foreach ($fee_items as $items) {
               echo "<tr><td>$items->item_name </td> <td>$items->amount</td> </tr>";
            }
            echo "</table>";
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

   public function create(Request $request)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('fee_category-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.accounts.std_fee_category.create', compact('stdclass'))->render();
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
         $haspermision = auth()->user()->can('fee_category-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required|unique:std_fee_categories,name',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $class_id = $request->input('class_id');
               $year = config('running_session');
               $title = $request->input('name');
               $amount = $request->input('amount');
               $month = $request->input('month');


               $rows = DB::table('std_fee_categories')
                 ->where('class_id', $class_id)
                 ->where('month', $request->input('month'))
                 ->where('year', $year)
                 ->count();
               if ($rows == 0) {
                  $feecategory = new Std_Fee_Category();
                  $feecategory->name = $title;
                  $feecategory->amount = $amount;
                  $feecategory->class_id = $class_id;
                  $feecategory->month = $month;
                  $feecategory->year = $year;
                  $feecategory->save(); //

                  if ($feecategory->id) {
                     if ($request->input('item_name')) {
                        $item_name = array_filter($request->input('item_name'));
                        $item_amount = $request->input('item_amount');
                        $total_item = count($item_name);
                     } else {
                        $total_item = 0;
                     }

                     if ($total_item > 0) {
                        for ($i = 0; $i < $total_item; $i++) {
                           $feeitems = New Fee_item();
                           $i_item_name = $item_name[$i];
                           $i_item_amount = $item_amount[$i];
                           $feeitems->item_name = $i_item_name;
                           $feeitems->amount = $i_item_amount;
                           $feeitems->fee_category_id = $feecategory->id;
                           $feeitems->save();
                        }
                     }

                     $students = DB::table('enrolls')
                       ->join('students', 'students.id', '=', 'enrolls.student_id')
                       ->select('students.*')
                       ->where('enrolls.class_id', $class_id)
                       ->where('enrolls.year', $year)->get();

                     foreach ($students as $student) {
                        //  dd($student->std_code . $class_id . rand(1000, 9999));
                        $invoice = New Invoice();
                        $invoice->std_id = $student->id;
                        $invoice->title = $title;
                        $invoice->barcode = $student->std_code . rand(100, 999);
                        $invoice->roles_id = $feecategory->id;
                        $invoice->amount = $amount;
                        $invoice->method = 'Bank';
                        $invoice->due = $amount;
                        $invoice->status = 'Not Paid';
                        $invoice->year = $year;
                        $invoice->save(); //
                     }
                  }
                  return response()->json(['type' => 'success', 'message' => "Successfully Created"]);
               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Roles already exist in same class</div>"]);

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
    * @param  \App\Models\Std_Fee_Category $std_Fee_Category
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Std_Fee_Category $feecategory)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('fee_category-view');
         if ($haspermision) {
            $view = View::make('backend.admin.accounts.std_fee_category.view', compact('feecategory'))->render();
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
    * @param  \App\Models\Std_Fee_Category $std_Fee_Category
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Std_Fee_Category $feecategory)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('fee_category-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.accounts.std_fee_category.edit', compact('feecategory', 'stdclass'))->render();
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
    * @param  \App\Models\Std_Fee_Category $std_Fee_Category
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Std_Fee_Category $feecategory)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('fee_category-create');
         if ($haspermision) {

            Std_Fee_Category::findOrFail($feecategory->id);

            $rules = [
              'name' => 'required|unique:std_fee_categories,name,' . $feecategory->id
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {

               $rows = DB::table('std_fee_categories')
                 ->where('class_id', $request->input('class_id'))
                 ->where('month', $request->input('month'))
                 ->where('year', config('running_session'))
                 ->whereNotIn('id', [$feecategory->id])
                 ->count();
               if ($rows == 0) {
                  $feecategory->name = $request->input('name');
                  $feecategory->amount = $request->input('amount');
                  $feecategory->class_id = $request->input('class_id');
                  $feecategory->month = $request->input('month');
                  $feecategory->year = config('running_session');
                  $feecategory->save(); //

                  if ($feecategory->id) {
                     if ($request->input('item_name')) {
                        $item_name = array_filter($request->input('item_name'));
                        $item_amount = array_filter($request->input('item_amount'));
                        $total_item = count($item_name);
                     } else {
                        $total_item = 0;
                     }


                     if ($total_item > 0) {

                        DB::table('fee_items')->where('fee_category_id', $feecategory->id)->delete();

                        for ($i = 0; $i < $total_item; $i++) {
                           $feeitems = New Fee_item();
                           $i_item_name = $item_name[$i];
                           $i_item_amount = $item_amount[$i];
                           $feeitems->item_name = $i_item_name;
                           $feeitems->amount = $i_item_amount;
                           $feeitems->fee_category_id = $feecategory->id;
                           $feeitems->save();
                        }

                     }

                  }
                  return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Roles already exist in same class</div>"]);

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
    * @param  \App\Models\Std_Fee_Category $std_Fee_Category
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Std_Fee_Category $feecategory)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('fee_category-delete');
         if ($haspermision) {
            $feecategory->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

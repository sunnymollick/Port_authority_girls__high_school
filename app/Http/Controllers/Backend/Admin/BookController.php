<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Book;
use App\Models\StdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

use View;
use DB;

class BookController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.library.book.all');
   }

   public function allBooks()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('book-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('book-delete')) {
         $can_delete = "style='display:none;'";
      }

      $books = Book::with('stdclass')->orderBy('id', 'asc')->get();
      return Datatables::of($books)
        ->addColumn('status', function ($book) {
           return $book->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('class_id', function ($book) {
           $class = $book->stdclass;
           return $class ? $class->name : '';
        })
        ->addColumn('issued_copies', function ($book) {
           $total_issued = $book->issued_book ? $book->issued_book()->count() : 0;
           return $total_issued;
        })
        ->addColumn('available', function ($book) {
           $total_issued = $book->issued_book ? $book->issued_book()->count() : 0;
           $available = $book->total_copies - $total_issued;
           return $available;
        })
        ->addColumn('action', function ($books) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip"  id="' . $books->id . '" class="btn btn-xs btn-success margin-r-5 view" title="View"><i class="fa fa-eye fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $books->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $books->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
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
         $haspermision = auth()->user()->can('book-create');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.library.book.create', compact('stdclass'))->render();
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
         $haspermision = auth()->user()->can('book-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required',
              'class_id' => 'required',
              'photo' => 'image|max:2024|mimes:jpeg,jpg,gif,png'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $upload_ok = 1;
               $file_path = 'assets/images/book_image/default.png';

               if ($request->hasFile('photo')) {

                  if (Input::file('photo')->isValid()) {
                     $destinationPath = 'assets/images/book_image'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = 'assets/images/book_image/' . $fileName;
                     Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                     $upload_ok = 1;

                  } else {
                     return response()->json([
                       'type' => 'error',
                       'message' => "<div class='alert alert-warning'>Please! File is not valid</div>"
                     ]);
                  }
               }


               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {

                  $name = $request->input('name');
                  $rows = DB::table('books')
                    ->where('class_id', $request->input('class_id'))
                    ->where('name', $request->input('name'))
                    ->count();
                  if ($rows == 0) {
                     $book = new Book;
                     $book->name = $request->input('name');
                     $book->author = $request->input('author');
                     $book->description = $request->input('description');
                     $book->class_id = $request->input('class_id');
                     $book->price = $request->input('price');
                     $book->total_copies = $request->input('total_copies');
                     $book->file_path = $file_path;
                     $book->save(); //
                     return response()->json(['type' => 'success', 'message' => "Successfully Created"]);

                  } else {
                     return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Book Name $name  already exist in same class</div>"]);

                  }
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
    * @param  \App\Models\Book $book
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Book $book)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('book-view');
         if ($haspermision) {
            $view = View::make('backend.admin.library.book.view', compact('book'))->render();
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
    * @param  \App\Models\Book $book
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Book $book)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('book-edit');
         if ($haspermision) {
            $stdclass = StdClass::all();
            $view = View::make('backend.admin.library.book.edit', compact('stdclass', 'book'))->render();
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
    * @param  \App\Models\Book $book
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Book $book)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('book-edit');
         if ($haspermision) {
            Book::findOrFail($book->id);
            $rules = [
              'name' => 'required',
              'class_id' => 'required',
              'photo' => 'image|max:2024|mimes:jpeg,jpg,gif,png'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               if ($request->hasFile('photo')) {
                  if (Input::file('photo')->isValid()) {
                     File::delete($book->file_path);
                     $destinationPath = 'assets/images/book_image'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = 'assets/images/book_image/' . $fileName;
                     Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                     $upload_ok = 1;
                  } else {
                     return response()->json([
                       'type' => 'error',
                       'message' => "<div class='alert alert-warning'>Please! File is not valid</div>"
                     ]);
                  }
               } else {
                  $upload_ok = 1;
                  $file_path = $request->input('SelectedFileName');
               }

               if ($upload_ok == 0) {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
                  ]);
               } else {
                  $name = $request->input('name');
                  $rows = DB::table('books')
                    ->where('class_id', $request->input('class_id'))
                    ->where('name', $request->input('name'))
                    ->whereNotIn('id', [$book->id])
                    ->count();
                  if ($rows == 0) {
                     $book->name = $request->input('name');
                     $book->author = $request->input('author');
                     $book->description = $request->input('description');
                     $book->class_id = $request->input('class_id');
                     $book->price = $request->input('price');
                     $book->total_copies = $request->input('total_copies');
                     $book->file_path = $file_path;
                     $book->status = $request->input('status');
                     $book->save(); //
                     return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
                  } else {
                     return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Book Name $name  already exist in same class</div>"]);
                  }
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
    * @param  \App\Models\Book $book
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Book $book)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('book-delete');
         if ($haspermision) {
            $book->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

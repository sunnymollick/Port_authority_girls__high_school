<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Input;

use View;
use DB;
use Calendar;

class EventController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      return view('backend.admin.event.all');
   }

   public function allEvents()
   {

      $can_edit = $can_delete = '';
      if (!auth()->user()->can('event-calender-edit')) {
         $can_edit = "style='display:none;'";
      }
      if (!auth()->user()->can('event-calender-delete')) {
         $can_delete = "style='display:none;'";
      }

      DB::statement(DB::raw('set @rownum=0'));
      $events = Event::where('year', config('running_session'))->get(['events.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
      return DataTables::of($events)
        ->addColumn('status', function ($event) {
           return $event->status ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>';
        })
        ->addColumn('action', function ($events) use ($can_edit, $can_delete) {
           $html = '<div class="btn-group">';
           $html .= '<a data-toggle="tooltip"   id="' . $events->id . '" class="btn btn-xs btn-success margin-r-5 view" title="View"><i class="fa fa-eye fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_edit . '  id="' . $events->id . '" class="btn btn-xs btn-primary margin-r-5 edit" title="Edit"><i class="fa fa-edit fa-fw"></i> </a>';
           $html .= '<a data-toggle="tooltip" ' . $can_delete . ' id="' . $events->id . '" class="btn btn-xs btn-danger margin-r-5 delete" title="Delete"><i class="fa fa-trash-o fa-fw"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action','status'])
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
         $haspermision = auth()->user()->can('event-create');
         if ($haspermision) {
            $view = View::make('backend.admin.event.create')->render();
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
         $haspermision = auth()->user()->can('event-create');
         if ($haspermision) {

            $rules = [
              'name' => 'required',
              'start_date' => 'required',
              'end_date' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $event = new Event;
               $event->name = $request->input('name');
               $event->location = $request->input('location');
               $event->details = $request->input('details');
               $event->start_date = $request->input('start_date');
               $event->end_date = $request->input('end_date');
               $event->year = config('running_session');
               $event->save(); //
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
    * @param  \App\Models\Event $event
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request, Event $event)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('event-view');
         if ($haspermision) {
            $view = View::make('backend.admin.event.view', compact('event'))->render();
            return response()->json(['html' => $view]);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function viewCalender()
   {
      $haspermision = auth()->user()->can('event-view');
      if ($haspermision) {
         $events = Event::get();
         return view('backend.admin.event.calender', compact('events'));
      } else {
         abort(403, 'Sorry, you are not authorized to access the page');
      }

   }


   /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Models\Event $event
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, Event $event)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('event-edit');
         if ($haspermision) {
            $view = View::make('backend.admin.event.edit', compact('event'))->render();
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
    * @param  \App\Models\Event $event
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, Event $event)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('event-edit');
         if ($haspermision) {

            Event::findOrFail($event->id);

            $rules = [
              'name' => 'required',
              'start_date' => 'required',
              'end_date' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
               return response()->json([
                 'type' => 'error',
                 'errors' => $validator->getMessageBag()->toArray()
               ]);
            } else {
               $event->name = $request->input('name');
               $event->location = $request->input('location');
               $event->details = $request->input('details');
               $event->start_date = $request->input('start_date');
               $event->end_date = $request->input('end_date');
               $event->status = $request->input('status');
               $event->save(); //
               return response()->json(['type' => 'success', 'message' => "Successfully Updated"]);
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
    * @param  \App\Models\Event $event
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, Event $event)
   {
      if ($request->ajax()) {
         $haspermision = auth()->user()->can('event-delete');
         if ($haspermision) {
            $event->delete();
            return response()->json(['type' => 'success', 'message' => 'Successfully Deleted']);
         } else {
            abort(403, 'Sorry, you are not authorized to access the page');
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }
}

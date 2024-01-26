<?php

namespace App\Http\Controllers\Frontend;

use App\Helper\Academic;
use App\Http\Controllers\Controller;
use App\Models\AcademicCalender;
use App\Models\AdmissionApplication;
use App\Models\AdmissionResult;
use App\Models\Download;
use App\Models\Enroll;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Slider;
use App\Models\StdClass;
use App\Models\Syllabus;
use App\Models\Teacher;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use View;
use Yajra\DataTables\DataTables;
use App\Models\OnlineApplicant;
use PDF;

class HomeController extends Controller
{

   // Home
   public function index()
   {
      return redirect()->route('login');
      $teacher = Teacher::count();
      $students = Enroll::where('year', config('running_session'))->count();
      $birthday = DB::table('students')
        ->leftJoin('enrolls', 'enrolls.student_id', '=', 'students.id')
        ->leftJoin('std_classes', 'std_classes.id', '=', 'enrolls.class_id')
        ->leftJoin('sections', 'sections.id', '=', 'enrolls.section_id')
        ->select('students.name as std_name', 'students.std_code', 'students.file_path',
          'sections.name as section', 'std_classes.name as class_name')
        ->whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [Carbon::now()->format('m-d')])
        ->where('enrolls.year', config('running_session'))
        ->orderBy('std_classes.in_digit', 'asc')
        ->get();
      $latest_news = News::with('author')->where('category', 'Latest News')->where('status', 1)->orderby('created_at', 'desc')->take(4)->get();
      $sliders = Slider::orderby('order', 'asc')->get();
      return View::make('frontend.index', compact('sliders', 'latest_news', 'teacher', 'students', 'birthday'));
   }


   /* ===== About Us Start  ======== */

   // About Us
   public function ourHistory()
   {
      return View::make('frontend.history');
   }


   // Chairman Message
   public function chairmanMessage()
   {
      return View::make('frontend.messagePresident');
   }

   // Principal Message
   public function principalMessage()
   {
      return View::make('frontend.messagePrincipal');
   }

   // Management Committee
   public function managementCommittee()
   {
      return View::make('frontend.managementCommittee');
   }
   /* ===== About Us End  ======== */


   // Eligibility
   public function eligibility()
   {
      return View::make('frontend.eligibility');
   }


   // Gallery
   public function gallery(Request $request)
   {
      $galleries = Gallery::orderby('created_at', 'desc')->paginate(16);
      if ($request->ajax()) {
         return view('frontend.galleryPag', compact('galleries'));
      }
      return view('frontend.gallery', compact('galleries'));
   }


   // Download
   public function downloads()
   {
      return View::make('frontend.download');
   }

   public function allDownloads(Request $request)
   {
      if ($request->ajax()) {
         DB::statement(DB::raw('set @rownum=0'));
         $downloads = Download::orderby('created_at', 'desc')->get(['downloads.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
         return Datatables::of($downloads)
           ->addColumn('file_path', function ($download) {
              return $download->file_path ? "<a class='btn btn-primary' href='" . asset($download->file_path) . "'>Download</a>" : '';
           })
           ->rawColumns(['file_path'])
           ->make(true);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   // Contact Us
   public function contact()
   {
      return View::make('frontend.contact');
   }

   /* ===== Academic Start  ======== */

   // Teachers
   public function teachers()
   {
      $teacher = Teacher::all();
      return View::make('frontend.teachers', compact('teacher'));
   }


   // apiTest
   public function apiTest()
   {
      $stdclass = StdClass::all();
      return View('frontend.apiTest', compact('stdclass'));
   }


   // Student
   public function student()
   {
      $stdclass = StdClass::all();
      return View('frontend.student', compact('stdclass'));
   }

   public function getSections(Request $request, $class_id)
   {
      if ($request->ajax()) {

         $class = StdClass::findOrFail($class_id);
         $sections = $class->sections;
         if ($sections) {
            echo "<option value='' selected disabled> Select a section</option>";
            // echo "<option value='all'> All </option>";
            foreach ($sections as $section) {
               echo "<option  value='$section->id'> $section->name</option>";
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function allStudents(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         if ($section_id == 'all') {
            $section_id = 'null';
         }

         DB::statement(DB::raw("SET @section_id = $section_id"));

         $students = DB::table('enrolls')
           ->join('students', 'students.id', '=', 'enrolls.student_id')
           ->select('students.*', 'enrolls.roll')
           ->where('enrolls.class_id', $class_id)
           ->where('enrolls.section_id', DB::raw('COALESCE(@section_id, enrolls.section_id)'))
           ->where('enrolls.year', config('running_session'))->get();
         return Datatables::of($students)
           ->addColumn('file_path', function ($student) {
              return "<img src='" . asset($student->file_path) . "' class='img-thumbnail' width='40px'>";
           })
           ->addColumn('roll', function ($student) {
              return $student->roll;
           })
           ->rawColumns(['action', 'file_path'])
           ->make(true);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   // Class Routine
   public function classRoutine()
   {
      $stdclass = StdClass::all();
      return view('frontend.classRoutine', compact('stdclass'));
   }

   public function getClassroutines(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');
         $data['class_name'] = $request->input('class_name');
         $data['section_name'] = $request->input('section_name');

         $data['routines'] = $data['routines'] = Academic::generateClassRoutine($class_id, $section_id);
         $view = View::make('frontend.classRoutineContent', compact('data'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   // Class Syllabus
   public function classSyllabus()
   {
      $stdclass = StdClass::all();
      return view('frontend.classSyllabus', compact('stdclass'));
   }

   public function getSyllabus(Request $request)
   {
      if ($request->ajax()) {

         $class_id = $request->input('class_id');
         $section_id = $request->input('section_id');

         DB::statement(DB::raw('set @rownum=0'));
         $syllabus = Syllabus::where('class_id', $class_id)->where('section_id', $section_id)->where('year', config('running_session'))->orderby('created_at', 'desc')->get(['syllabus.*', DB::raw('@rownum  := @rownum  + 1 AS rownum')]);
         return Datatables::of($syllabus)
           ->addColumn('file_path', function ($syllabus) {
              return $syllabus->file_path ? "<a class='btn btn-primary' href='" . asset($syllabus->file_path) . "'>Download</a>" : '';
           })
           ->addColumn('subject', function ($syllabus) {
              $subject = $syllabus->subject;
              return $subject ? $subject->name : '';
           })
           ->rawColumns(['action', 'file_path'])
           ->make(true);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }

   }

   // Academic Calender
   public function academicCalender()
   {
      $calender = AcademicCalender::where('year', config('running_session'))->first();
      return View('frontend.academicCalender', compact('calender'));
   }

   // Academic Event Calender
   public function academicEvents()
   {
      $events = Event::get();
      return view('frontend.academicEvents', compact('events'));
   }

   // Event Details
   public function eventDetails(Request $request, Event $event)
   {
      if ($request->ajax()) {
         $view = View::make('backend.admin.event.view', compact('event'))->render();
         return response()->json(['html' => $view]);
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   // News Details
   public function viewNews(News $news)
   {
      return view('frontend.newsDetails', compact('news'));
   }

   // Rules and Regulation
   public function rulesRegulation()
   {
      return view('frontend.rules');
   }

   // Academic Notice Board
   public function academicNotices(Request $request)
   {
      $notices = News::where('category', 'Notice Board')->orderby('created_at', 'desc')->paginate(5);
      $title = "Academic Notices";
      if ($request->ajax()) {
         return view('frontend.academicNoticesNewsPag', compact('notices'));
      }
      return view('frontend.academicNoticesNews', compact('notices', 'title'));
   }

   // Academic Latest News
   public function academicNews(Request $request)
   {
      $notices = News::where('category', 'Latest News')->orderby('created_at', 'desc')->paginate(5);
      $title = "Academic Latest News";
      if ($request->ajax()) {
         return view('frontend.academicNoticesNewsPag', compact('notices'));
      }
      return view('frontend.academicNoticesNews', compact('notices', 'title'));
   }


   /* ===== Careers Start  ======== */

   // Job circular
   public function jobCircular()
   {
      $jobs = News::where('category', 'Job News')->where('status', 1)->orderby('created_at', 'desc')->get();
      $total = $jobs->count();
      return view('frontend.jobCircular', compact('jobs', 'total'));
   }

   // Submit Resume
   public function submitResume()
   {
      return view('frontend.submitResume');
   }


   public function mailResume(Request $request)
   {

      if ($request->ajax()) {
         $rules = [
           'name' => 'required',
           'email' => 'required',
           'mobile' => 'required',
           'resume' => 'max:1024'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {
            if ($request->hasFile('resume')) {
               $extension = Input::file('resume')->getClientOriginalExtension();;
               if ($extension == "doc" || $extension == "docx" || $extension == "pdf") {
                  $destinationPath = 'assets/uploads/resume'; // upload path
                  $extension = Input::file('resume')->getClientOriginalExtension(); // getting image extension
                  $fileName = time() . '.' . $extension; // renameing image
                  $file_path = 'assets/uploads/resume/' . $fileName;
                  //  Input::file('resume')->move($destinationPath, $fileName); // uploading file to given path
                  $upload_ok = 1;

               } else {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>File type is not valid</div>"
                  ]);
               }
            } else {
               return response()->json([
                 'type' => 'error',
                 'message' => "<div class='alert alert-warning'>No file uploaded</div>"
               ]);
            }

            if ($upload_ok == 0) {
               return response()->json([
                 'type' => 'error',
                 'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
               ]);
            } else {
               $data = array(
                 'name' => $request->name,
                 'email' => $request->email,
                 'mobile' => $request->mobile,
                 'job_position' => $request->job_position,
                 'cover_letter' => $request->cover_letter
               );
               $files = $request->file('resume');
               \Mail::send('frontend.mailTemplate', compact('data'), function ($message) use ($data, $files) {
                  $message->from($data['email']);
                  $message->to('riyad@w3xplorers.com')->subject($data['job_position'] . ' - ' . $data['name']);
                  $message->attach($files->getRealPath(), array(
                      'as' => $files->getClientOriginalName(),
                      'mime' => $files->getMimeType())
                  );
               });
               Input::file('resume')->move($destinationPath, $fileName);
               return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success'>Successfully Uploaded</div>"]);
            }
         }
      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   /* ===== Academic Start  ======== */


   public function onlineAdmission()
   {
      $stdclass = StdClass::all();
      return View::make('frontend.admission_form', compact('stdclass'));
   }

   public function onlineAdmissionStore(Request $request)
   {
      if ($request->ajax()) {

         $rules = [
           'applicant_name_en' => 'required',
           'applicant_name_bn' => 'required',
           'father_name_en' => 'required',
           'admitted_class' => 'required'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {
            $upload_ok = 1;

            if ($request->hasFile('photo')) {
               $extension = Input::file('photo')->getClientOriginalExtension();;
               if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
                  if (Input::file('photo')->isValid()) {
                     $destinationPath = 'assets/uploads/admission_upload'; // upload path
                     $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
                     $fileName = time() . '.' . $extension; // renameing image
                     $file_path = 'assets/uploads/admission_upload/' . $fileName;
                     Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
                     $upload_ok = 1;

                  } else {
                     return response()->json([
                       'type' => 'error',
                       'message' => "<div class='alert alert-warning'>File is not valid</div>"
                     ]);
                  }
               } else {
                  return response()->json([
                    'type' => 'error',
                    'message' => "<div class='alert alert-warning'>Error! File type is not valid</div>"
                  ]);
               }
            } else {
               return response()->json([
                 'type' => 'error',
                 'message' => "<div class='alert alert-warning'>Error! File not selected</div>"
               ]);
            }
            if ($upload_ok == 0) {
               return response()->json([
                 'type' => 'error',
                 'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
               ]);
            } else {
               $rows = DB::table('admission_applications')
                 ->where('applicant_name_en', $request->input('applicant_name_en'))
                 ->where('applicant_name_bn', $request->input('applicant_name_bn'))
                 ->where('father_name_en', $request->input('father_name_en'))
                 ->where('father_name_bn', $request->input('father_name_bn'))
                 ->count();
               if ($rows == 0) {

                  $last_inserted = AdmissionApplication::where('applied_year', config('running_session'))->whereMonth('created_at', Carbon::now()->month)->orderBy('id', 'DESC')->first();
                  if ($last_inserted) {
                     $sum = '001' + $last_inserted->id;
                  }
                  $applicant_id = $last_inserted ? 'AID' . date('ym') . $sum : 'AID' . date('ym') . '001';

                  $application_no = $last_inserted ? date('Ym') . $sum : date('Ym') . '001';

                  $admission = new AdmissionApplication();
                  $admission->applicant_id = $applicant_id;
                  $admission->applicant_form_no = $application_no;
                  $admission->applicant_name_en = $request->input('applicant_name_en');
                  $admission->applicant_name_bn = $request->input('applicant_name_bn');
                  $admission->father_name_en = $request->input('father_name_en');
                  $admission->father_name_bn = $request->input('father_name_bn');
                  $admission->mother_name_en = $request->input('mother_name_en');
                  $admission->mother_name_bn = $request->input('mother_name_bn');
                  $admission->father_qualification = $request->input('father_qualification');
                  $admission->mother_qualification = $request->input('mother_qualification');
                  $admission->father_occupation = $request->input('father_occupation');
                  $admission->mother_occupation = $request->input('mother_occupation');
                  $admission->father_occupation_post_name = $request->input('father_occupation_post_name');
                  $admission->father_occupation_org_name = $request->input('father_occupation_org_name');
                  $admission->father_occupation_business_type = $request->input('father_occupation_business_type');
                  $admission->alternet_gurdian_name = $request->input('alternet_gurdian_name');
                  $admission->alternet_gurdian_phone = $request->input('alternet_gurdian_phone');
                  $admission->alternet_gurdian_address = $request->input('alternet_gurdian_address');
                  $admission->yearly_income = $request->input('yearly_income');
                  $admission->mobile = $request->input('mobile');
                  $admission->dob = $request->input('dob');
                  $admission->present_village = $request->input('present_village');
                  $admission->present_post_office = $request->input('present_post_office');
                  $admission->present_thana = $request->input('present_thana');
                  $admission->present_district = $request->input('present_district');
                  $admission->parmanent_village = $request->input('parmanent_village');
                  $admission->parmanent_post_office = $request->input('parmanent_post_office');
                  $admission->parmanent_thana = $request->input('parmanent_thana');
                  $admission->parmanent_district = $request->input('parmanent_district');
                  $admission->email = $request->input('email');
                  $admission->nationality = $request->input('nationality');
                  $admission->children_in_school = $request->input('children_in_school');
                  $admission->children_name = $request->input('children_name');
                  $admission->children_class = $request->input('children_class');
                  $admission->children_section = $request->input('children_section');
                  $admission->admitted_class = $request->input('admitted_class');
                  $admission->admitted_section = $request->input('admitted_section');
                  $admission->old_school_name = $request->input('old_school_name');
                  $admission->old_class = $request->input('old_class');
                  $admission->blood_group = $request->input('blood_group');
                  $admission->status = 2;
                  $admission->file_path = $file_path;
                  $admission->applied_year = config('running_session');
                  $admission->aggrement = $request->input('aggrement');
                  $admission->save(); //
                  return response()->json(['type' => 'success', 'message' => "<div class='alert alert-success' style='color: #fff'>Successfully Submitted.</div>"]);
               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Already applied with same information</div>"]);

               }
            }
         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }

   public function admissionResult()
   {
      $result = AdmissionResult::where('year', config('running_session'))->orderBy('id', 'desc')->get();
      return View::make('frontend.admission_result', compact('result'));
   }
   
   
   public function onlineApplicantForm()
   {
      return View::make('frontend.onlineApplicantForm');
   }

   public function onlineApplicantFormSubmit(Request $request)
   {
      //dd($request);
       if ($request->ajax()) {

         $rules = [
           'enrollment_class' => 'required',
           'girl_name' => 'required',
           'father_name' => 'required',
           'mother_name' => 'required'
         ];

         $validator = Validator::make($request->all(), $rules);
         if ($validator->fails()) {
            return response()->json([
              'type' => 'error',
              'errors' => $validator->getMessageBag()->toArray()
            ]);
         } else {

            $upload_ok = 1;

            // if ($request->hasFile('photo')) {
            //    $extension = Input::file('photo')->getClientOriginalExtension();;
            //    if ($extension == "jpg" || $extension == "jpeg" || $extension == "png") {
            //       if (Input::file('photo')->isValid()) {
            //          $destinationPath = 'assets/uploads/admission_upload'; // upload path
            //          $extension = Input::file('photo')->getClientOriginalExtension(); // getting image extension
            //          $fileName = time() . '.' . $extension; // renameing image
            //          $file_path = 'assets/uploads/admission_upload/' . $fileName;
            //          Input::file('photo')->move($destinationPath, $fileName); // uploading file to given path
            //          $upload_ok = 1;

            //       } else {
            //          return response()->json([
            //            'type' => 'error',
            //            'message' => "<div class='alert alert-warning'>File is not valid</div>"
            //          ]);
            //       }
            //    } else {
            //       return response()->json([
            //         'type' => 'error',
            //         'message' => "<div class='alert alert-warning'>Error! File type is not valid</div>"
            //       ]);
            //    }
            // } else {
            //    return response()->json([
            //      'type' => 'error',
            //      'message' => "<div class='alert alert-warning'>Error! File not selected</div>"
            //    ]);
            // }
            if ($upload_ok == 0) {
               return response()->json([
                 'type' => 'error',
                 'message' => "<div class='alert alert-warning'>Sorry Failed</div>"
               ]);
            } else {
               $rows = 0;
               $ref_id = time().uniqid();
               if ($rows == 0) {

                  $admission = new OnlineApplicant();
                  $admission->ref_id = $ref_id;
                  $admission->enrollment_class = $request->input('enrollment_class');
                  $admission->girl_name = $request->input('girl_name');
                  $admission->father_name = $request->input('father_name');
                  $admission->mother_name = $request->input('mother_name');
                  $admission->vill = $request->input('vill');
                  $admission->po = $request->input('po');
                  $admission->ps = $request->input('ps');
                  $admission->dist = $request->input('dist');
                  $admission->depended_on_port_authority = $request->input('depended_on_port_authority');
                  $admission->dob = $request->input('dob');
                  $admission->dob_text = $request->input('dob_text');
                  $admission->guardian_name = $request->input('guardian_name');
                  $admission->guardian_father_name = $request->input('guardian_father_name');
                  $admission->guardian_present_address = $request->input('guardian_present_address');
                  $admission->relation_with_guardian = $request->input('relation_with_guardian');
                  $admission->guardian_work_address = $request->input('guardian_work_address');
                  $admission->guardian_work_designation = $request->input('guardian_work_designation');
                  $admission->guardian_salary_scale = $request->input('guardian_salary_scale');
                  $admission->guardian_monthly_salary = $request->input('guardian_monthly_salary');
                  $admission->guardian_salary_next_increment_date = $request->input('guardian_salary_next_increment_date');
                  $admission->nationality = $request->input('nationality');
                  $admission->speciality = $request->input('speciality');
                  $admission->school_name = $request->input('school_name');
                  $admission->school_vill = $request->input('school_vill');
                  $admission->school_po = $request->input('school_po');
                  $admission->school_ps = $request->input('school_ps');
                  $admission->school_dist = $request->input('school_dist');
                  $admission->is_admit_by_tc = $request->input('is_admit_by_tc');
                  $admission->phone_no = $request->input('phone_no');
                  $admission->uploaded_by = 'normal user';
                  $admission->save(); //
                  return response()->json(['type' => 'success', 'ref_id' => $ref_id, 'message' => '<div class="alert alert-success" style="color: #fff">Successfully Submitted. <br> Please click the link download the PDF and print it on Legal paper <br><a target="_blank"  href="/onlineApplicantFormPDF/'.$ref_id.'">Cick Here</a></div>']);
               } else {
                  return response()->json(['type' => 'error', 'message' => "<div class='alert alert-warning'> Already applied with same information</div>"]);

               }
            }


         }

      } else {
         return response()->json(['status' => 'false', 'message' => "Access only ajax request"]);
      }
   }


   public function onlineApplicantFormPDF($ref_id)
   {

      $data = OnlineApplicant::where('ref_id', $ref_id)->first();

      if ($data) {
         $view = view('frontend.onlineAdmissionFormPdf', compact('data'));
         $html = $view->render();
      } else {
         $html = "<html><body><p> Sorry!! no records have found</p></body></html>";
      }

      //$customPaper = array(0, 0, 612.00, 1008.00);
      //$dompdf->set_paper($customPaper);

      $pdf_name = 'Online Application Form '.$data->girl_name;


      $pdf = PDF::loadHTML($html);
      $sheet = $pdf->setPaper('Legal', 'portrait');
      return $sheet->stream($pdf_name.'.pdf');

   }

}

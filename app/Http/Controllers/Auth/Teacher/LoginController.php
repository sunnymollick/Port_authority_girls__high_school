<?php

namespace App\Http\Controllers\Auth\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
   /*
   |--------------------------------------------------------------------------
   | Login Controller
   |--------------------------------------------------------------------------
   |
   | This controller handles authenticating users for the application and
   | redirecting them to your home screen. The controller uses a trait
   | to conveniently provide its functionality to your applications.
   |
   */
   use AuthenticatesUsers;

   /**
    * Where to redirect users after login.
    *
    * @var string
    */
   // protected $redirectTo = '/home';
   public function __construct()
   {
      $this->middleware('guest:teacher')->except('logout');
   }

   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function login()
   {
      return view('auth.teacher.login');
   }

   public function loginTeacher(Request $request)
   {
      // Validate the form data
      $this->validate($request, [
        'teacher_code' => 'required',
        'password' => 'required'
      ]);
      // Attempt to log the user in
      if (Auth::guard('teacher')->attempt(['teacher_code' => $request->teacher_code, 'password' => $request->password, 'status' => 1], $request->remember)) {
         // if successful, then redirect to their intended location
         return redirect()->route('teacher.dashboard');
        // return redirect()->intended(route('teacher.dashboard'));
      }
      // if unsuccessful, then redirect back to the login with the form data
      $errors = ['teacher_code' => 'Sorry!! Wrong teacher id or password '];
      return redirect()->back()->withInput($request->only('teacher_code', 'remember'))->withErrors($errors);
   }


   public function logout()
   {
      Auth::guard('teacher')->logout();
      return redirect()->route('teacher.auth.login');
   }
}
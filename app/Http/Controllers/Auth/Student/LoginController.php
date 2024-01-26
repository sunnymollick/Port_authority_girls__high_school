<?php

namespace App\Http\Controllers\Auth\Student;

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
      $this->middleware('guest:student')->except('logout');
   }

   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function login()
   {
      return view('auth.student.login');
   }

   public function loginStudent(Request $request)
   {
      // Validate the form data
      $this->validate($request, [
        'std_code' => 'required',
        'password' => 'required'
      ]);
      // Attempt to log the user in
      if (Auth::guard('student')->attempt(['std_code' => $request->std_code, 'password' => $request->password, 'status' => 1], $request->remember)) {
         // if successful, then redirect to their intended location
         return redirect()->route('student.dashboard');
         // return redirect()->intended(route('student.dashboard'));
      }
      // if unsuccessful, then redirect back to the login with the form data
      $errors = ['std_code' => 'Sorry!! Wrong student code or password '];
      return redirect()->back()->withInput($request->only('std_code', 'remember'))->withErrors($errors);
   }


   public function logout()
   {
      Auth::guard('student')->logout();
      return redirect()->route('student.auth.login');
   }
}
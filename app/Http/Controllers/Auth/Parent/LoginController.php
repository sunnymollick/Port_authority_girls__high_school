<?php

namespace App\Http\Controllers\Auth\Parent;

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
      $this->middleware('guest:parent')->except('logout');
   }

   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function login()
   {
      return view('auth.parent.login');
   }

   public function loginParent(Request $request)
   {
      // Validate the form data
      $this->validate($request, [
        'parent_code' => 'required',
        'password' => 'required'
      ]);
      // Attempt to log the user in
      if (Auth::guard('parent')->attempt(['parent_code' => $request->parent_code, 'password' => $request->password], $request->remember)) {
         // if successful, then redirect to their intended location
         return redirect()->route('parent.dashboard');
         // return redirect()->intended(route('parent.dashboard'));
      }
      // if unsuccessful, then redirect back to the login with the form data
      $errors = ['parent_code' => 'Sorry!! Wrong parent code or password '];
      return redirect()->back()->withInput($request->only('parent_code', 'remember'))->withErrors($errors);
   }


   public function logout()
   {
      Auth::guard('parent')->logout();
      return redirect()->route('parent.auth.login');
   }
}
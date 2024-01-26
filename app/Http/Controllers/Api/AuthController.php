<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController as ResponseController;
use App\Models\Authenticator;

class AuthController extends ResponseController
{

   /**
    * @var Authenticator
    */
   private $authenticator;

   public function __construct(Authenticator $authenticator)
   {
      $this->authenticator = $authenticator;
   }

   /**
    * Handles Login Request
    *
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
   public function login(Request $request)
   {
      $credentials = [$request->email, $request->password, $request->provider];

      if ($user = $this->authenticator->attempt(...$credentials)) {
         $success['token_type'] = 'Bearer';
         $success['token'] = $user->createToken('W3School' . $request->provider)->accessToken;
         return $this->sendResponse($success, 'Logged successfully.');
      } else {
         return $this->sendError('UnAuthorised Access', ['error' => 'UnAuthorised']);
      }
   }

   public function student_login(Request $request)
   {
      $credentials = [$request->student_code, $request->password, $request->provider];

      if ($user = $this->authenticator->std_attempt(...$credentials)) {
         $success['token_type'] = 'Bearer';
         $success['token'] = $user->createToken('W3School' . $request->provider)->accessToken;
         return $this->sendResponse($success, 'Logged successfully.');
      } else {
         return $this->sendError('UnAuthorised Access', ['error' => 'UnAuthorised']);
      }
   }

   /**
    * Returns Authenticated User Details
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function details()
   {
      return response()->json(['user' => auth()->user()], 200);
   }

}

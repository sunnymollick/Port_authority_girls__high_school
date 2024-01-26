<?php

use Illuminate\Http\Request;

// Public Api all frontend access
Route::group([
  'prefix' => 'public',
  'as' => 'public.'],
  function () {
     require base_path('routes/api/public_api.php');
  });


// get api token  for users and teachers email/pass
Route::post('/login', 'Api\AuthController@login');


// Users Api
Route::group([
  'prefix' => 'user',
  'as' => 'user.',
  'middleware' => 'auth:api'],
  function () {
     require base_path('routes/api/user_api.php');
  });


// Teacher Api
Route::group([
  'prefix' => 'teacher',
  'as' => 'teacher.',
  'middleware' => 'auth:teacher-api'],
  function () {
     require base_path('routes/api/teacher_api.php');
  });


// get api token for student using student code/pass
Route::post('/student_login', 'Api\AuthController@student_login');

// Student Api
Route::group([
  'prefix' => 'student',
  'as' => 'student.',
  'middleware' => 'auth:student-api'],
  function () {
     require base_path('routes/api/student_api.php');
  });



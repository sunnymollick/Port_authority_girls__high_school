<?php

Route::group([
  'namespace' => 'Frontend',
  'as' => 'frontend.'],
  function () {
     require base_path('routes/frontend/frontend.php');
  });


// Bakcend

Auth::routes();

// Admin Dashborad
Route::group([
  'namespace' => 'Backend\Admin',
  'prefix' => 'admin',
  'as' => 'admin.',
  'middleware' => 'auth'],
  function () {
     require base_path('routes/backend/admin.php');
  });

// Teacher Auth
Route::prefix('teacher_login')->group(function () {
   Route::get('login', 'Auth\Teacher\LoginController@login')->name('teacher.auth.login');
   Route::post('login', 'Auth\Teacher\LoginController@loginTeacher')->name('teacher.auth.loginTeacher');
   Route::post('logout', 'Auth\Teacher\LoginController@logout')->name('teacher.auth.logout');
});


// Teacher Dashborad
Route::group([
  'namespace' => 'Backend\Teacher',
  'prefix' => 'teacher',
  'as' => 'teacher.',
  'middleware' => 'auth:teacher'],
  function () {
     require base_path('routes/backend/teacher.php');
  });


// Student Auth
Route::prefix('student_login')->group(function () {
   Route::get('login', 'Auth\Student\LoginController@login')->name('student.auth.login');
   Route::post('login', 'Auth\Student\LoginController@loginStudent')->name('student.auth.loginStudent');
   Route::post('logout', 'Auth\Student\LoginController@logout')->name('student.auth.logout');
});


// Student Dashborad
Route::group([
  'namespace' => 'Backend\Student',
  'prefix' => 'student',
  'as' => 'student.',
  'middleware' => 'auth:student'],
  function () {
     require base_path('routes/backend/student.php');
  });


// Parent Auth
Route::prefix('parent_login')->group(function () {
   Route::get('login', 'Auth\Parent\LoginController@login')->name('parent.auth.login');
   Route::post('login', 'Auth\Parent\LoginController@loginParent')->name('parent.auth.loginParent');
   Route::post('logout', 'Auth\Parent\LoginController@logout')->name('parent.auth.logout');
});


// Parent Dashborad
Route::group([
  'namespace' => 'Backend\Parent',
  'prefix' => 'parent',
  'as' => 'parent.',
  'middleware' => 'auth:parent'],
  function () {
     require base_path('routes/backend/parent.php');
  });

// Print marksheet by admin, student and parents without auth
Route::get('/printMarksheet', 'Backend\Admin\TabulationSheetController@printMarksheet')->name('printMarksheet.access');
Route::get('/jrHalfyearlyprintMarksheet', 'Backend\Admin\MarkSheetController@jrHalfyearlyprintMarksheet')->name('jrHalfyearlyprintMarksheet.access');
Route::get('/jrfinalyprintMarksheet', 'Backend\Admin\MarkSheetController@jrfinalyprintMarksheet')->name('jrfinalyprintMarksheet.access');
Route::get('/srPrintMarksheet', 'Backend\Admin\MarkSheetController@srPrintMarksheet')->name('srPrintMarksheet.access');
Route::get('alumniPrint/{id}', 'Frontend\SommelonController@alumniPrint');

Route::get('/jrhalfSummeryResultGradeSummaryPrint', 'Backend\Admin\MarkSheetController@jrhalfSummeryResultGradeSummaryPrint')->name('jrhalfSummeryResultGradeSummaryPrint.access');
Route::get('/srSummeryResultGradeSummaryPrint', 'Backend\Admin\MarkSheetController@srSummeryResultGradeSummaryPrint')->name('srSummeryResultGradeSummaryPrint.access');

// clear config and cache
//['cache:clear', 'optimize', 'route:cache', 'route:clear', 'view:clear', 'config:cache']

//    /artisan/cache-clear  // replace (:) to (-)
Route::get('/artisan/{cmd}', function($cmd) {
  $cmd = trim(str_replace("-",":", $cmd));
  $validCommands = ['cache:clear', 'optimize', 'route:cache', 'route:clear', 'view:clear', 'config:cache'];
  if (in_array($cmd, $validCommands)) {
     Artisan::call($cmd);
     return "<h1>Ran Artisan command: {$cmd}</h1>";
  } else {
     return "<h1>Not valid Artisan command</h1>";
  }
});
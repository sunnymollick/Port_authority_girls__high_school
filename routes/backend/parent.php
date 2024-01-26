<?php

Route::get('/', 'ParentSelfController@index')->name('dashboard');
Route::get('/profile', 'ParentSelfController@profile')->name('parent');
Route::get('/edit_profile', 'ParentSelfController@edit')->name('parent');
Route::patch('/edit_profile', 'ParentSelfController@update')->name('parent');
Route::get('/change_password', 'ParentSelfController@change_password')->name('parent');
Route::patch('/change_password', 'ParentSelfController@update_password')->name('parent');

Route::get('/getClassroutines', 'ParentSelfController@getClassroutines')->name('getClassroutines');

Route::get('/getAcademicResult', 'ParentSelfController@getAcademicResult')->name('getAcademicResult');
Route::post('/generateMarksheet', 'ParentSelfController@generateMarksheet')->name('generateMarksheet');

Route::get('/attendance', 'ParentSelfController@getAttendance')->name('attendance');
Route::post('/attendance', 'ParentSelfController@attendanceReport')->name('attendance');


/* ===== Accounts Management Start =========== */


// Invoices/ Student Payment Controller
Route::get('feeBooks', 'ParentSelfController@feeBooks');
Route::get('/allFeeBooks', 'ParentSelfController@allFeeBooks')->name('parent');
Route::get('/printFeeBook/{month}', 'ParentSelfController@printFeeBook')->name('parent');
Route::get('/paymentHistory', 'ParentSelfController@paymentHistory')->name('parent');
Route::get('/paymentDetails', 'ParentSelfController@paymentDetails')->name('paymentDetails');



/* ===== Accounts Management End =========== */
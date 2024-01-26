<?php

Route::get('/', 'StudentSelfController@index')->name('dashboard');
Route::get('/profile', 'StudentSelfController@profile')->name('student');
Route::get('/edit_profile', 'StudentSelfController@edit')->name('student');
Route::patch('/edit_profile', 'StudentSelfController@update')->name('student');
Route::get('/optionalSubject', 'StudentSelfController@optional_subject')->name('student');
Route::patch('/optionalSubject', 'StudentSelfController@add_optional_subject')->name('student');
Route::get('/change_password', 'StudentSelfController@change_password')->name('student');
Route::patch('/change_password', 'StudentSelfController@update_password')->name('student');



Route::get('/getClassroutines', 'StudentSelfController@getClassroutines')->name('getClassroutines');
Route::get('/getAcademicResult', 'StudentSelfController@getAcademicResult')->name('getAcademicResult');
Route::post('/generateMarksheet', 'StudentSelfController@generateMarksheet')->name('generateMarksheet');
Route::get('/admitCard', 'StudentSelfController@admitCard')->name('exams');
Route::get('/generateAdmitCard', 'StudentSelfController@generateAdmitCard')->name('exams');



Route::get('/attendance', 'StudentSelfController@getAttendance')->name('attendance');
Route::post('/attendance', 'StudentSelfController@attendanceReport')->name('attendance');

Route::get('/syllabus', 'StudentSelfController@syllabus')->name('syllabus');
Route::get('/getSyllabus', 'StudentSelfController@getSyllabus')->name('getSyllabus.syllabus');


/* ===== Accounts Management Start =========== */


// Invoices/ Student Payment Controller
Route::get('feeBooks', 'StudentSelfController@feeBooks');
Route::get('/allFeeBooks', 'StudentSelfController@allFeeBooks')->name('student');
Route::get('/printFeeBook/{month}', 'StudentSelfController@printFeeBook')->name('student');
Route::get('/paymentHistory', 'StudentSelfController@paymentHistory')->name('student');
Route::get('/paymentDetails', 'StudentSelfController@paymentDetails')->name('paymentDetails');



/* ===== Accounts Management End =========== */
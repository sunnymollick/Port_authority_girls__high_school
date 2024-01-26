<?php

Route::get('/', 'HomeController@index');

/* ==== About us start =======  */

// Our History
Route::get('/ourHistory', 'HomeController@ourHistory');

// Chairman Message
Route::get('/chairmanMessage', 'HomeController@chairmanMessage');

// Principal Message
Route::get('/principalMessage', 'HomeController@principalMessage');

// Management Committee
Route::get('/managementCommittee', 'HomeController@managementCommittee');

/* ====== About us end =======  */

//  Teachers
Route::get('/teachers', 'HomeController@teachers');
Route::get('/getSections/{class_id}', 'HomeController@getSections')->name('getSections');

//  Students
Route::get('/students', 'HomeController@student')->name('allstudents');
Route::post('/allStudents', 'HomeController@allStudents')->name('allStudents.students');


//  Class Routines
Route::get('/classRoutine', 'HomeController@classRoutine')->name('classRoutine');
Route::post('/getClassroutines', 'HomeController@getClassroutines')->name('getClassroutines');

//  Class Syllabus
Route::get('classSyllabus', 'HomeController@classSyllabus');
Route::post('/getSyllabus', 'HomeController@getSyllabus');


//  Academic Calender
Route::get('/academicCalender', 'HomeController@academicCalender')->name('academicCalender');


//  Academic Event Calender
Route::get('/academicEvents', 'HomeController@academicEvents')->name('academicEvents');
Route::get('/eventDetails/{event}', 'HomeController@eventDetails')->name('eventDetails');


// Latest News and Notice Board
Route::get('/academicNotices', 'HomeController@academicNotices');
Route::get('/academicNews', 'HomeController@academicNews');
Route::get('/viewNews/{news}', 'HomeController@viewNews');

//  Rules and Regulation
Route::get('/rulesRegulation', 'HomeController@rulesRegulation');

//  Elegibility
Route::get('/eligibility', 'HomeController@eligibility');


//  Careers // job circular  // submit Resume
Route::get('/jobCircular', 'HomeController@jobCircular');
Route::get('/submitResume', 'HomeController@submitResume');
Route::post('/submitResume', 'HomeController@mailResume');

//  Download
Route::get('/downloads', 'HomeController@downloads');
Route::get('/allDownloads', 'HomeController@allDownloads');

// Gallery Message
Route::get('/gallery', 'HomeController@gallery');

//  Contact
Route::get('/contact', 'HomeController@contact');


//  Contact
Route::get('/apiTest', 'HomeController@apiTest');

// admission application

Route::get('/onlineAdmission', 'HomeController@onlineAdmission');
Route::post('/onlineAdmission', 'HomeController@onlineAdmissionStore');
Route::get('/admissionResult', 'HomeController@admissionResult');


// Sommelon
Route::get('/sommelon', 'SommelonController@index');
Route::post('/sommelon', 'SommelonController@store');

// Online Applicant
Route::get('/onlineApplicantForm', 'HomeController@onlineApplicantForm');
Route::post('/onlineApplicantFormSubmit', 'HomeController@onlineApplicantFormSubmit');
Route::get('/onlineApplicantFormPDF/{ref_id}', 'HomeController@onlineApplicantFormPDF');


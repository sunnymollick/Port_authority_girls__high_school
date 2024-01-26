<?php

Route::get('/', 'TeacherSelfController@index')->name('dashboard');
Route::get('/profile', 'TeacherSelfController@profile')->name('teacher');
Route::get('/edit_profile', 'TeacherSelfController@edit')->name('teacher');
Route::patch('/edit_profile', 'TeacherSelfController@update')->name('teacher');
Route::get('/change_password', 'TeacherSelfController@change_password')->name('teacher');
Route::patch('/change_password', 'TeacherSelfController@update_password')->name('teacher');

Route::get('/getSubjects', 'TeacherSelfController@getSubjects')->name('getSubjects');
Route::get('/getSections/{class_id}', 'TeacherSelfController@getSections')->name('getSections');

Route::get('/importMarks', 'TeacherSelfController@import')->name('importMarks.import');
Route::post('/importMarks', 'TeacherSelfController@importStore')->name('importMarks.import');


Route::get('/getClassroutines', 'TeacherSelfController@getClassroutines')->name('getClassroutines');

Route::get('/attendance', 'TeacherSelfController@getAttendance')->name('attendance');
Route::post('/attendance', 'TeacherSelfController@attendanceReport')->name('attendance');

Route::get('/manageMarks', 'TeacherSelfController@manageMarks')->name('manageMarks.marks');
Route::post('/getMarks', 'TeacherSelfController@getMarks')->name('getMarks');
Route::post('/updateMarks', 'TeacherSelfController@updateMarks')->name('updateMarks');

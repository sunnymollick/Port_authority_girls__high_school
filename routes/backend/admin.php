<?php

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');


/* ===== Start  Academic Users Management  =========== */

// Students Controller
Route::resource('students', 'StudentController');
Route::post('/allStudents', 'StudentController@allStudents')->name('allStudents.students');
Route::get('/getStudents/{class_id}', 'StudentController@getStudents')->name('getStudents');
Route::get('/importStudents', 'StudentController@import')->name('importStudents.import');
Route::post('/importStudents', 'StudentController@importStore')->name('importStudents.import');
Route::get('/promotions', 'StudentController@promotion')->name('students.promotion');
Route::post('/importPromotion', 'StudentController@importPromotion')->name('importPromotion.import');

Route::get('/exportStudentExcel/{class_id}/{section_id}', 'StudentController@exportStudentExcel')->name('exportStudentExcel');
Route::get('/exportStudentPdf/{class_id}/{section_id}', 'StudentController@exportStudentPdf')->name('exportStudentPdf');

Route::get('/std_change_password/{student_id}', 'StudentController@change_password')->name('std_change_password');
Route::patch('/std_change_password', 'StudentController@update_password')->name('std_change_password');

// Parent Controller
Route::resource('parents', 'StdParentController');
Route::get('/allParents', 'StdParentController@allParents')->name('allParents.parents');
Route::get('/importParents', 'StdParentController@import')->name('importParents.import');
Route::post('/importParents', 'StdParentController@importStore')->name('importParents.import');
Route::get('/parent_change_password/{teacher_id}', 'StdParentController@change_password')->name('parent_change_password');
Route::patch('/parent_change_password', 'StdParentController@update_password')->name('parent_change_password');


// Teacher Controller
Route::resource('teachers', 'TeacherController');
Route::get('/allTeachers', 'TeacherController@allTeachers')->name('allTeachers.teachers');
Route::get('/importTeachers', 'TeacherController@import')->name('importTeachers.import');
Route::post('/importTeachers', 'TeacherController@importStore')->name('importTeachers.import');
Route::get('/teacher_change_password/{teacher_id}', 'TeacherController@change_password')->name('teacher_change_password');
Route::patch('/teacher_change_password', 'TeacherController@update_password')->name('teacher_change_password');


// Staff Controller
Route::resource('staffs', 'StaffController');
Route::get('/allStaffs', 'StaffController@allStaffs')->name('allStaffs.staffs');


/* ===== End  Academic Users Management  =========== */


/* ===== Start  Academic Activities =========== */

// Student Class Controller
Route::resource('stdclasses', 'StdClassController');
Route::get('/allClasses', 'StdClassController@allClasses')->name('allClasses.classes');

// Class Room Controller
Route::resource('classrooms', 'ClassRoomController');
Route::get('/allClassrooms', 'ClassRoomController@allClassrooms')->name('allClassrooms.classrooms');

// Class Routine Controller
Route::resource('classroutines', 'ClassRoutineController');
Route::post('/getClassroutines', 'ClassRoutineController@getClassroutines')->name('getClassroutines');

// Section Controller
Route::resource('sections', 'SectionController');
Route::get('/allSections', 'SectionController@allSections')->name('allSections.sections');
Route::get('/getSections/{class_id}', 'SectionController@getSections')->name('getSections');

// Subjects Controller
Route::resource('subjects', 'SubjectController');
Route::post('/allSubjects', 'SubjectController@allSubjects')->name('allSubjects.subjects');
Route::get('/getSubjects/{class_id}', 'SubjectController@getSubjects')->name('getSubjects');
Route::get('/getOptionalSubjects/{class_id}', 'SubjectController@getSubjects')->name('getOptionalSubjects');

// Syllabus Controller
Route::resource('syllabus', 'SyllabusController');
Route::post('/allSyllabus', 'SyllabusController@allSyllabus')->name('allSyllabus.syllabus');

// Academic Calender Controller
Route::resource('academiccalenders', 'AcademicCalenderController');
Route::get('/allAcademicCalender', 'AcademicCalenderController@allAcademicCalender')->name('allAcademicCalender.calender');

// Event Controller
Route::resource('events', 'EventController');
Route::get('/allEvents', 'EventController@allEvents')->name('allEvents.events');
Route::get('/viewCalender', 'EventController@viewCalender')->name('viewCalender');

/* ===== End Academic Activities  =========== */


/* ===== Exam & Result Management Start =========== */

// Exam Controller
Route::resource('exams', 'ExamController');
Route::post('/allExams', 'ExamController@allExams')->name('allExams.exams');
Route::get('/getExams/{class_id}', 'ExamController@getExams')->name('exams');
Route::get('/assignExaminee', 'ExamController@assignExaminee')->name('exams');
Route::get('/allAssignedExaminee', 'ExamController@allAssignedExaminee')->name('allAssignedExaminee.exams');

Route::get('/createExaminee', 'ExamController@createExaminee')->name('exams');
Route::post('/createExaminee', 'ExamController@storeExaminee')->name('exams');
Route::get('/editExaminee/{id}', 'ExamController@editExaminee')->name('exams');
Route::patch('/editExaminee/{id}', 'ExamController@updateExaminee')->name('exams');
Route::post('/deleteExaminee/{id}', 'ExamController@deleteExaminee')->name('exams');

Route::get('/admitCard', 'ExamController@admitCard')->name('exams');
Route::get('/generateAdmitCard', 'ExamController@generateAdmitCard')->name('exams');


// Grade Controller
Route::resource('grades', 'GradeController');
Route::get('/allGrades', 'GradeController@allGrades')->name('allGrades.grades');

// Marks Controller
Route::resource('marks', 'MarkController');
Route::post('/getMarks', 'MarkController@getMarks')->name('getMarks');
Route::get('/importMarks', 'MarkController@import')->name('importMarks.import');
Route::post('/importMarks', 'MarkController@importStore')->name('importMarks.import');
Route::get('/exportExcelMarks', 'MarkController@exportExcelMarks')->name('exportExcelMarks');
Route::get('/exportPdfMarks', 'MarkController@exportPdfMarks')->name('exportPdfMarks');


// TabulationSheet Controller
Route::resource('tabulations', 'TabulationSheetController');
Route::post('/summeryResult', 'TabulationSheetController@summeryResult')->name('summeryResult');
Route::post('/viewMarksheet', 'TabulationSheetController@viewMarksheet')->name('viewMarksheet');


// Marksheet 6, 7, 8 half yearly exam
Route::get('/jrhalfYearlyExam', 'MarkSheetController@jrhalfYearlyExamSummery');
Route::post('/jrhalfSummeryResult', 'MarkSheetController@jrhalfSummeryResult');
Route::post('/jrhalfExamMarksheet', 'MarkSheetController@jrhalfExamMarksheet');
Route::get('/jrHalfyearlyMarksheetPrint', 'MarkSheetController@jrHalfyearlyMarksheetPrint')->name('jrHalfyearlyMarksheetPrint');

// Marksheet 9, 10
Route::get('/srResult', 'MarkSheetController@srResult');
Route::post('/srSummeryResult', 'MarkSheetController@srSummeryResult');
Route::post('/srMarksheet', 'MarkSheetController@srMarksheet');
Route::get('/srMarksheetPrint', 'MarkSheetController@srMarksheetPrint')->name('srMarksheetPrint');


Route::get('/jrfullMarksheet', 'MarkSheetController@jrfullMarksheet')->name('jrfullMarksheet');
Route::post('/jrfinalSummeryResult', 'MarkSheetController@jrfinalSummeryResult')->name('jrfinalSummeryResult');
Route::post('/jrfinalMarksheet', 'MarkSheetController@jrfinalMarksheet')->name('jrfinalMarksheet');


Route::post('/summeryStudent', 'TabulationSheetController@summeryStudent')->name('summeryStudent');
Route::post('/viewFullMarksheet', 'TabulationSheetController@viewFullMarksheet')->name('viewFullMarksheet');

// Online Admission
Route::resource('admissionApplication', 'AdmissionApplicationController');
Route::get('/allApplications', 'AdmissionApplicationController@allApplications');

// admission result

Route::resource('admissionResult', 'AdmissionResultController');
Route::get('/allAdmissionResult', 'AdmissionResultController@allAdmissionResult');

/* ===== Exam & Result Management End =========== */


/* ===== Accounts Management Start =========== */

// Bank accounts setup

Route::resource('bankAccounts', 'BankAccountSetupController');
Route::get('/allBankAccounts', 'BankAccountSetupController@allBankAccounts')->name('accounts');

// Accounts Head
Route::resource('accountsHead', 'AccountsHeadController');
Route::get('/allAccountsHead', 'AccountsHeadController@allAccountsHead')->name('accounts');


// Accounts Fees
Route::resource('accountsFees', 'AccountsFeeController');
Route::get('/allAccountsFees', 'AccountsFeeController@allAccountsFees')->name('allAccountsFees.accounts');
Route::get('/getAllSection/{class_id}', 'AccountsFeeController@getAllSection')->name('accounts');
Route::get('/printFeePdf', 'AccountsFeeController@printFeePdf')->name('accounts');
Route::get('/generateFeePdf', 'AccountsFeeController@generateFeePdf');
Route::get('/downloadFeeZipped/{class_name}/{section_name}', 'AccountsFeeController@downloadFeeZipped');
Route::get('/deleteFeeBookFile', 'AccountsFeeController@deleteFeeBookFile');

// Exceptional student Accounts Fees
Route::resource('accountsExceptionalStudent', 'AccountsExceptionalStudentController');
Route::get('/getAllStudents', 'StudentController@getAllStudents')->name('students');
Route::get('/getExceptionalStudentfeeDetails', 'AccountsExceptionalStudentController@getExceptionalStudentfeeDetails')->name('accounts');

// Accounts Income Statement
Route::get('/incomeStatement', 'AccountsStatementController@incomeStatement')->name('accounts');
Route::get('/studentFeeIncomeStatementReports', 'AccountsStatementController@studentFeeIncomeStatementReports')->name('accounts');
Route::get('/allIncomeStatementReports', 'AccountsStatementController@allIncomeStatementReports')->name('accounts');

Route::get('/accountsCategoryStatementReports', 'AccountsStatementController@accountsCategoryStatementReports')->name('accounts');


// Expense Statement
Route::get('/expenseStatement', 'AccountsStatementController@expenseStatement')->name('accounts');
Route::get('/expenseStatementReports', 'AccountsStatementController@expenseStatementReports')->name('accounts');


// Student Accounts payment
Route::get('/studentPayment', 'AccountsPaymentController@studentPayment');
Route::get('/accountsFeeDetails', 'AccountsPaymentController@accountsFeeDetails');
Route::post('/confirmFeePayment', 'AccountsPaymentController@confirmFeePayment');
Route::get('/accountsFeePaymentHistory', 'AccountsPaymentController@accountsFeePaymentHistory');
Route::post('/accountsFeePaymentHistory', 'AccountsPaymentController@accountsFeePaymentHistoryReports');
Route::get('/exportExcelPaymentHistory/{class_id}/{section_id}/{month}', 'AccountsPaymentController@exportExcelPaymentHistory')->name('exportExcelPaymentHistory');
Route::get('/exportPdfPaymentHistory/{class_id}/{section_id}/{month}', 'AccountsPaymentController@exportPdfPaymentHistory')->name('exportPdfPaymentHistory');


// Expense Category Controller
Route::resource('expensecategory', 'ExpenseCategoryController');
Route::get('/allExpensecategory', 'ExpenseCategoryController@allExpensecategory')->name('allExpensecategory.expensecategory');


// Income Expense Store
Route::resource('incomesExpenses', 'IncomeExpenseStoreController');

// Incomes
Route::get('/createIncomes/{Incomes}', 'IncomeExpenseStoreController@create')->name('accounts');
Route::get('/allIncomes', 'IncomeExpenseStoreController@allIncomes')->name('accounts');
Route::get('/allIncomesHistory', 'IncomeExpenseStoreController@allIncomesHistory')->name('accounts');

// expense
Route::get('/createExpenses/{Expenses}', 'IncomeExpenseStoreController@create')->name('accounts');
Route::get('/allExpenses', 'IncomeExpenseStoreController@allExpenses')->name('accounts');
Route::get('/allExpensesHistory', 'IncomeExpenseStoreController@allExpensesHistory')->name('accounts');


Route::get('/getAllStoreCategory/{category_type}', 'IncomeExpenseStoreController@getAllStoreCategory')->name('accounts');
Route::get('/getAllStoreCategoryItems/{category_id}', 'IncomeExpenseStoreController@getAllStoreCategoryItems')->name('accounts');
Route::get('/getBankAccountNumber/{bank_id}', 'IncomeExpenseStoreController@getBankAccountNumber')->name('accounts');


/* ===== Accounts Management End =========== */


/* ===== Attendance Management Start =========== */

// Attendance Controller

// Student Attendance
Route::get('/importStdattendances', 'AttendanceController@importStdattendances')->name('attendance');
Route::post('/importStdattendances', 'AttendanceController@importStdattendancesProcess')->name('attendance');
Route::get('/studentDailyAttendanceReport', 'AttendanceController@studentDailyAttendanceReport')->name('attendances');
Route::post('/studentDailyAttendanceReport', 'AttendanceController@getStudentDailyAttendanceReport')->name('attendances');
Route::get('/importStdAttendanceMonthly', 'AttendanceController@importStdAttendanceMonthly')->name('attendance');
Route::post('/importStdAttendanceMonthly', 'AttendanceController@importStdAttendanceMonthlyProcess')->name('attendance');
Route::get('/studentMonthlyAttendanceReport', 'AttendanceController@studentMonthlyAttendanceReport')->name('attendances');
Route::post('/studentMonthlyAttendanceReport', 'AttendanceController@getstudentMonthlyAttendanceReport')->name('attendances');


// Teacher Attendance
Route::get('/importTeacherattendances', 'AttendanceController@importTeacherattendances')->name('attendance');
Route::post('/importTeacherattendances', 'AttendanceController@importTeacherattendancesProcess')->name('attendance');
Route::get('/teacherDailyAttendanceReport', 'AttendanceController@teacherDailyAttendanceReport')->name('attendances');
Route::post('/teacherDailyAttendanceReport', 'AttendanceController@getTeacherDailyAttendanceReport')->name('attendances');
Route::get('/importTeacherAttendanceMonthly', 'AttendanceController@importTeacherAttendanceMonthly')->name('attendance');
Route::post('/importTeacherAttendanceMonthly', 'AttendanceController@importTeacherAttendanceMonthlyProcess')->name('attendance');
Route::get('/teacherMonthlyAttendanceReport', 'AttendanceController@teacherMonthlyAttendanceReport')->name('attendances');
Route::post('/teacherMonthlyAttendanceReport', 'AttendanceController@getTeacherMonthlyAttendanceReport')->name('attendances');


// Staff Attendance
Route::get('/importStaffattendances', 'AttendanceController@importStaffattendances')->name('attendance');
Route::post('/importStaffattendances', 'AttendanceController@importStaffattendancesProcess')->name('attendance');
Route::get('/staffDailyAttendanceReport', 'AttendanceController@staffDailyAttendanceReport')->name('attendances');
Route::post('/staffDailyAttendanceReport', 'AttendanceController@getstaffDailyAttendanceReport')->name('attendances');
Route::get('/importStaffAttendanceMonthly', 'AttendanceController@importStaffAttendanceMonthly')->name('attendance');
Route::post('/importStaffAttendanceMonthly', 'AttendanceController@importStaffAttendanceMonthlyProcess')->name('attendance');
Route::get('/staffMonthlyAttendanceReport', 'AttendanceController@staffMonthlyAttendanceReport')->name('attendances');
Route::post('/staffMonthlyAttendanceReport', 'AttendanceController@getstaffMonthlyAttendanceReport')->name('attendances');


/* ===== End Attendance Management  =========== */


/* ===== Start Library Management =========== */


// Book Controller
Route::resource('books', 'BookController');
Route::get('/allBooks', 'BookController@allBooks')->name('allBooks.books');

// Book Request Controller
Route::resource('bookrequests', 'BookRequestController');
Route::post('/allRequests', 'BookRequestController@allRequests')->name('allRequests.bookrequests');


/* ===== End Library Management =========== */


/* ===== Access Management Start =========== */

Route::resource('users', 'UserController');
Route::get('/allUser', 'UserController@allUser')->name('allUser.users');
Route::get('/export', 'UserController@export')->name('export');

Route::resource('permissions', 'PermissionController');
Route::get('/allPermission', 'PermissionController@allPermission')->name('allPermission.permissions');

Route::resource('roles', 'RoleController');
Route::get('/allRole', 'RoleController@allRole')->name('allRole.roles');


/* ===== Access Management End =========== */


/* ===== Settings Start =========== */

// Settings Controller
Route::resource('settings', 'SettingsController');
Route::get('/allSetting', 'SettingsController@allSetting')->name('allSetting.settings');

/* ===== Settings End =========== */


/* ===== Frontend Start =========== */

// Sommelon Application
Route::get('sommelon', 'AdmissionApplicationController@sommelon');
Route::get('/allSommelonApplications', 'AdmissionApplicationController@allSommelonApplications');
Route::get('/viewSommelon/{id}', 'AdmissionApplicationController@viewSommelon');
Route::delete('/deleteSommelon/{id}', 'AdmissionApplicationController@deleteSommelon');


// Notice Board & Lateset News Controller
Route::resource('news', 'NewsController');
Route::get('/allNews', 'NewsController@allNews')->name('allNews.news');

// Slider Controller
Route::resource('sliders', 'SliderController');
Route::get('/allSliders', 'SliderController@allSliders')->name('allSliders.sliders');

// Download Controller
Route::resource('downloads', 'DownloadController');
Route::get('/allDownloads', 'DownloadController@allDownloads')->name('allDownloads.downloads');

// Gallery Controller
Route::resource('galleries', 'GalleryController');
Route::get('/allGalleries', 'GalleryController@allGalleries')->name('allGalleries.galleries');


/* ===== Frontend End =========== */

/* ===== Backup Start =========== */

Route::get('backups', 'BackupController@index');
Route::get('allBackups', 'BackupController@getall')->name('allBackups.backups');
Route::post('backups/db_backup', 'BackupController@db_backup');
Route::post('backups/full_backup', 'BackupController@full_backup');
Route::get('backups/download/{file_name}', 'BackupController@download');
Route::delete('backups/delete/{file_name}', 'BackupController@delete');

/* ===== Backup End =========== */
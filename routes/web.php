<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Admin Routes*/
Route::group(['middleware' => 'App\Http\Middleware\AdminMiddleware'], function()
{
    Route::get('/adminauth', 'PagesController@adminauth');
    Route::get('/masterscheduleview', 'PagesController@masterscheduleview');

    /* TermController */
    Route::get('/manageTerm', 'TermController@index');
    Route::post('/saveTerm', 'TermController@createTerm');
    Route::post('/manageTerm', 'TermController@store');
    Route::post('/searchTerm', 'TermController@searchTerm');
    Route::get('/searchTerm', 'TermController@index');
    Route::post('/deleteTerm', 'TermController@deleteTerm');

    //Route::get('/searchTerm', 'AjaxController@searchTerm');

    /* InstructorController */
    Route::get('/manageInstructor', 'InstructorController@manageInstructor');
    Route::get('/manageInstructor', 'InstructorController@index');
    Route::get('/searchInstructor', 'AjaxController@searchInstructor');
    Route::post('/updateCourse', 'CourseController@updateCourse');
    Route::post('/courseInstructor', 'InstructorController@assign');
    Route::post('/manageInstructor', 'InstructorController@store');
    Route::post('/editInstructor', 'InstructorController@edit');
    Route::post('/showInstructorDetails', 'AjaxController@instructorDetails');
    Route::post('/manageInstructorDelete', 'InstructorController@deleteInstructor');
    Route::post('/deleteCourseInstructor', 'InstructorController@deleteCourseInstructor');

    /* CourseController */
    Route::get('/manageCourse', 'CourseController@manageCourse');
    Route::get('/manageCourse', 'CourseController@index');
    Route::post('/manageCourseStore', 'CourseController@store');
    Route::post('/manageCourseUpdate', 'CourseController@updateCourse');
    Route::post('/manageCourseDelete', 'CourseController@deleteCourse');
    Route::get('/searchCourse','AjaxController@searchCourse');

    /* ScheduleController */
    Route::get('/dragDrop', 'ScheduleController@index');
    Route::post('/dragDrop', 'ScheduleController@displayRoomsByWeek');
    Route::post('/addschedule', 'ScheduleController@store');
    Route::get('/addschedule', 'ScheduleController@index');
    Route::post('/dragDropGetWeeklySchedule', 'AjaxController@getWeeklySchedule');
    Route::get('/selecttermschedule', 'ScheduleController@selectTerm');

    /* AssignController*/
    Route::get('/assign', 'AssignController@index');
    Route::post('/assignCourse', 'AssignController@assignCourse');
    Route::get('/unassignCourse', 'AssignController@unassignCourse');
    Route::post('/getInstructorsForACourse', 'AjaxController@getInstructorsForACourse');
    Route::get('/addschedule', 'ScheduleController@index');
    Route::post('/reassignCourse', 'AssignController@reassignCourse');

    /* IntakeController */
    Route::get('/manageIntake', 'IntakeController@index');
    Route::post('/manageIntake', 'IntakeController@store');
    Route::get('/updateIntake', 'IntakeController@index');
    Route::post('/updateIntake', 'IntakeController@updateIntake');
    Route::post('/manageIntakeDelete', 'IntakeController@deleteIntake');

    /* Propagation Controller */
    Route::post('/getCourseOfferingsByTerm', 'AjaxController@getCourseOfferingsByTerm');
    Route::post('/getWeeklySchedule', 'AjaxController@getWeeklySchedule');
    Route::post('/extend', 'PropagationController@extend');
    Route::get('/propagateschedule', 'PagesController@propagateschedule');

    /* Student Controller */
    Route::get('/manageStudents/', 'StudentController@index');

    /* OnPropFinish Controller */
    Route::get('/propfinish', 'OnPropFinishController@index');
    Route::get('/propfinish/{date}', 'OnPropFinishController@index');

    /* Admin User Controller */
    Route::get('/addUser', 'AddUserController@index');
    Route::post('/addUsers', 'AddUserController@store');
    Route::post('/adminUserDelete', 'AddUserController@deleteAdminUser');
    Route::post('/editUser', 'AddUserController@editUser');

    /* News Routes */
    Route::get('/newsPage', 'NewsController@index');
    Route::post('/newsPage', 'NewsController@store');
    Route::post('/getNewsData', 'NewsController@getNewsData');
    //Route::get('/newsPage', 'NewsController@showImage');
    Route::post('/deleteNews', 'NewsController@deleteNews');
    Route::post('/editNews', 'NewsController@editNews');
});


/* Staff Routes*/
Route::group(['middleware' => 'App\Http\Middleware\StaffMiddleware'], function()
{
    //Route::get('/staffauth', 'PagesController@staffauth');
	//Home page for staff when they login
	Route::get('/staffauth', 'NewsController@index');

});

/* Student Routes*/
Route::group(['middleware' => 'App\Http\Middleware\StudentMiddleware'], function()
{
    //Route::get('/studauth', 'PagesController@studauth');
	//Home page for stud when they login
	Route::get('/studauth', 'NewsController@index');
});

/* Public Pages */

Auth::routes();



Route::get('/', 'PagesController@home');
Route::get('/about', 'PagesController@about');
Route::get('/home', 'HomeController@index');

/* Special routes: Student and Instructor Schedule View*/
Route::get('/selectinstructorschedule', 'ScheduleViewController@selectInstructor');
Route::post('/scheduleinstructor', 'ScheduleViewController@instructorIndex');
Route::get('/selectschedulestudent', 'ScheduleViewController@selectStudent');
Route::post('/schedulestudent', 'ScheduleViewController@studentIndex');
Route::get('/newsPage', 'NewsController@index');
Route::get('/selecttermschedule', 'ScheduleController@selectTerm');
Route::get('/dragDrop', 'ScheduleController@index');
Route::post('/dragDrop', 'ScheduleController@displayRoomsByWeek');
Route::get('/schedulemaster', 'ScheduleViewController@masterindex');
Route::post('/schedulemaster', 'ScheduleViewController@masterindex');

/* Master Schedule View */
Route::get('/schedulemaster', 'ScheduleViewController@masterindex');
Route::post('/schedulemaster', 'ScheduleViewController@masterindex');

/*Substitution Route*/

Route::resource('substitutions', 'SubstitutionController');
Route::post('/substitutions/range', 'SubstitutionController@GetCoursesInRange');
Route::post('/substitutions/replacements', 'SubstitutionController@getAvaliableReplacements');

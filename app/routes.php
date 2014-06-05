<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/** ------------------------------------------
 *  Route model binding
 *  ------------------------------------------
 */
Route::model('user', 'User');
Route::model('role', 'Role');

/** ------------------------------------------
 *  Route constraint patterns
 *  ------------------------------------------
 */
Route::pattern('user', '[0-9]+');
Route::pattern('token', '[0-9a-z]+');

/** ------------------------------------------
 *  Admin Routes
 *  ------------------------------------------
 */
Route::group(array('prefix' => 'admin', 'before' => 'auth'), function()
{

    # User Management
    Route::get('users/{user}/show', 'AdminUsersController@getShow');
    Route::get('users/{user}/edit', 'AdminUsersController@getEdit');
    Route::post('users/{user}/edit', 'AdminUsersController@postEdit');
    Route::get('users/{user}/delete', 'AdminUsersController@getDelete');
    Route::post('users/{user}/delete', 'AdminUsersController@postDelete');
    Route::controller('users', 'AdminUsersController');

    # User Role Management
    Route::get('roles/{role}/show', 'AdminRolesController@getShow');
    Route::get('roles/{role}/edit', 'AdminRolesController@getEdit');
    Route::post('roles/{role}/edit', 'AdminRolesController@postEdit');
    Route::get('roles/{role}/delete', 'AdminRolesController@getDelete');
    Route::post('roles/{role}/delete', 'AdminRolesController@postDelete');
    Route::controller('roles', 'AdminRolesController');

    # Admin Dashboard
    Route::controller('/', 'AdminDashboardController');
});
Route::group(array('before' => 'auth'), function()
{
//Routes for diskspace
Route::get('reports/runusshared', 'ReportController@RunUSShared');
Route::get('reports/runusssdshared', 'ReportController@RunUSSSDShared');
Route::get('reports/runusreseller', 'ReportController@RunUSReseller');
Route::get('reports/runusssdreseller', 'ReportController@RunUSSSDReseller');
Route::get('reports/runicelandshared', 'ReportController@RunIcelandShared');
Route::get('reports/runicelandssdshared', 'ReportController@RunIcelandSSDShared');
Route::get('reports/runicelandreseller', 'ReportController@RunIcelandReseller');

//routes for reseller stats
Route::get('reports/resellerstats', 'ReportController@ResellerStats');

Route::get('reports/functions', 'ReportController@ShowFunctions');
Route::get('reports/dashboard', 'HomeController@showDashboard');
Route::get('reports/results', 'ReportController@ShowResults');
Route::get('reports/jsonresponse', 'CpanelController@JsonResponse');
Route::get('reports/jsonresponsize', 'CpanelController@JsonResponseSize');
Route::get('reports/resetkeys', 'ReportController@ResetKeys');
Route::get('reports/resetdisk', 'ReportController@DropDiskUsage');
Route::get('reports/addservers', 'ReportController@AddServers');
Route::get('reports/graphs', 'ReportController@Graphs');
Route::post('reports/simplesearch', 'ReportController@SimpleSearch');
Route::put('reports/simplesearch', 'ReportController@SimpleSearch');
});
/** ------------------------------------------
 *  Frontend Routes
 *  ------------------------------------------
 */

// User reset routes
Route::get('user/reset/{token}', 'UserController@getReset');
// User password reset
Route::post('user/reset/{token}', 'UserController@postReset');
//:: User Account Routes ::
Route::post('user/{user}/edit', 'UserController@postEdit');

//:: User Account Routes ::
Route::post('user/login', 'UserController@postLogin');

# User RESTful Routes (Login, Logout, Register, etc)
Route::controller('user', 'UserController');

//:: Application Routes ::

# Filter for detect language
Route::when('contact-us','detectLang');

# Contact Us Static Page
Route::get('contact-us', function()
{
    // Return about us page
    return View::make('site/contact-us');
});

# Index Page - Last route, no matches
Route::get('/', 'HomeController@showWelcome');

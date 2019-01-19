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

Route::get('/', function () {
    return view('welcome');
});

// Route::get('test', function () {
//     return view('test');
// });


Route::get('form','FormController@index');
Route::get('form/login','FormController@login_page');
Route::post('form', 'FormController@send');
Route::post('form/login', 'FormController@login');
Route::get('test','TestController@index');
Route::post('test', 'TestController@verify');
Route::post('test/tfalogin', 'FormController@tfalogin');

// Route::get('test', function () {
//     return view('test');
// })->middleware(['auth', '2fa']);

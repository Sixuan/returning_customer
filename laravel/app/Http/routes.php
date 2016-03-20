<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'api'], function () {
    Route::get('faces/{id}', 'FaceController@get');
    Route::put('faces/{id}', 'FaceController@update');
    Route::post('faces', 'FaceController@store');

    Route::get('stores/{id}', 'StoreController@get');
    Route::get('stores/{id}/faces', 'StoreController@load');
    Route::put('stores/{id}', 'StoreController@update');
    Route::post('stores', 'StoreController@store');
    Route::post('stores/{id}/cameras', 'StoreController@addCamera');
    Route::post('stores/login', 'StoreController@login'); //@todo, move this to Auth

    Route::get('persons/{id}', 'PersonController@get');
    Route::get('persons/multi/{id}', 'PersonController@getMulti');
    Route::put('persons/{id}', 'MemberController@update');
    Route::post('persons', 'PersonController@store');

    Route::get('images/{id}', 'ImageController@get');
    Route::put('images/{id}', 'ImageController@update');
    Route::post('images', 'ImageController@store');

    Route::get('transactions/{id}', 'TransactionController@get');
    Route::put('transactions/{id}', 'TransactionController@update');
    Route::post('transactions', 'TransactionController@store');

    Route::post('members', 'MemberController@store');
    Route::put('members/{id}', 'MemberController@update');
    Route::get('members/{id}', 'MemberController@get');

    Route::post('admin/sales', 'SaleController@store');
    Route::delete('admin/sales/{id}', 'SaleController@destroy');
    Route::put('admin/sales/{id}', 'SaleController@update');


    Route::put('admin/persons/{id}', 'MemberController@update');
    Route::get('admin/stores/{id}/persons', 'StoreController@persons');
    Route::get('admin/stores/{id}/sales', 'StoreController@sales');
    Route::get('admin/stores/{id}/visitsAndTrans', 'StoreController@visitsAndTrans');
//    Route::get('admin/stores/{id}/sales', 'ImageController@update'); //@todo get accounts
//    Route::post('admin/stores/{id}/sales', 'ImageController@update'); //@todo create account

//    Route::post('auth', 'Auth\AuthController@create');

});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

});


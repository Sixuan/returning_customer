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


Route::group(['prefix' => 'api', 'middleware' => 'logRequest'], function () {

    //LOGIN
    Route::post('stores/login', 'AccountController@login');

    //Create sale account
    Route::post('admin/stores/{id}/sales', 'AccountController@create');

    //UPDATE RTSP
    Route::put('admin/cameras/{id}', 'StoreController@updateRtsp');

    //GET STORE/CAMERAS
    Route::get('stores/{id}', 'StoreController@get');
    Route::post('stores/{id}/cameras', 'StoreController@addCamera');
    Route::post('stores', 'StoreController@store');

    Route::get('stores', 'StoreController@all');

    Route::post('stores/photo', 'StoreController@photo');


//    Route::get('admin/stores/{id}/sales', 'ImageController@update'); //@todo get accounts
//    Route::post('admin/stores/{id}/sales', 'ImageController@update'); //@todo create account
    //CREATE ACCOUNT(NOT BEING USED)
    Route::post('auth/{storeId}', 'AccountController@create');
    
    Route::group(['middleware' => ['storeAdminAuth']], function () {
        Route::get('admin/stores/{id}/persons', 'StoreController@persons');
        Route::get('admin/stores/{id}/visitsAndTrans', 'StoreController@visitsAndTrans');

        Route::get('admin/stores/{id}/sales', 'SaleController@sales');
        Route::delete('admin/sales/{id}', 'SaleController@destroy');
        Route::put('admin/sales/{id}', 'SaleController@update');
        Route::put('admin/persons/{id}', 'MemberController@update');
        
        Route::delete('admin/store/{id}', 'StoreController@flush');

    });

    Route::group(['middleware' => ['storeAuth']], function () {

        Route::post('members', 'MemberController@store');
        Route::put('members/{id}', 'MemberController@update');
        Route::get('members/{id}', 'MemberController@get');

        Route::get('transactions/{id}', 'TransactionController@get');
        Route::put('transactions/{id}', 'TransactionController@update');
        Route::post('transactions', 'TransactionController@store');

        Route::get('faces/{id}', 'FaceController@get');
        Route::put('faces/{id}', 'FaceController@update');
        Route::post('faces', 'FaceController@store');
        Route::post('faces/persons/{id}', 'FaceController@addPerson');


        Route::get('stores/{id}/faces', 'StoreController@load');
        Route::put('stores/{id}', 'StoreController@update');

        Route::get('persons/{id}', 'PersonController@get');
        Route::get('persons/multi/{id}', 'PersonController@getMulti');
        Route::put('persons/{id}', 'MemberController@update');
        Route::post('persons', 'PersonController@store');

        Route::get('images/{id}', 'ImageController@get');
        Route::put('images/{id}', 'ImageController@update');
        Route::post('images', 'ImageController@store');
        Route::delete('images', 'ImageController@delete');



//    Route::post('admin/sales', 'SaleController@store');


    });

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

Route::group(['middleware' => ['storeAuth']], function () {
});


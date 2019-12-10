<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('cart/{userid}','CartproductController@index');
// Route::get('products','ProductAPIController@index');
// Route::post('products','CartController@store');
// Route::post('products','ProductAPIController@store');
Route::get('products','ProductAPIController@index');
Route::get('coupons','CouponsAPIController@index');
Route::post('billing','BillingAPItest@store');
Route::post('carts','CartAPIController@store');

// Route::get('isLogin','AuthUser@isLogin');
// Route::get('setsession','AuthUser@setSession');










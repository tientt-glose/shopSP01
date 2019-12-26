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

Route::get('products','ProductAPIController@index');
Route::get('coupons','CouponsAPIController@index');
Route::post('billing','BillingAPItest@store');
Route::get('cart/{user_id}','CartAPIController@show');
// Route::post('carts','CartController@storefromAPI');
Route::post('carts','CartAPIController@store');
Route::patch('cart/{user_id}','CartAPIController@update');
Route::delete('cart/{user_id}','CartAPIController@destroy');












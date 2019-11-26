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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/','ProductController@index');
Route::get('/','LandingPageController@index')->name('landing-page');

Route::get('/shop', 'ShopController@index')->name('shop.index');
Route::get('/shop/{product}', 'ShopController@show')->name('shop.show');

Route::get('/cart','CartController@index')->name('cart.index')->middleware('auth');
Route::post('/cart','CartController@store')->name('cart.store')->middleware('auth');
Route::patch('/cart/{product}','CartController@update')->name('cart.update');
Route::delete('/cart/{product}','CartController@destroy')->name('cart.destroy');

Route::get('/checkout','CheckoutController@index')->name('checkout.index')->middleware('auth');
Route::post('/checkout','CheckoutController@store')->name('checkout.store');

Route::post('/coupon', 'CouponsController@store')->name('coupon.store');
Route::delete('/coupon', 'CouponsController@destroy')->name('coupon.destroy');

Route::get('/thankyou','ConfirmationController@index')->name('confirmation.index');


Route::get('empty',function() {
    Cart::destroy();
});
// Cart::add($request->id,$request->name,1,$request->price, ['description' => $request->details,'image' => $request->image]);

Route::get('testAddCart',function() {
    $client = new \GuzzleHttp\Client();
    $url = config('app.add_cart');
    $response=$client->request('POST', $url, [
        'json' => [
            'user_id' => auth()->user()->id,
            'product_id' => 2,
        ]
    ]);
    // return redirect()->route('cart.index')->with('success_message','Item was added to your cart!');

    $data = $response->getBody();
    $data = json_decode($data);
    dd($data);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/product','ProductController@index')->name('product.index');


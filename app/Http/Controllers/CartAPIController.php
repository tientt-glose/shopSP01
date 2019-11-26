<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\CartController;
use App\CartProduct;
use App\CartUser;
use App\Product;

class CartAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cart = CartUser::where('user_id',$request->user_id)->firstOrCreate(['user_id' => $request->user_id]);   
        $cartproduct=CartProduct::where('cart_id',$cart->id)->where('product_id',$request->product_id)->first();
        // For POST method with missing optional data
        if ($cartproduct!=null) {
            if ($cartproduct->quantity==10) {
                $cartproduct=CartProduct::where('cart_id',$cart->id)->where('product_id',$request->product_id)->get();
                $cartproduct->put('message', 'FAIL:Quantity must be between 1 and 10.');
                return $cartproduct->toJson();
            }       
        }
        // Cart::add($request->id,$request->name,1,$request->price, ['description' => $request->details,'image' => $request->image]);
        // ->associate('App\Product');
        CartController::APIaddToCartProductsTables($request);
        $cartproduct=CartProduct::where('cart_id',$cart->id)->where('product_id',$request->product_id)->get();
        $cartproduct->put('message', 'SUCCESS:Item was added to your cart!');
        return $cartproduct->toJson();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

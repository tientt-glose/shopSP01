<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coupon;
use App\CartProduct;
use App\CartUser;

class CouponsController extends Controller
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
        $coupon = Coupon::findByCode($request->coupon_code);

        if (!$coupon) {
            return redirect()->route('checkout.index')->withErrors('Invalid coupon code. Please try again.');
        }

        $cart = CartUser::addToCartUsersTables();
        $cartproduct = CartProduct::getCartByCartID($cart->id);
        session()->put('coupon', [
            'code' => $coupon->code,
            'discount' => $coupon->discount(getSubTotal($cartproduct)),
        ]);

        return redirect()->route('checkout.index')->with('success_message', 'Coupon has been applied!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        session()->forget('coupon');
        return redirect()->route('checkout.index')->with('success_message', 'Coupon has been removed.');
    }
}

<?php

namespace App\Http\Controllers;

use App\CartUser;
use App\CartProduct;

use Illuminate\Http\Request;

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
        $cart = CartUser::addToCartUsersTablesbyUserID($request->user_id);
        $cartproduct = CartProduct::getProductByProductIDinSpecCart($cart->id, $request->product_id);
        // For POST method with missing optional data
        if ($cartproduct != null) {
            if ($cartproduct->quantity == 10) {
                $cartproduct = CartProduct::jSONProductByProductIDinSpecCart($cart->id, $request->product_id);
                $cartproduct->put('message', 'FAIL:Quantity must be between 1 and 10.');
                return $cartproduct->toJson();
            }
        }
        CartController::APIaddToCartProductsTables($request);
        $cartproduct = CartProduct::jSONProductByProductIDinSpecCart($cart->id, $request->product_id);
        $cartproduct->put('message', 'SUCCESS:Item was added to your cart!');
        return $cartproduct->toJson();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {
        $cart = CartUser::addToCartUsersTablesbyUserID($user_id);
        $cartproduct = CartProduct::getCartByCartID($cart->id);
        if ($cartproduct->isEmpty())
            return response()->json(
                [
                    'success' => false,
                    'message' => 'FAIL: Cart empty!'
                ],
                400
            );

        return response()->json(
            [
                'products' => $cartproduct,
                'success' => true,
            ],
            200
        );
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
    public function update(Request $request, $user_id)
    {
        $cart = CartUser::addToCartUsersTablesbyUserID($user_id);
        $cartproduct = CartProduct::getProductByProductIDinSpecCart($cart->id, $request->product_id);
        if ($cartproduct != null) {
            if ($request->quantity > 10 || $request->quantity < 1) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Quantity must be between 1 and 10!'
                    ],
                    400
                );
            }
            $cartproduct->quantity = $request->quantity;
            if ($cartproduct->save()) {
                return response()->json(
                    [
                        'products' => $cartproduct,
                        'success' => true,
                        'message' => 'SUCCESS: Update quantity successfully!'
                    ],
                    200
                );
            } else return response()->json(
                [
                    'success' => false,
                    'message' => 'Fail: UpdateUpdate fail!'
                ],
                400
            );
        } else return response()->json(
            [
                'success' => false,
                'message' => 'Fail: Item does not exist in your cart!'
            ],
            400
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $user_id)
    {
        $cart = CartUser::addToCartUsersTablesbyUserID($user_id);
        $cartproduct = CartProduct::getProductByProductIDinSpecCart($cart->id, $request->product_id);
        if ($cartproduct != null) {
            if ($cartproduct->delete()) {
                return response()->json(
                    [
                        'products' => $cartproduct,
                        'success' => true,
                        'message' => 'SUCCESS: Item was removed from your cart!'
                    ],
                    200
                );
            } else return response()->json(
                [
                    'success' => false,
                    'message' => 'Fail: Remove fail!'
                ],
                400
            );
        } else return response()->json(
            [
                'success' => false,
                'message' => 'Fail: Item does not exist in your cart!'
            ],
            400
        );
    }
}

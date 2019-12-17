<?php

namespace App\Http\Controllers;

use App\Product;
use App\CartUser;
use App\CartProduct;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //Sử dụng thiết kế MVC. Nội dung file code CartController với URI partern /cart (hiển thị ra toàn bộ)
    //sản phẩm trong giỏ hàng. 
    public function index()
    {
        if (isLogin() == false) return redirect(config('app.auth') . '/requirelogin?url=' . config('app.api'));

        //Lấy dữ liệu thông qua model.
        $cart = CartUser::addToCartUsersTables();
        $cartproduct = CartProduct::getCartByCartID($cart->id);
        $mightAlsoLike = Product::MightAlsoLike()->get();

        $url = null;
        if (session()->has('user')) {
            $user = session()->get('user');
            if (array_key_exists("url", $user))
                $url = $user['url'];
        } else $url = null;

        //Dùng dữ liệu render ra view hiển thị cho người dùng.
        return view('cart')->with([
            'mightAlsoLike' => $mightAlsoLike,
            'cartproduct' => $cartproduct,
            'user_id' => session()->get('user')['user_id'],
            'session_id' => session()->get('user')['session_id'],
            'url' => $url
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isLogin() == false) return redirect(config('app.auth') . '/requirelogin?url=' . config('app.api'));

        $cart = CartUser::addToCartUsersTables();
        $cartproduct = CartProduct::getProductByProductIDinSpecCart($cart->id, $request->id);
        // For POST method with missing optional data
        if ($cartproduct != null) {
            if ($cartproduct->quantity == 10) {
                session()->flash('errors', collect(['Quantity must be between 1 and 10.']));
                return redirect()->route('cart.index');
            }
        }
        $this->addToCartProductsTables($request);
        return redirect()->route('cart.index')->with('success_message', 'Item was added to your cart!');
    }

    public function storefromAPI(Request $request)
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
    public function show($session_id, $user_id, $url)
    {
        session(['user' => [
            'user_id' => $user_id,
            'session_id' => $session_id,
            'url' => 'http://' . $url
        ]]);
        $cart = CartUser::addToCartUsersTables();
        $cartproduct = CartProduct::getCartByCartID($cart->id);
        $mightAlsoLike = Product::MightAlsoLike()->get();
        return view('cart')->with([
            'mightAlsoLike' => $mightAlsoLike,
            'cartproduct' => $cartproduct,
            'user_id' => $user_id,
            'session_id' => $session_id,
            'url' => 'http://' . $url
        ]);
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
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|between:1,10'
        ]);

        if ($validator->fails()) {
            session()->flash('errors', collect(['Quantity must be between 1 and 10.']));
            return response()->json(['success' => false], 400);
        }

        $cart = CartUser::addToCartUsersTables();
        $cartproduct = CartProduct::getProductByRowIDinSpecCart($cart->id, $id);
        $cartproduct->quantity = $request->quantity;
        if ($cartproduct->save()) {
            session()->flash('success_message', 'Quantity was updated successfully!');
            return response()->json(['success' => true]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cart = CartUser::addToCartUsersTables();
        $cartproduct = CartProduct::getProductByRowIDinSpecCart($cart->id, $id);
        if ($cartproduct->delete()) {
            return back()->with('success_message', 'Item has been removed!');
        }
    }

    static public function addToCartProductsTables($request)
    {
        // Create a cart
        $cart = CartUser::addToCartUsersTables();

        // Add a product to cart
        $cartproduct = CartProduct::getProductByProductIDinSpecCart($cart->id, $request->id);
        if ($cartproduct === null) {
            CartProduct::create([
                'cart_id' => $cart->id,
                'product_id' => $request->id,
                'quantity' => 1,
                'name' => $request->name,
                'price' => $request->price
            ]);
        } else {
            $cartproduct->quantity += 1;
            $cartproduct->save();
        }
    }

    static public function APIaddToCartProductsTables($request)
    {
        // Create a cart
        $cart = CartUser::addToCartUsersTablesbyUserID($request->user_id);

        // Add a product to cart
        $cartproduct = CartProduct::getProductByProductIDinSpecCart($cart->id, $request->product_id);
        $product = Product::getProductById($request->product_id);
        if ($cartproduct === null) {
            CartProduct::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => 1,
                'name' => $product->name,
                'price' => $product->price
            ]);
        } else {
            $cartproduct->quantity += 1;
            $cartproduct->save();
        }
    }
}

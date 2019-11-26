<?php

namespace App\Http\Controllers;


use App\Product;
use App\CartUser;
use App\CartProduct;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(Cart::instance('1')->content());
        
        // $cart = CartUser::where('user_id',auth()->user()->id)->first();
        // dd($cart);
        $cart = $this->addToCartUsersTables();
        $cartproduct=CartProduct::where('cart_id',$cart->id)->get();
        $mightAlsoLike = Product::MightAlsoLike()->get();
        return view('cart')->with([
            'mightAlsoLike' => $mightAlsoLike,
            'cartproduct' => $cartproduct,
            ]);
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
        $cart = $this->addToCartUsersTables();   
        $cartproduct=CartProduct::where('cart_id',$cart->id)->where('product_id',$request->id)->first();
        // For POST method with missing optional data
        if ($cartproduct!=null) {
            if ($cartproduct->quantity==10) {
                session()->flash('errors', collect(['Quantity must be between 1 and 10.']));
                return redirect()->route('cart.index');
            }       
        }
          
        // Cart::add($request->id,$request->name,1,$request->price, ['description' => $request->details,'image' => $request->image]);
        // ->associate('App\Product');
        $this->addToCartProductsTables($request);
        return redirect()->route('cart.index')->with('success_message','Item was added to your cart!');
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
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|between:1,10'
        ]);

        if ($validator->fails()) {
            session()->flash('errors', collect(['Quantity must be between 1 and 10.']));
            return response()->json(['success' => false], 400);
        }

        $cart = $this->addToCartUsersTables();   
        $cartproduct=CartProduct::where('cart_id',$cart->id)->where('id',$id)->firstOrFail();
        // Cart::update($id, $request->quantity);
        $cartproduct->quantity=$request->quantity;
        if ($cartproduct->save()){
            session()->flash('success_message','Quantity was updated successfully!');
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
        $cart = $this->addToCartUsersTables();
        $cartproduct=CartProduct::where('cart_id',$cart->id)->where('id',$id)->firstOrFail();
        if ($cartproduct->delete()){
        return back()->with('success_message', 'Item has been removed!');
        }
    }

    static public function addToCartUsersTables()
    {
        $cart = CartUser::where('user_id',auth()->user()->id)->firstOrCreate(['user_id' => auth()->user()->id]);
        return $cart;
    }
    
    static public function addToCartProductsTables($request)
    {
        // Create a cart
        $cart = CartUser::where('user_id',auth()->user()->id)->firstOrCreate(['user_id' => auth()->user()->id]);

        // Add a product to cart
        $cartproduct=CartProduct::where('cart_id',$cart->id)->where('product_id',$request->id)->first();
        if ($cartproduct === null) {
            CartProduct::create([
                'cart_id' => $cart->id,
                'product_id' => $request->id,
                'quantity' => 1,
                'name' => $request->name,
                'price' => $request->price
            ]);
        }
        else {
            $cartproduct->quantity+=1;
            $cartproduct->save();
        }
        // dd($cart->id);
        // Insert into order_product table
            // CartProduct::create([
            //     'cart_id' => $cart->id,
            //     'product_id' => $request->id,
            //     'quantity' => 1,
            //     'name' => $request->name,
            //     'price' => $request->price
            // ]);
    }

    static public function APIaddToCartProductsTables($request)
    {
        // Create a cart
        $cart = CartUser::where('user_id',$request->user_id)->firstOrCreate(['user_id' => $request->user_id]);

        // Add a product to cart
        $cartproduct=CartProduct::where('cart_id',$cart->id)->where('product_id',$request->product_id)->first();
        $product=Product::where('id',$request->product_id)->first();
        if ($cartproduct === null) {
            CartProduct::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => 1,
                'name' => $product->name,
                'price' => $product->price
            ]);
        }
        else {
            $cartproduct->quantity+=1;
            $cartproduct->save();
        }
        // dd($cart->id);
        // Insert into order_product table
            // CartProduct::create([
            //     'cart_id' => $cart->id,
            //     'product_id' => $request->id,
            //     'quantity' => 1,
            //     'name' => $request->name,
            //     'price' => $request->price
            // ]);
    }
}

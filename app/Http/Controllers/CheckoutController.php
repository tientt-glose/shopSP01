<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CheckoutRequest;
use App\Http\Controllers\CartController;
use Gloudemans\Shoppingcart\Facades\Cart;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Cartalyst\Stripe\Exception\CardErrorException;
use App\CartProduct;
use App\CartUser;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = CartController::addToCartUsersTables();
        $cartproduct= CartProduct::where('cart_id',$cart->id)->get();

        if (getQuantitybyCartProduct($cartproduct) == 0) {
            return redirect()->route('shop.index');
        }

        return view('checkout')->with([
            'discount' => getNumbers()->get('discount'),
            'newSubtotal' => getNumbers()->get('newSubtotal'),
            'newTax' => getNumbers()->get('newTax'),
            'newTotal' => getNumbers()->get('newTotal'),
            'cartproduct' => $cartproduct
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
    public function store(CheckoutRequest $request)
    {
        $cart = CartController::addToCartUsersTables();
        $cartproduct= CartProduct::where('cart_id',$cart->id)->select('product_id','quantity','name','price')->get()->toJson(JSON_UNESCAPED_UNICODE);

        // dd($cartproduct);
        // $contents = Cart::content()->map(function ($item) {
        //     return $item->name.', '.$item->qty;
        // })->values()->toJson();
       
        $full_address=$request->address.', '.$request->province.', '.$request->city;
        try {
            $charge = Stripe::charges()->create([
                'amount' => getNumbers()->get('newTotal'),
                'currency' => 'VND',
                'source' => $request->stripeToken,
                'description' => 'Order',
                'receipt_email' => $request->email,
                'metadata' => [
                    'contents' => $cartproduct,
                    'quantity' => getQuantity(),
                    'discount' => collect(session()->get('coupon'))->toJson(),
                ],
            ]);

            $cartproducts= CartProduct::where('cart_id',$cart->id)->select('product_id','quantity','name','price')->get();

            // $client = new \GuzzleHttp\Client();
            // $url = config('app.create_billing');
            // $response = $client->request('POST', $url, [
            //     'json' => [
            //         'user'=> [
            //             'id' => auth()->user()->id,
            //             'name' => $request->name,
            //             'address' => $full_address,
            //             'phone' => $request->phone,
            //         ],
            //         'products'=> $cartproducts,
            //         // [
            //         //     'id' => 1266,
            //         //     'amount' => 1,
            //         //     'name' => 'MacBook Pro',
            //         //     'price' => (int) 25000000,
            //         //     'subTotal' => (int) 25000000
            //         // ],
            //         // 'delivery'=> [
            //         //     'date' =>'19-11-23 10:00:00',
            //         //     'status' => 'On going'
            //         // ],
            //         'payment'=>[
            //             'type' => 'VISA',
            //             'status' => 'Cancel'
            //         ],
            //         'status' => 'Success',
            //         'discount' => getNumbers()->get('discount'),
            //         'totalValue' => getNumbers()->get('newTotal'),
            //     ]
            // ]);	

            // // Testing respon
            // // $data = $response->getBody()->getContents();
            // $data = $response->getBody();
            // $data = json_decode($data);
            // dd($data);

            $this->updateToCartUsersTables($request);
            $cart->delete();
            // Cart::instance('default')->destroy();
            session()->forget('coupon');
            // return back()->with('success_message', 'Thank you! Your payment has been successfully accepted!');
            return redirect()->route('confirmation.index')->with('success_message', 'Thank you! Your payment has been successfully accepted!');
        } catch (CardErrorException $e) {
            return back()->withErrors('Error! ' . $e->getMessage());
        }
    }

    
    protected function updateToCartUsersTables($request)
    {
        // Insert into orders table
        $cart = CartController::addToCartUsersTables();
        $cart->billing_email = $request->email;
        $cart->billing_name = $request->name;
        $cart->billing_address = $request->email;
        $cart->billing_city = $request->city;
        $cart->billing_province = $request->province;
        $cart->billing_phone = $request->phone;
        $cart->billing_name_on_card= $request->name_on_card;
        $cart->billing_discount = getNumbers()->get('discount');
        $cart->billing_discount_code = getNumbers()->get('code');
        $cart->billing_subtotal = getNumbers()->get('newSubtotal');
        $cart->billing_tax = getNumbers()->get('newTax');
        $cart->billing_total = getNumbers()->get('newTotal');
        $cart->save();
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

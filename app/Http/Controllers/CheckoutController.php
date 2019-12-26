<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CheckoutRequest;
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
        if (isLogin() == false) return redirect(config('app.auth') . '/requirelogin?url=' . config('app.api'));

        $cart = CartUser::addToCartUsersTables();
        $cartproduct = CartProduct::getCartByCartID($cart->id);

        if (getQuantitybyCartProduct($cartproduct) == 0) {
            return redirect()->route('cart.index');
        }

        $delivery = getDeliveryUnits();

        if (!(session()->has('delivery'))) {
            session(['delivery' => [
                'id' => 0,
                'delivery_id' => $delivery['0']['id'],
                'name' => $delivery['0']['name'],
                'base_fee' => $delivery['0']['base_fee'],
            ]]);
        }

        $payment = getUserPayment();
        if (!(session()->has('payment'))) {
            if ($payment != null) {
                session(['payment' => [
                    'type' => 'card',
                    'card_id' => $payment[0]['card_id'],
                    'card_number' => $payment[0]['card_number'],
                ]]);
            }
            else {
                session(['payment' => [
                    'type' => 'COD',
                    'card_id' => null,
                    'card_number' => null,
                ]]);
            }
        }


        return view('checkout')->with([
            'discount' => getNumbers()->get('discount'),
            'newSubtotal' => getNumbers()->get('newSubtotal'),
            'newTax' => getNumbers()->get('newTax'),
            'newTotal' => getNumbers()->get('newTotal'),
            'shipping' => getNumbers()->get('shipping'),
            'cartproduct' => $cartproduct,
            'delivery' => $delivery,
            'payment' => $payment
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CheckoutRequest $request)
    {
        $cart = CartUser::addToCartUsersTables();
        $cartproducts = CartProduct::where('cart_id', $cart->id)->select('product_id', 'quantity')->get();
        $full_address = $request->address . ', ' . $request->province . ', ' . $request->city;

        $client = new \GuzzleHttp\Client();
        $url = config('app.create_billing');
        $response = $client->request('POST', $url, [
            'json' => [
                'user' => [
                    'id' => session()->get('user')['user_id'],
                    'name' => $request->name,
                    'address' => $full_address,
                    'phone' => $request->phone,
                ],
                'products' => $cartproducts,
                'payment' => [
                    'type' => session()->get('payment')['type'],
                    'status' => (session()->get('payment')['type'] == "COD") ? 'Pending' : 'Cancel'
                ],
                'deliveryUnitId' => session()->get('delivery')['delivery_id'],
                'value' => [
                    'discount' => getNumbers()->get('discount'),
                    'shipping' => getNumbers()->get('shipping'),
                    'totalValue' => getNumbers()->get('newTotal'),
                    'subTotal' => getNumbers()->get('subtotal'),
                    'tax' => getNumbers()->get('newTax')
                ]
            ]
        ]);

        // // Testing respon
        // $data = $response->getBody()->getContents();
        // // $data = $response->getBody();
        // echo $data;
        // // $data = json_decode($data);
        // dd($response);

        // $this->updateToCartUsersTables($request, $full_address);
        $cart->delete();
        session()->forget('coupon');
        session()->forget('delivery');
        session()->forget('payment');
        return redirect()->route('confirmation.index')->with('success_message', 'Thank you! Your payment has been successfully accepted!');
    }


    protected function updateToCartUsersTables($request, $full_address)
    {
        // Insert into orders table
        $cart = CartUser::addToCartUsersTables();
        // $cart->billing_email = $request->email;
        $cart->billing_name = $request->name;
        $cart->billing_address = $full_address;
        $cart->billing_city = $request->city;
        $cart->billing_province = $request->province;
        $cart->billing_phone = $request->phone;
        // $cart->billing_name_on_card= $request->name_on_card;
        $cart->billing_discount = getNumbers()->get('discount');
        $cart->billing_discount_code = getNumbers()->get('code');
        $cart->billing_subtotal = getNumbers()->get('newSubtotal');
        $cart->billing_tax = getNumbers()->get('newTax');
        $cart->billing_total = getNumbers()->get('newTotal');
        $cart->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDelivery(Request $request, $id)
    {
        $delivery = getDeliveryUnits();
        session(['delivery' => [
            'id' => $request->delivery_id,
            'delivery_id' => $delivery[$request->delivery_id]['id'],
            'name' => $delivery[$request->delivery_id]['name'],
            'base_fee' => $delivery[$request->delivery_id]['base_fee'],
        ]]);
        session()->flash('success_message', 'Delivery unit was updated successfully!');
        return response()->json(['success' => true]);
    }

    public function updatePay(Request $request, $id)
    {
        $payment = getUserPayment();
        if ($request->type == "COD") {
            session(['payment' => [
                'type' => 'COD',
                'card_id' => null,
                'card_number' => null,
            ]]);
        } elseif ($request->type == "Other") {
            session(['payment' => [
                'type' => 'Card',
                'card_id' => null,
                'card_number' => null,
            ]]);
        } else {
            foreach ($payment as $pay) {
                if ($pay['card_id'] == $request->type) {
                    $card = $pay;
                }
            }
            session(['payment' => [
                'type' => 'Card',
                'card_id' => $card['card_id'],
                'card_number' => $card['card_number'],
            ]]);
        }

        session()->flash('success_message', 'Payment was updated successfully!');
        return response()->json(['success' => true]);
    }
}

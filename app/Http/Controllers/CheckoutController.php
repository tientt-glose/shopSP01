<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CheckoutRequest;
use Gloudemans\Shoppingcart\Facades\Cart;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Cartalyst\Stripe\Exception\CardErrorException;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Cart::instance('default')->count() == 0) {
            return redirect()->route('shop.index');
        }

        return view('checkout')->with([
            'discount' => getNumbers()->get('discount'),
            'newSubtotal' => getNumbers()->get('newSubtotal'),
            'newTax' => getNumbers()->get('newTax'),
            'newTotal' => getNumbers()->get('newTotal'),
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
        // dd($request->all());
        $contents = Cart::content()->map(function ($item) {
            return $item->name.', '.$item->qty;
        })->values()->toJson();
        $contentss=Cart::content()->groupBy('name')->toArray();
       
        // $contentss = Cart::content()->map(function ($item) {
        //     return $item->name.', '.$item->qty;
        // })->values();
        $full_address=$request->address.', tien'.$request->province.', '.$request->city;
        try {
            $charge = Stripe::charges()->create([
                'amount' => getNumbers()->get('newTotal'),
                'currency' => 'VND',
                'source' => $request->stripeToken,
                'description' => 'Order',
                'receipt_email' => $request->email,
                'metadata' => [
                    'contents' => $contents,
                    'quantity' => Cart::instance('default')->count(),
                    'discount' => collect(session()->get('coupon'))->toJson(),
                ],
            ]);

            // $client = new \GuzzleHttp\Client();
            // $url = config('app.create_billing');
            // $response = $client->request('POST', $url, [
            //     'json' => [
            //         'user_id'=> auth()->user()->id,
            //         'billing_email' => $request->email,
            //         'billing_name' => $request->name,
            //         'billing_address' => $full_address,
            //         'billing_phone' => $request->phone,
            //         'billing_name_on_card' => $request->name_on_card ?? null,
            //         'billing_content' => $contents,
            //         'billing_discount' => getNumbers()->get('discount'),
            //         'billing_discount_code' => getNumbers()->get('code'),
            //         'billing_subtotal' => getNumbers()->get('newSubtotal'),
            //         'billing_tax' => getNumbers()->get('newTax'),
            //         'billing_total' => getNumbers()->get('newTotal'),
            //         'payment_gateway' => 'Card',
            //     ]
            // ]);

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
            //         'products'=> [
            //             'id' => 1266,
            //             'amount' => 1,
            //             'name' => 'MacBook Pro',
            //             'price' => (int) 25000000,
            //             'subTotal' => (int) 25000000
            //         ],
            //         'delivery'=> [
            //             'date' =>'19-11-23 10:00:00',
            //             'status' => 'On going'
            //         ],
            //         'payment'=>[
            //             'type' => 'VISA',
            //             'status' => 'Cancel'
            //         ],
            //         'status' => 'Success',
            //         'discount' => getNumbers()->get('discount'),
            //         'total' => getNumbers()->get('newTotal'),
            //     ]
            // ]);
            
            // "{
            //     ""user"": {
            //         ""id"": 93,
            //         ""name"": ""Greer Mcintyre"",
            //         ""address"": ""384 Williamsburg Street, Tuttle, Virgin Islands (British)}"",
            //         ""phone"": ""+84 (837) 555-3648""
            //     },
            //     ""products"": [
            //         {
            //             ""id"": 1266,
            //             ""amount"": 2,
            //             ""name"": ""aliquip duis aute sint"",
            //             ""price"": 380000,
            //             ""subTotal"": 760000
            //         },
            //         {
            //             ""id"": 2464,
            //             ""amount"": 3,
            //             ""name"": ""pariatur id id occaecat"",
            //             ""price"": 124000,
            //             ""subTotal"": 372000
            //         }
            //     ],
            //     ""delivery"": {
            //         ""date"": ""20-07-14 07:56:00"",
            //         ""status"": ""esse cupidatat in sint tempor""
            //     },
            //     ""payment"": {
            //         ""type"": ""VISA"",
            //         ""status"": ""Cancel""
            //     },
            //     ""status"": ""Success"",
            //     ""discount"": 42000,
            //     ""totalValue"": 1090000,
            //     ""warranty"": ""magna aliquip sunt sit commodo""
            // }"		

            // Testing respon
            // $data = $response->getBody()->getContents();
            // // $data = $response->getBody();
            // // $data = json_decode($data);
            // dd($data);

            Cart::instance('default')->destroy();
            session()->forget('coupon');
            // return back()->with('success_message', 'Thank you! Your payment has been successfully accepted!');
            // return redirect()->route('confirmation.index')->with('success_message', 'Thank you! Your payment has been successfully accepted!');
        } catch (CardErrorException $e) {
            return back()->withErrors('Error! ' . $e->getMessage());
        }
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

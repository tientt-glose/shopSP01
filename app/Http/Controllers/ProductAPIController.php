<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Product;
use App\Http\Resources\Product as ProductResource;
use GuzzleHttp\Client;
use Gloudemans\Shoppingcart\Facades\Cart;

class ProductAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Get products
        // Product::truncate();
        $client = new \GuzzleHttp\Client();
        $url = config('app.add_product');
        $response = $client->get($url);
        $data = $response->getBody();
        $data = json_decode($data,true);
        // dd($data->data);
        foreach($data['data'] as $i)
        {
            unset($i['created_at']);
            unset($i['updated_at']);
            Product::insert($i);
            // var_dump($i);
            // echo '<br/>';
        }
        return "Success add!";
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
        // Cart::add($request->id,$request->name,1,$request->price, ['description' => $request->details,'image' => $request->image]);
        session()->put('cartt', [
            'name' => $request->name,
            'price' => $request->price,
        ]);
        return session()->get('cartt');
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

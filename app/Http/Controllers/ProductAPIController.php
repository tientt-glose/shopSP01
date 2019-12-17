<?php

namespace App\Http\Controllers;

use App\Product;

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
        $data = json_decode($data, true);
        // dd($data->data);
        foreach ($data['data'] as $i) {
            unset($i['created_at']);
            unset($i['updated_at']);
            Product::insert($i);
            // var_dump($i);
            // echo '<br/>';
        }
        return "Success add!";
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Resources\Product as ProductResource;
use GuzzleHttp\Client;
use App\Product;
use PhpParser\Node\Stmt\Foreach_;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // // Get products
        // $client = new Client([
        //     // Base URI is used with relative requests
        //     'base_uri' => 'http://localhost/shopSP01/public/api/',
        //     // You can set any number of default request options.
        //     'timeout'  => 2.0,
        // ]);
        // $response = $client->request('GET', 'products');
        // $data = $response->getBody();
        // $data = json_decode($data);
        // // return $data;
        // // dd($data);
        // return Product::make($data)->resolve();
        // return 'something';
        Product::truncate();
        $client = new \GuzzleHttp\Client();
        $url = config('app.add_product');
        // $response=$client->request('POST', $url, [
        //     'json' => [
        //         'user_id' => 1,
        //         'id' => 1,
        //         'name' => 'MacBook Pro',
        //         'price' => (int) 25000000,
        //         'details' => '15 inch, 1TB SSD, 32GB RAM',
        //         'image' => 'https://i.imgur.com/xS1NwjK.jpg',
        //     ]
        // ]);
        $response = $client->get($url);
        $data = $response->getBody();
        $data = json_decode($data,true);
        // dd($data->data);
        foreach($data['data'] as $i)
        {
            unset($i['created_at']);
            unset($i['updated_at']);
            Product::insert($i);
            var_dump($i);
            echo '<br/>';
        }
        // foreach($data['data'] as $i)
        //     {
        //         echo $i['name'].'<br/>';
        //     }
        // dd($data);
        //Get products
        // $products = Product::paginate(2);

        // return ProductResource::collection($products);
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
        //
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

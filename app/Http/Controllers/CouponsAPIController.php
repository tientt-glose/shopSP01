<?php

namespace App\Http\Controllers;

use App\Coupon;

class CouponsAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Coupon::truncate();
        $client = new \GuzzleHttp\Client();
        $url = config('app.add_coupon');
        $response = $client->get($url);
        $data = $response->getBody();
        $data = json_decode($data, true);
        // dd($data->data);
        // var_dump($data['data'][0]['id']);
        foreach ($data['data'] as $i) {
            unset($i['expiry_date']);
            unset($i['status']);
            $i = change_key($i, 'coupon_code', 'code');
            $i = change_key($i, 'amount_type', 'type');

            Coupon::insert($i);
            var_dump($i);
            echo '<br/>';
        }
    }
}

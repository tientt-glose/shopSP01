<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function presentPrice()
    {
        $fmt = new \NumberFormatter('vi-VN', \NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($this->price, "VND");

        #en-US USD
        #vi-VN VND
    }

    public function scopeMightAlsoLike($query)
    {
        return $query->inRandomOrder()->take(4);
    }

    public function getImgById($id)
    {
        return Product::where('id', $id)->select('image')->get();
    }

    public static function getProductById($id)
    {
        return Product::where('id', $id)->first();
    }
}

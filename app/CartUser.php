<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartUser extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function cartproducts()
    {
        return $this->hasMany('App\CartProduct');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product')->withPivot('quantity');
    }

    protected $fillable = [
        'user_id', 'billing_email', 'billing_name', 'billing_address', 'billing_city',
        'billing_province', 'billing_postalcode', 'billing_phone', 'billing_name_on_card', 'billing_discount',
        'billing_discount_code', 'billing_subtotal', 'billing_tax', 'billing_total', 'error',
    ];

    public static function addToCartUsersTables()
    {
        return CartUser::where('user_id', session()->get('user')['user_id'])->firstOrCreate(['user_id' => session()->get('user')['user_id']]);
    }

    public static function addToCartUsersTablesbyUserID($user_id)
    {
        return CartUser::where('user_id', $user_id)->firstOrCreate(['user_id' => $user_id]);
    }
}

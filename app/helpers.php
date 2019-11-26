<?php
// use Money\Currencies\ISOCurrencies;
// use Money\Currency;
// use Money\Formatter\IntlMoneyFormatter;
// use Money\Money;
use App\CartProduct;
use App\CartUser;


function presentPrice($price)
{
    // $money = new Money($price, new Currency('USD'));
    // $currencies = new ISOCurrencies();
    // $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    // $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
    // return $moneyFormatter->format($money);
    $fmt = new NumberFormatter('vi-VN', NumberFormatter::CURRENCY);
    return $fmt->formatCurrency($price, "VND");

    #en-US USD
    #vi-VN VND
}

function getNumbers()
{
    $cart = CartUser::where('user_id', auth()->user()->id)->first();
    $cartproduct = CartProduct::where('cart_id', $cart->id)->get();
    $tax = config('cart.tax') / 100;
    $discount = session()->get('coupon')['discount'] ?? 0;
    $code = session()->get('coupon')['code'] ?? null;
    $newSubtotal = (getSubTotal($cartproduct) - $discount);

    if ($newSubtotal < 0) {
        $newSubtotal = 0;
    }
    $newTax = $newSubtotal * $tax;
    $newTotal = $newSubtotal + $newTax + config('app.ship');

    return collect([
        'tax' => $tax,
        'discount' => $discount,
        'code' => $code,
        'newSubtotal' => $newSubtotal,
        'newTax' => $newTax,
        'newTotal' => $newTotal,
    ]);
}

function change_key($array, $old_key, $new_key)
{

    if (!array_key_exists($old_key, $array))
        return $array;

    $keys = array_keys($array);
    $keys[array_search($old_key, $keys)] = $new_key;

    return array_combine($keys, $array);
}

function getSubTotal($cartproduct)
{
    $subTotal = 0;
    foreach ($cartproduct as $item) {
        $eachItemTotal = $item->price * $item->quantity;
        $subTotal += $eachItemTotal;
    }
    return $subTotal;
}

function getTax($cartproduct)
{
    $subTotal = getSubTotal($cartproduct);
    $tax = $subTotal * config('app.tax');
    return $tax;
}

function getTotal($cartproduct)
{
    $subTotal = getSubTotal($cartproduct);
    $tax = $subTotal * config('app.tax');
    return $subTotal + $tax + config('app.ship');
}

function getQuantity()
{
    $qty = 0;
    if (auth()->user()) {
        $cart = CartUser::where('user_id', auth()->user()->id)->first();
        if ($cart!=null){
        $cartproduct = CartProduct::where('cart_id', $cart->id)->get();
        foreach ($cartproduct as $item) {
            $qty += $item->quantity;
        }
    }
    }
    return $qty;
}

function getQuantitybyCartProduct($cartproduct)
{
    $qty = 0;
    foreach ($cartproduct as $item) {
        $qty += $item->quantity;
    }
    return $qty;
}

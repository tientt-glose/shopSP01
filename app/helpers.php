<?php
// use Money\Currencies\ISOCurrencies;
// use Money\Currency;
// use Money\Formatter\IntlMoneyFormatter;
// use Money\Money;


function presentPrice($price)
{
    // $money = new Money($price, new Currency('USD'));
    // $currencies = new ISOCurrencies();
    // $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
    // $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
    // return $moneyFormatter->format($money);
    $fmt = new NumberFormatter( 'vi-VN', NumberFormatter::CURRENCY );
    return $fmt->formatCurrency($price, "VND");

    #en-US USD
    #vi-VN VND
}

function getNumbers()
{
    $tax = config('cart.tax') / 100;
    $discount = session()->get('coupon')['discount'] ?? 0;
    $code = session()->get('coupon')['code'] ?? null;
    $newSubtotal = (Cart::subtotal() - $discount);

    $tax = config('cart.tax') / 100;
    $discount = session()->get('coupon')['discount'] ?? 0;
    $code = session()->get('coupon')['code'] ?? null;
    $newSubtotal = (Cart::subtotal() - $discount);
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

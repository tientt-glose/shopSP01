<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public static function findByCode($code)
    {
        return self::where('code', $code)->first();
    }
    public function discount($total)
    {
        if ($this->type == 'Fixed') {
            return $this->amount;
        } elseif ($this->type == 'Percentage') {
            return round(($this->amount / 100) * $total);
        } else {
            return 0;
        }
    }
}

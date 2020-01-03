<?php

namespace App\Constants;

use Illuminate\Database\Eloquent\Model;

class AttackStrategyConstants extends Model
{
    public static function getRandomID() {
        return 1;
    }

    public static function getWeakestID() {
        return 2;
    }

    public static function getStrongestID() {
        return 3;
    }
}

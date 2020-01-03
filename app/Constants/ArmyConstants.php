<?php

namespace App\Constants;

use Illuminate\Database\Eloquent\Model;

class ArmyConstants extends Model
{
    public static function getAliveStatusID() {
        return 0;
    }

    public static function getDeadStatusID() {
        return 1;
    }
}

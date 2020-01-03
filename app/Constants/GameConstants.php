<?php

namespace App\Constants;

use Illuminate\Database\Eloquent\Model;

class GameConstants extends Model
{
    public static function getWaitingStatusID() {
        return 0;
    }

    public static function getInProgressStatusID() {
        return 1;
    }

    public static function getCompletedStatusID() {
        return 2;
    }

    public static function getMaxActiveGames() {
        return 5;
    }

    public static function getMinToStart() {
        return 5;
    }
}

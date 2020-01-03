<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'game';
    protected $guarded = [];

    public function armies() {
        return $this->hasMany('App\Models\Army');
    }
}

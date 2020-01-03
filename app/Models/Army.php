<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Army extends Model
{
    protected $table = 'army';
    protected $guarded = [];

    public function calculateAliveUnits($damage_dealt)
    {
        $total_damage = $this->damage_received + $damage_dealt;
        if ($total_damage > $this->units) $total_damage = $this->units;
        return ceil($this->units - $total_damage);
    }
}

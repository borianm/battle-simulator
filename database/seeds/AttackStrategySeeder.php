<?php

use App\Constants\AttackStrategyConstants;
use App\Models\AttackStrategy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttackStrategySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attack_strategy')->insert([
            [
                'id' => AttackStrategyConstants::getRandomID(),
                'name' => 'Random'
            ],
            [
                'id' => AttackStrategyConstants::getWeakestID(),
                'name' => 'Weakest'
            ],
            [
                'id' => AttackStrategyConstants::getStrongestID(),
                'name' => 'Strongest'
            ]
        ]);
    }
}

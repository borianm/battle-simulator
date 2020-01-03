<?php

namespace App\Helpers;

use App\Models\Army;
use App\Models\BattleLog;
use App\Constants\ArmyConstants;
use App\Constants\GameConstants;
use App\Constants\AttackStrategyConstants;

class GameHelper
{
    private $game, $attacking_army, $attacked_army, $damage_dealt, $attacked_army_alive_units;

    public function __construct($game)
    {
        $this->game = $game;
    }

    public function startGame()
    {
        $army_count = Army::where('game_id', $this->game->id)->count();
        if ($army_count >= GameConstants::getMinToStart()) {
            // enough armies in game to start
            $this->game->update([
                'status' => GameConstants::getInProgressStatusID()
            ]);
            BattleLog::create([
                'game_id' => $this->game->id,
                'log_text' => 'Igra započeta!'
            ]);
            return response()->json([
                'message' => 'Uspešno započeta igra!'
            ]);
        } else {
            return response()->json([
                'message' => 'Nema dovoljno vojski u igri.'
            ], 422);
        }
    }

    public function runAttack()
    {
        $this->checkForNewTurn();
        if ($this->isAttackSuccessful()) {
            $this->loadAttackedArmy();
            $this->loadDamageDealt();
            sleep(0.01 * $this->attacking_army->alive_units);
            $this->attacked_army_alive_units = $this->attacked_army->calculateAliveUnits($this->damage_dealt);
            if ($this->attacked_army_alive_units == 0) {
                $this->handleArmyKilled();
            } else {
                $this->handleKillsOnly();
            }
            $this->attacking_army->update([
                'turn_made' => 1
            ]);
            return response()->json([
                'message' => 'Napad izvršen!'
            ]);
        } else {
            return $this->handleUnsuccessfulAttack();
        }
    }

    private function checkForNewTurn()
    {
        $armies = Army::where('game_id', $this->game->id)->where('turn_made', 0)->where('status', ArmyConstants::getAliveStatusID())->orderBy('id', 'DESC')->get();
        if ($armies->count() == 0) {
            Army::where('game_id', $this->game->id)->update([
                'turn_made' => 0
            ]);
        }
    }

    private function isAttackSuccessful()
    {
        $this->attacking_army = Army::where('game_id', $this->game->id)->where('turn_made', 0)->where('status', ArmyConstants::getAliveStatusID())->orderBy('id', 'DESC')->first();
        $random_int = random_int(1, 100);
        return $random_int <= $this->attacking_army->alive_units;
    }

    private function handleUnsuccessfulAttack()
    {
        $this->attacking_army->update([
            'turn_made' => 1
        ]);
        $army_name = $this->attacking_army->name;
        BattleLog::create([
            'game_id' => $this->game->id,
            'log_text' => "Vojska $army_name je pokušala i nije uspela da izvrši napad."
        ]);
        return response()->json([
            'message' => 'Napad nije uspeo!'
        ]);
    }

    private function loadAttackedArmy()
    {
        if ($this->attacking_army->attack_strategy_id == AttackStrategyConstants::getRandomID()) {
            $this->attacked_army = Army::where('game_id', $this->game->id)->where('status', ArmyConstants::getAliveStatusID())->where('id', '<>', $this->attacking_army->id)
                ->inRandomOrder()->first();
        } else if ($this->attacking_army->attack_strategy_id == AttackStrategyConstants::getStrongestID()) {
            $this->attacked_army = Army::where('game_id', $this->game->id)->where('status', ArmyConstants::getAliveStatusID())->where('id', '<>', $this->attacking_army->id)
                ->orderBy('alive_units', 'DESC')->first();
        } else if ($this->attacking_army->attack_strategy_id == AttackStrategyConstants::getWeakestID()) {
            $this->attacked_army = Army::where('game_id', $this->game->id)->where('status', ArmyConstants::getAliveStatusID())->where('id', '<>', $this->attacking_army->id)
                ->orderBy('alive_units', 'ASC')->first();
        }
    }

    private function loadDamageDealt()
    {
        if ($this->attacking_army->alive_units == 1) {
            $this->damage_dealt = 1;
        } else {
            $this->damage_dealt = $this->attacking_army->alive_units * 0.5;
        }
    }

    private function handleKillsOnly()
    {
        $units_killed = $this->attacked_army->alive_units - $this->attacked_army_alive_units;
        $this->attacked_army->update([
            'damage_received' => $this->attacked_army->damage_received + $this->damage_dealt,
            'alive_units' => $this->attacked_army_alive_units
        ]);
        $attacking_army_name = $this->attacking_army->name;
        $attacked_army_name = $this->attacked_army->name;
        BattleLog::create([
            'game_id' => $this->game->id,
            'log_text' => "Vojska $attacking_army_name je napala vojsku $attacked_army_name i pritom je ubila $units_killed vojnika."
        ]);
    }

    private function handleArmyKilled()
    {
        $this->attacked_army->update([
            'status' => ArmyConstants::getDeadStatusID(),
            'damage_received' => $this->attacked_army->damage_received + $this->damage_dealt,
            'alive_units' => $this->attacked_army_alive_units
        ]);
        $alive_armies_count = Army::where('game_id', $this->game->id)->where('status', ArmyConstants::getAliveStatusID())->count();
        $attacking_army_name = $this->attacking_army->name;
        $attacked_army_name = $this->attacked_army->name;
        if ($alive_armies_count == 1) {
            $this->game->update([
                'status' => GameConstants::getCompletedStatusID()
            ]);
            BattleLog::create([
                'game_id' => $this->game->id,
                'log_text' => "Vojska $attacking_army_name je napala vojsku $attacked_army_name i pritom je ubila sve vojnike čime je postala pobednička vojska."
            ]);
        } else {
            BattleLog::create([
                'game_id' => $this->game->id,
                'log_text' => "Vojska $attacking_army_name je napala vojsku $attacked_army_name i pritom je ubila sve vojnike."
            ]);
        }
    }
}

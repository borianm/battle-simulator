<?php

namespace App\Http\Controllers\API;

use App\Constants\ArmyConstants;
use App\Constants\AttackStrategyConstants;
use App\Constants\GameConstants;
use App\Helpers\GameHelper;
use App\Http\Controllers\Controller;
use App\Models\Army;
use App\Models\BattleLog;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    public function create(Request $request)
    {
        $game_count = Game::where('status', '<>', GameConstants::getCompletedStatusID())->count();
        if ($game_count < GameConstants::getMaxActiveGames()) {
            $game = Game::create();
            return response()->json([
                'data' => [
                    'game_id' => $game->id
                ],
                'message' => 'Uspešno ste kreirali novu igru.'
            ]);
        } else {
            return response()->json([
                'message' => 'Prekoračili ste maksimalan broj aktivnih igara.'
            ], 422);
        }
    }

    public function index()
    {
        return response()->json([
            'data' => Game::all()->toArray()
        ]);
    }

    public function show(Request $request, $id)
    {
        $game = Game::with('armies')->find($id);
        if ($game === null) {
            return response()->json([
                'message' => 'Igra sa zadatim ID-jem ne postoji.'
            ], 422);
        }
        return response()->json([
            'data' => $game->toArray()
        ]);
    }

    public function reset(Request $request, $id)
    {
        $game = Game::find($id);
        if ($game === null) {
            return response()->json([
                'message' => 'Igra sa zadatim ID-jem ne postoji.'
            ], 422);
        }
        $game->update([
            'status' => GameConstants::getWaitingStatusID()
        ]);
        $armies = Army::where('game_id', $game->id)->get();
        foreach ($armies as $army) {
            $army->update([
                'damage_received' => 0,
                'status' => ArmyConstants::getAliveStatusID(),
                'turn_made' => 0,
                'alive_units' => $army->units
            ]);
        }
        BattleLog::where('game_id', $game->id)->delete();
        return response()->json([
            'message' => 'Uspešno ste resetovali igru.'
        ]);
    }

    public function run(Request $request, $id)
    {
        $game = Game::find($id);
        if ($game === null) {
            return response()->json([
                'message' => 'Igra sa zadatim ID-jem ne postoji.'
            ], 422);
        }
        if ($game->status == GameConstants::getCompletedStatusID()) {
            return response()->json([
                'message' => 'Igra je već završena.'
            ], 422);
        }
        $game_helper = new GameHelper($game);
        if ($game->status == GameConstants::getWaitingStatusID()) {
            // not started
            return $game_helper->startGame();
        } else {
            // in progress
            return $game_helper->runAttack();
        }
    }
}

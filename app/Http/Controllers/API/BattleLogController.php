<?php

namespace App\Http\Controllers\API;

use App\Constants\GameConstants;
use App\Http\Controllers\Controller;
use App\Models\BattleLog;
use App\Models\Game;
use Illuminate\Http\Request;

class BattleLogController extends Controller
{
    public function index(Request $request, $id)
    {
        $game = Game::find($id);
        if ($game === null) {
            return response()->json([
                'message' => 'Igra sa zadatim ID-jem ne postoji.'
            ], 422);
        }
        return response()->json([
            'data' => BattleLog::where('game_id', $game->id)->orderBy('id', 'DESC')->get()->toArray()
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Constants\GameConstants;
use App\Http\Controllers\Controller;
use App\Models\Army;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArmyController extends Controller
{
    public function create(Request $request, $id)
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'units' => 'required|integer|min:80|max:100',
            'attack_strategy_id' => 'required|exists:attack_strategy,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }
        Army::create([
            'game_id' => $game->id,
            'name' => $request->input('name'),
            'units' => $request->input('units'),
            'alive_units' => $request->input('units'),
            'attack_strategy_id' => $request->input('attack_strategy_id')
        ]);
        return response()->json([
            'message' => 'Uspešno!'
        ]);
    }
}

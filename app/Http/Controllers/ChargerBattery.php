<?php

namespace App\Http\Controllers;

use App\Models\BatteryCharger;
use App\Models\battrei;
use Illuminate\Http\Request;

class ChargerBattery extends Controller
{
    public function create(Request $request)
    {
        $id_battery     = $request->input('id');
        $date           = $request->input('date');
        $duration       = $request->input('duration');
        $note           = $request->input('note');
        $pre_flight     = $request->input('pre_flight');
        $post_flight    = $request->input('post_flight');
        $before_charger = $request->input('before_charger');
        $after_charger  = $request->input('after_charger');
        $capacity       = $request->input('capacity');
        $resistance     = $request->input('resistance');
        $cell1          = $request->input('cell1');
        $cell2          = $request->input('cell2');
        $cell3          = $request->input('cell3');
        $cell4          = $request->input('cell4');
        $cell5          = $request->input('cell5');
        $cell6          = $request->input('cell6');
        $cell7          = $request->input('cell7');
        $cell8          = $request->input('cell8');
    
        $sql = BatteryCharger::create([
            'date' => $date,
            'duration' => $duration,
            'note' => $note,
            'pre_flight' => $pre_flight,
            'post_flight' => $post_flight,
            'before_charger' => $before_charger,
            'after_charger' => $after_charger,
            'capacity' => $capacity,
            'resistance' => $resistance,
            'cell1' => $cell1,
            'cell2' => $cell2,
            'cell3' => $cell3,
            'cell4' => $cell4,
            'cell5' => $cell5,
            'cell6' => $cell6,
            'cell7' => $cell7,
            'cell8' => $cell8,
            'batteris_id' => $id_battery,
        ]);
        
        if($sql){
            //update initial_Cycle_count batteri
            battrei::where('id',$id_battery)->increment('initial_Cycle_count');
            // Respons JSON
            return response()->json([
                'success' => true,
                'message' => 'Created Successfully',
                'data'    => [
                    'id_battery'     => $id_battery,
                    'date'           => $date,
                    'duration'       => $duration,
                    'note'           => $note,
                    'pre_flight'     => $pre_flight,
                    'post_flight'    => $post_flight,
                    'before_charger' => $before_charger,
                    'after_charger'  => $after_charger,
                    'capacity'       => $capacity,
                    'resistance'     => $resistance,
                    'cell1'          => $cell1,
                    'cell2'          => $cell2,
                    'cell3'          => $cell3,
                    'cell4'          => $cell4,
                    'cell5'          => $cell5,
                    'cell6'          => $cell6,
                    'cell7'          => $cell7,
                    'cell8'          => $cell8,
                ]
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'can\'t not to create',
            ]);
        }
    }

    public function edit(Request $request){
        $id             = $request->input('idItem');
        $id_battery     = $request->input('idEdit');
        $date           = $request->input('dateEdit');
        $duration       = $request->input('durationEdit');
        $note           = $request->input('noteEdit');
        $pre_flight     = $request->input('pre_flightEdit');
        $post_flight    = $request->input('post_flightEdit');
        $before_charger = $request->input('before_chargerEdit');
        $after_charger  = $request->input('after_chargerEdit');
        $capacity       = $request->input('capacityEdit');
        $resistance     = $request->input('resistanceEdit');
        $cell1          = $request->input('cell1Edit');
        $cell2          = $request->input('cell2Edit');
        $cell3          = $request->input('cell3Edit');
        $cell4          = $request->input('cell4Edit');
        $cell5          = $request->input('cell5Edit');
        $cell6          = $request->input('cell6Edit');
        $cell7          = $request->input('cell7Edit');
        $cell8          = $request->input('cell8Edit');

        // Update
        $sql = BatteryCharger::where('id', $id)->Update([
            'date' => $date,
            'duration' => $duration,
            'note' => $note,
            'pre_flight' => $pre_flight,
            'post_flight' => $post_flight,
            'before_charger' => $before_charger,
            'after_charger' => $after_charger,
            'capacity' => $capacity,
            'resistance' => $resistance,
            'cell1' => $cell1,
            'cell2' => $cell2,
            'cell3' => $cell3,
            'cell4' => $cell4,
            'cell5' => $cell5,
            'cell6' => $cell6,
            'cell7' => $cell7,
            'cell8' => $cell8,
            'batteris_id' => $id_battery,
        ]);

        if($sql){
            // Respons JSON
            return response()->json([
                'success' => true,
                'message' => 'Created Successfully',
                'data'    => [
                    'id_battery'     => $id_battery,
                    'date'           => $date,
                    'duration'       => $duration,
                    'note'           => $note,
                    'pre_flight'     => $pre_flight,
                    'post_flight'    => $post_flight,
                    'before_charger' => $before_charger,
                    'after_charger'  => $after_charger,
                    'capacity'       => $capacity,
                    'resistance'     => $resistance,
                    'cell1'          => $cell1,
                    'cell2'          => $cell2,
                    'cell3'          => $cell3,
                    'cell4'          => $cell4,
                    'cell5'          => $cell5,
                    'cell6'          => $cell6,
                    'cell7'          => $cell7,
                    'cell8'          => $cell8,
                ]
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'can\'t not to create',
            ]);
        }
    }

    public function clone(Request $request)
    {
        $id_battery     = $request->input('id');
        $date           = $request->input('date');
        $duration       = $request->input('duration');
        $note           = $request->input('note');
        $pre_flight     = $request->input('pre_flight');
        $post_flight    = $request->input('post_flight');
        $before_charger = $request->input('before_charger');
        $after_charger  = $request->input('after_charger');
        $capacity       = $request->input('capacity');
        $resistance     = $request->input('resistance');
        $cell1          = $request->input('cell1');
        $cell2          = $request->input('cell2');
        $cell3          = $request->input('cell3');
        $cell4          = $request->input('cell4');
        $cell5          = $request->input('cell5');
        $cell6          = $request->input('cell6');
        $cell7          = $request->input('cell7');
        $cell8          = $request->input('cell8');
    
        $sql = BatteryCharger::create([
            'date' => $date,
            'duration' => $duration,
            'note' => $note,
            'pre_flight' => $pre_flight,
            'post_flight' => $post_flight,
            'before_charger' => $before_charger,
            'after_charger' => $after_charger,
            'capacity' => $capacity,
            'resistance' => $resistance,
            'cell1' => $cell1,
            'cell2' => $cell2,
            'cell3' => $cell3,
            'cell4' => $cell4,
            'cell5' => $cell5,
            'cell6' => $cell6,
            'cell7' => $cell7,
            'cell8' => $cell8,
            'batteris_id' => $id_battery,
        ]);
        
        if($sql){
            // update Batterie initial_Cycle_count
            battrei::where('id',$id_battery)->increment('initial_Cycle_count');
            // Respons JSON
            return response()->json([
                'success' => true,
                'message' => 'Created Successfully',
                'data'    => [
                    'id_battery'     => $id_battery,
                    'date'           => $date,
                    'duration'       => $duration,
                    'note'           => $note,
                    'pre_flight'     => $pre_flight,
                    'post_flight'    => $post_flight,
                    'before_charger' => $before_charger,
                    'after_charger'  => $after_charger,
                    'capacity'       => $capacity,
                    'resistance'     => $resistance,
                    'cell1'          => $cell1,
                    'cell2'          => $cell2,
                    'cell3'          => $cell3,
                    'cell4'          => $cell4,
                    'cell5'          => $cell5,
                    'cell6'          => $cell6,
                    'cell7'          => $cell7,
                    'cell8'          => $cell8,
                ]
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'can\'t not to create',
            ]);
        }
    }
    
}

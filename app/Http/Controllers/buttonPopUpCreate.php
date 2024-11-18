<?php

namespace App\Http\Controllers;

use App\Models\battrei;
use App\Models\drone;
use App\Models\equidment;
use App\Models\fligh_location;
use App\Models\Projects;
use DB;
use Illuminate\Http\Request;

class buttonPopUpCreate extends Controller
{

    public function buttonProject(Request $request)
    {
        $id = Auth()->user()->teams()->first()->id;
        $case = $request->input('case');
        $revenue = $request->input('revenue');
        $customer = $request->input('customer');
        $currency = $request->input('currency');
        $description = $request->input('description');
        
        $sql = Projects::create([
            'case' => $case,
            'revenue' => $revenue,
            'customers_id' => $customer,
            'currencies_id' => $currency,
            'description' => $description,
            'teams_id' => $id,
        ]);
        
        if($sql){
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully',
            'case' => $revenue]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }
    }

    public function buttonDrone(Request $request)
    {
        // Mendapatkan ID tim dari pengguna yang sedang login
        $id = Auth()->user()->teams()->first()->id;
    
        // Mengambil data dari input request
        $name = $request->input('name');
        $status = $request->input('status');
        $idlegal = $request->input('idlegal');
        $brand = $request->input('brand');
        $model = $request->input('model');
        $type = $request->input('type');
        $serialP = $request->input('serial_p');
        $serialI = $request->input('serial_i');
        $flightC = $request->input('flight_c');
        $remoteC = $request->input('remote_c');
        $remoteCC = $request->input('remote_cc');
        $geometry = $request->input('geometry');
        $inventoryAsset = $request->input('inventory_asset');
        $description = $request->input('description');
        $usersId = $request->input('users_id');
        $firmwareV = $request->input('firmware_v');
        $hardwareV = $request->input('hardware_v');
        $propulsionV = $request->input('propulsion_v');
        $color = $request->input('color');
        $remote = $request->input('remote');
        $connCard = $request->input('conn_card');
        $initialFlight = $request->input('initial_flight');
        $initialFlightTime = $request->input('initial_flight_time');
        $maxFlightTime = $request->input('max_flight_time');
    
        // Menyimpan data ke dalam tabel equipment
        $sql = drone::create([
            'name' => $name,
            'status' => $status,
            'idlegal' => $idlegal,
            'brand' => $brand,
            'model' => $model,
            'type' => $type,
            'serial_p' => $serialP,
            'serial_i' => $serialI,
            'flight_c' => $flightC,
            'remote_c' => $remoteC,
            'remote_cc' => $remoteCC,
            'geometry' => $geometry,
            'inventory_asset' => $inventoryAsset,
            'description' => $description,
            'users_id' => $usersId,
            'firmware_v' => $firmwareV,
            'hardware_v' => $hardwareV,
            'propulsion_v' => $propulsionV,
            'color' => $color,
            'remote' => $remote,
            'conn_card' => $connCard,
            'initial_flight' => $initialFlight,
            'initial_flight_time' => $initialFlightTime,
            'max_flight_time' => $maxFlightTime,
            'teams_id' => $id,
        ]);
    
        // Mengecek apakah data berhasil disimpan
        if ($name) {
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json([
                'success' => 'Create successfully.',
                'name' => $name,
                'status' => $status,
                'idlegal' => $idlegal,
                'brand' => $brand,
                'model' => $model,
                'type' => $type,
                'serial_p' => $serialP,
                'serial_i' => $serialI,
                'flight_c' => $flightC,
                'remote_c' => $remoteC,
                'remote_cc' => $remoteCC,
                'geometry' => $geometry,
                'inventory_asset' => $inventoryAsset,
                'description' => $description,
                'users_id' => $usersId,
                'firmware_v' => $firmwareV,
                'hardware_v' => $hardwareV,
                'propulsion_v' => $propulsionV,
                'color' => $color,
                'remote' => $remote,
                'conn_card' => $connCard,
                'initial_flight' => $initialFlight,
                'initial_flight_time' => $initialFlightTime,
                'max_flight_time' => $maxFlightTime,
            ]);
        } else {
            return response()->json(['error' => 'Error To Create.']);
        }
    }
    

    public function buttonBattrei(Request $request)
    {
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $model = $request->input('model');
        $status = $request->input('status');
        $asset_inventory = $request->input('asset_inventory');
        $serial_p= $request->input('serial_P');
        $serial_i= $request->input('serial_I');
        $nominal_voltage = $request->input('nominal_voltage');
        $cellCount = $request->input('cellCountValue');
        $firmware_v = $request->input('firmware_version');
        $hardware_v= $request->input('hardware_version');
        $capacity = $request->input('capacity');
        $initial_Cycle_count = $request->input('initial_Cycle_count');
        $life_span = $request->input('life_span');
        $flaight_count = $request->input('flight_count');
        $for_drone= $request->input('for_drone');
        $purchase_date= $request->input('purchase_date');
        $insurable_value= $request->input('insurable_value');
        $wight= $request->input('weight');
        $is_loaner= $request->input('is_loaner');
        $description= $request->input('description');
        $owner= $request->input('users_id');
          
        $sql = battrei::create([
                'name' => $name,
                'model' => $model,
                'status' => $status,
                'asset_inventory' => $asset_inventory,
                'serial_P' => $serial_p,
                'serial_I' => $serial_i,
                'cellCount' => $cellCount,
                'nominal_voltage' => $nominal_voltage,
                'capacity' => $capacity,
                'initial_Cycle_count' => $initial_Cycle_count,
                'life_span' => $life_span,
                'flaight_count' => $flaight_count,
                'for_drone' => $for_drone,
                'purchase_date' => $purchase_date,
                'insurable_value'=> $insurable_value,
                'wight'=> $wight,
                'firmware_version' => $firmware_v,
                'hardware_version'=> $hardware_v,
                'is_loaner' => $is_loaner,
                'description' =>$description,
                'users_id' =>$owner,
                'teams_id'=>$id,
            ]);
        if($sql){
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully.',
            'case' => $name,
        'id' => $id,
        'name' => $name,
        'model' => $model,
        'status' => $status,
        'asset_inventory' => $asset_inventory,
        'serial_p' => $serial_p,
        'serial_i' => $serial_i,
        'nominal_voltage' => $nominal_voltage,
        'cell_count' => $cellCount,
        'firmware_version' => $firmware_v,
        'hardware_version' => $hardware_v,
        'capacity' => $capacity,
        'initial_cycle_count' => $initial_Cycle_count,
        'life_span' => $life_span,
        'flight_count' => $flaight_count,
        'for_drone' => $for_drone,
        'purchase_date' => $purchase_date,
        'insurable_value' => $insurable_value,
        'weight' => $wight,
        'is_loaner' => $is_loaner,
        'description' => $description,
        'owner' => $owner]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }
    }
    public function buttonEquipment(Request $request)
    {
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $model = $request->input('model');
        $status = $request->input('status');
        $asset_inventory = $request->input('inventory_asset');
        $serial= $request->input('serial');
        $firmware_v = $request->input('firmware_v');
        $hardware_v= $request->input('hardware_v');
        $type = $request->input('type');
        $drone= $request->input('drones_id');
        $purchase_date= $request->input('purchase_date');
        $insurable_value= $request->input('insurable_value');
        $wight= $request->input('weight');
        $is_loaner= $request->input('is_loaner');
        $description= $request->input('description');
        $owner= $request->input('users_id');
          
        $sql = equidment::create([
                'name' => $name,
                'model' => $model,
                'status' => $status,
                'inventory_asset' => $asset_inventory,
                'serial' => $serial,
                'type' => $type,
                'drones_id' => $drone,
                'users_id'=> $owner,
                'purchase_date' => $purchase_date,
                'insurable_value' => $insurable_value,
                'weight' => $wight,
                'is_loaner' => $is_loaner,
                'firmware_v' => $firmware_v,
                'hardware_v' => $hardware_v,
                'description' => $description,
                'teams_id'=> $id,
            ]);
        if($sql){
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully.',
            'case' => $name,
            'name' => $name,
            'model' => $model,
            'status' => $status,
            'asset_inventory' => $asset_inventory,
            'serial' => $serial,
            'firmware_v' => $firmware_v,
            'hardware_v' => $hardware_v,
            'type' => $type,
            'drone' => $drone,
            'purchase_date' => $purchase_date,
            'insurable_value' => $insurable_value,
            'wight' => $wight,
            'is_loaner' => $is_loaner,
            'description' => $description,
            'owner' => $owner]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }
    }
    public function buttonLocation(Request $request)
    {
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $address = $request->input('address');
        $city = $request->input('city');
        $country = $request->input('country');
        $description = $request->input('description');
        $state = $request->input('state');
        $pos_code = $request->input('pos_code');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $altitude = $request->input('altitude');
        $customers = $request->input('customers');
        $projects = $request->input('projects');
        
        $sql = fligh_location::create([
                'name' =>$name,
                'description'=>$description,
                'address' =>$address,
                'city' =>$city,
                'state' =>$state,
                'country' =>$country,
                'pos_code' =>$pos_code,
                'latitude' =>$latitude,
                'longitude' =>$longitude,
                'altitude' =>$altitude,
                'teams_id' => $id,
                'customers_id' => $customers,
                'projects_id' => $projects,
            ]);
        if($sql){
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully.',
            'case' => $name,
            'id' => $id,
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'country' => $country,
            'description' => $description,
            'state' => $state,
            'pos_code' => $pos_code,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'altitude' => $altitude,
            'customers' => $customers,
            'projects' => $projects]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }
    }
}

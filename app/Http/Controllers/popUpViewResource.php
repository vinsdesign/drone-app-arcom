<?php

namespace App\Http\Controllers;

use App\Models\document;
use App\Models\media_fligh;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class popUpViewResource extends Controller
{

    //Create Media Flight
    public function createMediaFlight(Request $request){
        $name = $request->input('name');
        $url = $request->input('url');
        $type = $request->input('type');
        $description = $request->input('notes');
        $owner = $request->input('owner');
        $flight = $request->input('flight');


        $sql = media_fligh::create([
            'title'=>$name,
            'description'=>$description,
            'type'=>$type,
            'url'=>$url,
            'owner_id'=>$owner,
            'fligh_id'=>$flight
        ]);
        if($sql){
        
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully',
            'name' => $name]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }
    }

    //flight document
    public function createFlightDocument(Request $request){
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $expired = $request->input('expired');
        $refNumber = $request->input('refNumber');
        $link = $request->input('link');
        $description = $request->input('notes');
        $owner = $request->input('owner');
        $flight = $request->input('flight');

        //untuk file ke path

        $file = $request->file('dock');
        $uniqueFileName = Str::uuid() . '-' . $file->getClientOriginalName();

        //upload ke database

        $sql = document::create([
            'name' => $name,
            'refnumber'=>$refNumber,
            'expired_date'=>$expired,
            'scope'=> $flight,
            'external link' =>$link,
            'description'=> $description,
            'doc'=>$uniqueFileName,
            'users_id'=>$owner,
            'teams_id'=>$id,
        ]);

        if($sql){
        
            Storage::disk('public')->putFileAs('/', $file, $uniqueFileName);
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully',
            'name' => $uniqueFileName]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }


    }

    //project document
    public function createProjectDocument(Request $request){
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $expired = $request->input('expired');
        $refNumber = $request->input('refNumber');
        $link = $request->input('link');
        $description = $request->input('notes');
        $owner = $request->input('owner');
        $project = $request->input('project');

        //untuk file ke path

        $file = $request->file('dock');
        $uniqueFileName = Str::uuid() . '-' . $file->getClientOriginalName();

        //upload ke database

        $sql = document::create([
            'name' => $name,
            'refnumber'=>$refNumber,
            'expired_date'=>$expired,
            'scope'=> 'Projects',
            'external link' =>$link,
            'description'=> $description,
            'doc'=>$uniqueFileName,
            'users_id'=>$owner,
            'projects_id' =>$project,
            'teams_id'=>$id,
        ]);

        if($sql){
        
            Storage::disk('public')->putFileAs('/', $file, $uniqueFileName);
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully',
            'name' => $uniqueFileName]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }


    }

    //equipment document
    public function createEquipmentDocument(Request $request){
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $expired = $request->input('expired');
        $refNumber = $request->input('refNumber');
        $link = $request->input('link');
        $description = $request->input('notes');
        $owner = $request->input('owner');
        $project = $request->input('relation');

        //untuk file ke path

        $file = $request->file('dock');
        $uniqueFileName = Str::uuid() . '-' . $file->getClientOriginalName();

        //upload ke database

        $sql = document::create([
            'name' => $name,
            'refnumber'=>$refNumber,
            'expired_date'=>$expired,
            'scope'=> 'Equipment',
            'external link' =>$link,
            'description'=> $description,
            'doc'=>$uniqueFileName,
            'users_id'=>$owner,
            'projects_id' =>$project,
            'teams_id'=>$id,
        ]);

        if($sql){
        
            Storage::disk('public')->putFileAs('/', $file, $uniqueFileName);
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully',
            'name' => $uniqueFileName]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }


    }

    // battrei DOcument
    public function createBattreiDocument(Request $request){
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $expired = $request->input('expired');
        $refNumber = $request->input('refNumber');
        $link = $request->input('link');
        $description = $request->input('notes');
        $owner = $request->input('owner');
        $project = $request->input('relation');

        //untuk file ke path

        $file = $request->file('dock');
        $uniqueFileName = Str::uuid() . '-' . $file->getClientOriginalName();

        //upload ke database

        $sql = document::create([
            'name' => $name,
            'refnumber'=>$refNumber,
            'expired_date'=>$expired,
            'scope'=> 'Equipment',
            'external link' =>$link,
            'description'=> $description,
            'doc'=>$uniqueFileName,
            'users_id'=>$owner,
            'projects_id' =>$project,
            'teams_id'=>$id,
        ]);

        if($sql){
        
            Storage::disk('public')->putFileAs('/', $file, $uniqueFileName);
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully',
            'name' => $uniqueFileName]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }


    }

    //Document drone
    public function createDroneDocument(Request $request){
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $expired = $request->input('expired');
        $refNumber = $request->input('refNumber');
        $link = $request->input('link');
        $description = $request->input('notes');
        $owner = $request->input('owner');
        $project = $request->input('relation');

        //untuk file ke path

        $file = $request->file('dock');
        $uniqueFileName = Str::uuid() . '-' . $file->getClientOriginalName();

        //upload ke database

        $sql = document::create([
            'name' => $name,
            'refnumber'=>$refNumber,
            'expired_date'=>$expired,
            'scope'=> 'Equipment',
            'external link' =>$link,
            'description'=> $description,
            'doc'=>$uniqueFileName,
            'users_id'=>$owner,
            'projects_id' =>$project,
            'teams_id'=>$id,
        ]);

        if($sql){
        
            Storage::disk('public')->putFileAs('/', $file, $uniqueFileName);
            $sql->teams()->attach($id);
            session()->flash('successfully', 'Data berhasil ditambahkan!');
            return response()->json(['success' => 'Create successfully',
            'name' => $uniqueFileName]);      
        }else{
            return response()->json(['error' => 'Error To Create.']);
        }


    }

        //Document drone
        public function createPersonnelDocument(Request $request){
            $id = Auth()->user()->teams()->first()->id;
            $name = $request->input('name');
            $expired = $request->input('expired');
            $refNumber = $request->input('refNumber');
            $link = $request->input('link');
            $description = $request->input('notes');
            $owner = $request->input('owner');
            $project = $request->input('relation');
            $type = $request->input('type');
    
            //untuk file ke path
    
            $file = $request->file('dock');
            $uniqueFileName = Str::uuid() . '-' . $file->getClientOriginalName();
    
            //upload ke database
    
            $sql = document::create([
                'name' => $name,
                'refnumber'=>$refNumber,
                'expired_date'=>$expired,
                'scope'=> 'Equipment',
                'external link' =>$link,
                'description'=> $description,
                'doc'=>$uniqueFileName,
                'users_id'=>$owner,
                'projects_id' =>$project,
                'type'=>$type,
                'teams_id'=>$id,
            ]);
    
            if($sql){
            
                Storage::disk('public')->putFileAs('/', $file, $uniqueFileName);
                $sql->teams()->attach($id);
                session()->flash('successfully', 'Data berhasil ditambahkan!');
                return response()->json(['success' => 'Create successfully',
                'name' => $uniqueFileName]);      
            }else{
                return response()->json(['error' => 'Error To Create.']);
            }
    
    
        }

}
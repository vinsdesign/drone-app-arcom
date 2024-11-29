<?php

namespace App\Http\Controllers;

use App\Models\document;
use Illuminate\Http\Request;
use Str;

class popUpViewResource extends Controller
{
    public function createProjectDocument(Request $request){
        $id = Auth()->user()->teams()->first()->id;
        $name = $request->input('name');
        $expired = $request->input('expired');
        $refNumber = $request->input('refNumber');
        $link = $request->input('link');
        $dock = $request->file('dock');
        $description = $request->input('notes');
        $owner = $request->input('owner');
        $project = $request->input('project');
        return response()->json(['success' => 'Create successfully',
            'name' => $dock]);  
        // $uniqueFileName = Str::uuid() . '.' . $dock->getClientOriginalExtension();

        // $sql = document::create([
        //     'name' => $name,
        //     'refnumber'=>$refNumber,
        //     'expired_date'=>$expired,
        //     'scope'=> 'projects',
        //     'external link' =>$link,
        //     'description'=> $description,
        //     'doc'=>$dock,
        //     'users_id'=>$owner,
        //     'projects_id' =>$project,
        //     'teams_id'=>$id,
        // ]);

        // if($sql){
        
        //     $sql->teams()->attach($id);
        //     session()->flash('successfully', 'Data berhasil ditambahkan!');
        //     return response()->json(['success' => 'Create successfully',
        //     'name' => $name]);      
        // }else{
        //     return response()->json(['error' => 'Error To Create.']);
        // }


    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatGroupController extends Controller
{
    public function index(Request $request)
    {
        $id = Auth::user()->id;
        
        $groups = ChatGroup::where('users', 'like', '%' . $id . '%')->get();

        return response([
            'groups' => $groups,
        ], 200);
    }

    // creae chat group
    public function store(Request $request)
    {
        $input = $request->validate([
            'name' => 'required',
        ]);

        // dd($request->all());
        $userIds = $request->users;
        $users = [];

        foreach ($userIds as $key => $value)
        {
            $id = substr($key, 7);
            $users[] = $id;
        }
        dd($users);

        $count = count($users);

        if($count >= 2)
        {
            $users = implode(', ', $users);
            $users = $users . ', ' . $request->my_id;

            // group image
            if(isset($request->image))
            {
                $image = $request->file('image');
        
                $extension = $image->getClientOriginalExtension();
                $baseNameImage = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $name = Str::slug($baseNameImage, '-');
                $imageFileName = $name.'.'.$extension;
                $destinationFilePath = '/uploads/groups/';
            
                // image path
                $filePath = $destinationFilePath . $imageFileName;

                $image->move(public_path($destinationFilePath),$imageFileName);

                // store value
                $input['image'] = $filePath;
            }
    
            ChatGroup::create([
                'name' => $input['name'],
                'image' => $input['image'],
                'users' => $users,
            ]);
    
            return response([
                'message' => 'Chat group created successfully',
            ], 200);
        }
        else
        {
            return response([
                'message' => 'Minimum of two group members required to create a chat group',
            ], 400);
        }
    }
}

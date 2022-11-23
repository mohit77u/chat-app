<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\User;
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
        
        $userIds = [$request->users];
        $users = explode(',', $userIds[0]);

        $count = count($users);

        if($count >= 2)
        {
            $users = $userIds[0];
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

            // dd($input);
    
            ChatGroup::create([
                'name' => $input['name'],
                'image' => isset($input['image']) ? $input['image'] : null,
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

     /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChatGroup  $ChatGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ChatGroup $group)
    {
        $groupUsers = explode(', ', $group->users);
        // dd($groupUsers);
        $users = User::whereIn('id', $groupUsers)->get();
        return response([
            'group' => $group,
            'users' => $users,
        ]);
    }
}

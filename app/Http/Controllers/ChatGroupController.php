<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ContactList;
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
        $authUser = Auth::user();

        $input = $request->validate([
            'name' => 'required',
        ]);
        
        $userIds = [$request->users];
        $users = explode(',', $userIds[0]);
        $users = array_merge($users, [''. $authUser->id. '']);
        // dd($users);

        $count = count($users);

        if($count >= 2)
        {
            // $users = $userIds[0];
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

            // $users = array_values($users);
            // dd($users);
            $users = json_encode($users);
    
            ChatGroup::create([
                'name'          => $input['name'],
                'image'         => isset($input['image']) ? $input['image'] : null,
                'users'         => $users,
                'created_by'    => (int)$authUser->id,
            ]);
    
            return response([
                'message' => 'Chat group created successfully',
            ], 200);
        }
        else
        {
            return response([
                'message' => 'Minimum of one group members required to create a chat group',
            ], 400);
        }
    }

     /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChatGroup  $ChatGroup
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, ChatGroup $group)
    {
        $authUser = $request->user_id;
        $groupUsers = json_decode($group->users);
        $users = ContactList::from('contact_lists as CL')
                ->join('users as U', 'U.id', '=', 'CL.user_two')
                // ->where('CL.user_one', $authUser)
                ->whereIn('CL.user_two', $groupUsers)
                ->orderBy('display_name')
                ->distinct('U.id')
                ->get();
                
        $users = $users->unique('id');
        // dd($users);
        return response([
            'group' => $group,
            'users' => $users,
        ]);
    }

    // update chat group
    public function update(Request $request, $id)
    {
        $authUser = Auth::user();
        $chatGroup = ChatGroup::find($id);
        $input = $request->validate([
            'name' => 'required',
        ]);
        
        $userIds = [$request->users];
        $users = explode(',', $userIds[0]);

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

        if($userIds[0] != "" || $userIds[0] != null || $userIds[0] != '' )
        {
            $authUserId = [''.$authUser->id.''];
            // dd($users);
            $users = array_merge(json_decode($chatGroup->users), $users, $authUserId);
            $users = array_unique($users);
    
            $users = array_values($users);
            // $users = json_encode($users);
            // dd($input);
            
            $chatGroup->update([
                'name' => $input['name'],
                'image' => isset($input['image']) ? $input['image'] : $chatGroup->image,
                'users' => $users,
            ]);
    
            return response([
                'message' => 'Chat group updated successfully',
            ], 200);
        }
        else
        {
            $chatGroup->update([
                'name' => $input['name'],
                'image' => isset($input['image']) ? $input['image'] : $chatGroup->image,
            ]);
            return response([
                'message' => 'Chat group updated successfully',
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChatGroup  $chatGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(ChatGroup $group)
    {
        $group->delete();
        // return response
        return response([
            'message' => 'Group deleted successfully.',
        ], 200);

    }

    // remove user from group
    public function removeUser(Request $request)
    {
        // $input = $request->all();
        $group = ChatGroup::find($request->group_id);
        $userId = $request->user_id;
        $groupUsers = $group->users;
        $groupUsers = json_decode($groupUsers);
        // dd($groupUsers);
        
        // dd($groupUsers);
        foreach($groupUsers as $key => $user)
        {
            if($user == $userId || $user == '')
            {
                // dd($userId, $groupUsers, $key);
                unset($groupUsers[$key]);
                // dd($user, $groupUsers);
            }
        }
        $groupUsers = array_values($groupUsers);
        // dd($groupUsers);
        $group->users = $groupUsers;
        $group->save();

        return response([
            'message' => 'User removed from group successfully.',
        ], 200);
    }
}

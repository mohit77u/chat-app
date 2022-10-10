<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $users = User::latest()->get();

        return response([
            'users' => $users,
        ]);
    }

    public function authUser()
    {
        $user = Auth::user();

        return response([
            'user' => $user,
        ]);
    }
    
    public function updateUser(Request $request)
    {
        // dd($request->avatar);
        $user = User::where('id', $request->id)->first();

        $input = $request->validate([
            'name'      => 'required|max:255',
            'email'     => [ 'required','email', Rule::unique('users')->ignore($user->id) ],
            // 'avatar'    => 'dimensions:min_width=120,min_height=120',
        ]);


        if(isset($request->password))
        {
            $request->validate([
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required|same:password',
            ]);
            
            $input['password'] = Hash::make($request->password);
        } 
        else 
        {
            $input['password'] = $user->password;
        }

        if($request->file('avatar'))
        {
            
            // requested file
            $image = $request->file('avatar');
        
            $extension = $image->getClientOriginalExtension();
            $baseNameImage = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $name = Str::slug($baseNameImage, '-');
            $imageFileName = $name.'.'.$extension;
            $destinationFilePath = '/uploads/users/';
           
            // image path
            $filePath = $destinationFilePath . $imageFileName;

            $image->move(public_path($destinationFilePath),$imageFileName);

            // store value
            $input['avatar'] = $filePath;
            
        }

        // store in db table
        $user->update($input);

        // return response
        return response([
            'message' => 'User successfully updated',
        ], 200);
    }
}

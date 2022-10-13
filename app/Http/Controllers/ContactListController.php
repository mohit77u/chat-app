<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ContactList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ContactListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $contactList = ContactList::from('contact_lists as CL')
            ->join('users AS U', 'U.id', '=', 'CL.user_two')
            ->where('CL.user_one', '=', $user->id)
            ->select([
                'U.*', 'CL.display_name AS display_name', 'CL.id AS contactList_id'
            ])->get();

        // return response
        return response([
            'users' => $contactList,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->validate([
            'user_one'                   => 'required',
            'display_name'               => 'required',
            'mobile_number'              => 'required',
        ]);
        
        // bycrypt password
        $input['password'] = Hash::make('password');

        // check if user exists or not
        $user = User::where('mobile_number', $input['mobile_number'])->first();

        // if user exists update user else create new user
        if($user != null)
        {
            ContactList::create([
                'user_one'      => $input['user_one'],
                'user_two'      => $user->id,
                'display_name'  => $input['display_name'],
                'mobile_number'  => $input['mobile_number'],
            ]);

            // return response
            return response([
                'message' => 'User added to contact list successfully.',
            ], 200);
        }
        else 
        {
            // store in db table
            $user = User::create([
                'name'              => $input['display_name'],
                'email'             => strtolower($input['display_name']). $input['mobile_number']. '@gmail.com',
                'password'          => $input['password'],
                'mobile_number'     => $input['mobile_number'],
            ]);

            ContactList::create([
                'user_one'      => $input['user_one'],
                'user_two'      => $user->id,
                'display_name'  => $input['display_name'],
                'mobile_number'  => $input['mobile_number'],
            ]);

            // return response
            return response([
                'message' => 'User added to contact list successfully.',
            ], 200);
        }
        
        
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ContactList  $contactList
     * @return \Illuminate\Http\Response
     */
    public function show(ContactList $contactList)
    {
        return response([
            'contact' => $contactList
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ContactList  $contactList
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactList $contactList)
    {
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ContactList  $contactList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactList $contactList)
    {
        $input = $request->validate([
            'display_name'               => 'required',
            'mobile_number'              => 'required',
        ]);

        $contactList->update($input);

        // return response
        return response([
            'message' => 'User updated to contact list successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ContactList  $contactList
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactList $contactList)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GroupMessages;

class GroupMessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $groupId = $request->groupId;
        // dd($groupId);

        $messages = GroupMessages::where('group_id', $groupId)->latest()->get();
        $messages = $messages->sortBy('created_at');
        
        $messages = $messages->groupBy(function($item){
          return date('d F Y', strtotime($item['created_at']));
        });

        return response([
            'messages' => $messages,
        ]);
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
        $message = $request->validate([
            'message' => 'required',
            'user_id' => 'required',
            'group_id' => 'required',
        ]);

        // dd($request->all());

        if($request->message != 'null')
        {
            GroupMessages::create($message);
        }

        $attachments = $request->attachments;
       
        if(isset($attachments))
        {
            $extension = $attachments->getClientOriginalExtension();
            // dd($extension);
            $baseNamefile = pathinfo($attachments->getClientOriginalName(), PATHINFO_FILENAME);
            $name = Str::slug($baseNamefile, '-');
            $fileName = $name.'.'.$extension;
            $destinationFilePath = '/uploads/group-messages/attachments/';
           
            // file path
            $filePath = $destinationFilePath . $fileName;

            $attachments->move(public_path($destinationFilePath),$fileName);

            if($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'svg')
            {
                $type = 'image';
            } 
            elseif($extension == 'pdf' || $extension == 'docs')
            {
                $type = 'docs';
            } 
            // store value
            GroupMessages::create([
                'user_id'       => $message['user_id'],
                'group_id'      => $message['group_id'],
                'type'          => $type,
                'message'       => $filePath,
            ]);
        }
        return response([
            'message' => 'Messsage sent successfully',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GroupMessages  $groupMessages
     * @return \Illuminate\Http\Response
     */
    public function show(GroupMessages $groupMessages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GroupMessages  $groupMessages
     * @return \Illuminate\Http\Response
     */
    public function edit(GroupMessages $groupMessages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GroupMessages  $groupMessages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GroupMessages $groupMessages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GroupMessages  $groupMessages
     * @return \Illuminate\Http\Response
     */
    public function destroy(GroupMessages $groupMessages)
    {
        //
    }
}

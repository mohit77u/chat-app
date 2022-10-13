<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
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
            'from_user_id' => 'required',
            'to_user_id' => 'required',
        ]);

        if($request->message != 'null')
        {
            Message::create($message);
        }

        $attachments = $request->attachments;
       
        if(isset($attachments))
        {
            $extension = $attachments->getClientOriginalExtension();
            // dd($extension);
            $baseNamefile = pathinfo($attachments->getClientOriginalName(), PATHINFO_FILENAME);
            $name = Str::slug($baseNamefile, '-');
            $fileName = $name.'.'.$extension;
            $destinationFilePath = '/uploads/messages/attachements/';
           
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
            Message::create([
                'from_user_id'  => $message['from_user_id'],
                'to_user_id'    => $message['to_user_id'],
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
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }

    // get user messages
    public function userMessages(Request $request)
    {
        $messagesOne = Message::where('from_user_id', $request->from_user_id)->where('to_user_id', $request->to_user_id)->latest()->get()->toArray();
        $messagesTwo = Message::where('from_user_id', $request->to_user_id)->where('to_user_id', $request->from_user_id)->latest()->get()->toArray();

        $messages = array_merge($messagesOne, $messagesTwo);

        $messages = array_values(Arr::sort($messages, function ($value) {
            return $value['created_at'];
        }));

        $messages = collect($messages);
        $messages = $messages->sortBy('created_at');
        $messages = $messages->groupBy(function($item)
        {
          return date('d F Y', strtotime($item['created_at']));
        });

        return response([
            'user_messages' => $messages,
        ]);
    }

}

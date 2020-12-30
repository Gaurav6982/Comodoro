<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reminders;
use App\Events;
class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //Create Reminder
        $event=Events::where('user_id',auth()->user()->id)->where('event_name',$request->event_name)->first();
        // return $event;
        if(is_null($event))
        return response()->json([
            'status'=>'NOT OK',
            'data'=>'Event Not Found!'
        ],400);
        //check if priority is true and phone number not set and send at 200.
        if($request->priority=='true' && is_null(auth()->user()->phone))
        return response()->json([
            'status'=>'NOT OK',
            'data'=>'Verify Phone First!'
        ],200);

        $rem=new Reminders;
        $rem->event_id=$event->id;
        $rem->reminder=$request->reminder_msg;
        $rem->date=$request->dateTime;
        $rem->priority=$request->priority?1:0;
        $rem->snooze_before=$request->snooze_before;
        $rem->difference=$request->difference;
        if($rem->save())
        return response()->json([
            'status'=>'OK',
            'data'=>'Reminder Created'
        ],200);

        return response()->json([
            'status'=>'NOT OK',
            'data'=>'Something Went Wrong!'
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

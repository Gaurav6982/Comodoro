<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events;

class Event{
    public $event_name='';
    // public $reminders=[];
}

class Reminder{
    public $reminder_msg='';
    public $priority;
    public $dateTime;
    public $snooze_before;
    public $difference;
}
class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
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
        //Create New Event.
        if(auth()->user()->verified==0)
        return response()->json([
            "status"=>"NOT OK",
            "data"=>"USER NOT REGISTERED"
        ],200);
        $event=Events::where('user_id',auth()->user()->id)->where('event_name',strtolower($request->event_name))->first();
        // return $event;
        if(!is_null($event))
        return response()->json([
            "status"=>"NOT OK",
            "data"=>"Event Already Exist"
        ],500);
        $event=new Events;
        $event->event_name=strtolower($request->event_name);
        $event->user_id=auth()->user()->id;
        if($event->save())
        return response()->json([
            "status"=>"OK",
            "data"=>"Event Created"
        ],200);

        return response()->json([
            'status'=>'NOT OK',
            'data'=>'Something Went Wrong!'
        ],400);
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
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $user=auth()->user();
        $events=$user->events;
        $Res=[];
        foreach ($events as $key => $event) {
            $n=new Event;
            $n->event_name=$event->event_name;
            // $n->reminders=$event->reminders;
            // $rems=$event->reminders;
            // foreach ($rems as $rem) {
            //     $r=new Reminder;
            //     $r->reminder_msg=
            // }
            array_push($Res,$n);
        }
        return response()->json([
            'status'=>'OK',
            'data'=>$Res
        ],200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMachine;
use Illuminate\Support\Facades\Request;

class AttendanceMachineController extends Controller
{
    protected $attendanceMachine;

    function __construct(AttendanceMachine $attendanceMachine)
    {
        $this->attendanceMachine = $attendanceMachine;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->attendanceMachine->datatable());
        }

        return view('attendance_machine.index', ['js' => 'js/attendance_machine/index.js']);
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
     * @param \Illuminate\Http\Request|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AttendanceMachine  $attendanceMachine
     * @return \Illuminate\Http\Response
     */
    public function show(AttendanceMachine $attendanceMachine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AttendanceMachine  $attendanceMachine
     * @return \Illuminate\Http\Response
     */
    public function edit(AttendanceMachine $attendanceMachine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request|Request $request
     * @param  \App\Models\AttendanceMachine $attendanceMachine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttendanceMachine $attendanceMachine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AttendanceMachine  $attendanceMachine
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttendanceMachine $attendanceMachine)
    {
        //
    }
}

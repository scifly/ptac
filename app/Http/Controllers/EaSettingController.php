<?php

namespace App\Http\Controllers;

use App\Http\Requests\EaSettingRequest;
use App\Models\EducatorAttendanceSetting;
use Illuminate\Support\Facades\Request;

class EaSettingController extends Controller
{
    protected $educatorAttendanceSetting;

    function __construct(EducatorAttendanceSetting $educatorAttendanceSetting) {

        $this->educatorAttendanceSetting = $educatorAttendanceSetting;

    }

    /**
     * 显示教职员工考勤列表.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->educatorAttendanceSetting->datatable());
        }
        return parent::output(__METHOD__);
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
     * @param EaSettingRequest $request
     */
    public function store(EaSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EducatorAttendanceSetting  $educatorAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function show(EducatorAttendanceSetting $educatorAttendanceSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EducatorAttendanceSetting  $educatorAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(EducatorAttendanceSetting $educatorAttendanceSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EducatorAttendanceSetting  $educatorAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EducatorAttendanceSetting $educatorAttendanceSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EducatorAttendanceSetting  $educatorAttendanceSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(EducatorAttendanceSetting $educatorAttendanceSetting)
    {
        //
    }
}

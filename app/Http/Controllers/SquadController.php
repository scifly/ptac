<?php

namespace App\Http\Controllers;

use App\Http\Requests\SquadRequest;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Support\Facades\Request;

/**
 * @property array message
 */
class SquadController extends Controller
{
    protected $squad ;

    public function __construct(Squad $squad)
    {
        $this->squad = $squad;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (Request::get('draw')) {
            return response()->json($this->squad->datatable());
        }
        return $this->output(__METHOD__);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return $this->output(__METHOD__);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SquadRequest $squadRequest
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(SquadRequest $squadRequest)
    {
        //
        // request
        $data['name'] = $squadRequest->input('name');
        $data['grade_id'] = $squadRequest->input('grade_id');
        $ids = $squadRequest->input('educator_ids');
        $data['educator_ids'] = implode(',', $ids);
        $data['enabled'] = $squadRequest->input('enabled');

        $row = $this->squad->where(['grade_id' => $data['grade_id'], 'name' => $data['name']])->first();
        if(!empty($row) ){

            return $this->fail('班级名称重复！');
        }else{

            return $this->squad->create($data) ? $this->succeed() : $this->fail();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $squad = $this->squad->find($id);
        if (!$squad) { return parent::notFound(); }

        $educators = User::whereHas('educator' , function($query) use ($squad) {

            $f = explode(",", $squad->educator_ids);
            $query-> whereIn('id', $f);

        })->get(['id','realname'])->toArray();
        if (!$squad) { return parent::notFound(); }
        return parent::output(__METHOD__, [
            'squad' => $squad,
            'educators' => $educators
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Squad  $squad
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $squad = $this->squad->find($id);

        if (!$squad) { return parent::notFound(); }

        $educators = User::whereHas('educator' , function($query) use ($squad) {

            $f = explode(",", $squad->educator_ids);
            $query->whereIn('id', $f);

        })->get(['id','realname'])->toArray();

        $selectedEducators = [];
        foreach ($educators as $value) {
            $selectedEducators[$value['id']] = $value['realname'];
        }
        return parent::output(__METHOD__, [
            'squad' => $squad,
            'selectedEducators' => $selectedEducators,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param SquadRequest $squadRequest
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     * @internal param Squad $squad
     */
    public function update(SquadRequest $squadRequest, $id)
    {
        $data = Squad::find($id);
        if (!$data) { return parent::notFound(); }
        $ids = $squadRequest->input('educator_ids');

        $data->name = $squadRequest->input('name');
        $data->grade_id = $squadRequest->input('grade_id');
        $data->educator_ids = implode(',', $ids);
        $data->enabled = $squadRequest->input('enabled');

        $row = $this->squad->where([
                'grade_id' => $data->grade_id,
                'name' => $data->name
            ])->first();
        if(!empty($row) && $row->id != $id){

            return $this->fail('班级名称重复！');
        }else{

            return $data->save() ? $this->succeed() : $this->fail();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Squad $squad
     */
    public function destroy($id)
    {
        $squad = $this->squad->find($id);

        if (!$squad) { return parent::notFound(); }
        return $squad->delete() ? parent::succeed() : parent::fail();
    }
}

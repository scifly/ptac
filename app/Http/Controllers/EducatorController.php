<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorRequest;
use App\Models\Educator;
use App\Models\Team;
use Illuminate\Support\Facades\Request;

/**
 * @property array message
 */
class EducatorController extends Controller
{

    protected $educator;

    public function __construct(Educator $educator)
    {
        $this->educator = $educator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Request::get('draw')) {
            return response()->json($this->educator->datatable());
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
        return $this->output(__METHOD__);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EducatorRequest $educatorRequest
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function store(EducatorRequest $educatorRequest)
    {
        // request
        $data['user_id'] = $educatorRequest->input('user_id');
        $ids = $educatorRequest->input('team_ids');
        $data['team_ids'] = implode(',', $ids);
        $data['school_id'] = $educatorRequest->input('school_id');
        $data['sms_quote'] = $educatorRequest->input('sms_quote');
        $data['enabled'] = $educatorRequest->input('enabled');

        return $this->educator->create($data) ? $this->succeed() : $this->fail();

    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Educator $educator
     */
    public function show($id)
    {

        $educator = $this->educator->find($id);

        if (!$educator) { return parent::notFound(); }
        $f = explode(",", $educator->team_ids);
        $teams=Team::whereIn('id',$f)->get(['id','name']);


        if (!$educator) { return parent::notFound(); }
        return parent::output(__METHOD__, [
            'educator' => $educator,
            'teams' => $teams
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $educator =  $this->educator->find($id);
        if (!$educator) { return parent::notFound(); }

        $ids = explode(",", $educator->team_ids);

        $teams = Team::whereIn('id', $ids)
            ->get(['id','name']);
        $selectedTeams = [];
        foreach ($teams as $value) {
            $selectedTeams[$value->id] = $value->name;
        }
        return parent::output(__METHOD__, [
            'educator' => $educator,
            'selectedTeams' => $selectedTeams,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param EducatorRequest|\Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Educator $educator
     */
    public function update(EducatorRequest $request ,$id)
    {
        // find the record by id
        // update the record with the request data
        $data = Educator::find($id);
        if (!$data) { return parent::notFound(); }
        $ids = $request->input('team_ids');

        $data->user_id = $request->input('user_id');
        $data->school_id = $request->input('school_id');
        $data->team_ids = implode(',', $ids);
        $data->sms_quote = $request->input('sms_quote');
        $data->enabled = $request->input('enabled');
        return $data->save() ? $this->succeed() : $this->fail();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Educator $educator
     */
    public function destroy($id)
    {
        $educator = $this->educator->find($id);

        if (!$educator) { return parent::notFound(); }
        return $educator->delete() ? parent::succeed() : parent::fail();
    }
}

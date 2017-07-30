<?php

namespace App\Http\Controllers;

use App\Http\Requests\EducatorRequest;
use App\Models\Educator;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
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
        return view('educator.index' , [
            'js' => 'js/educator/index.js',
            'dialog' => true,
            'show' => true,
            'datatable' => true,
            'form' => true,
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
        return view('educator.create',[
            'js' => 'js/educator/create.js',
            'form' => true
            ]);
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

        if($this->educator->create($data))
        {
            $this->result['message'] = self::MSG_CREATE_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';

        }
        return response()->json($this->result);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Educator  $educator
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $educator = $this->educator->whereId($id)->first();

        $teams = Educator::with('team',function ($query) use ($educator) {
            $f = explode(",", $educator->team_ids);
            $query->whereIn('id', $f);
        })->get(['id','name'])->toArray();

//        return view('educator.show', [
//            'educator' => $educator,
//            'teams' => $teams
//        ]);
        return response()->json(
            [
            'educator' => $educator,
            'teams' => $teams
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Educator  $educator
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $educator = $this->educator->whereId($id)->first();
        $ids = explode(",", $educator->team_ids);

        $teams = DB::table('teams')
            ->whereIn('id', $ids )
            ->get(['id','name']);
        $teamIds = [];
        foreach ($teams as $value) {
            $teamIds[$value->id] = $value->name;
        }
        return view('educator.edit', [
            'js' => 'js/educator/edit.js',
            'educator' => $educator,
            'teamIds' => $teamIds,
            'form' => true
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

        $data->user_id = $request->input('user_id');
        $data->school_id = $request->input('school_id');
        $ids = $request->input('team_ids');
        $data->team_ids = implode(',', $ids);
        $data->sms_quote = $request->input('sms_quote');

        if($data->save())
        {
            $this->result['message'] = self::MSG_EDIT_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';

        }
        return response()->json($this->result);
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
        if ($this->educator->findOrFail($id)->delete()) {
            $this->result['message'] = self::MSG_DEL_OK;
        } else {
            $this->result['statusCode'] = self::HTTP_STATUSCODE_INTERNAL_SERVER_ERROR;
            $this->result['message'] = '';
        }
        return response()->json($this->result);
    }
}

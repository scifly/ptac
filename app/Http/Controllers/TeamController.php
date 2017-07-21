<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Illuminate\Support\Facades\Request;

class TeamController extends Controller {
    
    protected $team;
    
    /**
     * TeamController constructor.
     * @param Team $team
     */
    public function __construct(Team $team) { $this->team = $team; }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->team->datatable());
        }
        return view('team.index', ['js' => 'js/team/index.js']);
        
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        
        return view('team.create', ['js' => 'js/team/create.js']);
        
    }
    
    /**
     * Store a newly created resource in storage.
     * @param TeamRequest $request
     * @return \Illuminate\Http\Response
     * @internal param HttpRequest|HttpRequst|Request $request
     */
    public function store(TeamRequest $request) {
    
    
    
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team) {
        
        return view('team.show', $team);
        
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team) {
        
        return view('team.edit', $team);
        
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Team $team
     * @return \Illuminate\Http\Response
     * @internal param \Illuminate\Http\Request $request
     */
    public function update(Team $team) {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team) {
        //
    }
}

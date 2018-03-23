<?php
namespace App\Http\Controllers;


use App\Models\User;

/**
 * 超级用户管理
 *
 * Class OperatorController
 * @package App\Http\Controllers
 */
class OperatorController extends Controller {
    
    protected $user;
    
    function __construct(User $user) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->user = $user;
        
    }
    
    public function index() {
    
    
    
    }
    
    public function create() {}
    
    public function store() {}
    
    public function edit() {}
    
    public function update() {}
    
    public function destroy() {}
    
}

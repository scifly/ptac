<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class ApiSrv
 * @package App\Console\Commands
 */
class ApiSrv extends Command {
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apisrv:serve';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'api server';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        
        $this->line('hi there');
        return true;
        
    }
}

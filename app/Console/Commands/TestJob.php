<?php
namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

/**
 * Class TestJob
 * @package App\Console\Commands
 */
class TestJob extends Command {
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
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
     */
    public function handle() {
    
    
    }
}

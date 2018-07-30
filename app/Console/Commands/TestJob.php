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
    
        Event::create([
            'title' => '37289',
            'remark' => 'remark',
            'location' => '437289',
            'contact' => '888',
            'url' => 'http://',
            'start' => '2018-03-20 12:35:40',
            'end' => '2018-03-21 12:35:40',
            'ispublic' => 0,
            'iscourse' => 0,
            'educator_id' => 1,
            'subject_id' => 2,
            'alertable' => 1,
            'alert_mins' => 10,
            'user_id' => 1,
            'enabled' => 1
        ]);
        
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Events\TaskDue;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use SebastianBergmann\Environment\Console;

class TaskDueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this will raise events on sockets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $start = Str::substr(now()->toDateTimeString(), 0, -3);
        $end = $start . ':59';
        $tasks = Task::query()->whereBetween('due_at', [$start, $end])->get();
        // $tasks = Task::inRandomOrder()->limit(1)->get();
        
        // dd($tasks->toArray());
        
        $tasks->each(function($task){
            event(new TaskDue($task));
        });
        
        return 0;
    }
}

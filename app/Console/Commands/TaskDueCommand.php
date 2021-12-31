<?php

namespace App\Console\Commands;

use App\Core\Notifications\PushNotifications;
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

    protected $pushNotifications;

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
    public function __construct(PushNotifications $pushNotifications)
    {
        parent::__construct();

        $this->pushNotifications = $pushNotifications;
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
        $tasks = Task::query()->with('owner:id,device_id')->whereBetween('due_at', [$start, $end])->get();
        // $tasks = Task::with('owner:id,device_id')->inRandomOrder()->limit(1)->get();

        $tasks->each(function ($task) {
            if ($task->owner->device_id) {
                $this
                    ->pushNotifications
                    ->addDevice($task->owner->device_id, 'ANDROID')
                    ->send("Digitolk - Task (" . $task->summary . ")", $task->description);
            }
            event(new TaskDue($task));
        });

        return 0;
    }
}

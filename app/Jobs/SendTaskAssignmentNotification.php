<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Task;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTaskAssignmentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;
    protected $users;

    public function __construct(Task $task, $users)
    {
        $this->task = $task;
        $this->users = $users;
    }

    public function handle()
    {
        
        foreach ($this->users as $user) {
            $user->notify(new TaskAssignedNotification($this->task));
        }
    }
}

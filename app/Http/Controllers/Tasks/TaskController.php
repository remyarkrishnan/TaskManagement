<?php

namespace App\Http\Controllers\Tasks;

use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Jobs\SendTaskAssignmentNotification;

class TaskController extends Controller
{
    protected $userRepository;

    protected $taskRepository;

    public function __construct(
        UserRepository $userRepository,
        TaskRepository $taskRepository
        )
    {
        $this->userRepository = $userRepository;

        $this->taskRepository = $taskRepository;
    }

    public function index()
    {
        $users = $this->userRepository->all();

        $tasks = $this->taskRepository->all()->load('users');

        $canCreateTask = Gate::allows('create', Task::class);

        // $canChangeStatus = Gate::allows('changeStatus', Task::class);

        return Inertia::render('Tasks/Index',[
            'users' => $users,
            'tasks' => $tasks,
            'canCreateTask' => $canCreateTask,
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'selectedUsers' => 'nullable|array', 
            'selectedUsers.*' => 'exists:users,id', 
        ]);

        if($request->task_id){

            $task = $this->taskRepository->update($request->task_id, [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'user_id' => Auth::user()->id
            ]);

            if ($request->has('selectedUsers')) {
                $task->users()->sync($request->selectedUsers);  
            } else {
                $task->users()->sync([]);  
            }

        }else {
            $task = $this->taskRepository->create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'user_id' => Auth::user()->id
            ]);

            if (!empty($validated['selectedUsers'])) {
                $task->users()->attach($validated['selectedUsers']);
            }

            $this->sendNotification($task, $request->selectedUsers);
        }
        
        
    }

    public function sendNotification($task, $selectedUsers)
    {
        if(empty($selectedUsers)){
            return ;
        }

        $users = $this->userRepository->findMany($selectedUsers);

        dispatch(new SendTaskAssignmentNotification($task, $users));
    }

    public function delete($taskId)
    {
        $task = $this->taskRepository->find($taskId);
        $task->users()->detach();
        $task->delete();
        
    }

    public function assignToMe(Task $task)
    {
        $user = Auth::user();

        if (!$task->users()->where('user_id', $user->id)->exists()) {
            $task->users()->attach($user->id);
        }

    }

    public function unassignFromMe(Task $task)
    {
        $user = Auth::user();

        if ($task->users()->where('user_id', $user->id)->exists()) {
            $task->users()->detach($user->id);
        }

    }

    public function updateStatus(Request $request, $taskId)

    {

        $task = $this->taskRepository->find($taskId);

        $task->update(['status' => $request->status]);

    }

    public function show($id)
    {
        $task = $this->taskRepository->find($id)->load('users');

        return Inertia::render('Tasks/Show', [
            'task' => $task
        ]);
    }

}

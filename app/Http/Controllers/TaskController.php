<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return auth()->user()->tasks;
        return [
            'completed_task' => auth()->user()->tasks()->whereCompleted(true)->get(),
            'incompleted_task' => auth()->user()->tasks()->whereCompleted(false)->get(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'description' => 'required',
            'due_at' => 'required',
            'summary' => 'required',
        ]);

        $task
            ->fill($request->only($task->getFillable()));

        auth()->user()->tasks()->save($task);

        return response(['message' => "Task Created Successfully"], Response::HTTP_CREATED);

        return response()->json($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return $task;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'description' => 'required',
            'due_at' => 'required',
            'summary' => 'required',
        ]);

        $task
            ->fill($request->only($task->getFillable()))
            ->save();

        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function toggleComplete(Request $request, Task $task)
    {
        // $request->validate(['completed' => 'required']);

        $task->completed = !$task->completed;
        $task->save();

        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return $this->index();
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();

        return $tasks;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'status'  => ['sometimes', new Enum(TaskStatus::class)],
        ]);

        $task = Task::create($data);

        return $task;
    }

    public function show(Task $task)
    {
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'status'  => ['sometimes', new Enum(TaskStatus::class)],
        ]);

        $task->update($data);

        return $task;
    }

    public function updateStatus(Request $request, Task $task)
    {
        $data = $request->validate([
            'status' => ['required', new Enum(TaskStatus::class)],
        ]);

        $task->update($data);

        return $task;
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(null, 204);
    }
}

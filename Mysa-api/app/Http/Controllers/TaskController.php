<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
   
    // API Lấy danh sách tất cả Task
    public function index()
    {
        $tasks = Task::all(); // Lấy hết từ Database
        return response()->json($tasks); // Trả về dạng JSON
    }
    // API Tạo một Task mới
    public function store(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào (Validation)
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        // 2. Lưu vào Database
        $task = Task::create([
            'title' => $request->title,
        ]);
        // 3. Trả về kết quả
        return response()->json($task, 201); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}

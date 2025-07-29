<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json(['message' => 'List of students']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        //
        // dd($request->validated());
        $student = Student::create($request->validated());
        return response()->json(['message' => 'Student created successfully', 'student' => $student], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        //
        return response()->json(['message' => 'Student details', 'student' => $student]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        return response()->json(['message' => 'Update student with ID: ' . $id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        return response()->json(['message' => 'Delete student with ID: ' . $id]);
    }
}

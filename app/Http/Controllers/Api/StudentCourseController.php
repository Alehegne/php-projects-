<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    //
    public function store(Request $request)
    {
        $studentId = $request->route('s_id');
        $courseId = $request->route('c_id');

        $student = Student::findOrFail($studentId);
        $course = Course::findOrFail($courseId);
        // Logic to enroll the student in the course
        //prevent re-enrollment to a course
        if ($student->courses()->where('course_id', $course->id)->exists()) {
            return response()->json([
                'message' => "Student {$student->name} is already enrolled in course {$course->title}."
            ], 409);
        }
        // Validate the request data
        $request->validate([
            'semester' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'grade' => 'nullable|numeric|min:0|max:100',
            'score' => 'nullable|numeric|min:0|max:100', // Assuming score is a numeric value
        ]);
        $student->courses()->attach($course, [
            'enrollment_date' => now(),
            'status' => 'enrolled',
            'semester' => $request->input('semester', null),
            'year' => $request->input('year', null),
            'grade' => $request->input('grade', null)
        ]);

        return response()->json([
            'message' => "Student {$student->name} enrolled in course {$course->title} successfully.",
            'enrollment' => [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'enrollment_date' => now(),
                'status' => 'enrolled',
                'semester' => $request->input('semester', null),
                'year' => $request->input('year', null),
                'grade' => $request->input('grade', null)
            ]
        ], 201);
    }
    public function index($s_id)
    {
        $student = Student::findOrFail($s_id);
        $courses = $student->courses()->withPivot('enrollment_date', 'status', 'semester', 'year', 'grade', 'score')->get();
        $enrollments = [];
        foreach ($courses as $course) {
            $enrollments[] = [
                'course_id' => $course->id,
                'title' => $course->title,
                'semester' => $course->pivot->semester,
                'year' => $course->pivot->year,
                'grade' => $course->pivot->grade,
                'score' => $course->pivot->score,
                'enrollment_date' => $course->pivot->enrollment_date,
                'status' => $course->pivot->status
            ];
        }
        return response()->json(['message' => "Courses for student {$student->name}", 'enrollments' => $enrollments]);
    }
    public function update(Request $request, $s_id, $c_id)
    {
        $student = Student::findOrFail($s_id);
        $course = Course::findOrFail($c_id);
        $student_course = $student->courses()->where('course_id', $course->id)->first();
        if (!$student_course) {
            return response()->json([
                'message' => "Student {$student->name} is not enrolled in course {$course->title}."
            ]);
        }
        // dd($request->all());
        //if score is passed, calculate the grade too
        if ($request->has('score')) {
            // dd("dump 1 {$student_course}");
            $score = $request->input('score');
            $grade = $this->getGrade($score);
            $request->merge(['grade' => $grade]);
        }
        // dd("dump 2 {$student_course}");
        //update the enrollment details with the given

        $student->courses()->updateExistingPivot($course->id, [
            'semester' => $request->input('semester', $student_course->semester),
            'year' => $request->input('year', $student_course->year),
            'grade' => $request->input('grade', $student_course->grade),
            'score' => $request->input('score', $student_course->score),
        ]);
        return response()->json([
            'message' => "Enrollment updated successfully for student {$student->name} in course {$course->title}.",
            'enrollment' => [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'semester' => $request->input('semester', $student_course->semester),
                'year' => $request->input('year', $student_course->year),
                'grade' => $request->input('grade', $student_course->grade),
                'score' => $request->input('score', $student_course->score),
            ]
        ]);
    }
    private function getGrade($score)
    {
        $grade_map = [
            'A+' => [90, 100],
            'A' => [85, 89],
            'A-' => [80, 84],
            'B+' => [75, 79],
            'B' => [70, 74],
            'B-' => [65, 69],
            'C+' => [60, 64],
            'C' => [55, 59],
            'C-' => [50, 54],
            'F' => [0, 49]
        ];
        foreach ($grade_map as $grade => $range) {
            if ($score >= $range[0] && $score <= $range[1]) {
                return $grade;
            }
        }
        return null;
    }
    public function destroy($s_id, $c_id)
    {
        $student = Student::findOrFail($s_id);
        $course = Course::findOrFail($c_id);
        $student->courses()->detach($course->id);
        return response()->json([
            'message' => "Enrollment for student {$student->name} in course {$course->title} has been deleted successfully."
        ]);
    }
    public function allEnrollments()
    {
        $enrollments = [];
        $students = Student::with('courses')->get();
        foreach ($students as $student) {
            foreach ($student->courses as $course) {
                $enrollments[] = [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'enrollment_date' => $course->pivot->enrollment_date,
                    'status' => $course->pivot->status,
                    'semester' => $course->pivot->semester,
                    'year' => $course->pivot->year,
                    'grade' => $course->pivot->grade,
                    'score' => $course->pivot->score
                ];
            }
        }
        return response()->json(['message' => 'All enrollments', 'enrollments' => $enrollments]);
    }

    public function studentsInCourse($c_id)
    {
        // dd("students in course {$c_id}");
        $course = Course::findOrFail($c_id);
        $students = $course->students()->withPivot('enrollment_date', 'status', 'semester', 'year', 'grade', 'score')->get();
        $enrollments = [];
        foreach ($students as $student) {
            $enrollments[] = [
                'student_id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'enrollment_date' => $student->pivot->enrollment_date,
                'status' => $student->pivot->status,
                'semester' => $student->pivot->semester,
                'year' => $student->pivot->year,
                'grade' => $student->pivot->grade,
                'score' => $student->pivot->score
            ];
        }
        return response()->json(['message' => 'Students in course', 'enrollments' => $enrollments]);
    }

    public function report($s_id)
    {

        // get report for a student with courses, scores, grades, and gpa organized by semester and year
        $student = Student::findOrFail($s_id);
        $courses = $student->courses()->withPivot('enrollment_date', 'status', 'semester', 'year', 'grade', 'score')->get();
        //group courses by semester and year
        $grouped_courses = $courses->groupBy(function ($course) {
            $key = $course->pivot->semester . ' ' . $course->pivot->year;
            return $key;
        });
        $total_grade_points = 0;
        $total_credits = 0;
        $report = [];
        $gpa = 0.0;

        foreach ($grouped_courses as $key => $course) {
            foreach ($course as $c) {
                $grade_points = $this->getGradePoints($c->pivot->grade);
                $total_credits += $c->credit_hours; // Assuming credit_hours is a field in Course model
                $total_grade_points += $grade_points * $c->credit_hours; // Assuming credit_hours is a field in Course model
                $report[$key][] = [
                    'course_id' => $c->id,
                    'title' => $c->title,
                    'semester' => $c->pivot->semester,
                    'year' => $c->pivot->year,
                    'grade' => $c->pivot->grade,
                    'score' => $c->pivot->score,
                    'enrollment_date' => $c->pivot->enrollment_date,
                    'credit_hours' => $c->credit_hours, // Assuming credit_hours is a field in Course model
                    'status' => $c->pivot->status
                ];
            }
            if ($total_credits > 0) {
                $gpa = $total_grade_points / $total_credits;
            }
            $report[$key]['gpa'] = number_format($gpa, 2);
        }
        return response()->json([
            'message' => "Report for student {$student->name}",
            'report' => $report,
            'total_grade_points' => $total_grade_points,
            'total_credits' => $total_credits,
            'gpa' => number_format($gpa, 2)
        ]);
    }
    private function getGradePoints($grade)
    {
        $grade_points_map = [
            'A+' => 4.0,
            'A' => 4.0,
            'A-' => 3.75,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.75,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.75,
            'F' => 0.0
        ];
        return $grade_points_map[$grade] ?? 0.0;
    }
}

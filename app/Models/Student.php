<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable = [
        'name',
        'email',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'student_id',
    ];
    public function courses()
    {
        return $this->belongsToMany(Course::class, "student_courses")
            ->withPivot('enrollment_date', 'status', 'grade', 'semester', 'year')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'credit_hours',

    ];
    public function students()
    {
        return $this->belongsToMany(Student::class, "student_courses")
            ->withPivot('enrollment_date', 'status', 'grade', 'semester', 'year')
            ->withTimestamps();
    }
}

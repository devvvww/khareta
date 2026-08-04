<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    protected $fillable = ['code','color','name','is_elective','description','department_prefix_id','department_id'];
    public function prerequisites() : BelongsToMany {
        return $this->belongsToMany(Course::class, 'course_prerequisite', 'course_id', 'prerequisite_id');
    }
    public function requiredForCourses() : BelongsToMany {
        return $this->belongsToMany(Course::class, 'course_prerequisite', 'prerequisite_id', 'course_id');
    }
    public function department() : BelongsTo {
        return $this->belongsTo(Department::class);
    }

    public function departmentPrefix() : BelongsTo {
        return $this->belongsTo(DepartmentPrefix::class);
    }
}

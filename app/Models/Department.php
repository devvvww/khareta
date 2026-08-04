<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name', 'color','prefix','allows_electives'];
    
    public function courses() : HasMany {
        return $this->hasMany(Course::class);
    }

    public function prefixes() : HasMany {
        return $this->hasMany(DepartmentPrefix::class);
    }
}

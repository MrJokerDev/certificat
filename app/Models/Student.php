<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $casts = [
        'date_of_issue' => 'datetime:Y-m-d'
    ];

    protected $fillable = [
        'seria',
        'seria_number',
        'nik_name',
        'full_name',
        'course',
        'season',
        'sertificat_1',
        'sertificat_2',
        'progress_0',
        'progress_1',
        'progress_2',
        'progress_3',
        'progress_4',
        'progress_5',
        'date_of_issue'
    ];

    public function scopeSearch($query, $searchQuery)
    {
        return $query->where(function ($q) use ($searchQuery) {
            foreach ($this->fillable as $attribute) {
                $q->orWhere($attribute, 'LIKE', "%$searchQuery%");
            }
        });
    }

    public function certificates()
    {
        return $this->hasMany(StudentCertificate::class, 'student_id');
    }
}

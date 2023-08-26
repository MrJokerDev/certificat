<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $casts = [
        'date_of_issue' => 'datetime:Y-m-d'
    ];

    protected $fillable = [
        'seria',
        'seria_number',
        'ism',
        'familiya',
        'sharif',
        'berilgan_sana',
        'umumiy',
        'umumiy_ball',
        'modul_1',
        'modul_ball_1',
        'modul_2',
        'modul_ball_2',
        'modul_3',
        'modul_ball_3',
    ];

    public function scopeSearch($query, $searchQuery)
    {
        return $query->where(function ($q) use ($searchQuery) {
            foreach ($this->fillable as $attribute) {
                $q->orWhere($attribute, 'LIKE', "%$searchQuery%");
            }
        });
    }

    public function fullName()
    {
        return "{$this->ism} {$this->familiya} {$this->sharif}";
    }
}

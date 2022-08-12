<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    public $table = 'meals';
    public $primarykey = 'id';
    public $fillable = [
        'type',
        'description',
        'user_id'
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'updated_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'deleted_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dietmeal()
    {
        return $this->hasMany(DietMeal::class);
    }

    public function mealfood()
    {
        return $this->hasMany(MealFood::class);
    }
}

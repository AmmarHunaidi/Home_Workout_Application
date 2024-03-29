<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietMeal extends Model
{
    use HasFactory;
    public $table = 'diet_meals';
    public $primarykey = 'id';
    public $fillable = [
        'diet_id',
        'meal_id',
        'day'
    ];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'updated_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'deleted_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
    ];
    
    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function diet()
    {
        return $this->belongsTo(Diet::class);
    }

}

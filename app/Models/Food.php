<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    public $table = 'food';
    public $primarykey = 'id';
    //Food Image
    public $fillable = [
        'name',
        'calories',
        'description',
        'user_id',
        'food_image_url'
    ];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'updated_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'deleted_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
    ];
    
    public function meal_food()
    {
        return $this->hasMany(MealFood::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}

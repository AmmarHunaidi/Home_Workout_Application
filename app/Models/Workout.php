<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;
    public $table = 'workouts';
    public $primarykey = 'workout_id';
    //like is bookmark
    //difficulty
    public $fillable = [
        'name',
        'length',
        'excersise_count',
        'predicted_burnt_calories',
        'like_count',
        'review_count',
        'user_id',
        'categorie_id',
        'equipment',
        'workout_image_url'
    ];
    public $timestamps = true;

    public function review()
    {
        return $this->hasMany(WorkoutReview::class);
    }

    public function workout_excersise()
    {
        return $this->hasMany(WorkoutExcersises::class);
    }

    public function coach()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function categorie()
    {
        return $this->belongsTo(WorkoutCatgorie::class);
    }

    public function practices()
    {
        return $this->hasMany(Practice::class);
    }

}

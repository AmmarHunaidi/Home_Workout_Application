<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticeWorkout extends Model
{
    use HasFactory;
    public $table = 'practice_workouts';
    public $primarykey = 'id';
    public $fillable = [
        'workout_id',
        'practice_id'
    ];
    public $timestamps = true;

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'updated_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'deleted_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
    ];
    
    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
}

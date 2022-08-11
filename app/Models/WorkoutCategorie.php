<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutCategorie extends Model
{
    use HasFactory;
    public $table = 'workout_categories';
    public $primarykey = 'id';
    public $fillable = [
        'name',
        'user_id'
    ];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'updated_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'deleted_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
    ];
    
    public function workout()
    {
        return $this->hasMany(Workout::class);
    }

    public function coach()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Excersise extends Model
{
    use HasFactory;
    public $table = 'excersises';
    public $primarykey = 'id';
    public $fillable = [
        'name',
        'description',
        'burn_calories',
        'user_id',
        'excersise_media_url'
    ];
    public $timestamps = true;

    public function workout()
    {
        return $this->hasMany(WorkoutExcersises::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}

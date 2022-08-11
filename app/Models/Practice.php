<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practice extends Model
{
    use HasFactory;
    public $table = 'practices';
    public $primarykey = 'id';
    public $fillable = [
        'user_id',
        'summary_calories',
        'excersises_played',
        'summary_time'
    ];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'updated_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'deleted_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
    ];
    
    public function trainee()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}

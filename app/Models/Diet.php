<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{
    use HasFactory;
    public $table = 'diets';
    public $primarykey = 'id';
    public $fillable = [
        'name',
        'user_id',
        'created_by'
    ];
     public $timestamps = true;

     public function user()
     {
        return $this->belongsTo(User::class,'user_id');
     }

     public function creator()
     {
        return $this->belongsTo(User::class,'created_by');
     }
}

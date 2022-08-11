<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteDiet extends Model
{
    use HasFactory;
    public $table = 'favorite_diets';
    public $primarykey = 'id';
    public $fillable = [
        'user_id',
        'diet_id'
    ];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'updated_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'deleted_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
    ];
    
    public function workout()
    {
        return $this->belongsTo(Diet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

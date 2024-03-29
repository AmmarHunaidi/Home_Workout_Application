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
        'review_count',
        'created_by'
    ];
    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'updated_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
        'deleted_at' => 'datetime:Y-m-d\TH:i:s.u\Z',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dietmeal()
    {
        return $this->hasMany(DietMeal::class);
    }

    public function subscribers()
    {
        return $this->hasMany(DietSubscribe::class);
    }

    public function favorites()
    {
        return $this->hasMany(FavoriteDiet::class);
    }

    public function reviews()
    {
        return $this->hasMany(DietReview::class);
    }
}

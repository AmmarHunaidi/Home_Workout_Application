<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComments extends Model
{
    use HasFactory;
    protected $table = "post_comments";
    protected $primaryKey = "id";
    protected $fillable = ['user_id', 'post_id', 'text', 'reports'];
    protected $timestamp = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}

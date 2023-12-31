<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\CommentDeleting;

class Comment extends Model
{
    use HasFactory;

    protected $fillable =['content','user_id','post_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class,'likeable');
    }
    public function image()
    {
        return $this->morphOne('App\Models\Image', 'imageable');
    }
    public function video()
    {
        return $this->morphOne('App\Models\Video', 'videoable');
    }
    
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
    ];

    /**
     * Get the user that owns the blog.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

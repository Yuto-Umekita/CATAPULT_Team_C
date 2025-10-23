<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeBookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'recipe_id',
        'title',
        'image_url',
    ];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

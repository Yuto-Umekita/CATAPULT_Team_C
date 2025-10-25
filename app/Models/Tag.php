<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    // ✅ group_id を追加
    protected $fillable = ['name', 'item_id', 'group_id'];

    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            'item_tag',
            'tag_id',
            'item_id'
        )->withTimestamps();
    }
}

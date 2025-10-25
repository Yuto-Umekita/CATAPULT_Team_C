<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AdminPassword extends Model
{
    protected $fillable = ['password', 'group_id']; // ← ✅ group_idを追加

    public function setPasswordAttribute($value)
    {
        if (!str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    // ✅ グループ関連
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}


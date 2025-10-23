<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AdminPassword extends Model
{
    protected $fillable = ['password'];

    public function setPasswordAttribute($value)
    {
        // ✅ すでにbcrypt形式（$2y$...）なら再ハッシュしない
        if (!str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}

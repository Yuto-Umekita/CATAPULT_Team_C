<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'phone',
        'company',
        'position',
        'occupation',
        'notify_low_stock',
        'notify_recipe_updates',
        'notify_system',
        'low_stock_threshold',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // ========================================================
    // 🟡 最初の登録ユーザーを自動的に管理者にする
    // ========================================================
    protected static function booted()
    {
        static::creating(function ($user) {
            // まだユーザーが1人もいない場合（最初の登録者）
            if (self::count() === 0) {
                $user->is_admin = true; // 管理者権限を自動付与
            }
        });
    }

    // ========================================================
    // 🧩 関連リレーション
    // ========================================================
    public function pinnedIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'user_ingredient_pins')
                    ->withPivot('pinned_order')
                    ->withTimestamps()
                    ->orderBy('user_ingredient_pins.pinned_order');
    }

    public function pinUsers()
    {
        return $this->belongsToMany(User::class, 'user_ingredient_pins')
                    ->withPivot('pinned_order')
                    ->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function memos()
    {
        return $this->hasMany(Memo::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}

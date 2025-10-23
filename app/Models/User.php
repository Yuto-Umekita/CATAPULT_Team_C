<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
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
            if (self::count() === 0) {
                $user->is_admin = true;
            }
        });
    }

    // ========================================================
    // 🧩 関連リレーション
    // ========================================================

    // 📦 アイテム
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // 📝 メモ
    public function memos()
    {
        return $this->hasMany(Memo::class);
    }

    // 👤 プロフィール
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // 📍 ピン留め食材
    public function pinnedIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'user_ingredient_pins')
                    ->withPivot('pinned_order')
                    ->withTimestamps()
                    ->orderBy('user_ingredient_pins.pinned_order');
    }

    // 🧑‍🤝‍🧑 他ユーザー（ピン共有など）
    public function pinUsers()
    {
        return $this->belongsToMany(User::class, 'user_ingredient_pins')
                    ->withPivot('pinned_order')
                    ->withTimestamps();
    }

    // 🏢 グループ（企業・チーム）
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

}

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
        ];
    }

    // app/Models/User.php
    public function pinnedIngredients()
    {
        return $this->belongsToMany(Ingredient::class, 'user_ingredient_pins')
                    ->withPivot('pinned_order')
                    ->withTimestamps()
                    ->orderBy('user_ingredient_pins.pinned_order');
    }

    // app/Models/Ingredient.php
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     */
    protected $fillable = [
        'name',          // 食材名
        'quantity',      // 数量
        'unit',          // 単位（例：個、g、mlなど）
        'expiration',    // 賞味期限
        'category',      // カテゴリ（例：野菜、肉、調味料など）
        'is_pinned',     // ピン留めフラグ（true=固定）
        'pinned_order',  // ピン内の並び順
        'pinned_at',     // ピン留めした日時
    ];

    /**
     * 日付として扱う属性
     */
    protected $casts = [
        'expiration' => 'date',
        'is_pinned' => 'boolean',
        'pinned_at' => 'datetime',
    ];

    /**
     * デフォルトの並び順
     * ピン留めされたものを上に表示
     */
    protected static function booted()
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderByDesc('is_pinned')
                  ->orderByRaw('CASE WHEN pinned_order IS NULL THEN 1 ELSE 0 END')
                  ->orderBy('pinned_order')
                  ->orderBy('name');
        });
    }

    /**
     * 関連するユーザー（必要に応じて）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 在庫切れ判定などの便利アクセサ
     */
    public function getIsExpiredAttribute()
    {
        return $this->expiration && $this->expiration->isPast();
    }

    /**
     * ピン留め状態をトグル（ON/OFF）
     */
    public function togglePin()
    {
        $this->is_pinned = ! $this->is_pinned;

        if ($this->is_pinned) {
            $maxOrder = static::where('is_pinned', true)->max('pinned_order');
            $this->pinned_order = is_null($maxOrder) ? 1 : ($maxOrder + 1);
            $this->pinned_at = now();
        } else {
            $this->pinned_order = null;
            $this->pinned_at = null;
        }

        $this->save();
    }

        // ★ 追加：この食材をピンしているユーザーリレーション
    public function pinUsers()
    {
        return $this->belongsToMany(User::class, 'user_ingredient_pins')
            ->withPivot('pinned_order')
            ->withTimestamps();
    }
}

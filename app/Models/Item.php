<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Item extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     */
    protected $fillable = [
        'item',
        'quantity',
        'expiration_date',
        'user_id',
        'group_id',
    ];

    /**
     * 日付型として扱うカラム
     */
    protected $dates = [
        'expiration_date',
    ];

    /**
     * Cast設定（必要なら）
     */
    protected $casts = [
        'expiration_date' => 'date',
    ];

    /**
     * ユーザーとのリレーション
     * 1つの商品は1人のユーザーに属する
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'item_tag', 'item_id', 'tag_id')->withTimestamps();
    }



    public function memos()
    {
        return $this->hasMany(Memo::class);
    }

    /**
     * 賞味期限をフォーマットして返す
     * 例: 2025-10-16 → 2025/10/16
     */
    public function getFormattedExpirationDateAttribute()
    {
        return $this->expiration_date
            ? Carbon::parse($this->expiration_date)->format('Y/m/d')
            : 'なし';
    }

    /**
     * 賞味期限が切れているかどうかを判定するアクセサ
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->expiration_date) {
            return false;
        }

        return Carbon::parse($this->expiration_date)->isPast();
    }
}

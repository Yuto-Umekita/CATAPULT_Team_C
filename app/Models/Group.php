<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // 作成者
        'name',    // グループ名
        'mode',    // household / enterprise
    ];

    // ==========================================================
    // 🧩 リレーション定義
    // ==========================================================

    /**
     * 🧑‍💼 グループ作成者（1対多：ユーザー1人が複数グループ作成可）
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 👥 グループに所属するすべてのユーザー（中間テーブル group_user 経由）
     * - pivot: role (admin/member など)
     * - timestamps: 参加日時管理
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * 📦 グループに属するアイテム（在庫）
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * 🏷 グループに属するタグ
     */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * 🥦 グループに属する食材
     */
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    // ==========================================================
    // 🧠 ユーティリティメソッド
    // ==========================================================

    /**
     * 現在のモードをわかりやすく取得
     */
    public function getModeLabelAttribute(): string
    {
        return $this->mode === 'enterprise' ? '企業用' : '家庭用';
    }

    /**
     * 指定したユーザーがこのグループに所属しているか判定
     */
    public function hasMember($userId): bool
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withPivot('role')
            ->withTimestamps();
    }

}

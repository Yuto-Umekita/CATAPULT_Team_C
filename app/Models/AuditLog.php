<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'group_id', // ✅ 追加
        'action',
        'target_type',
        'target_id',
        'changes',
        'ip',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // 🧩 ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🧩 ターゲット（ポリモーフィック）
    public function target()
    {
        return $this->morphTo();
    }

    // 🏷️ グループとのリレーション
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}

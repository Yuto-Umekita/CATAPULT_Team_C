<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseList extends Model
{
    use HasFactory;

    protected $fillable = [
        'item',
        'quantity',
        'purchase_date',
        'group_id', // ✅ 追加
        'user_id',  // ✅ 追加
    ];

    // ✅ 関連付け
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

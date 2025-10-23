<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    /** @use HasFactory<\Database\Factories\MemoFactory> */
    use HasFactory;

    
    protected $fillable = ['memo', 'item_id', 'user_id', 'group_id',];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

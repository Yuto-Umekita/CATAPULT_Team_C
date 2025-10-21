<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id','action','target_type','target_id','changes','ip'];
    protected $casts = ['changes' => 'array'];

    public function user(){ return $this->belongsTo(User::class); }
    
    public function target()
    {
        return $this->morphTo();
    }

}
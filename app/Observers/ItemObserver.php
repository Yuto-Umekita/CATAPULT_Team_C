<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ItemObserver
{
    /**
     * 在庫が作成された時
     */
    public function created(Item $item): void
    {
        AuditLog::create([
            'user_id'     => Auth::id(),
            'group_id'    => $item->group_id ?? session('selected_group_id'), // ✅ 追加
            'action'      => 'created',
            'target_type' => Item::class,
            'target_id'   => $item->id,
            'changes'     => ['after' => $item->getAttributes()],
            'ip'          => request()?->ip(),
        ]);
    }

    /**
     * 在庫が更新された時
     */
    public function updated(Item $item): void
    {
        AuditLog::create([
            'user_id'     => Auth::id(),
            'group_id'    => $item->group_id ?? session('selected_group_id'), // ✅ 追加
            'action'      => 'updated',
            'target_type' => Item::class,
            'target_id'   => $item->id,
            'changes'     => [
                'before' => $item->getOriginal(),
                'after'  => $item->getAttributes(),
            ],
            'ip'          => request()?->ip(),
        ]);
    }

    /**
     * 在庫が削除された時
     */
    public function deleted(Item $item): void
    {
        AuditLog::create([
            'user_id'     => Auth::id(),
            'group_id'    => $item->group_id ?? session('selected_group_id'), // ✅ 追加
            'action'      => 'deleted',
            'target_type' => Item::class,
            'target_id'   => $item->id,
            'changes'     => ['before' => $item->getOriginal()],
            'ip'          => request()?->ip(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
{
    $query = \App\Models\AuditLog::query()->with(['user', 'target']);

    // アクションフィルタ
    if ($request->filled('action')) {
        $query->where('action', $request->action);
    }

    // 対象モデル
    if ($request->filled('target_type')) {
        $query->where('target_type', $request->target_type);
    }

    // ユーザー名
    if ($request->filled('user_name')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->user_name . '%');
        });
    }

    // 日付範囲
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // 並び替えとページネーション
    $logs = $query->latest()->paginate(10);

    return view('audit_logs.index', compact('logs'));
}

}

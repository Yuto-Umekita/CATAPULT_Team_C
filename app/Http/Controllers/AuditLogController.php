<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * 📜 監査ログ一覧（管理者のみ）
     */
    public function index(Request $request)
    {
        // ✅ 管理者でない場合は403
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403, '監査ログを閲覧する権限がありません。');
        }

        // ✅ グループ選択チェック
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // ✅ ログクエリ（選択中グループのみ）
        $query = AuditLog::with(['user', 'target'])
            ->where('group_id', $groupId);

        // 🔍 フィルタ：アクション種別
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // 🔍 フィルタ：対象モデルタイプ
        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        // 🔍 フィルタ：ユーザー名
        if ($request->filled('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%');
            });
        }

        // 🔍 フィルタ：日付範囲
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ⏰ 並び順・ページネーション
        $logs = $query->orderByDesc('created_at')->paginate(15);

        return view('audit_logs.index', compact('logs'));
    }
}

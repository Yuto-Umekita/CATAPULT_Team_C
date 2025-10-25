<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    /**
     * 📜 監査ログ一覧（管理者のみ・グループ対応）
     */
    public function index(Request $request)
    {
        // ✅ 管理者チェック
        if (!Auth::check() || !Auth::user()->is_admin) {
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

        // 🔍 アクション種別フィルタ
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // 🔍 対象モデルタイプフィルタ
        if ($request->filled('target_type')) {
            $query->where('target_type', $request->target_type);
        }

        // 🔍 ユーザー名フィルタ
        if ($request->filled('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user_name . '%');
            });
        }

        // 🔍 日付範囲フィルタ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ✅ 並び替え & ページネーション
        $logs = $query->orderByDesc('created_at')->paginate(15);

        return view('audit_logs.index', compact('logs'));
    }
}

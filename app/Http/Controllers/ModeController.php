<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Profile;
use App\Models\Group;

class ModeController extends Controller
{
    /**
     * 🌈 モード選択画面を表示
     */
    public function index()
    {
        $user = Auth::user();

        // 現在のモード（セッション → プロフィール → デフォルト household）
        $currentMode = Session::get('mode')
            ?? optional($user->profile)->user_type
            ?? 'household';

        return view('mode.select', compact('currentMode'));
    }

    /**
     * 💾 モード選択の保存とリダイレクト
     *
     * 仕様：
     * - 企業/家庭 いずれも、当該モードで所属グループがあれば /group/select、無ければ /groups/create
     */
    public function store(Request $request)
    {
        // 🔍 バリデーション（UIは home / company を送る前提）
        $validated = $request->validate([
            'user_type' => 'required|in:home,company',
        ]);

        // 🧠 内部モードに正規化（household / enterprise）
        $mode = $validated['user_type'] === 'home' ? 'household' : 'enterprise';

        // 💾 セッションへ保存（既存選択はクリア）
        Session::put('mode', $mode);
        Session::forget('selected_group_id');

        // 👤 プロフィールにも保存（初回は作成）
        $user = Auth::user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $profile->update(['user_type' => $mode]);

        // ============================================
        // 🧩 グループ存在チェック（作成者 or メンバー）
        // ============================================
        $hasGroup = Group::where('mode', $mode)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('members', function ($qq) use ($user) {
                      $qq->where('user_id', $user->id);
                  });
            })
            ->exists();

        // ✅ グループがある → 選択画面へ
        if ($hasGroup) {
            return redirect()
                ->route('group.select')
                ->with('success', ($mode === 'household' ? '家庭用' : '企業用') . 'モードを選択しました。グループを選択してください。');
        }

        // 🚪 グループがない → 作成画面へ
        return redirect()
            ->route('groups.create')
            ->with('info', ($mode === 'household' ? '家庭用' : '企業用') . 'グループがありません。新規作成してください。');
    }
}

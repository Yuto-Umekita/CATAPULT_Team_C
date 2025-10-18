<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeController extends Controller
{
    /**
     * モード選択画面を表示
     */
    public function select()
    {
        return view('mode.select');
    }

    /**
     * モード選択の保存とリダイレクト
     */
    public function store(Request $request)
    {
        $userType = $request->input('user_type');

        // 🧠 モードをセッションに保存
        if (in_array($userType, ['home', 'company'])) {
            session(['mode' => $userType]);
        }

        // 🏠 家庭モード
        if ($userType === 'home') {
            return redirect()->route('dashboard.home')
                ->with('status', '家庭モードでログインしました。');
        }
        // 🏢 企業モード
        elseif ($userType === 'company') {
            return redirect()->route('dashboard.company')
                ->with('status', '企業モードでログインしました。');
        }

        // ⚠️ 不正な入力
        return redirect()->back()->with('error', '無効な選択です');
    }
}

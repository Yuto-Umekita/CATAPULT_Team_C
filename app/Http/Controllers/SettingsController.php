<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // 🔸 リクエスト内容を確認するため一時ログ出力（デバッグ用）
        \Log::info('Settings update request:', $request->all());

        $validated = $request->validate([
            'low_stock_threshold' => 'required|integer|min:1|max:50',
        ]);

        // チェックボックスは未チェックだと値が送られないため boolean() で処理
        $user->notify_low_stock = $request->boolean('notify_low_stock');
        $user->notify_recipe_updates = $request->boolean('notify_recipe_updates');
        $user->notify_system = $request->boolean('notify_system');
        $user->low_stock_threshold = $validated['low_stock_threshold'];
        $user->save();

        // DB保存後に最新状態で再表示
        $user->refresh();

        return redirect()->route('settings.index')->with('success', '設定を保存しました。');
    }
}

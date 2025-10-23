<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Memo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * 🏠 家庭モード用ダッシュボード（グループ対応）
     */
    public function home()
    {
        $user = Auth::user();
        $groupId = session('selected_group_id');

        // ✅ グループ未選択ならグループ選択ページへ
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // --------------------------------------------
        // 🧮 設定情報
        // --------------------------------------------
        $threshold = $user->low_stock_threshold ?? 3;
        $today = Carbon::today();
        $oneWeekLater = $today->copy()->addWeek();

        // --------------------------------------------
        // 📦 グループ内在庫データ取得
        // --------------------------------------------
        $expiredItems = Item::where('group_id', $groupId)
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', $today)
            ->get();

        $nearExpiredItems = Item::where('group_id', $groupId)
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [$today, $oneWeekLater])
            ->get();

        $memos = Memo::with(['item', 'user'])
            ->where('group_id', $groupId)
            ->latest()
            ->take(10)
            ->get();

        // --------------------------------------------
        // 🔔 通知生成（ユーザー設定に基づく）
        // --------------------------------------------
        $notifications = [];

        // 🧂 在庫数が閾値未満
        if ($user->notify_low_stock ?? false) {
            $lowStockItems = Item::where('group_id', $groupId)
                ->where('quantity', '<', $threshold)
                ->get();

            if ($lowStockItems->isNotEmpty()) {
                $names = $lowStockItems->pluck('item')->take(3)->implode('、');
                $notifications[] = "在庫が{$threshold}個未満になっています：{$names}";
            }
        }

        // ⚙️ システム通知
        if ($user->notify_system ?? false) {
            $notifications[] = "システムメンテナンスは 10月22日 03:00〜04:00 に予定されています。";
        }

        // --------------------------------------------
        // 🖥️ ビューへデータ送信
        // --------------------------------------------
        return view('dashboard.home', compact(
            'expiredItems',
            'nearExpiredItems',
            'memos',
            'notifications',
            'threshold',
            'groupId'
        ));
    }

    /**
     * 🏢 企業モード用ダッシュボード（グループ対応）
     */
    public function company()
    {
        $user = Auth::user();
        $groupId = session('selected_group_id');

        // ✅ グループ未選択時はリダイレクト
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // --------------------------------------------
        // 🧮 設定情報
        // --------------------------------------------
        $threshold = $user->low_stock_threshold ?? 5; // 企業用はデフォルト高め
        $today = Carbon::today();
        $oneWeekLater = $today->copy()->addWeek();

        // --------------------------------------------
        // 📦 グループ在庫取得（担当者付き）
        // --------------------------------------------
        $expiredItems = Item::with('user')
            ->where('group_id', $groupId)
            ->whereNotNull('expiration_date')
            ->whereDate('expiration_date', '<', $today)
            ->get();

        $nearExpiredItems = Item::with('user')
            ->where('group_id', $groupId)
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [$today, $oneWeekLater])
            ->get();

        $memos = Memo::with(['item', 'user'])
            ->where('group_id', $groupId)
            ->latest()
            ->take(10)
            ->get();

        // --------------------------------------------
        // 📣 通知生成（企業用）
        // --------------------------------------------
        $notifications = [];

        // 在庫不足通知
        if ($user->notify_low_stock ?? false) {
            $lowStockItems = Item::where('group_id', $groupId)
                ->where('quantity', '<', $threshold)
                ->get();

            if ($lowStockItems->isNotEmpty()) {
                $names = $lowStockItems->pluck('item')->take(3)->implode('、');
                $notifications[] = "在庫が{$threshold}個未満になっています：{$names}";
            }
        }

        // システム通知
        if ($user->notify_system ?? false) {
            $notifications[] = "【企業向け】システムメンテナンスは 10月22日 03:00〜04:00 に予定されています。";
        }

        // --------------------------------------------
        // 🖥️ ビューへデータ送信
        // --------------------------------------------
        return view('dashboard.company', compact(
            'expiredItems',
            'nearExpiredItems',
            'memos',
            'notifications',
            'threshold',
            'groupId'
        ));
    }
}

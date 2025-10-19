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
     * 🏠 家庭モード用ダッシュボード
     */
    public function home()
    {
        $user = Auth::user();
        $threshold = $user->low_stock_threshold ?? 3; // ← ここで定義
        $today = Carbon::today();
        $oneWeekLater = Carbon::today()->addWeek();

        // ----------------------------------------------------
        // 📦 データ取得（賞味期限＆メモ）
        // ----------------------------------------------------
        $expiredItems = Item::whereDate('expiration_date', '<', $today)->get();
        $nearExpiredItems = Item::whereDate('expiration_date', '>=', $today)
                                ->whereDate('expiration_date', '<=', $oneWeekLater)
                                ->get();
        $memos = Memo::with(['item', 'user'])->latest()->get();

        // ----------------------------------------------------
        // 🔔 通知生成（ユーザー設定に応じて）
        // ----------------------------------------------------
        $notifications = [];

        // 🧂 在庫が少ない商品通知
        if ($user->notify_low_stock ?? false) {
            $lowStockItems = Item::where('quantity', '<', $threshold)->get();
            if ($lowStockItems->count() > 0) {
                $names = $lowStockItems->pluck('item')->take(3)->implode('、');
                $notifications[] = "在庫が{$threshold}個未満になっています：{$names}";
            }
        }

        // ⚙️ システム通知（固定例）
        if ($user->notify_system ?? false) {
            $notifications[] = "システムメンテナンスは 10月22日 03:00〜04:00 に予定されています。";
        }

        // ----------------------------------------------------
        // 📤 ビューへデータ送信
        // ----------------------------------------------------
        return view('dashboard.home', compact(
            'expiredItems',
            'nearExpiredItems',
            'memos',
            'notifications',
            'threshold'
        ));
    }

    /**
     * 🏢 企業モード用ダッシュボード
     */
    public function company()
    {
        $user = Auth::user();
        $threshold = $user->low_stock_threshold ?? 3; // ← ここを追加
        $today = Carbon::today();
        $oneWeekLater = Carbon::today()->addWeek();

        $expiredItems = Item::whereDate('expiration_date', '<', $today)->get();
        $nearExpiredItems = Item::whereDate('expiration_date', '>=', $today)
                                ->whereDate('expiration_date', '<=', $oneWeekLater)
                                ->get();
        $memos = Memo::with(['item', 'user'])->latest()->get();

        $notifications = [];

        // 📣 企業向け通知
        if ($user->notify_system ?? false) {
            $notifications[] = "【企業向け】システムメンテナンス：10月22日 03:00〜04:00 に実施予定です。";
        }

        return view('dashboard.company', compact(
            'expiredItems',
            'nearExpiredItems',
            'memos',
            'notifications',
            'threshold' // ← ここで使ってもエラーにならない
        ));
    }
}

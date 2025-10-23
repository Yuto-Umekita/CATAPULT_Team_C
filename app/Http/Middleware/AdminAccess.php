<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ 1. 共通パスワードを通過済みセッションなら許可
        if (session('admin_authenticated') === true) {
            return $next($request);
        }

        // ✅ 2. ログイン中ユーザーが管理者なら許可（保険）
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // 🚫 どちらでもない場合は共通パスワードゲートへリダイレクト
        return redirect()->route('admin.password.gate.show')
            ->with('error', '共通パスワードを入力してください。');
    }
}

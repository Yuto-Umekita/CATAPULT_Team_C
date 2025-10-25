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
        // ✅ 1. このログインユーザーがゲートを通過済みなら許可
        if (Auth::check() && session('admin_authenticated_' . Auth::id()) === true) {
            return $next($request);
        }

        // ✅ 2. ログインユーザーが管理者なら許可（保険）
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // 🚫 どちらでもない場合 → ゲート画面へ
        return redirect()
            ->route('admin.gate.show')
            ->with('error', '共通パスワードを入力してください。');
    }
}

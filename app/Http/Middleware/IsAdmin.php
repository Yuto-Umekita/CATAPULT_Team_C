<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🔒 ユーザーが未ログインまたは管理者でない場合
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, '管理者権限が必要です。');
        }

        return $next($request);
    }
}

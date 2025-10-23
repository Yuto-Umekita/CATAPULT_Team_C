<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VerifyAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('admin_verified')) {
            return redirect()->route('admin.gate')->with('error', '管理者パスワードを入力してください。');
        }

        return $next($request);
    }
}

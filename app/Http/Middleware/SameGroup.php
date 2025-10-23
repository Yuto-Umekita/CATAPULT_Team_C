<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SameGroup
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $groupId = session('selected_group_id');

        // 🚫 グループ未選択 または 他グループのアクセスを禁止
        if (!$groupId || $user->group_id !== $groupId) {
            abort(403, '他のグループのページにはアクセスできません。');
        }

        return $next($request);
    }
}

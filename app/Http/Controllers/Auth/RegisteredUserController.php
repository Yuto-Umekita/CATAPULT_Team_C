<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\GroupInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // ✅ 入力バリデーション
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // ✅ ユーザー作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 登録イベント発火
        event(new Registered($user));

        // 自動ログイン
        Auth::login($user);

        // ✅ 招待トークンがある場合は自動承認
        if (session('pending_invite_token')) {
            $token = session('pending_invite_token');

            $invitation = GroupInvitation::where('token', $token)->first();

            if ($invitation && !$invitation->accepted) {
                // グループに自動参加
                $invitation->group->members()->attach($user->id, ['role' => 'member']);

                // 招待を承認済みに変更
                $invitation->update(['accepted' => true]);

                // セッションからトークン削除
                session()->forget('pending_invite_token');
            }
        }

        // ✅ 登録後はモード選択またはグループ選択ページへ誘導
        return redirect()->route('mode.select')
            ->with('success', '登録が完了しました。');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Profile;


class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $profile = $request->user()->profile()->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        // ✅ household / enterprise をセッションから取得
        $currentMode = session('mode');

        // セッションに何もないときは profile の設定を優先
        if (!$currentMode) {
            $currentMode = $profile->user_type ?? 'household';
        }

        return view('profile.edit', compact('profile', 'currentMode'));
    }



    public function update(Request $request)
    {
        $profile = $request->user()->profile()->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        // 共通＋条件付きバリデーション
        $rules = [
            'user_type' => ['required','in:household,enterprise'],
            // household のとき必須
            'gender'     => ['nullable','in:男性,女性,その他','required_if:user_type,household'],
            'age'        => ['nullable','integer','min:0','max:150','required_if:user_type,household'],
            'occupation' => ['nullable','string','max:255','required_if:user_type,household'],
            // enterprise のとき必須
            'contact_email' => ['nullable','email','max:255','required_if:user_type,enterprise'],
            'phone'         => ['nullable','string','max:50','required_if:user_type,enterprise'],
            'company_name'  => ['nullable','string','max:255','required_if:user_type,enterprise'],
            'position'      => ['nullable','string','max:255','required_if:user_type,enterprise'],
        ];

        $validated = $request->validate($rules);

        // household と enterprise で不要なフィールドはクリアしておくと安全
        if ($validated['user_type'] === 'household') {
            $validated = array_merge($validated, [
                'contact_email' => null,
                'phone' => null,
                'company_name' => null,
                'position' => null,
            ]);
        } else {
            $validated = array_merge($validated, [
                'gender' => null,
                'age' => null,
                'occupation' => null,
            ]);
        }

        $profile->update($validated);

        return redirect()->route('profile.edit')->with('status', 'プロフィールを更新しました');
    }

    public function show()
    {
        $user = auth()->user();
        $profile = $user->profile; // Profileモデルをリレーションしている前提
        return view('profile.show', compact('user', 'profile'));
    }


}

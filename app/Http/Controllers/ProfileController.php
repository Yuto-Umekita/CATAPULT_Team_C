<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\Profile;

class ProfileController extends Controller
{
    /**
     * ✏️ プロフィール編集画面
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        // ✅ プロフィールを取得 or 作成
        $profile = $user->profile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        // ✅ 現在のモード（セッション or プロフィール or デフォルト）
        $currentMode = session('mode') ?? $profile->user_type ?? 'household';

        // ✅ 現在のグループ（セッションから取得）
        $currentGroupId = session('selected_group_id');
        $currentGroup = null;

        if ($currentGroupId) {
            $currentGroup = \App\Models\Group::find($currentGroupId);
        }

        return view('profile.edit', compact('profile', 'currentMode', 'currentGroup'));
    }

    /**
     * 💾 プロフィール更新処理
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        // ✅ household / enterprise モードに応じたバリデーション
        $rules = [
            'user_type' => ['required', 'in:household,enterprise'],

            // 家庭用プロフィール項目
            'gender'     => ['nullable', 'in:男性,女性,その他', 'required_if:user_type,household'],
            'age'        => ['nullable', 'integer', 'min:0', 'max:150', 'required_if:user_type,household'],
            'occupation' => ['nullable', 'string', 'max:255', 'required_if:user_type,household'],

            // 企業用プロフィール項目
            'contact_email' => ['nullable', 'email', 'max:255', 'required_if:user_type,enterprise'],
            'phone'         => ['nullable', 'string', 'max:50', 'required_if:user_type,enterprise'],
            'company_name'  => ['nullable', 'string', 'max:255', 'required_if:user_type,enterprise'],
            'position'      => ['nullable', 'string', 'max:255', 'required_if:user_type,enterprise'],
        ];

        $validated = $request->validate($rules);

        // ✅ household と enterprise で不要なフィールドをクリア
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

        // ✅ 現在のモードをセッションに反映
        session(['mode' => $validated['user_type']]);

        return redirect()
            ->route('profile.edit')
            ->with('status', 'プロフィールを更新しました');
    }

    /**
     * 👤 プロフィール閲覧画面
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile;

        $currentGroupId = session('selected_group_id');
        $currentGroup = $currentGroupId
            ? \App\Models\Group::find($currentGroupId)
            : null;

        return view('profile.show', compact('user', 'profile', 'currentGroup'));
    }
}

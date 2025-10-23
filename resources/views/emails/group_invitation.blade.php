@component('mail::message')
# もちログ グループ招待のお知らせ

{{ $invitation->inviter->name }} さんが、あなたを **{{ $invitation->group->name }}** に招待しました。

グループ種別：{{ $invitation->group->mode === 'household' ? '家庭用' : '企業用' }}

---

@component('mail::button', ['url' => route('group.invite.accept', $invitation->token)])
招待を承認する
@endcomponent

このリンクをクリックすると、ログインまたは新規登録後に自動的にグループへ参加します。

@endcomponent

<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl sm:text-2xl font-semibold text-center text-gray-800 dark:text-gray-100 leading-tight">
      🧾 監査ログ
    </h2>
  </x-slot>

  <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- 🔍 検索フォーム --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg mb-8">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
        🔎 検索フィルター
      </h3>

      <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">操作内容</label>
          <select name="action" class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
            <option value="">すべて</option>
            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>作成</option>
            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>更新</option>
            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>削除</option>
          </select>
        </div>

        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">ユーザー名</label>
          <input type="text" name="user_name" placeholder="例: 田中太郎"
                 value="{{ request('user_name') }}"
                 class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
        </div>

        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">開始日</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}"
                 class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
        </div>
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">終了日</label>
          <input type="date" name="date_to" value="{{ request('date_to') }}"
                 class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
        </div>

        <div class="sm:col-span-3 flex justify-end mt-2">
          <button type="submit"
                  class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition">
            検索
          </button>
          <a href="{{ route('audit_logs.index') }}"
             class="ml-3 px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600 rounded-lg transition">
            リセット
          </a>
        </div>
      </form>
    </div>

    {{-- 🧾 ログ一覧 --}}
    <div class="space-y-5">
      @forelse($logs as $log)
        @php
          $rawChanges = $log->changes ?? [];
          $changes = is_string($rawChanges) ? json_decode($rawChanges, true) ?? [] : $rawChanges;
          $before = $changes['before'] ?? ($changes['old_values'] ?? []);
          $after  = $changes['after'] ?? ($changes['new_values'] ?? []);

          if (empty($before) && empty($after) && is_string($rawChanges) && str_contains($rawChanges, 'Before：')) {
              preg_match('/Before：\s*(\{.*?\})/us', $rawChanges, $bMatch);
              preg_match('/After：\s*(\{.*?\})/us',  $rawChanges, $aMatch);
              $before = isset($bMatch[1]) ? json_decode($bMatch[1], true) : [];
              $after  = isset($aMatch[1]) ? json_decode($aMatch[1], true) : [];
          }

          $formatDate = function ($value) {
              if (empty($value)) return '';
              if (preg_match('/^\d{4}-\d{2}-\d{2}/', (string)$value)) {
                  return \Carbon\Carbon::parse($value)->format('Y年m月d日');
              }
              return (string)$value;
          };

          $actionLabel = match($log->action) {
              'created' => '作成',
              'updated' => '更新',
              'deleted' => '削除',
              default   => strtoupper($log->action)
          };

          $targetName = $after['item'] ?? $before['item'] ?? ($log->target->item ?? class_basename($log->target_type));

          $rows = [];

          if ($log->action === 'created') {
              $rows[] = ['label' => '数量', 'html' => e($after['quantity'] ?? '')];
              $rows[] = ['label' => '賞味期限', 'html' => e($formatDate($after['expiration_date'] ?? ''))];
          }
          elseif ($log->action === 'updated') {
              $oldQ = $before['quantity'] ?? null;
              $newQ = $after['quantity']  ?? null;
              if ($oldQ !== null && $newQ !== null && $oldQ != $newQ) {
                  $qtyHtml = '<span class="text-red-500 line-through">'.e($oldQ).'</span> → <span class="text-green-600 font-medium">'.e($newQ).'</span>';
              } else {
                  $qtyHtml = e($newQ ?? $oldQ ?? '');
              }
              $rows[] = ['label' => '数量', 'html' => $qtyHtml];
              $rows[] = ['label' => '賞味期限', 'html' => e($formatDate($after['expiration_date'] ?? $before['expiration_date'] ?? ''))];
          }
          elseif ($log->action === 'deleted') {
              $rows[] = ['label' => '数量', 'html' => e($before['quantity'] ?? '')];
              $rows[] = ['label' => '賞味期限', 'html' => e($formatDate($before['expiration_date'] ?? ''))];
          }
        @endphp

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow hover:shadow-lg transition">

          {{-- ヘッダー --}}
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">
              {{ $log->created_at->format('Y-m-d H:i') }}
            </span>
            <span class="text-xs px-3 py-1 rounded-full
              {{ $log->action === 'created' ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-green-100' : '' }}
              {{ $log->action === 'updated' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-700 dark:text-yellow-100' : '' }}
              {{ $log->action === 'deleted' ? 'bg-red-100 text-red-700 dark:bg-red-700 dark:text-red-100' : '' }}">
              {{ $actionLabel }}
            </span>
          </div>

          {{-- 商品名（小さめ＋左寄せ） --}}
          <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4 text-left">
            {{ $targetName }}
          </h3>

          {{-- 変更内容（縦並び） --}}
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-sm border border-gray-200 dark:border-gray-600">
            <ul class="space-y-2">
              @foreach ($rows as $row)
                <li class="text-gray-700 dark:text-gray-200">
                  <span class="font-semibold">{{ $row['label'] }}：</span>
                  {!! $row['html'] !!}
                </li>
              @endforeach
            </ul>
          </div>

          <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
            👤 {{ $log->user->name ?? 'ゲスト / 不明' }}
          </p>
        </div>
      @empty
        <p class="text-center text-gray-500 dark:text-gray-400 mt-6">ログがありません。</p>
      @endforelse
    </div>

    {{-- ページネーション --}}
    <div class="mt-6">
      {{ $logs->withQueryString()->links() }}
    </div>

  </div>
</x-app-layout>

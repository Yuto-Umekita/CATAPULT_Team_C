<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CalendarEvent;
use App\Models\Item;
use Carbon\Carbon;

class CalendarEventController extends Controller
{
    /**
     * 📅 カレンダー画面表示
     */
    public function index()
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')->with('info', '先にグループを選択してください。');
        }

        $today = Carbon::today();

        $todayEvents = CalendarEvent::with('item')
            ->where('group_id', $groupId)
            ->whereDate('date', $today)
            ->orderBy('date')
            ->get();

        return view('calendar.index', compact('todayEvents'));
    }

    /**
     * 🧾 JSONでイベント一覧取得
     */
    public function fetch()
{
    // 🧭 現在選択中のグループIDを取得
    $groupId = session('selected_group_id');

    // 🚨 グループ未選択時はエラー返却（セキュリティのため）
    if (!$groupId) {
        return response()->json(['error' => 'グループが選択されていません。'], 403);
    }

    // ✅ グループ内のイベントのみ取得
    $events = CalendarEvent::with('item')
        ->where('group_id', $groupId)
        ->orderBy('date', 'asc')
        ->get()
        ->map(fn($e) => [
            'id' => $e->id,
            'title' => "{$e->type}：" . ($e->item->item ?? $e->item_name ?? '未指定') . "（{$e->quantity}）",
            'start' => $e->date instanceof \Carbon\Carbon ? $e->date->toDateString() : $e->date,
            'color' => $e->type === '入庫' ? '#16a34a' : '#3b82f6',
            'extendedProps' => [
                'status' => $e->status,
                'notes' => $e->notes,
            ],
        ]);

    return response()->json($events);
}



    /**
     * 📆 特定日イベントをJSONで取得
     */
    public function getByDate(Request $request)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return response()->json(['error' => 'グループ未選択です。'], 403);
        }

        $date = $request->input('date');
        if (!$date) {
            return response()->json(['error' => '日付が指定されていません'], 400);
        }

        $events = CalendarEvent::with('item')
            ->where('group_id', $groupId)
            ->whereDate('date', $date)
            ->orderBy('date')
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'type' => $e->type,
                'name' => $e->item->item ?? $e->item_name ?? '未指定',
                'quantity' => $e->quantity,
                'notes' => $e->notes,
                'status' => $e->status,
            ]);

        return response()->json($events);
    }

    /**
     * 🆕 イベント登録
     */
    public function store(Request $request)
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return response()->json(['error' => '先にグループを選択してください。'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:入庫,出庫',
            'date' => 'required|date',
            'quantity' => 'nullable|integer|min:1',
            'item_name' => 'nullable|string|max:255',
            'item_id' => 'nullable|exists:items,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['group_id'] = session('selected_group_id');
        
        // 出庫時：同名アイテムが複数存在する場合は候補を返す
        if ($validated['type'] === '出庫' && empty($validated['item_id']) && !empty($validated['item_name'])) {
            $matchedItems = Item::where('item', $validated['item_name'])
                ->where('group_id', $groupId)
                ->where('quantity', '>', 0)
                ->get();

            if ($matchedItems->count() > 1) {
                return response()->json([
                    'multiple' => true,
                    'options' => $matchedItems->map(fn($i) => [
                        'id' => $i->id,
                        'name' => $i->item,
                        'quantity' => $i->quantity,
                    ]),
                ]);
            } elseif ($matchedItems->count() === 1) {
                $validated['item_id'] = $matchedItems->first()->id;
            }
        }

        CalendarEvent::create($validated);
        return response()->json(['success' => true]);
    }

    /**
     * 🏷️ 日付変更（ドラッグ対応）
     */
    public function update(Request $request, CalendarEvent $event)
    {
        $this->authorizeGroupAccess($event);

        $request->validate(['date' => 'required|date']);
        $event->update(['date' => $request->date]);

        return response()->json(['success' => true]);
    }

    /**
     * 🗑️ イベント削除
     */
    public function destroy(CalendarEvent $event)
    {
        $this->authorizeGroupAccess($event);

        $event->delete();
        return response()->json(['success' => true]);
    }

    /**
     * 📜 履歴一覧（完了済イベント）
     */
    public function history()
    {
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')->with('info', '先にグループを選択してください。');
        }

        $completedEvents = CalendarEvent::with('item')
            ->where('group_id', $groupId)
            ->where('status', '完了')
            ->orderByDesc('date')
            ->get();

        return view('calendar.history', compact('completedEvents'));
    }

    /**
     * ✅ イベント完了処理
     */
    public function complete(CalendarEvent $event)
    {
        $this->authorizeGroupAccess($event);

        $event->update(['status' => '完了']);

        // 入庫処理
        if ($event->type === '入庫') {
            return redirect()->route('items.create')->with([
                'prefill_item_name' => $event->item_name ?? ($event->item->item ?? ''),
                'prefill_quantity' => $event->quantity,
                'calendar_event_id' => $event->id,
            ]);
        }

        // 出庫処理
        if ($event->type === '出庫' && $event->item_id && $event->quantity) {
            $item = Item::where('id', $event->item_id)
                ->where('group_id', $event->group_id)
                ->first();

            if ($item) {
                $newQuantity = max(0, $item->quantity - $event->quantity);
                $item->update(['quantity' => $newQuantity]);
            }
        }

        return back()->with('success', '予定を完了しました。');
    }

    /**
     * 🛡️ グループ権限チェック
     */
    private function authorizeGroupAccess(CalendarEvent $event)
    {
        $currentGroupId = session('selected_group_id');
        if ($event->group_id !== $currentGroupId) {
            abort(403, 'このイベントを操作する権限がありません。');
        }
    }
}

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
     * カレンダービュー
     */
    public function index()
    {
        $today = Carbon::today();
        $todayEvents = CalendarEvent::with('item')
            ->whereDate('date', $today)
            ->where('user_id', Auth::id())
            ->orderBy('date')
            ->get();

        return view('calendar.index', compact('todayEvents'));
    }

    /**
     * JSONでイベント一覧取得
     */
    public function fetch()
    {
        $events = CalendarEvent::with('item')
            ->where('user_id', Auth::id())
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                // ✅ item_name も考慮してタイトルを生成
                'title' => "{$e->type}：" . ($e->item->item ?? $e->item_name ?? '未指定') . "（{$e->quantity}）",
                'start' => $e->date->toDateString(),
                'color' => $e->type === '入庫' ? '#16a34a' : '#3b82f6',
                'extendedProps' => [
                    'status' => $e->status,
                    'notes' => $e->notes,
                ],
            ]);

        return response()->json($events);
    }

    /**
    * 特定日の予定をJSONで取得
    */
    public function getByDate(Request $request)
    {
        $date = $request->input('date');
        if (!$date) {
            return response()->json(['error' => '日付が指定されていません'], 400);
        }

        $events = CalendarEvent::with('item')
            ->where('user_id', Auth::id())
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
     * 新規登録
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'type' => 'required|in:入庫,出庫',
        'date' => 'required|date',
        'quantity' => 'nullable|integer|min:1',
        'item_name' => 'nullable|string|max:255',
        'item_id' => 'nullable|exists:items,id',
        'notes' => 'nullable|string|max:255',
    ]);

    $validated['user_id'] = Auth::id();

    // 出庫時に同名アイテムが複数ある場合は候補を返す
    if ($validated['type'] === '出庫' && empty($validated['item_id']) && !empty($validated['item_name'])) {
        $matchedItems = Item::where('item', $validated['item_name'])
            ->where('quantity', '>', 0)
            ->get();

        if ($matchedItems->count() > 1) {
            return response()->json([
                'multiple' => true,
                'options' => $matchedItems->map(fn($i) => [
                    'id' => $i->id,
                    'name' => $i->item,
                    'quantity' => $i->quantity,
                ])
            ]);
        } elseif ($matchedItems->count() === 1) {
            $validated['item_id'] = $matchedItems->first()->id;
        }
    }

    CalendarEvent::create($validated);
    return response()->json(['success' => true]);
}



    /**
     * ドラッグで日付変更
     */
    public function update(Request $request, CalendarEvent $event)
    {
        $request->validate(['date' => 'required|date']);
        $event->update(['date' => $request->date]);
        return response()->json(['success' => true]);
    }

    /**
     * 削除
     */
    public function destroy(CalendarEvent $event)
    {
        $event->delete();
        return response()->json(['success' => true]);
    }

    public function history()
    {
        $completedEvents = CalendarEvent::with('item')
            ->where('user_id', Auth::id())
            ->where('status', '完了')
            ->orderByDesc('date')
            ->get();

        return view('calendar.history', compact('completedEvents'));
    }

    public function complete(CalendarEvent $event)
    {
        // 予定を完了に更新
        $event->update(['status' => '完了']);

        // ---------------------------------
        // 入庫処理：在庫追加ページに遷移
        // ---------------------------------
        if ($event->type === '入庫') {
            // 入庫の場合 → 在庫登録ページに遷移（初期値を渡す）
            return redirect()->route('items.create')->with([
                'prefill_item_name' => $event->item_name ?? ($event->item->item ?? ''),
                'prefill_quantity' => $event->quantity,
                'calendar_event_id' => $event->id,
            ]);
        }

        // ---------------------------------
        // 出庫処理：該当在庫を減らす
        // ---------------------------------
        if ($event->type === '出庫' && $event->item_id && $event->quantity) {
            $item = Item::find($event->item_id);
            if ($item) {
                $newQuantity = max(0, $item->quantity - $event->quantity);
                $item->update(['quantity' => $newQuantity]);
            }
        }

        return back()->with('success', '予定を完了しました。');
    }

    }

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Item;

class InventoryCsvController extends Controller
{
    /** CSVカラム定義 */
    const HEADERS = [
        'id',
        '商品名',
        '在庫数',
        '賞味期限',
        'ユーザーID',
    ];

    /**
     * 🗂 CSV管理ページを表示（グループ選択必須・管理者専用）
     */
    public function index()
    {
        $user = Auth::user();

        // ✅ 未ログインまたは一般ユーザー → カスタムエラーページ
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->view('errors.403', [], 403);
        }


        // ✅ グループ未選択時はリダイレクト
        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        // ✅ 管理者OK → 在庫CSV画面へ
        return view('items.csv');
    }


    /**
     * 🟩 CSVエクスポート（グループ単位）
     */
    public function export(Request $request): StreamedResponse
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403);
        }

        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')->with('info', '先にグループを選択してください。');
        }

        $query = Item::where('group_id', $groupId);

        // 🔍 キーワード検索
        if ($kw = $request->input('q')) {
            $query->where('item', 'like', "%{$kw}%");
        }

        $items = $query->orderBy('item')->cursor();

        $filename = "inventory_group{$groupId}_" . now()->format('Ymd_His') . ".csv";

        return response()->streamDownload(function () use ($items) {
            echo "\xEF\xBB\xBF"; // Excel対応のBOM

            $out = fopen('php://output', 'w');
            fputcsv($out, self::HEADERS);

            foreach ($items as $item) {
                fputcsv($out, [
                    $item->id,
                    $item->item,
                    $item->quantity,
                    optional($item->expiration_date)?->format('Y-m-d'),
                    $item->user_id,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * 🟨 CSVインポート（グループ単位）
     */
    public function import(Request $request)
    {
        if (!Auth::user() || !Auth::user()->is_admin) {
            abort(403);
        }

        $groupId = session('selected_group_id');
        if (!$groupId) {
            return redirect()->route('group.select')
                ->with('info', '先にグループを選択してください。');
        }

        $request->validate([
            'csv_file' => 'required|file|mimetypes:text/csv,application/vnd.ms-excel|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->withErrors(['csv_file' => 'CSVファイルを読み込めませんでした。']);
        }

        // UTF-8 BOM除去 & ヘッダー検証
        $firstLine = fgets($handle);
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $headers = str_getcsv($firstLine);

        if ($headers !== self::HEADERS) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'ヘッダーが一致しません。テンプレートを使用してください。']);
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row)) === 0) continue;
            $rows[] = $row;
        }
        fclose($handle);

        if (empty($rows)) {
            return back()->withErrors(['csv_file' => 'データが空です。']);
        }

        DB::transaction(function () use ($rows, $groupId) {
            foreach ($rows as $cols) {
                [$id, $itemName, $quantity, $expirationDate, $userId] = array_pad($cols, 5, null);

                if (empty($itemName)) continue;

                $quantity = is_numeric($quantity) ? (int)$quantity : 0;

                $data = [
                    'item' => trim($itemName),
                    'quantity' => $quantity,
                    'expiration_date' => $expirationDate ?: null,
                    'user_id' => Auth::id(),
                    'group_id' => $groupId,
                ];

                // 既存データ更新 or 新規作成
                if ($id && Item::where('id', $id)->where('group_id', $groupId)->exists()) {
                    Item::where('id', $id)->update($data);
                } else {
                    Item::create($data);
                }
            }
        });

        return back()->with('status', '✅ CSVインポートが完了しました。');
    }

    /**
     * 🧾 テンプレート出力
     */
    public function template(): StreamedResponse
    {
        $filename = 'inventory_template.csv';

        return response()->streamDownload(function () {
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            $out = fopen('php://output', 'w');
            fputcsv($out, self::HEADERS);
            fputcsv($out, [null, 'りんご', 10, '2025-12-31', '']); // サンプル
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

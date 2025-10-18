<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Item;

class InventoryCsvController extends Controller
{
    /** CSVのカラム定義 */
    const HEADERS = [
        'id',
        '商品名',
        '在庫数',
        '賞味期限',
        'ユーザーID',
    ];

    /**
     * 🗂 CSV管理ページを表示
     */
    public function index()
    {
        return view('items.csv');
    }

    /**
     * 🟩 CSVエクスポート
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Item::query()
            ->where('user_id', Auth::id());

        if ($kw = $request->input('q')) {
            $query->where('item', 'like', "%{$kw}%");
        }

        $items = $query->orderBy('item')->cursor();

        $filename = 'inventory_' . Auth::id() . '_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($items) {
            // UTF-8 BOMを出力（Excel対策）
            echo "\xEF\xBB\xBF";

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
     * 🟨 CSVインポート
     */
    public function import(Request $request)
    {
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
            // 空行はスキップ
            if (count(array_filter($row)) === 0) continue;
            $rows[] = $row;
        }
        fclose($handle);

        if (empty($rows)) {
            return back()->withErrors(['csv_file' => 'データが空です。']);
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $cols) {
                [$id, $itemName, $quantity, $expirationDate, $userId] = array_pad($cols, 5, null);

                // 不正データをスキップ
                if (empty($itemName)) continue;

                $quantity = is_numeric($quantity) ? (int)$quantity : 0;

                $data = [
                    'item' => trim($itemName),
                    'quantity' => $quantity,
                    'expiration_date' => $expirationDate ?: null,
                    'user_id' => Auth::id(), // 他人のデータ登録を防止
                ];

                // 既存IDが本人のデータなら更新、それ以外は新規作成
                if ($id && Item::where('id', $id)->where('user_id', Auth::id())->exists()) {
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
            fputcsv($out, [null, 'りんご', 10, '2025-12-31', '']); // サンプル行
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

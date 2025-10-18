<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuditLogController,
    ProfileController,
    ModeController,
    MemoController,
    ItemController,
    RecipeController,
    RecipeBookmarkController,
    PurchaseListController,
    DashboardController,
    AdminController,
    TagController,
    ItemTagController,
    InventoryCsvController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| 一般ユーザー・管理者のルート定義
| 「auth」＝一般ユーザー、「auth:admin」＝管理者専用。
|--------------------------------------------------------------------------
*/

// ====================================================================
// 🌟 トップページ
// ====================================================================
Route::get('/', fn() => view('welcome'));

// ====================================================================
// 🌟 ログイン後：モード選択へリダイレクト
// ====================================================================
Route::get('/dashboard', fn() => redirect('/mode-select'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ====================================================================
// 🌟 家庭・企業のモード選択ページ
// ====================================================================
Route::middleware('auth')->group(function () {
    Route::get('/mode-select', [ModeController::class, 'select'])->name('mode.select');
    Route::post('/mode-select', [ModeController::class, 'store'])->name('mode.store');
});

// ====================================================================
// 🌟 一般ユーザー用ルート群
// ====================================================================
Route::middleware('auth')->group(function () {

    // --------------------------------------------------------------
    // 🧭 メニュー画面（全ページ統合UI）
    // --------------------------------------------------------------
    // resources/views/menu/index.blade.php を表示
    Route::get('/menu', function () {
        return view('menu.index'); // ファイルが menu/index.blade.php の場合
        // return view('menu'); // ファイルが menu.blade.php の場合はこちら
    })->name('menu.index');

    // --------------------------------------------------------------
    // 🏠 ダッシュボード（家庭 / 企業）
    // --------------------------------------------------------------
    Route::get('/dashboard/home', [DashboardController::class, 'home'])->name('dashboard.home');
    Route::get('/dashboard/company', [DashboardController::class, 'company'])->name('dashboard.company');

    // --------------------------------------------------------------
    // 🍳 レシピ関連 (Spoonacular API)
    // --------------------------------------------------------------
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/{id}', [RecipeController::class, 'show'])
        ->whereNumber('id')
        ->name('recipes.show');

    // --------------------------------------------------------------
    // 🔖 ブックマーク機能
    // --------------------------------------------------------------
    Route::get('/bookmarks', [RecipeBookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [RecipeBookmarkController::class, 'store'])->name('bookmarks.store');
    Route::delete('/bookmarks/{id}', [RecipeBookmarkController::class, 'destroy'])
        ->whereNumber('id')
        ->name('bookmarks.destroy');

    // --------------------------------------------------------------
    // 📦 在庫（Item）・メモ（Memo）
    // --------------------------------------------------------------
    Route::resource('items', ItemController::class);
    Route::resource('items.memos', MemoController::class);

    // --------------------------------------------------------------
    // 🏷 タグ関連
    // --------------------------------------------------------------
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    // アイテムごとのタグ操作
    Route::get('/items/{item}/tags', [ItemTagController::class, 'index'])->name('items.tags.index');
    Route::post('/items/{item}/tags/toggle', [ItemTagController::class, 'toggle'])->name('items.tags.toggle');

    // --------------------------------------------------------------
    // 📊 在庫CSVインポート・エクスポート（InventoryCsvController）
    // --------------------------------------------------------------
    Route::get('/items/csv', [InventoryCsvController::class, 'index'])->name('items.csv.index');
    Route::post('/items/csv/export', [InventoryCsvController::class, 'export'])->name('items.csv.export');
    Route::post('/items/csv/import', [InventoryCsvController::class, 'import'])->name('items.csv.import');
    Route::get('/items/csv/template', [InventoryCsvController::class, 'template'])->name('items.csv.template');

    // --------------------------------------------------------------
    // 👤 プロフィール
    // --------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --------------------------------------------------------------
    // 🛒 購入リスト
    // --------------------------------------------------------------
    Route::get('/purchase-lists', [PurchaseListController::class, 'index'])->name('purchase_lists.index');
    Route::post('/purchase-lists', [PurchaseListController::class, 'store'])->name('purchase_lists.store');
    Route::delete('/purchase-lists/{purchaseList}', [PurchaseListController::class, 'destroy'])
        ->whereNumber('purchaseList')
        ->name('purchase_lists.destroy');

    // --------------------------------------------------------------
    // 📜 監査ログ
    // --------------------------------------------------------------
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // 旧URL互換
    Route::get('/purchase-lists/audit-logs', fn() => redirect()->route('audit-logs.index'))
        ->name('legacy.audit-logs');
});

// ====================================================================
// 🌟 管理者用ルート群
// ====================================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // --------------------------------------------------------------
    // 🔑 管理者ログイン
    // --------------------------------------------------------------
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.submit');

    // --------------------------------------------------------------
    // 🧭 管理者専用ページ
    // --------------------------------------------------------------
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
});

// ====================================================================
// 🌟 Laravel Breeze / Jetstream 認証ルート
// ====================================================================
require __DIR__ . '/auth.php';

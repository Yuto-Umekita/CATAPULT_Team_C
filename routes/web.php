<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuditLogController,
    ProfileController,
    ModeController,
    MemoController,
    ItemController,
    IngredientController,
    RecipeController,
    RecipeBookmarkController,
    PurchaseListController,
    DashboardController,
    AdminController,
    TagController,
    ItemTagController,
    InventoryCsvController,
    SettingsController // ← 設定ページ用
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

// 🌟 ログイン後：モード選択ページへリダイレクト
Route::get('/dashboard', function () {
    return redirect()->route('mode.select'); // ← ここでモード選択へリダイレクト
})->middleware(['auth', 'verified'])->name('dashboard');


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
    // 🧭 メニュー画面
    // --------------------------------------------------------------
    Route::get('/menu', fn() => view('menu.index'))->name('menu.index');

    // --------------------------------------------------------------
    // ⚙️ 設定ページ（表示・更新）
    // --------------------------------------------------------------
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');

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
    // 🥦 食材（Ingredient）
    // --------------------------------------------------------------
    Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
    Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    // 🏷 タグ関連
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{id}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{id}', [TagController::class, 'destroy'])->name('tags.destroy');

});


    // アイテムごとのタグ操作
    Route::get('/items/{item}/tags', [ItemTagController::class, 'index'])->name('items.tags.index');
    Route::post('/items/{item}/tags/toggle', [ItemTagController::class, 'toggle'])->name('items.tags.toggle');

// 📊 在庫CSVインポート・エクスポート（管理者専用）
Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::get('/items/csv', [\App\Http\Controllers\InventoryCsvController::class, 'index'])->name('items.csv.index');
    Route::post('/items/csv/export', [\App\Http\Controllers\InventoryCsvController::class, 'export'])->name('items.csv.export');
    Route::post('/items/csv/import', [\App\Http\Controllers\InventoryCsvController::class, 'import'])->name('items.csv.import');
    Route::get('/items/csv/template', [\App\Http\Controllers\InventoryCsvController::class, 'template'])->name('items.csv.template');
});





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


    // 📜 監査ログ（管理者専用）
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware(['auth', 'admin'])
        ->name('audit-logs.index');


    // 旧URL互換
    Route::get('/purchase-lists/audit-logs', fn() => redirect()->route('audit-logs.index'))
        ->name('legacy.audit-logs');

    // --------------------------------------------------------------
    // 📌 ピン機能（Ajax対応）
    // --------------------------------------------------------------
    Route::post('/items/{item}/pin', [ItemController::class, 'togglePin'])->name('items.pin');


// ====================================================================
// 🌟 管理者用ルート群
// ====================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // --------------------------------------------------------------
    // 🧭 管理者ダッシュボード（管理者設定ページ）
    // --------------------------------------------------------------
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // ← あなたの管理者設定ページ
    })->name('dashboard');

    // --------------------------------------------------------------
    // 👑 管理者権限付与・解除
    // --------------------------------------------------------------
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])
        ->name('users.toggle-admin');
});



// 🌟 管理者設定ページ（全ユーザーアクセス可能）
// URL: /admin/settings-dashboard
Route::middleware(['auth'])->get('/admin/settings-dashboard', function () {
    return view('admin.dashboard'); // ← resources/views/admin/dashboard.blade.php
})->name('admin.settings.dashboard');



// ====================================================================
// 🌟 Laravel Breeze / Jetstream 認証ルート
// ====================================================================
require __DIR__ . '/auth.php';

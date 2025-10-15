<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuditLogController,
    ProfileController,
    ModeController,
    MemoController,
    ItemController,
    RecipeController,
    PurchaseListController,
    DashboardController,
    AdminController,
    TagController,
    ItemTagController
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

    // -------------------------------
    // 🏠 ダッシュボード
    // -------------------------------
    Route::get('/dashboard/home', [DashboardController::class, 'home'])->name('dashboard.home');
    Route::get('/dashboard/company', [DashboardController::class, 'company'])->name('dashboard.company');

    // -------------------------------
    // 🍳 レシピ
    // -------------------------------
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

    // -------------------------------
    // 📦 在庫（Item）・メモ（Memo）
    // -------------------------------
    Route::resource('items', ItemController::class);
    Route::resource('items.memos', MemoController::class);

    // -------------------------------
    // 🏷 タグ関連
    // -------------------------------
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');  // ←★ 編集機能 追加
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    // アイテムごとのタグ操作
    Route::get('/items/{item}/tags', [ItemTagController::class, 'index'])->name('items.tags.index');
    Route::post('/items/{item}/tags/toggle', [ItemTagController::class, 'toggle'])->name('items.tags.toggle');

    // -------------------------------
    // 👤 プロフィール
    // -------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // -------------------------------
    // 🛒 購入リスト
    // -------------------------------
    Route::get('/purchase-lists', [PurchaseListController::class, 'index'])->name('purchase_lists.index');
    Route::post('/purchase-lists', [PurchaseListController::class, 'store'])->name('purchase_lists.store');
    Route::delete('/purchase-lists/{purchaseList}', [PurchaseListController::class, 'destroy'])
        ->whereNumber('purchaseList')
        ->name('purchase_lists.destroy');

    // -------------------------------
    // 📜 監査ログ
    // -------------------------------
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // 旧URL互換
    Route::get('/purchase-lists/audit-logs', fn() => redirect()->route('audit-logs.index'))
        ->name('legacy.audit-logs');
});

// ====================================================================
// 🌟 管理者用ルート群
// ====================================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // -------------------------------
    // 🔑 管理者ログイン
    // -------------------------------
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.submit');

    // -------------------------------
    // 🧭 管理者専用ページ
    // -------------------------------
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    });
});

// ====================================================================
// 🌟 Laravel Breeze / Jetstream 認証ルート
// ====================================================================
require __DIR__ . '/auth.php';

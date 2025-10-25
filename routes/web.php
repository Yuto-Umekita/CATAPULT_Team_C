<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
    SettingsController,
    CalendarEventController,
    AdminGateController,
    GroupController,
    GroupSelectionController,
    GroupMemberController,
    GroupInvitationController
};

/*
|--------------------------------------------------------------------------
| Web Routes（完全版）
|--------------------------------------------------------------------------
| 一般ユーザー・管理者・グループ・招待機能を含むルート定義
|--------------------------------------------------------------------------
*/

// ====================================================================
// 🌟 ファビコン
// ====================================================================
Route::get('/favicon.ico', fn() => response()->file(public_path('favicon.ico')));
Route::get('/favicon.png', fn() => response()->file(public_path('favicon.png')));
Route::get('/favicon.svg', fn() => response()->file(public_path('favicon.svg')));

// ====================================================================
// 🌟 トップページ
// ====================================================================
Route::get('/', fn() => view('welcome'));

// 🌟 ログイン後：モード選択ページへ
Route::get('/dashboard', fn() => redirect()->route('mode.select'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ====================================================================
// 🌟 家庭・企業のモード選択
// ====================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/mode-select', [ModeController::class, 'index'])->name('mode.select');
    Route::post('/mode-select', [ModeController::class, 'store'])->name('mode.store');
});


// ====================================================================
// 🌟 グループ関連
// ====================================================================
Route::middleware(['web', 'auth'])->group(function () {
    Route::resource('groups', GroupController::class)->except(['show']);
    Route::get('/group/select', [GroupSelectionController::class, 'select'])->name('group.select');
    Route::post('/group/set', [GroupSelectionController::class, 'set'])->name('group.set');

    Route::get('/groups/{group}/members', [GroupMemberController::class, 'index'])->name('group.members.index');
    Route::get('/groups/{group}/members/create', [GroupMemberController::class, 'create'])->name('group.members.create');
    Route::post('/groups/{group}/members', [GroupMemberController::class, 'store'])->name('group.members.store');
    Route::delete('/groups/{group}/members/{user}', [GroupMemberController::class, 'destroy'])->name('group.members.destroy');

    Route::get('/group/invite/{token}', [GroupInvitationController::class, 'accept'])->name('group.invite.accept');
});



// ====================================================================
// 🌟 ダッシュボード（家庭・企業）
// ====================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/home/dashboard', [DashboardController::class, 'home'])->name('home.dashboard');
    Route::get('/company/dashboard', [DashboardController::class, 'company'])->name('company.dashboard');
});

// ====================================================================
// 🌟 一般ユーザー機能
// ====================================================================
Route::middleware(['auth'])->group(function () {
    // 🧭 メニュー
    Route::view('/menu', 'menu.index')->name('menu.index');

    // ⚙️ 設定
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/update-admin-password', [SettingsController::class, 'updateAdminPassword'])
        ->middleware(['admin.access'])
        ->name('settings.updateAdminPassword');

    // 🍳 レシピ関連
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/{id}', [RecipeController::class, 'show'])->whereNumber('id')->name('recipes.show');

    // 🔖 ブックマーク
    Route::resource('bookmarks', RecipeBookmarkController::class)->only(['index', 'store', 'destroy']);

    // 🗓️ カレンダー
    Route::controller(CalendarEventController::class)->group(function () {
        Route::get('/calendar', 'index')->name('calendar.index');
        Route::get('/calendar/events', 'fetch')->name('calendar.fetch');
        Route::post('/calendar/events', 'store')->name('calendar.store');
        Route::put('/calendar/events/{event}', 'update')->name('calendar.update');
        Route::delete('/calendar/events/{event}', 'destroy')->name('calendar.destroy');
        Route::post('/calendar/events/{event}/complete', 'complete')->name('calendar.complete');
        Route::get('/calendar/history', 'history')->name('calendar.history');
        Route::get('/calendar/date', 'getByDate')->name('calendar.byDate');
    });

    // 📊 在庫CSV
    Route::controller(InventoryCsvController::class)->group(function () {
        Route::get('/items/csv', 'index')->name('items.csv.index');
        Route::post('/items/csv/export', 'export')->name('items.csv.export');
        Route::post('/items/csv/import', 'import')->name('items.csv.import');
        Route::get('/items/csv/template', 'template')->name('items.csv.template');
    });

    // 📦 在庫管理
    Route::resource('items', ItemController::class);
    Route::post('/items/{item}/pin', [ItemController::class, 'togglePin'])->name('items.pin');
    Route::resource('items.memos', MemoController::class);

    // 🥦 食材
    Route::resource('ingredients', IngredientController::class)->except(['show', 'create', 'edit']);

    // 🏷 タグ
    Route::resource('tags', TagController::class)->except(['show', 'create', 'edit']);

    // アイテムタグ操作
    Route::get('/items/{item}/tags', [ItemTagController::class, 'index'])->name('items.tags.index');
    Route::post('/items/{item}/tags/toggle', [ItemTagController::class, 'toggle'])->name('items.tags.toggle');

    // 🛒 購入予定品
    Route::resource('purchase_lists', PurchaseListController::class)->only(['index', 'store', 'destroy']);

    // 👤 プロフィール
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
        Route::get('/profile/view', 'show')->name('profile.view');
    });
});

// ====================================================================
// 💡 共通パスワードゲート（admin/gate.blade.php 対応）
// ====================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/password-gate', [AdminGateController::class, 'show'])->name('admin.password.gate.show');
    Route::post('/admin/password-gate', [AdminGateController::class, 'check'])->name('admin.password.gate.check');
});


// ====================================================================
// 🌟 管理者専用ルート群
// ====================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.access'])->group(function () {
    // ✅ 修正：ビュー直返しではなく Controller 経由に変更
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/update-admin-password', [SettingsController::class, 'updateAdminPassword'])->name('password.update');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('/toggle-self', [AdminController::class, 'toggleSelf'])->name('toggle.self');
});



// ====================================================================
// 📜 監査ログ（管理者専用）
// ====================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/audit-logs', function () {
        $user = Auth::user();

        // 一般ユーザーは403エラーを返す
        if (!$user->is_admin) {
            abort(403, 'このページにアクセスする権限がありません。');
        }

    // 管理者ならコントローラへ
    return app(AuditLogController::class)->index(request());
    })->name('audit_logs.index'); // ✅ ← 名前を正しく設定
});

// ====================================================================
// 🌟 Laravel Breeze / Jetstream 認証ルート
// ====================================================================
require __DIR__ . '/auth.php';

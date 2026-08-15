<?php

use Illuminate\Support\Facades\Route;

// --- IMPORT CONTROLLER SELLER ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\ShopController;
use App\Http\Controllers\Seller\ChatController as SellerChatController;

/*
|--------------------------------------------------------------------------
| Web Routes - Pondasikita Seller Center
|--------------------------------------------------------------------------
*/

// Redirect root domain langsung ke halaman login seller
Route::get('/', function () {
    return redirect()->route('seller.login');
})->name('home');

// Pengajuan Banding Akun (Dibutuhkan oleh seller)
Route::post('/account/appeal', [SellerController::class, 'submitAppeal'])->name('account.appeal')->middleware('auth');

// 1. AUTHENTICATION SYSTEM UNTUK SELLER
Route::controller(AuthController::class)->group(function () {
    Route::get('/seller/login', 'showLoginSeller')->name('seller.login');
    Route::post('/seller/login', 'loginSeller')->name('seller.login.process');
    Route::get('/seller/register', 'showRegisterSeller')->name('seller.register');
    Route::post('/seller/register', 'registerSeller')->name('seller.register.process');

    Route::post('/logout', 'logoutSeller')->name('logout');
});

// 2. SELLER CENTER (PROTECTED ROUTES)
Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Product Management
    Route::get('/products/template', [SellerProductController::class, 'downloadTemplate'])->name('products.template');
    Route::post('/products/import', [SellerProductController::class, 'importExcel'])->name('products.import');
    Route::resource('products', SellerProductController::class)->except(['show']);
    Route::post('/products/toggle-status', [SellerProductController::class, 'toggleStatus'])->name('products.toggle');

    // Order & Logistics
    Route::prefix('orders')->name('orders.')->group(function() {
        Route::get('/', [SellerController::class, 'pesanan'])->name('index');
        Route::post('/update-status', [SellerController::class, 'updateOrderStatus'])->name('updateStatus');
        Route::post('/mass-update', [SellerController::class, 'massUpdateOrderStatus'])->name('massUpdate');
        Route::post('/pelunasan-dp', [SellerController::class, 'pelunasanDp'])->name('pelunasan_dp');
        Route::get('/return', [SellerController::class, 'pengembalian'])->name('return');
        Route::post('/return/process', [SellerController::class, 'processPengembalian'])->name('return.process');
        Route::get('/{invoice}/detail', [SellerController::class, 'detailPesanan'])->name('show');
    });

    // Shipping Settings
    Route::prefix('pengaturan')->name('pengaturan.')->group(function() {
        Route::get('/pengiriman', [SellerController::class, 'pengaturanPengiriman'])->name('pengiriman');
        Route::post('/pengiriman/store', [SellerController::class, 'storePengiriman'])->name('pengiriman.store');
        Route::post('/pengiriman/toggle', [SellerController::class, 'togglePengiriman'])->name('pengiriman.toggle');
        Route::delete('/pengiriman/{id}', [SellerController::class, 'destroyPengiriman'])->name('pengiriman.destroy');
    });

    // Promotions & Marketing
    Route::prefix('promotion')->name('promotion.')->group(function() {
        Route::get('/discounts', [SellerController::class, 'promosi'])->name('discounts');
        Route::post('/discounts/update', [SellerController::class, 'updateDiscount'])->name('discounts.update');
        Route::get('/vouchers', [SellerController::class, 'voucher'])->name('vouchers');
        Route::post('/vouchers/store', [SellerController::class, 'storeVoucher'])->name('vouchers.store');
        Route::post('/vouchers/toggle', [SellerController::class, 'toggleVoucher'])->name('vouchers.toggle');
        Route::delete('/vouchers/{id}', [SellerController::class, 'destroyVoucher'])->name('vouchers.destroy');
    });

    // Customer Service (Chat & Reviews)
    Route::prefix('service')->name('service.')->group(function() {
        Route::get('/chat', [SellerChatController::class, 'chat'])->name('chat');
        Route::get('/chat/list', [SellerChatController::class, 'getChatList'])->name('chat.list');
        Route::get('/chat/messages/{chatId}', [SellerChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/chat/send', [SellerChatController::class, 'sendMessage'])->name('chat.send');

        Route::get('/reviews', [SellerController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/reply', [SellerController::class, 'replyReview'])->name('reviews.reply');
    });

    // Finance & Wallet
    Route::prefix('finance')->name('finance.')->group(function() {
        Route::get('/income', [SellerController::class, 'income'])->name('income');
        Route::post('/payout', [SellerController::class, 'requestPayout'])->name('payout');
        Route::get('/bank', [SellerController::class, 'bank'])->name('bank');
        Route::post('/bank/update', [SellerController::class, 'updateBank'])->name('bank.update');
        Route::post('/bank/destroy', [SellerController::class, 'destroyBank'])->name('bank.destroy');
    });

    // Data Analytics
    Route::prefix('data')->name('data.')->group(function() {
        Route::get('/performance', [SellerController::class, 'performance'])->name('performance');
        Route::get('/performance/export-pdf', [SellerController::class, 'exportPerformancePdf'])->name('performance.export');
        Route::get('/health', [SellerController::class, 'health'])->name('health');
        Route::post('/health/appeal', [SellerController::class, 'submitAppeal'])->name('health.appeal');
        Route::get('/health/export-pdf', [SellerController::class, 'exportHealthPdf'])->name('health.export');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function() {
        Route::get('/fetch', [SellerController::class, 'fetchNotifications'])->name('fetch');
        Route::post('/read/{id}', [SellerController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [SellerController::class, 'markAllAsRead'])->name('readAll');
    });

    // Shop Management
    Route::prefix('shop')->name('shop.')->group(function() {
        Route::get('/profile', [ShopController::class, 'profile'])->name('profile');
        Route::put('/profile/update', [ShopController::class, 'updateProfile'])->name('profile.update');

        Route::get('/tier', [ShopController::class, 'tier'])->name('tier');
        Route::post('/tier/apply', [ShopController::class, 'applyTierUpgrade'])->name('tier.apply');

        Route::get('/decoration', [ShopController::class, 'decoration'])->name('decoration');
        Route::get('/decoration/editor', [ShopController::class, 'editor'])->name('decoration.editor');
        Route::get('/decoration/editor-desktop', [ShopController::class, 'editorDesktop'])->name('decoration.editor.desktop');
        Route::get('/decoration/template', [ShopController::class, 'templateSelection'])->name('decoration.template');
        Route::post('/decoration/update', [ShopController::class, 'updateDecoration'])->name('decoration.update');
        Route::post('/decoration/save', [ShopController::class, 'saveDecoration'])->name('decoration.save');

        Route::get('/settings', [ShopController::class, 'settings'])->name('settings');
        Route::put('/settings/update', [ShopController::class, 'updateSettings'])->name('settings.update');

        Route::get('/security', [ShopController::class, 'securityIndex'])->name('security');
        Route::post('/security/send-otp', [ShopController::class, 'sendSecurityOtp'])->name('security.sendOtp');
        Route::post('/security/verify-otp', [ShopController::class, 'verifySecurityOtp'])->name('security.verifyOtp');
        Route::put('/security/reset-password', [ShopController::class, 'resetPassword'])->name('security.resetPassword');
    });

    // Point of Sale (POS)
    Route::prefix('pos')->name('pos.')->group(function() {
        Route::get('/', [SellerController::class, 'pos'])->name('index');
        Route::get('/api/products', [SellerController::class, 'getPosProducts'])->name('api.products');
        Route::get('/api/categories', [SellerController::class, 'getPosCategories'])->name('api.categories');
        Route::post('/api/checkout', [SellerController::class, 'processPosCheckout'])->name('api.checkout');
        Route::get('/print/{invoice}', [SellerController::class, 'printStruk'])->name('print');
    });
});

// ROUTE PENJAGA PINTU MEDIA PRIVATE (Bila seller butuh akses file chat)
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/media/{filename}', function ($filename) {
        $path = 'private_chats/' . $filename;
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($path)) { 
            abort(404, 'File media tidak ditemukan.'); 
        }
        return response()->file(storage_path('app/' . $path));
    })->name('chat.file');
});

// ========================================================
// RUTE SAPU JAGAT
// ========================================================
Route::get('/bersih', function() {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return "<h1>Sapu Jagat Berhasil!</h1><p>Semua memori lama sudah dihapus.</p>";
});
<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BusinessSettingController as AdminBusinessSettingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PromoPopupController as AdminPromoPopupController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SearchController as AdminSearchController;
use App\Http\Controllers\Admin\ToolsController as AdminToolsController;
use App\Http\Controllers\Admin\MarketplaceController as AdminMarketplaceController;
use App\Http\Controllers\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Admin\ReturnSupportController as AdminReturnSupportController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\AdminAccessController as AdminAccessController;
use App\Http\Controllers\Admin\PromoCodeController as AdminPromoCodeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Marketplace\HomeController as MarketplaceHomeController;
use App\Http\Controllers\Marketplace\ProductController as MarketplaceProductController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewReplyController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\OnboardingController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\SettingsController as SellerSettingsController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::post('/language/{locale}',function(\Illuminate\Http\Request $request,string $locale){$settings=\App\Models\BusinessSetting::current();$enabled=$settings->enabled_locales?:['ru','tk','en'];abort_unless(in_array($locale,$enabled,true),404);$request->session()->put('locale',$locale);return back();})->whereIn('locale',['ru','tk','en'])->name('language.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::redirect('/dashboard', '/')->middleware('auth')->name('dashboard');
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/search-suggest', [ProductController::class, 'suggest'])->name('search.suggest');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/track-order', [OrderController::class, 'track'])->name('orders.track');
Route::post('/track-order', [OrderController::class, 'lookup'])
    ->middleware('throttle:10,1')
    ->name('orders.lookup');

Route::prefix('marketplace')->name('marketplace.')->group(function () {
    Route::get('/', [MarketplaceHomeController::class, 'index'])->name('home');
    Route::get('/catalog', [MarketplaceProductController::class, 'index'])->name('catalog');
    Route::get('/search-suggest', [MarketplaceProductController::class, 'suggest'])->name('search.suggest');
    Route::get('/products/{slug}', [MarketplaceProductController::class, 'show'])->name('products.show');
});

Route::prefix('marketplace')->group(function () {
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/{slug}', [StoreController::class, 'show'])->name('stores.show');
});

Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/reviews', [ProductController::class, 'storeReview'])->name('products.reviews.store');
    Route::post('/reviews/{review}/replies', [ReviewReplyController::class, 'store'])->name('reviews.replies.store');
    Route::delete('/review-replies/{reply}', [ReviewReplyController::class, 'destroy'])->name('reviews.replies.destroy');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/item/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/item/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist', [WishlistController::class, 'clear'])->name('wishlist.clear');

    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
    Route::post('/compare/{product}', [CompareController::class, 'toggle'])->name('compare.toggle');
    Route::delete('/compare', [CompareController::class, 'clear'])->name('compare.clear');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/repeat', [OrderController::class, 'repeat'])->name('orders.repeat');

    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::get('/notifications/{id}/open', [NotificationController::class, 'open'])->name('notifications.open');

    Route::get('/sell', [OnboardingController::class, 'create'])->name('seller.become');
    Route::post('/sell', [OnboardingController::class, 'store'])->name('seller.become.store');

    Route::prefix('marketplace')->group(function () {
        Route::post('/stores/{slug}/reviews', [StoreController::class, 'storeReview'])->name('stores.reviews.store');
        Route::post('/stores/{slug}/report', [StoreController::class, 'storeReport'])->name('stores.report');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/', [SellerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [SellerProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [SellerProductController::class, 'create'])->name('products.create');
    Route::post('/products', [SellerProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [SellerProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [SellerProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [SellerProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{orderItem}', [SellerOrderController::class, 'update'])->name('orders.update');

    Route::get('/settings', [SellerSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SellerSettingsController::class, 'update'])->name('settings.update');
    Route::post('/vip', [SellerSettingsController::class, 'activateVip'])->name('vip.activate');
});

Route::middleware(['auth', 'role:admin', 'admin.permission', 'admin.audit'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', AdminSearchController::class)->name('search');
    Route::get('/tools', [AdminToolsController::class, 'index'])->name('tools');
    Route::get('/marketplace', [AdminMarketplaceController::class, 'index'])->name('marketplace');
    Route::get('/finance', [AdminFinanceController::class, 'index'])->name('finance.index');
    Route::get('/returns', [AdminReturnSupportController::class, 'returns'])->name('returns.index');
    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/admins', [AdminAccessController::class, 'admins'])->name('admins.index');
    Route::get('/promocodes', [AdminPromoCodeController::class, 'index'])->name('promocodes.index');
    Route::post('/promocodes', [AdminPromoCodeController::class, 'store'])->name('promocodes.store');
    Route::patch('/promocodes/{promocode}', [AdminPromoCodeController::class, 'update'])->name('promocodes.update');
    Route::delete('/promocodes/{promocode}', [AdminPromoCodeController::class, 'destroy'])->name('promocodes.destroy');
    Route::post('/admins', [AdminAccessController::class, 'store'])->name('admins.store');
    Route::patch('/admins/{admin}', [AdminAccessController::class, 'update'])->name('admins.update');
    Route::get('/audit', [AdminAccessController::class, 'audit'])->name('audit.index');
    Route::patch('/reviews/{review}', [AdminReviewController::class, 'moderate'])->name('reviews.moderate');
    Route::get('/inventory/export', [AdminInventoryController::class, 'export'])->name('inventory.export');
    Route::get('/inventory/{product}', [AdminInventoryController::class, 'show'])->name('inventory.show');
    Route::post('/inventory/{product}/adjust', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('/inventory/{product}/variants', [AdminInventoryController::class, 'variant'])->name('inventory.variants.store');
    Route::delete('/inventory/{product}/variants/{variant}', [AdminInventoryController::class, 'destroyVariant'])->name('inventory.variants.destroy');
    Route::patch('/returns/{return}', [AdminReturnSupportController::class, 'resolveReturn'])->name('returns.resolve');
    Route::get('/support', [AdminReturnSupportController::class, 'tickets'])->name('support.index');
    Route::get('/payments', [AdminFinanceController::class, 'payments'])->name('payments.index');
    Route::get('/payouts', [AdminFinanceController::class, 'payouts'])->name('payouts.index');
    Route::post('/payouts', [AdminFinanceController::class, 'storePayout'])->name('payouts.store');
    Route::patch('/payouts/{payout}/complete', [AdminFinanceController::class, 'completePayout'])->name('payouts.complete');
    Route::get('/settings', [AdminBusinessSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminBusinessSettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-mail', [AdminBusinessSettingController::class, 'testMail'])->name('settings.test-mail');

    Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [AdminBannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/{banner}/edit', [AdminBannerController::class, 'edit'])->name('banners.edit');
    Route::put('/banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');
    Route::post('/banners/{banner}/toggle', [AdminBannerController::class, 'toggle'])->name('banners.toggle');
    Route::post('/banners/reorder', [AdminBannerController::class, 'reorder'])->name('banners.reorder');

    Route::get('/popups', [AdminPromoPopupController::class, 'index'])->name('popups.index');
    Route::get('/popups/create', [AdminPromoPopupController::class, 'create'])->name('popups.create');
    Route::post('/popups', [AdminPromoPopupController::class, 'store'])->name('popups.store');
    Route::get('/popups/{popup}/edit', [AdminPromoPopupController::class, 'edit'])->name('popups.edit');
    Route::put('/popups/{popup}', [AdminPromoPopupController::class, 'update'])->name('popups.update');
    Route::delete('/popups/{popup}', [AdminPromoPopupController::class, 'destroy'])->name('popups.destroy');
    Route::post('/popups/{popup}/toggle', [AdminPromoPopupController::class, 'toggle'])->name('popups.toggle');

    Route::get('/sellers', [AdminSellerController::class, 'index'])->name('sellers');
    Route::post('/sellers/{seller}/approve', [AdminSellerController::class, 'approve'])->name('sellers.approve');
    Route::post('/sellers/{seller}/reject', [AdminSellerController::class, 'reject'])->name('sellers.reject');
    Route::post('/sellers/{seller}/block', [AdminSellerController::class, 'block'])->name('sellers.block');
    Route::post('/reports/{report}/resolve', [AdminSellerController::class, 'resolveReport'])->name('reports.resolve');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products');
    Route::resource('categories', AdminCategoryController::class)->except('show');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/approve', [AdminProductController::class, 'approve'])->name('products.approve');
    Route::post('/products/{product}/reject', [AdminProductController::class, 'reject'])->name('products.reject');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/print', [AdminOrderController::class, 'print'])->name('orders.print');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::post('/users/{user}/block', [AdminUserController::class, 'block'])->name('users.block');
});

require __DIR__.'/auth.php';

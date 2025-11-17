<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Seller\NotificationController;

// Public Front Pages
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/about', fn () => Inertia::render('About'))->name('about');
Route::get('/getStarted', fn () => Inertia::render('GetStarted'))->name('getStarted');


// Authenticated User Dashboard
Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


// User Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});


// Seller Routes
Route::middleware(['auth'])->prefix('seller')->name('seller.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');

    //shop
    Route::get('/seller/shop', [SellerController::class, 'index'])->name('seller.shop.edit');

    // Optional: If needed for guest/shop view
    Route::get('/shop', [SellerController::class, 'index'])->name('shop');

    // Shop submission
    Route::post('/shop', [SellerController::class, 'storeOrUpdate'])->name('shop.store');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Archive routes
    Route::get('/products/archived', [ProductController::class, 'archived'])->name('seller.products.archived');
    Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('seller.products.restore');
    Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('seller.products.forceDelete');

    // Orders
    Route::get('/orders', [OrderController::class, 'sellerOrders']);
    Route::post('/orders/{order}/approve', [OrderController::class, 'approve']);
    Route::post('/orders/{order}/decline', [OrderController::class, 'decline']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/delete-read', [NotificationController::class, 'deleteRead'])->name('notifications.deleteRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});


// Public Product Routes
Route::get('/view', [ProductController::class, 'showPublic'])->name('products.public');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// At the bottom or under Product routes
Route::get('/shop/{shop}/products', [ProductController::class, 'shopProducts'])->name('shop.products');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');


// Cart (User Only)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
});


// Orders
Route::middleware('auth')->group(function () {
    Route::post('/buy', [OrderController::class, 'store']);
    Route::post('/orders/bulk', [OrderController::class, 'storeBulk']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::post('/orders/{order}/received', [OrderController::class, 'markAsReceived']);
    Route::post('/orders/{order}/report', [OrderController::class, 'reportIssue']);
    Route::get('/orders/{order}', [OrderController::class, 'receipt']);
});

// Receipt (Public Link)
Route::get('/receipt/{order}', [OrderController::class, 'receipt']);

//For seller to see the receipt
Route::prefix('seller')->middleware('auth')->group(function () {
    Route::get('/orders/{order}/receipt', [OrderController::class, 'sellerReceipt'])->name('seller.receipt');
});

use App\Http\Controllers\User\UserNotificationController;

Route::middleware(['auth'])->group(function () {
    Route::get('/user/notifications', [UserNotificationController::class, 'index'])->name('user.notifications');

    Route::post('/user/notifications/mark-all-as-read', [UserNotificationController::class, 'markAllAsRead'])->name('user.notifications.markAll');
    Route::post('/user/notifications/{id}/mark-as-read', [UserNotificationController::class, 'markAsRead'])->name('user.notifications.markOne');
});

//For Buy
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout/{product}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my-orders');
});

Route::middleware('auth')->group(function () {
    Route::get('/checkout-bulk', [CheckoutController::class, 'bulkForm'])->name('checkout.bulk'); // optional
    Route::post('/checkout-bulk', [CheckoutController::class, 'bulkForm']); // receive selected items
    Route::post('/checkout-bulk/store', [CheckoutController::class, 'bulkStore'])->name('checkout.bulk.store');
});
Route::post('/checkout-bulk', [CheckoutController::class, 'bulkForm'])->name('checkout.bulkForm');


use App\Http\Controllers\ShopFollowController;

Route::middleware(['auth'])->post('/shop/{shop}/toggle-follow', [ShopFollowController::class, 'toggle']);

Route::post('/seller/orders/{order}/delivery-status', [OrderController::class, 'updateDeliveryStatus']);


use App\Http\Controllers\Seller\CustomProductController;

Route::get('/seller/custom-products/create', [CustomProductController::class, 'create'])->name('custom-products.create');
Route::post('/seller/custom-products', [CustomProductController::class, 'store'])->name('custom-products.store');


Route::post('/orders/{order}/review', [OrderController::class, 'submitReview']);



use App\Http\Controllers\OrderRatingController;


Route::post('/orders/{order}/rate-shop', [OrderRatingController::class, 'rateShop']);

Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);




use App\Http\Controllers\Seller\OrderNotificationController;

Route::get('/notifications', [OrderNotificationController::class, 'index'])->middleware('auth');
Route::post('/notifications/mark-all-as-read', [OrderNotificationController::class, 'markAllAsRead'])->middleware('auth');
Route::post('/notifications/{id}/mark-as-read', [OrderNotificationController::class, 'markAsRead']);



use App\Http\Controllers\MessageController;

Route::middleware(['auth'])->group(function () {

    Route::get('/user/inbox', [MessageController::class, 'userInboxList'])->name('user.inbox.list');  // Show list of shops
    Route::get('/user/inbox/{shop}', [MessageController::class, 'userInbox'])->name('user.inbox.chat'); // Chat with a shop


    Route::get('/seller/inbox', [MessageController::class, 'sellerInboxList'])->name('seller.inbox.list');// Show list of users
    Route::get('/seller/inbox/{user}', [MessageController::class, 'sellerInbox'])->name('seller.inbox.chat');// Chat with a customer


    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);

});



use App\Http\Controllers\Seller\AnalyticsController;

Route::middleware(['auth', 'verified'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
});



use App\Http\Controllers\AnnouncementController;

Route::prefix('admin')->group(function () {
    Route::get('/announcements', [AnnouncementController::class, 'adminIndex'])->name('admin.announcements');
    Route::get('/announcements/archived', [AnnouncementController::class, 'archived'])->name('admin.announcements.archived');
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
    Route::put('/announcements/{id}/restore', [AnnouncementController::class, 'restore']);
    Route::delete('/announcements/{id}/force', [AnnouncementController::class, 'forceDelete']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [AnnouncementController::class, 'index'])->name('user.dashboard');
});


use App\Http\Controllers\Admin\UserController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/archived', [UserController::class, 'archived'])->name('users.archived');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

    // Archive instead of delete
    Route::delete('/users/{user}', [UserController::class, 'archive'])->name('users.archive');

    // Restore + Permanent delete
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
});


use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Route::get('/apply-seller', [SellerApplicationController::class, 'showForm']);
// Route::post('/apply-seller', [SellerApplicationController::class, 'apply']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/apply-seller', [SellerApplicationController::class, 'showForm'])->name('seller.apply.form');
    Route::post('/apply-seller', [SellerApplicationController::class, 'apply'])->name('seller.apply.submit');
    Route::get('/apply-seller/status', [SellerApplicationController::class, 'status'])->name('seller.apply.status');
});


// use App\Http\Controllers\Admin\SellerApprovalController;


// Route::prefix('admin')->group(function () {
//     Route::get('/seller-applications', [SellerApprovalController::class, 'index']);
//     // Route::get('/seller-applications/{user}', [SellerApprovalController::class, 'show']);
//     Route::get('/admin/seller-applications/{user}', [SellerApprovalController::class, 'show'])
//     ->name('admin.seller-applications.show');
//     Route::post('/seller-applications/{user}/approve', [SellerApprovalController::class, 'approve']);
//     Route::post('/seller-applications/{user}/decline', [SellerApprovalController::class, 'decline']);
// });
use App\Http\Controllers\Admin\SellerApprovalController;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // ✅ Index (list of applications)
    Route::get('/seller-applications', [SellerApprovalController::class, 'index'])
        ->name('seller-applications.index');

    // ✅ Show single application
    Route::get('/seller-applications/{user}', [SellerApprovalController::class, 'show'])
        ->name('seller-applications.show');

    // ✅ Approve and Decline
    Route::post('/seller-applications/{user}/approve', [SellerApprovalController::class, 'approve'])
        ->name('seller-applications.approve');
    Route::post('/seller-applications/{user}/decline', [SellerApprovalController::class, 'decline'])
        ->name('seller-applications.decline');
});


Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/marketplace', [\App\Http\Controllers\Admin\AdminMarketplaceController::class, 'index'])->name('marketplace.index');
    Route::delete('/marketplace/{product}', [\App\Http\Controllers\Admin\AdminMarketplaceController::class, 'destroy'])->name('marketplace.destroy');
});



use App\Http\Controllers\AdminDashboardController;

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');


use App\Http\Controllers\Admin\AdminMarketplaceController;

// Marketplace
Route::prefix('admin/marketplace')->name('admin.marketplace.')->group(function () {
    Route::get('/', [AdminMarketplaceController::class, 'index'])->name('index');

    // ✅ Put this above the dynamic one
    Route::get('/archived', [AdminMarketplaceController::class, 'archived'])->name('archived');

    Route::post('/{product}/archive', [AdminMarketplaceController::class, 'archive'])->name('archive');
    Route::post('/{id}/restore', [AdminMarketplaceController::class, 'restore'])->name('restore');
    Route::delete('/{id}/force-delete', [AdminMarketplaceController::class, 'forceDelete'])->name('force-delete');

    // ✅ Keep this at the bottom
    Route::get('/{product}', [AdminMarketplaceController::class, 'show'])->name('show');
});

use App\Http\Controllers\Seller\OrderCancelNotificationController;

Route::prefix('seller/notifications/canceled')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [OrderCancelNotificationController::class, 'index'])->name('seller.notifications.canceled');
    Route::post('/{id}/read', [OrderCancelNotificationController::class, 'markAsRead']);
    Route::post('/mark-all-as-read', [OrderCancelNotificationController::class, 'markAllAsRead']);
});

Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->middleware('auth');


Route::get('/seller/orders/{order}/view', [OrderController::class, 'sellerView'])->name('seller.orders.view');
Route::get('/seller/orders', [OrderController::class, 'sellerOrders'])->name('seller.orders');


use App\Http\Controllers\ChatBotController;

// Chat page route (renders the Vue chatbot)
Route::get('/chat', function () {
    return Inertia::render('ChatBot');
})->name('chat');

// BotMan handle route (handles user messages)
Route::post('/botman', [ChatBotController::class, 'handle'])->name('botman.handle');

// use App\Http\Controllers\Seller\ProductController;

Route::get('/', [ProductController::class, 'showWelcomeProducts'])->name('welcome');

use App\Http\Controllers\Seller\ProductController as SellerProductController;

Route::get('/user/view', [SellerProductController::class, 'showPublic'])->name('user.view');

Route::get('/products/{id}/guest', [ProductController::class, 'showGuest'])->name('products.guest.show');

Route::get('/seller/analytics/export', [AnalyticsController::class, 'export'])->name('seller.analytics.export');

Route::get('/admin/dashboard/export', [AdminDashboardController::class, 'export'])
    ->name('admin.dashboard.export');

require __DIR__.'/auth.php';

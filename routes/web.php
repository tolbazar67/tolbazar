<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CustomerChargeRuleController;
use App\Http\Controllers\Admin\PartnerCommissionRuleController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceTaxRuleController;
use App\Http\Controllers\Admin\WalletAdminController;
use App\Http\Controllers\BranchStaffController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellerServiceController;
use App\Http\Controllers\SellerBranchController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SocialCommunityController;
use App\Http\Controllers\UserControllers;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SellerPaymentController;

// ✅ NAYA — seller-specific customer charge rules controller
use App\Http\Controllers\Seller\CustomerChargeRuleController as SellerChargeRuleController;

// ============================================================
// PUBLIC
// ============================================================
Route::get('/', [UserControllers::class, 'home'])->name('home');
Route::get('/u/{username}', [UserControllers::class, 'showProfile'])->name('profile.show');

// ============================================================
// PUBLIC MARKETPLACE
// ============================================================
Route::get('/services', [\App\Http\Controllers\CatalogController::class, 'services'])->name('services.index');
Route::get('/services/{serviceSlug}', [\App\Http\Controllers\CatalogController::class, 'serviceProducts'])->name('services.products');
Route::get('/services/{serviceSlug}/{productSlug}', [\App\Http\Controllers\CatalogController::class, 'showProduct'])->name('services.product');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',              [\App\Http\Controllers\CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [\App\Http\Controllers\CartController::class, 'add'])->name('add');
    Route::patch('/{item}',       [\App\Http\Controllers\CartController::class, 'update'])->name('update');
    Route::delete('/{item}',      [\App\Http\Controllers\CartController::class, 'remove'])->name('remove');
    Route::delete('/',            [\App\Http\Controllers\CartController::class, 'clear'])->name('clear');
});
Route::post('/booking/start', [\App\Http\Controllers\BookingController::class, 'start'])->name('booking.start');

// Checkout / Booking
Route::get('/checkout',  [\App\Http\Controllers\BookingController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [\App\Http\Controllers\BookingController::class, 'store'])->name('checkout.store');
Route::get('/booking/{reference}', [\App\Http\Controllers\BookingController::class, 'success'])->name('booking.success');

// ============================================================
// SUPER ADMIN SETUP
// ============================================================
Route::middleware(['web', \App\Http\Middleware\EnsureSuperAdminSetupAllowed::class])->group(function () {
    Route::get('system/setup', [\App\Http\Controllers\SuperAdminSetupController::class, 'showSetupForm'])->name('superadmin.setup');
    Route::post('system/setup', [\App\Http\Controllers\SuperAdminSetupController::class, 'createSetup'])->name('superadmin.setup.store');
});

// ============================================================
// GUEST (register / login / OTP / password reset / google)
// ============================================================
Route::middleware('guest')->group(function () {

    // Register
    Route::get('register', [UserControllers::class, 'showRegisterForm'])->name('register');
    Route::post('register', [UserControllers::class, 'register']);

    // ---------- CUSTOMER & DELIVERY PORTAL (public) ----------
    Route::get('login', [UserControllers::class, 'showLoginForm'])->name('login');
    Route::post('login', [UserControllers::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');

    // ---------- SELLER PORTAL ----------
    Route::prefix('seller')->name('seller.')->group(function () {
        Route::get('login', [UserControllers::class, 'showSellerLoginForm'])->name('login');
        Route::post('login', [UserControllers::class, 'sellerLogin'])
            ->middleware('throttle:10,1')
            ->name('login.attempt');
    });

    Route::prefix(config('auth.admin_login_path', 'admin-access-4b9f'))
        ->middleware('admin.ip:login')
        ->name('admin.auth.')
        ->group(function () {
            Route::get('login',  [UserControllers::class, 'showAdminLoginForm'])->name('login');
            Route::post('login', [UserControllers::class, 'adminLogin'])
                ->middleware('throttle:5,1')
                ->name('login.attempt');
        });

    // OTP verification
    Route::get('verify-otp', [UserControllers::class, 'showVerifyForm'])->name('verify.otp');
    Route::post('verify-otp', [UserControllers::class, 'verifyOtp']);
    Route::post('resend-otp', [UserControllers::class, 'resendOtp'])->name('resend.otp');

    // Forgot & reset password
    Route::get('forgot-password', [UserControllers::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [UserControllers::class, 'sendPasswordResetOtp'])->name('password.email');
    Route::get('reset-password', [UserControllers::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('reset-password', [UserControllers::class, 'resetPassword'])->name('password.reset');

    // Google OAuth
    Route::get('auth/google', [UserControllers::class, 'googleLogin'])->name('auth.google');
    Route::get('auth/google-callback', [UserControllers::class, 'googleAuthentication'])->name('auth.google-callback');
});

// ============================================================
// PUBLIC ACCOUNT — Reactivation / Cancel Deletion forms
// ============================================================
Route::get('/account/reactivate',      [UserControllers::class, 'showReactivateForm'])->name('account.reactivate.form');
Route::get('/account/cancel-deletion', [UserControllers::class, 'showCancelDeletionForm'])->name('account.cancel-deletion.form');
Route::post('/account/reactivate',      [UserControllers::class, 'reactivateAccount'])->name('account.reactivate');
Route::post('/account/cancel-deletion', [UserControllers::class, 'cancelDeletion'])->name('account.cancel-deletion');

// ============================================================
// AUTHENTICATED
// ============================================================
Route::middleware('auth')->group(function () {

    // ---------- Profile ----------
    Route::get('profile',            [UserControllers::class, 'myProfile'])->name('profile.me');
    Route::get('profile/edit',       [UserControllers::class, 'editProfile'])->name('profile.edit');
    Route::put('profile',            [UserControllers::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/picture',   [UserControllers::class, 'updateProfilePicture'])->name('profile.picture.update');
    Route::delete('profile/picture', [UserControllers::class, 'removeProfilePicture'])->name('profile.picture.remove');
    Route::post('profile/cover',     [UserControllers::class, 'updateCoverPhoto'])->name('profile.cover.update');
    Route::delete('profile/cover',   [UserControllers::class, 'removeCoverPhoto'])->name('profile.cover.remove');

    // ---------- Verification ----------
    Route::get('verification',                [UserControllers::class, 'verificationCenter'])->name('verification.center');
    Route::post('verification/email/resend',  [UserControllers::class, 'resendEmailVerification'])->name('verification.email.resend');
    Route::post('verification/mobile/send',   [UserControllers::class, 'sendMobileOtp'])->name('verification.mobile.send');
    Route::post('verification/mobile/verify', [UserControllers::class, 'verifyMobileOtp'])->name('verification.mobile.verify');

    // ---------- Auth control ----------
    Route::post('logout',             [UserControllers::class, 'logout'])->name('logout');
    Route::post('switch-mode/{mode}', [RoleSwitchController::class, 'switchMode'])->name('switch.mode');

    // ---------- Become a seller ----------
    Route::get('become-a-seller',  [SellerController::class, 'showApplyForm'])->name('seller.apply');
    Route::post('become-a-seller', [SellerController::class, 'storeApply'])->name('seller.store');

    // ============================================================
    // MY BOOKINGS (customer side)
    // ============================================================
    Route::prefix('my-bookings')->name('my-bookings.')->group(function () {
        Route::get('/',                      [\App\Http\Controllers\BookingController::class, 'myBookings'])->name('index');
        Route::get('/{reference}',           [\App\Http\Controllers\BookingController::class, 'showBooking'])->name('show');
        Route::get('/{reference}/invoice',   [\App\Http\Controllers\BookingController::class, 'invoice'])->name('invoice');
        Route::post('/{reference}/cancel',   [\App\Http\Controllers\BookingController::class, 'cancelBooking'])->name('cancel');
        Route::post('/{reference}/pay',      [\App\Http\Controllers\BookingController::class, 'payRemaining'])->name('pay-remaining');
    });

    // ============================================================
    // SELLER PANEL
    // ============================================================
    Route::prefix('seller')->name('seller.')->middleware(['mode:seller', 'seller.role', 'branch.access'])->group(function () {
        Route::get('dashboard', [SellerController::class, 'dashboard'])->name('dashboard');

        // Branches
        Route::prefix('branches')->name('branches.')->group(function () {
            Route::get('/',         [SellerBranchController::class, 'index'])->name('index');
            Route::get('create',    [SellerBranchController::class, 'create'])->name('create');
            Route::post('/',        [SellerBranchController::class, 'store'])->name('store');
            Route::get('{id}/edit', [SellerBranchController::class, 'edit'])->name('edit');
            Route::put('{id}',      [SellerBranchController::class, 'update'])->name('update');
            Route::delete('{id}',   [SellerBranchController::class, 'destroy'])->name('destroy');
        });

        // Staff
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::get('/',           [BranchStaffController::class, 'index'])->name('index');
            Route::post('/',          [BranchStaffController::class, 'store'])->name('store');
            Route::patch('{id}/toggle', [BranchStaffController::class, 'toggleStatus'])->name('toggle');
            Route::delete('{id}',     [BranchStaffController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/',                     [\App\Http\Controllers\SellerBookingController::class, 'index'])->name('index');
            Route::get('/{reference}/invoice',  [\App\Http\Controllers\SellerBookingController::class, 'invoice'])->name('invoice');
            Route::get('/{reference}',          [\App\Http\Controllers\SellerBookingController::class, 'show'])->name('show');
            Route::post('/{reference}/accept',  [\App\Http\Controllers\SellerBookingController::class, 'accept'])->name('accept');
            Route::post('/{reference}/reject',  [\App\Http\Controllers\SellerBookingController::class, 'reject'])->name('reject');
            Route::post('/{reference}/start',   [\App\Http\Controllers\SellerBookingController::class, 'startProcessing'])->name('start');
            Route::post('/{reference}/complete', [\App\Http\Controllers\SellerBookingController::class, 'complete'])->name('complete');
            Route::post('/{reference}/collect', [\App\Http\Controllers\SellerBookingController::class, 'collectPayment'])->name('collect');
            Route::post('/{reference}/cancel',  [\App\Http\Controllers\SellerBookingController::class, 'cancel'])->name('cancel');
        });

        // Payments
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/',             [SellerPaymentController::class, 'index'])->name('index');
            Route::get('/products',     [SellerPaymentController::class, 'products'])->name('products');
            Route::get('/collections',  [SellerPaymentController::class, 'collections'])->name('collections');
            Route::get('/pending-cod',  [SellerPaymentController::class, 'pendingCod'])->name('pending-cod');
        });

        // Services
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/',                     [SellerServiceController::class, 'index'])->name('index');
            Route::post('{service}/enable',     [SellerServiceController::class, 'enable'])->name('enable');
            Route::post('{service}/disable',    [SellerServiceController::class, 'disable'])->name('disable');
            Route::post('{service}/toggle',     [SellerServiceController::class, 'toggle'])->name('toggle');
        });

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/',       [ProductController::class, 'index'])->name('index');
            Route::get('create',  [ProductController::class, 'create'])->name('create');
            Route::post('/',      [ProductController::class, 'store'])->name('store');
            Route::get('featured', [ProductController::class, 'featuredIndex'])->name('featured');

            Route::get('{product}',              [ProductController::class, 'show'])->name('show');
            Route::get('{product}/edit',         [ProductController::class, 'edit'])->name('edit');
            Route::put('{product}',              [ProductController::class, 'update'])->name('update');
            Route::delete('{product}',           [ProductController::class, 'destroy'])->name('destroy');
            Route::patch('{product}/toggle-active',   [ProductController::class, 'toggleActive'])->name('toggle-active');
            Route::patch('{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::delete('{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('images.delete');
        });

        // Social community
        Route::prefix('community')->group(function () {
            Route::get('feed', [SocialCommunityController::class, 'index'])->name('social.feed');

            Route::get('posts/create',    [SocialCommunityController::class, 'createPost'])->name('posts.create');
            Route::post('posts',          [SocialCommunityController::class, 'storePost'])->name('posts.store');
            Route::get('posts/{id}/edit', [SocialCommunityController::class, 'editPost'])->name('posts.edit');
            Route::put('posts/{id}',      [SocialCommunityController::class, 'updatePost'])->name('posts.update');
            Route::delete('posts/{id}',   [SocialCommunityController::class, 'destroyPost'])->name('posts.destroy');

            Route::get('my-posts', [SocialCommunityController::class, 'myPosts'])->name('posts.my');

            Route::get('saved-posts',              [SocialCommunityController::class, 'savedPostsFeed'])->name('posts.saved');
            Route::post('posts/{postId}/save',     [SocialCommunityController::class, 'toggleSavePost'])->name('posts.save');

            Route::post('posts/{postId}/reaction', [SocialCommunityController::class, 'toggleReaction'])->name('posts.reaction');

            Route::get('posts/{postId}/comments',  [SocialCommunityController::class, 'getComments'])->name('comments.get');
            Route::post('posts/{postId}/comments', [SocialCommunityController::class, 'storeComment'])->name('comments.store');
            Route::put('comments/{commentId}',     [SocialCommunityController::class, 'updateComment'])->name('comments.update');
            Route::delete('comments/{commentId}',  [SocialCommunityController::class, 'destroyComment'])->name('comments.destroy');

            Route::post('posts/{postId}/share',    [SocialCommunityController::class, 'sharePost'])->name('posts.share');

            Route::post('users/{userId}/follow',   [SocialCommunityController::class, 'toggleFollow'])->name('user.follow');
        });

        Route::resource('customer-charge-rules', SellerChargeRuleController::class)->except(['show']);
        Route::patch(
            'customer-charge-rules/{customer_charge_rule}/toggle-active',
            [SellerChargeRuleController::class, 'toggleActive']
        )->name('customer-charge-rules.toggle-active');
    });

    // ============================================================
    // DELIVERY PANEL
    // ============================================================
    Route::prefix('delivery')->name('delivery.')->middleware('mode:delivery')->group(function () {
        Route::get('dashboard', fn() => view('delivery.dashboard'))->name('dashboard');
    });

    // ============================================================
    // ADMIN PANEL
    // ============================================================
    Route::prefix('admin')->name('admin.')->middleware('mode:admin')->group(function () {

        Route::get('dashboard', fn() => view('admin.dashboard'))->name('dashboard');

        Route::prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/', [WalletAdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/wallets', [WalletAdminController::class, 'wallets'])->name('wallets');

            Route::get('/load-requests', [WalletAdminController::class, 'loadRequests'])->name('load-requests');
            Route::post('/load-requests/{id}/approve', [WalletAdminController::class, 'approveLoad'])->name('load.approve');
            Route::post('/load-requests/{id}/reject', [WalletAdminController::class, 'rejectLoad'])->name('load.reject');

            Route::get('/withdraw-requests', [WalletAdminController::class, 'withdrawRequests'])->name('withdraw-requests');
            Route::post('/withdraw-requests/{id}/approve', [WalletAdminController::class, 'approveWithdraw'])->name('withdraw.approve');
            Route::post('/withdraw-requests/{id}/reject', [WalletAdminController::class, 'rejectWithdraw'])->name('withdraw.reject');

            Route::get('/audits', [WalletAdminController::class, 'audits'])->name('audits');

            Route::post('/{id}/freeze', [WalletAdminController::class, 'freezeWallet'])->name('freeze');
            Route::post('/{id}/unfreeze', [WalletAdminController::class, 'unfreezeWallet'])->name('unfreeze');
            Route::post('/{id}/kyc', [WalletAdminController::class, 'verifyKyc'])->name('kyc');
            Route::post('/adjust/{user}', [WalletAdminController::class, 'adjustWallet'])->name('adjust');
        });

        // Services
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/',                      [AdminServiceController::class, 'index'])->name('index');
            Route::get('create',                 [AdminServiceController::class, 'create'])->name('create');
            Route::post('/',                     [AdminServiceController::class, 'store'])->name('store');
            Route::get('{service}',              [AdminServiceController::class, 'show'])->name('show');
            Route::get('{service}/edit',         [AdminServiceController::class, 'edit'])->name('edit');
            Route::put('{service}',              [AdminServiceController::class, 'update'])->name('update');
            Route::delete('{service}',           [AdminServiceController::class, 'destroy'])->name('destroy');
            Route::patch('{service}/toggle-active',   [AdminServiceController::class, 'toggleActive'])->name('toggle-active');
            Route::patch('{service}/toggle-featured', [AdminServiceController::class, 'toggleFeatured'])->name('toggle-featured');
        });

        // Service categories
        Route::resource('service-categories', ServiceCategoryController::class)->except(['show']);
        Route::patch('service-categories/{service_category}/toggle-active', [ServiceCategoryController::class, 'toggleActive'])->name('service-categories.toggle-active');

        // Service tax rules
        Route::resource('service-tax-rules', ServiceTaxRuleController::class)->except(['show']);
        Route::patch('service-tax-rules/{service_tax_rule}/toggle-active', [ServiceTaxRuleController::class, 'toggleActive'])->name('service-tax-rules.toggle-active');

        // Partner commission rules
        Route::resource('partner-commission-rules', PartnerCommissionRuleController::class)->except(['show']);
        Route::patch('partner-commission-rules/{partner_commission_rule}/toggle-active', [PartnerCommissionRuleController::class, 'toggleActive'])->name('partner-commission-rules.toggle-active');

        // ✅ Admin — Customer charge rules (global + per-seller)
        Route::resource('customer-charge-rules', CustomerChargeRuleController::class)->except(['show']);
        Route::patch('customer-charge-rules/{customer_charge_rule}/toggle-active', [CustomerChargeRuleController::class, 'toggleActive'])->name('customer-charge-rules.toggle-active');
    });

    Route::prefix('customer')->name('customer.')->middleware('mode:customer')->group(function () {
        Route::get('dashboard', [UserControllers::class, 'customerDashboard'])->name('dashboard');
    });

    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [UserControllers::class, 'accountSettings'])->name('settings');
        Route::get('/login-activity', [UserControllers::class, 'loginActivity'])->name('login-activity');

        Route::get('/sessions',                   [UserControllers::class, 'sessions'])->name('sessions');
        Route::delete('/sessions/{sessionId}',    [UserControllers::class, 'destroySession'])->name('sessions.destroy');
        Route::post('/sessions/logout-all',       [UserControllers::class, 'logoutAllDevices'])->name('sessions.logout-all');

        Route::get('/deactivate',   [UserControllers::class, 'showDeactivateForm'])->name('deactivate');
        Route::post('/deactivate',  [UserControllers::class, 'deactivateAccount'])->name('deactivate.process');

        Route::get('/delete',       [UserControllers::class, 'showDeleteForm'])->name('delete');
        Route::post('/delete',      [UserControllers::class, 'deleteAccount'])->name('delete.process');

        Route::get('/switch',           [UserControllers::class, 'accountSwitcher'])->name('switcher');
        Route::post('/switch/{mode}',   [UserControllers::class, 'switchMode'])->name('switch');
        Route::post('/accounts/switch/{userId}', [UserControllers::class, 'switchAccount'])->name('accounts.switch');
        Route::delete('/accounts/remove/{userId}', [UserControllers::class, 'removeSavedAccount'])->name('accounts.remove');
        Route::post('/accounts/add', [UserControllers::class, 'addAnotherAccount'])->name('accounts.add');

        Route::get('/change-username', [UserControllers::class, 'showChangeUsernameForm'])->name('change-username');
        Route::put('/change-username', [UserControllers::class, 'updateUsername'])->name('change-username.update');

        Route::get('/change-email',          [UserControllers::class, 'showChangeEmailForm'])->name('change-email');
        Route::post('/change-email',         [UserControllers::class, 'sendEmailChangeOtp'])->name('change-email.send');
        Route::post('/change-email/verify',  [UserControllers::class, 'verifyEmailChangeOtp'])->name('change-email.verify');
        Route::post('/change-email/resend',  [UserControllers::class, 'resendEmailChangeOtp'])->name('change-email.resend');
        Route::post('/change-email/cancel',  [UserControllers::class, 'cancelEmailChange'])->name('change-email.cancel');

        Route::get('/change-name', [UserControllers::class, 'showChangeNameForm'])->name('change-name');
        Route::put('/change-name', [UserControllers::class, 'updateName'])->name('change-name.update');

        Route::get('/change-mobile',          [UserControllers::class, 'showChangeMobileForm'])->name('change-mobile');
        Route::post('/change-mobile',         [UserControllers::class, 'sendMobileChangeOtp'])->name('change-mobile.send');
        Route::post('/change-mobile/verify',  [UserControllers::class, 'verifyMobileChangeOtp'])->name('change-mobile.verify');
        Route::post('/change-mobile/resend',  [UserControllers::class, 'resendMobileChangeOtp'])->name('change-mobile.resend');
        Route::post('/change-mobile/cancel',  [UserControllers::class, 'cancelMobileChange'])->name('change-mobile.cancel');
    });

    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'dashboard'])->name('dashboard');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');

        Route::get('/add-money', [WalletController::class, 'addMoney'])->name('add-money');
        Route::post('/add-money', [WalletController::class, 'submitLoad'])->name('add-money.submit');
        Route::get('/load-requests', [WalletController::class, 'loadRequests'])->name('load-requests');

        Route::get('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
        Route::post('/withdraw', [WalletController::class, 'submitWithdraw'])->name('withdraw.submit');
        Route::get('/withdraw-requests', [WalletController::class, 'withdrawRequests'])->name('withdraw-requests');

        Route::get('/transfer', [WalletController::class, 'transfer'])->name('transfer');
        Route::post('/transfer', [WalletController::class, 'submitTransfer'])->name('transfer.submit');

        Route::get('/bank-accounts', [WalletController::class, 'bankAccounts'])->name('bank-accounts');
        Route::post('/bank-accounts', [WalletController::class, 'storeBankAccount'])->name('bank-accounts.store');
        Route::delete('/bank-accounts/{id}', [WalletController::class, 'deleteBankAccount'])->name('bank-accounts.delete');
        Route::post('/bank-accounts/{id}/primary', [WalletController::class, 'setPrimaryBank'])->name('bank-accounts.primary');

        Route::get('/security', [WalletController::class, 'security'])->name('security');
        Route::post('/security/pin', [WalletController::class, 'setPin'])->name('security.pin');
        Route::post('/security/limits', [WalletController::class, 'changeLimits'])->name('security.limits');

        Route::get('/rewards', [WalletController::class, 'rewards'])->name('rewards');
    });
});

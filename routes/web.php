
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CustomerChargeRuleController;
use App\Http\Controllers\Admin\PartnerCommissionRuleController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceTaxRuleController;

use App\Http\Controllers\BranchStaffController;
use App\Http\Controllers\RoleSwitchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellerServiceController;
use App\Http\Controllers\SellerAuthController;
use App\Http\Controllers\SellerBranchController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\SocialCommunityController;
use App\Http\Controllers\UserControllers;



Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware([
    'web',
    \App\Http\Middleware\EnsureSuperAdminSetupAllowed::class,
])->group(function () {

    Route::get('system/setup', [
        \App\Http\Controllers\SuperAdminSetupController::class,
        'showSetupForm',
    ])->name('superadmin.setup');

    Route::post('system/setup', [
        \App\Http\Controllers\SuperAdminSetupController::class,
        'createSetup',
    ])->name('superadmin.setup.store');

});

Route::middleware('guest')->group(function () {

    Route::get('register', [
        UserControllers::class,
        'showRegisterForm',
    ])->name('register');

    Route::post('register', [
        UserControllers::class,
        'register',
    ]);

    Route::get('login', [
        UserControllers::class,
        'showLoginForm',
    ])->name('login');

    Route::post('login', [
        UserControllers::class,
        'login',
    ]);

    // OTP verification
    Route::get('verify-otp', [
        UserControllers::class,
        'showVerifyForm',
    ])->name('verify.otp');

    Route::post('verify-otp', [
        UserControllers::class,
        'verifyOtp',
    ]);

    Route::post('resend-otp', [
        UserControllers::class,
        'resendOtp',
    ])->name('resend.otp');

    // Forgot and reset password
    Route::get('forgot-password', [
        UserControllers::class,
        'showForgotPasswordForm',
    ])->name('password.request');

    Route::post('forgot-password', [
        UserControllers::class,
        'sendPasswordResetOtp',
    ])->name('password.email');

    Route::get('reset-password', [
        UserControllers::class,
        'showResetPasswordForm',
    ])->name('password.reset.form');

    Route::post('reset-password', [
        UserControllers::class,
        'resetPassword',
    ])->name('password.reset');

});


// Google OAuth — Customer
Route::get('auth/google', [
    UserControllers::class,
    'googleLogin',
])->name('auth.google');

Route::get('auth/google-callback', [
    UserControllers::class,
    'googleAuthentication',
])->name('auth.google-callback');

Route::prefix('seller')
    ->name('seller.')
    ->middleware('guest')
    ->group(function () {

        Route::get('register', [
            SellerAuthController::class,
            'showRegisterForm',
        ])->name('register');

        Route::post('register', [
            SellerAuthController::class,
            'register',
        ]);

        Route::get('login', [
            SellerAuthController::class,
            'showLoginForm',
        ])->name('login');

        Route::post('login', [
            SellerAuthController::class,
            'login',
        ]);

        // Seller OTP
        Route::get('verify-otp', [
            SellerAuthController::class,
            'showVerifyForm',
        ])->name('verify.otp');

        Route::post('verify-otp', [
            SellerAuthController::class,
            'verifyOtp',
        ]);

        Route::post('resend-otp', [
            SellerAuthController::class,
            'resendOtp',
        ])->name('resend.otp');

        // Seller password reset
        Route::get('forgot-password', [
            SellerAuthController::class,
            'showForgotPasswordForm',
        ])->name('password.request');

        Route::post('forgot-password', [
            SellerAuthController::class,
            'sendPasswordResetOtp',
        ])->name('password.email');

        Route::get('reset-password', [
            SellerAuthController::class,
            'showResetPasswordForm',
        ])->name('password.reset.form');

        Route::post('reset-password', [
            SellerAuthController::class,
            'resetPassword',
        ])->name('password.reset');

        // Seller Google OAuth
        Route::get('auth/google', [
            SellerAuthController::class,
            'googleLogin',
        ])->name('auth.google');

        Route::get('auth/google-callback', [
            SellerAuthController::class,
            'googleAuthentication',
        ])->name('auth.google-callback');

    });

Route::middleware('auth')->group(function () {

    // Common authenticated routes
    Route::get('home', [
        UserControllers::class,
        'home',
    ])->name('home');

    Route::post('logout', [
        UserControllers::class,
        'logout',
    ])->name('logout');

    // Customer / seller / delivery / admin mode switching
    Route::post('switch-mode/{mode}', [
        RoleSwitchController::class,
        'switchMode',
    ])->name('switch.mode');


    /*
    |--------------------------------------------------------------------------
    | BECOME A SELLER
    |--------------------------------------------------------------------------
    */

    Route::get('become-a-seller', [
        SellerController::class,
        'showApplyForm',
    ])->name('seller.apply');

    Route::post('become-a-seller', [
        SellerController::class,
        'storeApply',
    ])->name('seller.store');


    /*
    |--------------------------------------------------------------------------
    | SELLER PANEL
    |--------------------------------------------------------------------------
    | Owner, branch manager and branch staff.
    | Middleware must verify the user's role and branch permissions.
    */

    Route::prefix('seller')
        ->name('seller.')
        ->middleware([
            'mode:seller',
            'seller.role',
            'branch.access',
        ])
        ->group(function () {

            // Dashboard
            Route::get('dashboard', [
                SellerController::class,
                'dashboard',
            ])->name('dashboard');


            /*
            |--------------------------------------------------------------------------
            | BRANCHES
            |--------------------------------------------------------------------------
            */

            Route::prefix('branches')
                ->name('branches.')
                ->group(function () {

                    Route::get('/', [
                        SellerBranchController::class,
                        'index',
                    ])->name('index');

                    Route::get('create', [
                        SellerBranchController::class,
                        'create',
                    ])->name('create');

                    Route::post('/', [
                        SellerBranchController::class,
                        'store',
                    ])->name('store');

                    Route::get('{id}/edit', [
                        SellerBranchController::class,
                        'edit',
                    ])->name('edit');

                    Route::put('{id}', [
                        SellerBranchController::class,
                        'update',
                    ])->name('update');

                    Route::delete('{id}', [
                        SellerBranchController::class,
                        'destroy',
                    ])->name('destroy');

                });


            /*
            |--------------------------------------------------------------------------
            | BRANCH STAFF
            |--------------------------------------------------------------------------
            */

            Route::prefix('staff')
                ->name('staff.')
                ->group(function () {

                    Route::get('/', [
                        BranchStaffController::class,
                        'index',
                    ])->name('index');

                    Route::post('/', [
                        BranchStaffController::class,
                        'store',
                    ])->name('store');

                    Route::patch('{id}/toggle', [
                        BranchStaffController::class,
                        'toggleStatus',
                    ])->name('toggle');

                    Route::delete('{id}', [
                        BranchStaffController::class,
                        'destroy',
                    ])->name('destroy');

                });


            /*
            |--------------------------------------------------------------------------
            | SELLER SERVICES
            |--------------------------------------------------------------------------
            | Seller can enable only services permitted by the platform.
            */

            Route::prefix('services')
                ->name('services.')
                ->group(function () {

                    Route::get('/', [
                        SellerServiceController::class,
                        'index',
                    ])->name('index');

                    Route::post('{service}/enable', [
                        SellerServiceController::class,
                        'enable',
                    ])->name('enable');

                    Route::post('{service}/disable', [
                        SellerServiceController::class,
                        'disable',
                    ])->name('disable');

                    Route::post('{service}/toggle', [
                        SellerServiceController::class,
                        'toggle',
                    ])->name('toggle');

                });


            /*
            |--------------------------------------------------------------------------
            | PRODUCTS
            |--------------------------------------------------------------------------
            */

            Route::prefix('products')
                ->name('products.')
                ->group(function () {

                    Route::get('/', [
                        ProductController::class,
                        'index',
                    ])->name('index');

                    Route::get('create', [
                        ProductController::class,
                        'create',
                    ])->name('create');

                    Route::post('/', [
                        ProductController::class,
                        'store',
                    ])->name('store');

                    Route::get('featured', [
                        ProductController::class,
                        'featuredIndex',
                    ])->name('featured');

                    Route::get('{product}', [
                        ProductController::class,
                        'show',
                    ])->name('show');

                    Route::get('{product}/edit', [
                        ProductController::class,
                        'edit',
                    ])->name('edit');

                    Route::put('{product}', [
                        ProductController::class,
                        'update',
                    ])->name('update');

                    Route::delete('{product}', [
                        ProductController::class,
                        'destroy',
                    ])->name('destroy');

                    Route::patch('{product}/toggle-active', [
                        ProductController::class,
                        'toggleActive',
                    ])->name('toggle-active');

                    Route::patch('{product}/toggle-featured', [
                        ProductController::class,
                        'toggleFeatured',
                    ])->name('toggle-featured');

                    Route::delete('{product}/images/{image}', [
                        ProductController::class,
                        'deleteImage',
                    ])->name('images.delete');

                });


            /*
            |--------------------------------------------------------------------------
            | COMMUNITY / SOCIAL FEED
            |--------------------------------------------------------------------------
            */

            Route::prefix('community')
                ->name('social.')
                ->group(function () {

                    Route::get('feed', [
                        SocialCommunityController::class,
                        'index',
                    ])->name('feed');

                });

            Route::prefix('community')->group(function () {

                // Posts
                Route::get('posts/create', [
                    SocialCommunityController::class,
                    'createPost',
                ])->name('posts.create');

                Route::post('posts', [
                    SocialCommunityController::class,
                    'storePost',
                ])->name('posts.store');

                Route::get('posts/{id}/edit', [
                    SocialCommunityController::class,
                    'editPost',
                ])->name('posts.edit');

                Route::put('posts/{id}', [
                    SocialCommunityController::class,
                    'updatePost',
                ])->name('posts.update');

                Route::delete('posts/{id}', [
                    SocialCommunityController::class,
                    'destroyPost',
                ])->name('posts.destroy');


                // User posts
                Route::get('my-posts', [
                    SocialCommunityController::class,
                    'myPosts',
                ])->name('posts.my');


                // Saved posts
                Route::get('saved-posts', [
                    SocialCommunityController::class,
                    'savedPostsFeed',
                ])->name('posts.saved');

                Route::post('posts/{postId}/save', [
                    SocialCommunityController::class,
                    'toggleSavePost',
                ])->name('posts.save');


                // Reactions
                Route::post('posts/{postId}/reaction', [
                    SocialCommunityController::class,
                    'toggleReaction',
                ])->name('posts.reaction');


                // Comments
                Route::get('posts/{postId}/comments', [
                    SocialCommunityController::class,
                    'getComments',
                ])->name('comments.get');

                Route::post('posts/{postId}/comments', [
                    SocialCommunityController::class,
                    'storeComment',
                ])->name('comments.store');

                Route::put('comments/{commentId}', [
                    SocialCommunityController::class,
                    'updateComment',
                ])->name('comments.update');

                Route::delete('comments/{commentId}', [
                    SocialCommunityController::class,
                    'destroyComment',
                ])->name('comments.destroy');


                // Share
                Route::post('posts/{postId}/share', [
                    SocialCommunityController::class,
                    'sharePost',
                ])->name('posts.share');


                // Follow
                Route::post('users/{userId}/follow', [
                    SocialCommunityController::class,
                    'toggleFollow',
                ])->name('user.follow');

            });

        });


    /*
    |--------------------------------------------------------------------------
    | DELIVERY PANEL
    |--------------------------------------------------------------------------
    */

    Route::prefix('delivery')
        ->name('delivery.')
        ->middleware('mode:delivery')
        ->group(function () {

            Route::get('dashboard', function () {
                return view('delivery.dashboard');
            })->name('dashboard');

        });


    /*
    |--------------------------------------------------------------------------
    | ADMIN PANEL
    |--------------------------------------------------------------------------
    | Keep admin configuration routes inside this protected group.
    | Confirm mode:admin also checks the user's authorized role.
    */

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('mode:admin')
        ->group(function () {

            // Dashboard
            Route::get('dashboard', function () {
                return view('admin.dashboard');
            })->name('dashboard');


            /*
            |--------------------------------------------------------------------------
            | SERVICE MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::prefix('services')
                ->name('services.')
                ->group(function () {

                    Route::get('/', [
                        AdminServiceController::class,
                        'index',
                    ])->name('index');

                    Route::get('create', [
                        AdminServiceController::class,
                        'create',
                    ])->name('create');

                    Route::post('/', [
                        AdminServiceController::class,
                        'store',
                    ])->name('store');

                    Route::get('{service}', [
                        AdminServiceController::class,
                        'show',
                    ])->name('show');

                    Route::get('{service}/edit', [
                        AdminServiceController::class,
                        'edit',
                    ])->name('edit');

                    Route::put('{service}', [
                        AdminServiceController::class,
                        'update',
                    ])->name('update');

                    Route::delete('{service}', [
                        AdminServiceController::class,
                        'destroy',
                    ])->name('destroy');

                    Route::patch('{service}/toggle-active', [
                        AdminServiceController::class,
                        'toggleActive',
                    ])->name('toggle-active');

                    Route::patch('{service}/toggle-featured', [
                        AdminServiceController::class,
                        'toggleFeatured',
                    ])->name('toggle-featured');

                });

            Route::resource(
                'service-categories',
                ServiceCategoryController::class
            )->except(['show']);


            Route::patch(
                'service-categories/{service_category}/toggle-active',
                [ServiceCategoryController::class, 'toggleActive']
            )->name('service-categories.toggle-active');

            Route::resource(
                'service-tax-rules',
                ServiceTaxRuleController::class
            )->except(['show']);


            Route::patch(
                'service-tax-rules/{service_tax_rule}/toggle-active',
                [ServiceTaxRuleController::class, 'toggleActive']
            )->name('service-tax-rules.toggle-active');


            Route::resource(
                'partner-commission-rules',
                PartnerCommissionRuleController::class
            )->except(['show']);


            Route::patch(
                'partner-commission-rules/{partner_commission_rule}/toggle-active',
                [PartnerCommissionRuleController::class, 'toggleActive']
            )->name('partner-commission-rules.toggle-active');


            Route::resource(
                'customer-charge-rules',
                CustomerChargeRuleController::class
            )->except(['show']);


            Route::patch(
                'customer-charge-rules/{customer_charge_rule}/toggle-active',
                [CustomerChargeRuleController::class, 'toggleActive']
            )->name('customer-charge-rules.toggle-active');

        });

});

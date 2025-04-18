<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\SongController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


Route::get('/', [FrontController::class, 'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}', [ShopController::class, 'index'])->name('front.shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('front.product');

Route::get('/cart', [CartController::class, 'cart'])->name('front.cart');
Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('front.addToCart');
Route::post('/delete-item', [CartController::class, 'deleteItem'])->name('front.deleteItem.cart');

Route::get('/checkout', [CartController::class, 'checkout'])->name('front.checkout');
Route::get('/thanks/{orderId}', [CartController::class, 'thankyou'])->name('front.thanks');
Route::get('/error/{orderId}', [CartController::class, 'error'])->name('front.failed');
Route::post('/prepare-payment', [CartController::class, 'preparePayment'])->name('front.preparePayment');
Route::get('/payment-success', [CartController::class, 'paymentSuccess'])->name('front.paymentSuccess');
Route::get('/payment-failed', [CartController::class, 'paymentFailed'])->name('front.paymentFailed');

Route::post('/add-to-wishlist', [FrontController::class, 'addToWishlist'])->name('front.addToWishlist');

Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('front.forgotPassword');
Route::post('/process-forgot-password', [AuthController::class, 'processForgotPassword'])->name('front.processForgotPassword');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('front.resetPassword');
Route::post('/process-reset-password', [AuthController::class, 'processResetPassword'])->name('front.processResetPassword');

Route::post('/save-rating/{productId}', [ShopController::class, 'saveRating'])->name('front.saveRating');

// Authenticate Route
Route::middleware(['web'])->group(function () {
    Route::group(['prefix' => 'account'], function() {
        Route::group(['middleware' => 'guest'], function() {
            Route::get('/login', [AuthController::class, 'login'])->name('account.login');
            Route::post('/login', [AuthController::class, 'authenticate'])->name('account.authenticate');


            Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
            Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');

            Route::get('auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('facebook.login');
            Route::get('auth/facebook/callback', [AuthController::class, 'handleFacebookCallback'])->name('facebook.callback');


            Route::get('/register', [AuthController::class, 'register'])->name('account.register');
            Route::post('/process-register', [AuthController::class, 'processRegister'])->name('account.processRegister');


        });

        Route::group(['middleware' => 'auth'], function() {
            Route::get('/profile', [AuthController::class, 'profile'])->name('account.profile');
            Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('account.changePassword');
            Route::post('/process-change-password', [AuthController::class, 'changePassword'])->name('account.processChangePassword');
            // Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('account.updateProfile');
            Route::get('/my-orders', [AuthController::class, 'myorders'])->name('account.orders');
            Route::get('/my-wishlist', [AuthController::class, 'wishlist'])->name('account.wishlists');
            Route::post('/remove-product-from-wishlist', [AuthController::class, 'removeProductFromWishlist'])->name('account.removeProductFromWishlist');
            Route::get('/order-detail/{orderId}', [AuthController::class, 'orderDetail'])->name('account.orderDetail');
            Route::post('/logout', [AuthController::class, 'logout'])->name('account.logout');
        });
    });
});

Route::middleware(['web'])->group(function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::group(['middleware' => 'admin.guest'], function () {
            Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
            Route::post('/authenticate', [AdminLoginController::class, 'authenticate'])->name('admin.authenticate');
            Route::get('forgot-password', [AdminLoginController::class, 'forgotPassword'])->name('admin.forgotPassword');
            Route::post('forgot-password', [AdminLoginController::class, 'processForgotPassword'])->name('admin.processForgotPassword');
            Route::get('reset-password/{token}', [AdminLoginController::class, 'resetPassword'])->name('admin.resetPassword');
            Route::post('process-reset-password', [AdminLoginController::class, 'processResetPassword'])->name('admin.processResetPassword');
        });

        Route::group(['middleware' => 'admin.auth'], function () {
            Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
            Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');
            // Category Route
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.delete');
            Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');
            // Sub Category Route
            Route::get('/sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories.index');
            Route::get('/sub-categories/create', [SubCategoryController::class, 'create'])->name('sub-categories.create');
            Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
            Route::get('/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('sub-categories.edit');
            Route::put('/sub-categories/{subCategory}', [SubCategoryController::class, 'update'])->name('sub-categories.update');
            Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy'])->name('sub-categories.delete');

            // Song Route
            Route::get('/songs', [SongController::class, 'index'])->name('songs.index');
            Route::get('/songs/create', [SongController::class, 'create'])->name('songs.create');
            Route::post('/songs', [SongController::class, 'store'])->name('songs.store');
            Route::get('/songs/{song}/edit', [SongController::class, 'edit'])->name('songs.edit');
            Route::put('/songs/{song}', [SongController::class, 'update'])->name('songs.update');
            Route::delete('/songs/{song}', [SongController::class, 'destroy'])->name('songs.delete');

            // Product Route
            Route::get('/products', [ProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.delete');
            Route::get('/get-products', [ProductController::class, 'getProducts'])->name('products.getProducts');
            Route::get('/ratings', [ProductController::class, 'productRatings'])->name('products.ratings');
            Route::get('/change-rating-status', [ProductController::class, 'changeRatingStatus'])->name('products.changeRatingStatus');

            // Product Sub-Category Route
            Route::get('/product-subcategories', [ProductSubCategoryController::class, 'index'])->name('product-subcategories.index');

            // Product Image Route
            Route::post('/product-images/update', [ProductImageController::class, 'update'])->name('product-images.update');
            Route::delete('/product-images', [ProductImageController::class, 'destroy'])->name('product-images.delete');

            // Order Route
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{id}', [OrderController::class, 'detail'])->name('orders.detail');
            Route::post('/orders/change-status/{id}', [OrderController::class, 'changeOrderStatus'])->name('orders.changeOrderStatus');

            // Settings Route
            Route::get('/change-password', [SettingController::class, 'showChangePasswordForm'])->name('admin.showChangePasswordForm');
            Route::post('/process-change-password', [SettingController::class, 'changePassword'])->name('admin.processChangePassword');

            // Users Route
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');


            Route::get('/getSlug', function (Request $request) {
                $slug = '';
                if (!empty($request->title)) {
                    if (!empty($request->singer)) {
                        $slug = Str::slug($request->title . '-' . $request->singer);
                    } else {
                        $slug = Str::slug($request->title);
                    }
                }

                return response()->json([
                    'status' => true,
                    'slug' => $slug
                ]);
            })->name('getSlug');
        });
    });
});

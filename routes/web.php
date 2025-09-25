<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\home\BannerDetailsController;
use App\Http\Controllers\Backend\home\HomeContactAdverstimentDetailsController;
use App\Http\Controllers\Backend\home\SignageWellnessDetailsController;
use App\Http\Controllers\Backend\home\CustomerReviewDetailsController;
use App\Http\Controllers\Backend\home\FooterDetailsController;
use App\Http\Controllers\Backend\products\CategoryDetailsController;
use App\Http\Controllers\Backend\products\SabCategoryDetailsController;
use App\Http\Controllers\Backend\products\PerfumeNotesDetailsController;
use App\Http\Controllers\Backend\products\FragranceTypeDetailsController;
use App\Http\Controllers\Backend\products\ProductsDetailsController;
use App\Http\Controllers\Backend\products\PerfumeNotesLevelDetailsController;




// frontend conroller path
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\CategoryProductsListingDetailsController;
use App\Http\Controllers\Frontend\ProductsListingDetailsController;
use App\Http\Controllers\Frontend\AllProductsDetailsController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\RegisterController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\LocationController;
use App\Http\Controllers\Frontend\PaymentController;

// Frontend routes
Route::get('/', function () {
    return view('welcome');
});




// Backend Routes
Route::get('/admin-login', [LoginController::class, 'login'])->name('admin.login');
Route::post('/admin-login', [LoginController::class, 'authenticate'])->name('admin.authenticate');
Route::get('/admin-logout', [LoginController::class, 'logout'])->name('admin.logout');
Route::get('/change-password', [LoginController::class, 'change_password'])->name('admin.changepassword');
Route::post('/update-password', [LoginController::class, 'updatePassword'])->name('admin.updatepassword');
Route::get('/admin-register', [LoginController::class, 'register'])->name('admin.register');
Route::post('/register', [LoginController::class, 'authenticate_register'])->name('admin.register.authenticate');
Route::resource('banner-details', BannerDetailsController::class);
Route::resource('contact-adverstiment-details', HomeContactAdverstimentDetailsController::class);
Route::resource('signage-wellness-details', SignageWellnessDetailsController::class);
Route::resource('customer-review-details', CustomerReviewDetailsController::class);
Route::resource('footer-details', FooterDetailsController::class);
Route::resource('category-details', CategoryDetailsController::class);
Route::resource('sab-category-details', SabCategoryDetailsController::class);
Route::resource('perfume-notes-details', PerfumeNotesDetailsController::class);
Route::resource('perfume-notes-level-details', PerfumeNotesLevelDetailsController::class);

Route::resource('fragrance-type-details', FragranceTypeDetailsController::class);
Route::resource('products-details', ProductsDetailsController::class);

// // Admin Routes with Middleware
Route::group(['middleware' => ['auth:web', \App\Http\Middleware\PreventBackHistoryMiddleware::class]], function () {
        Route::get('/dashboard', function () {
            return view('backend.dashboard'); 
        })->name('admin.dashboard');
});

// Frontend
Route::get('/clear', function() {
  Artisan::call('cache:clear');
  Artisan::call('config:clear');
  Artisan::call('config:cache');
  Artisan::call('view:clear');

  return "Cleared!";
});
Route::get('/', [HomeController::class, 'home'])->name('frontend.index');

//location 
Route::get('/get-states/{country_id?}', [LocationController::class, 'getStates'])->name('getStates');
Route::get('/get-cities/{state_id?}', [LocationController::class, 'getCities'])->name('getCities');


// registeration
Route::get('/register', [RegisterController::class, 'showRegister'])->name('user.registration');
Route::post('/register', [RegisterController::class, 'authenticateRegister'])->name('registration.store');



Route::get('/user-login', [RegisterController::class, 'login'])->name('user.login');
Route::post('/user-login', [RegisterController::class, 'authenticateLogin'])->name('login.store');
Route::post('/logout', [RegisterController::class, 'logout'])->name('user.logout');


Route::get('/logout', [RegisterController::class, 'logout'])->name('user.logout');
Route::get('/my-account', [MyAccountController::class, 'index'])->name('frontend.account');

// Social login
Route::get('login/{provider}', [CustomAuthController::class,'redirectToSocial'])->name('social.login');
Route::get('login/{provider}/callback', [CustomAuthController::class,'handleSocialCallback']);



// Master category products
Route::get('/category/{slug}', [CategoryProductsListingDetailsController::class, 'index'])->name('product.category');

// Subcategory products
Route::get('/{category}/{sabcat}', [ProductsListingDetailsController::class, 'subcategory'])->name('product.subcategory');

Route::get('/{cat}/{sabcat}/{slug}', [AllProductsDetailsController::class, 'productDetail'])->name('product.details');

// Coming soon page
Route::get('/coming-soon', function () {return view('frontend.comingsoon');})->name('coming.soon');

//Add to cart 
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');



//Add to wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::delete('/wishlist/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');

//checkout
Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('show.checkout');
Route::post('/cart/store-checkout-data', [CheckoutController::class, 'storeCheckoutData'])->name('cart.storeCheckoutData');


//======== Send OTP
Route::post('/send-otp', [CheckoutController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [CheckoutController::class, 'verifyOtp'])->name('verify.otp');


// ===== Payment Integration URL
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('payment.process')->middleware('auth:custom');
Route::post('/verify-payment', [PaymentController::class, 'verifyPayment'])->name('payment.verify');

  
//===== Order confirmation
Route::get('/order-confirmation', [CheckoutController::class, 'order_confirmation'])->name('order.confirm');
Route::get('/admin/order/{id}/ship', [CheckoutController::class, 'shiprocket'])->name('admin.shiprocket.ship');

    Route::get('/test-mail', function () {
    try {
        Mail::raw('Test Email', function($message){
            $message->to('riddhi@matrixbricks.com')
                    ->subject('Test Email');
        });
        return 'Mail sent!';
    } catch (\Exception $e) {
        return 'Mail failed: '.$e->getMessage();
    }
});

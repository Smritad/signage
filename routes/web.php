<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
use App\Http\Controllers\Backend\seo\SeoManagementController ;
use App\Http\Controllers\Backend\stock\StockDetailsController ;
use App\Http\Controllers\Backend\ReportDetailsController;
use App\Http\Controllers\Backend\footers\AboutusController;
use App\Http\Controllers\Backend\home\ReturnPolicyDetailsController;
use App\Http\Controllers\Backend\home\PrivacyPolicyDetailsController;
use App\Http\Controllers\Backend\home\TermsConditionsDetailsController;
use App\Http\Controllers\Backend\ShiprocketController;
use App\Http\Controllers\Backend\offer\OfferController;
use App\Http\Controllers\Backend\CustomerReviewController;




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
use App\Http\Controllers\Frontend\MyAccountController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\PoliciesController;
use App\Http\Controllers\Frontend\SubscribeController;
use App\Http\Controllers\Frontend\ProductReviewController;
use App\Http\Controllers\Frontend\CarzyDealController;
use App\Http\Controllers\Frontend\SocialAuthController;
use App\Http\Controllers\Frontend\CustomAuthController;

use App\Http\Controllers\Frontend\FragranceProductsListingController;

// Frontend routes
Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-route', function () {
    return 'Route Working';
});
Route::get('/deals/{slug}', function ($id) {
    dd($id);
});
// Backend Routes

Route::get('admin-login', [LoginController::class, 'login'])->name('admin.login');
Route::post('admin-login', [LoginController::class, 'authenticate'])->name('admin.authenticate');
Route::get('admin-logout', [LoginController::class, 'logout'])->name('admin.logout');
Route::get('change-password', [LoginController::class, 'change_password'])->name('admin.changepassword');
Route::post('update-password', [LoginController::class, 'updatePassword'])->name('admin.updatepassword');
Route::get('admin-register', [LoginController::class, 'register'])->name('admin.register');
Route::post('register', [LoginController::class, 'authenticate_register'])->name('admin.register.authenticate');
Route::resource('banner-details', BannerDetailsController::class);
Route::resource('contact-adverstiment-details', HomeContactAdverstimentDetailsController::class);
Route::resource('signage-wellness-details', SignageWellnessDetailsController::class);
Route::resource('customer-review-details', CustomerReviewDetailsController::class);
Route::resource('footer-details', FooterDetailsController::class);
Route::resource('category-details', CategoryDetailsController::class);
Route::resource('sab-category-details', SabCategoryDetailsController::class);
Route::resource('perfume-notes-details', PerfumeNotesDetailsController::class);
Route::resource('perfume-notes-level-details', PerfumeNotesLevelDetailsController::class);
Route::resource('aboutus-details', AboutusController::class);
Route::resource('manage-return-policy', ReturnPolicyDetailsController::class);
Route::resource('manage-privacy-policy', PrivacyPolicyDetailsController::class);
Route::resource('manage-terms-conditions', TermsConditionsDetailsController::class);

Route::resource('fragrance-type-details', FragranceTypeDetailsController::class);
Route::resource('offer-details', OfferController::class);

Route::post('offer-details/{id}/toggle-status',
    [OfferController::class, 'toggleStatus']
)->name('offer-details.toggle-status');
 
Route::resource('products-details', ProductsDetailsController::class);
Route::patch('products-details/{id}/priority', [ProductsDetailsController::class, 'updatePriority'])->name('products-details.updatePriority');
Route::patch('products-details/{id}/toggle-bestseller', [ProductsDetailsController::class, 'toggleBestseller'])->name('products-details.toggleBestseller');
Route::patch('products-details/{id}/toggle-new-arrival', [ProductsDetailsController::class, 'toggleNewArrival'])->name('products-details.toggleNewArrival');

Route::resource('seo-tags', SeoManagementController ::class);
// ==== Manage Stock Management
Route::resource('stock-details', StockDetailsController::class);
Route::post('stock-details/{id}/toggle-status', [StockDetailsController::class, 'toggleStatus'])
     ->name('stock-details.toggle-status');

Route::get('/customer/view/{email}', [ReportDetailsController::class, 'viewInvoice'])->name('customer.view');


Route::prefix('report-details')->name('report-details.')->group(function () {
    Route::get('/', [ReportDetailsController::class, 'index'])->name('index');
    Route::get('/export', [ReportDetailsController::class, 'export'])->name('export');
});




/* ── Paid orders list ── */
Route::resource('shiprocket-details', ShiprocketController::class);
Route::get('orders-details-admin/{id}', [ShiprocketController::class, 'showOrderDetails'])->name('admin.Orderdetails.index');

/* ── Failed orders ── */
Route::get('failed-details',                   [ShiprocketController::class, 'showfailedOrderDetails'])->name('failed-details.data');
Route::get('orders-failed-details-admin/{id}', [ShiprocketController::class, 'OrderFailedDetails'])->name('admin.Orderfaileddetails.index');

/* ── COD orders ── */
Route::get('Cod-order-details',                [ShiprocketController::class, 'showCodOrderDetails'])->name('cod-order-details.data');
Route::get('orders-cod-details-admin/{id}',    [ShiprocketController::class, 'OrderCodDetails'])->name('admin.Ordercoddetails.index');

/* ── Ship order (COD + Prepaid) ──
 * IMPORTANT: Must use ShiprocketController, NOT PaymentController.
 * Remove any old route pointing to PaymentController with this name.
 */
Route::get('shiprocket/ship/{orderId}',  [ShiprocketController::class, 'shipOrder'])->name('admin.shiprocket.ship');

/* ── Live tracking (AJAX) ── */
Route::get('shiprocket/track/{orderId}', [ShiprocketController::class, 'trackOrder'])->name('admin.shiprocket.track');

/* ── COD remittance check (AJAX) ── */
Route::get('shiprocket/cod-remittance/{orderId}', [ShiprocketController::class, 'checkCodRemittance'])->name('admin.shiprocket.cod-remittance');

/* ── Manual COD paid / unpaid ── */
Route::post('cod/mark-paid/{orderId}',   [ShiprocketController::class, 'markCodAsPaid'])->name('admin.cod.mark-paid');
Route::post('cod/mark-unpaid/{orderId}', [ShiprocketController::class, 'markCodAsUnpaid'])->name('admin.cod.mark-unpaid');

/* NEW: dedicated ship route (with error feedback) */
Route::get('shiprocket-ship/{orderId}',
    [ShiprocketController::class, 'shipOrder']
)->name('admin.shiprocket.ship');

// // Admin Routes with Middleware
Route::group(['middleware' => ['auth:web', \App\Http\Middleware\PreventBackHistoryMiddleware::class]], function () {
                Route::get('/dashboard',  [DashboardController::class, 'dashboard'])->name('admin.dashboard');

});
// Customer Rating Review
/* Listing page */
Route::get('/customer-review-rating',
    [CustomerReviewController::class, 'index']
)->name('admin.customer-rating-review');
 
 
 
/* Toggle approved status (AJAX) */
Route::post('/customer-review-rating/toggle/{id}',
    [CustomerReviewController::class, 'toggleApproval']
)->name('admin.review.toggle');
 
/* View single review (modal via AJAX) */
Route::get('/customer-review-rating/view/{id}',
    [CustomerReviewController::class, 'view']
)->name('admin.review.view');
 
/* Delete review */
Route::delete('/customer-review-rating/delete/{id}',
    [CustomerReviewController::class, 'destroy']
)->name('admin.review.delete');
// Frontend
Route::get('/clear', function() {
  Artisan::call('cache:clear');
  Artisan::call('config:clear');
  Artisan::call('config:cache');
  Artisan::call('view:clear');
  Artisan::call('route:clear');

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
Route::get('/forgot-password', [RegisterController::class, 'forgotpassword'])->name('user.forgotpassword');
Route::post('/forgot-password', [RegisterController::class, 'sendResetLink'])->name('user.sendResetLink');

Route::get('/reset-password/{token}', [RegisterController::class, 'resetpassword'])
     ->name('password.reset');
     Route::post('/reset-password', [RegisterController::class, 'updatePassword'])->name('user.updatePassword');

// AJAX search route
Route::get('/global-search', [RegisterController::class, 'globalSearch'])
     ->name('global.search');


Route::get('/logout', [RegisterController::class, 'logout'])->name('user.logout');
Route::get('/my-account', [MyAccountController::class, 'index'])->name('frontend.account');
Route::get('/order-details', [MyAccountController::class, 'orderdetails'])->name('frontend.ordersdetails');

// Order details view (with order_id)
Route::get('/order-details-view/{id}', [MyAccountController::class, 'orderdetailsview'])->name('frontend.ordersdetailsview');

// Customer cancels their own order (allowed only before "out for delivery")
Route::post('/order-cancel/{id}', [MyAccountController::class, 'cancelOrder'])->name('frontend.order.cancel');
Route::get('/account-addresses', [MyAccountController::class, 'address'])->name('frontend.address');
Route::post('/user/update-address/{type}', [MyAccountController::class, 'updateAddress'])
     ->name('user.updateAddress');
Route::get('/account-setting', [MyAccountController::class, 'accountsetting'])->name('frontend.accountsetting');
Route::post('/account-setting', [MyAccountController::class, 'updateAccount'])->name('user.updateAccount');
Route::middleware('auth:custom')->group(function () {
    Route::post('/user/update-profile-image', [MyAccountController::class, 'updateProfileImage'])
         ->name('user.updateProfileImage');
});



// Social login
Route::get('login/{provider}', [CustomAuthController::class,'redirectToSocial'])->name('social.login');
Route::get('login/{provider}/callback', [CustomAuthController::class,'handleSocialCallback']);











    
    // Coming soon page
Route::get('/coming-soon', function () {return view('frontend.comingsoon');})->name('coming.soon');

// // Fragrance routes (specific) – move these above catch-all routes
// Route::get('/perfume/{fragrance}', [ProductsListingDetailsController::class, 'fragranceProducts'])
//     ->name('fragrance.products');



 

    
    
// Shop All page
Route::get('/shop-all', [FragranceProductsListingController::class, 'all'])
    ->name('product.all');
 
// Fragrance page
Route::get('/fragrance/{slug}', [FragranceProductsListingController::class, 'fragrance'])
    ->name('product.fragrance');
 
// ── AJAX filter endpoints ──────────────────────────────────────────
 
// Shop All filter  (was hitting category.filter — now has its own method)
Route::post('/shop-all/filter', [FragranceProductsListingController::class, 'filterAll'])
    ->name('product.all.filter');
 
// Fragrance filter  (keep existing name so blade {{ route('frgrance.filter') }} still works)
Route::post('/fragrance/filter', [FragranceProductsListingController::class, 'filter'])
    ->name('frgrance.filter');
 
    
    
// Master category products
Route::get('/category/{slug}', [CategoryProductsListingDetailsController::class, 'index'])->name('product.category');

// Ajax filter
Route::post('/category/filter', [CategoryProductsListingDetailsController::class, 'filter'])->name('category.filter');

Route::get('/products/list', [CategoryProductsListingDetailsController::class, 'list'])->name('product.list');

// Social login — MUST be registered before the catch-all wildcard routes below,
// otherwise /auth/{provider}/redirect gets captured by /{cat}/{sabcat}/{slug}.
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
    ->name('social.callback');

// Subcategory products
Route::get('/{category}/{sabcat}', [ProductsListingDetailsController::class, 'subcategory'])->name('product.subcategory');

Route::post('/subcategory/filter', [ProductsListingDetailsController::class, 'filterSubcategory'])
    ->name('subcategory.filter');

Route::get('/{cat}/{sabcat}/{slug}', [AllProductsDetailsController::class, 'productDetail'])->name('product.details');
Route::post('/fragrance/filter', [FragranceProductsListingController::class, 'filter'])->name('frgrance.filter');

Route::post('/signage/review/store',    [ProductReviewController::class, 'store'])   ->name('review.store');
Route::post('/signage/review/load-more',[ProductReviewController::class, 'loadMore'])->name('review.loadMore');
//Add to cart 
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/add-combo', [CartController::class, 'addCombo'])->name('cart.add_combo');
Route::post('/cart/remove-combo', [CartController::class, 'removeCombo'])->name('cart.remove_combo');
Route::get('/cart/get-combo-products', [CartController::class,'getComboProducts'])->name('cart.get_combo_products');
Route::post('/wishlist/bulk-add-to-cart', [CartController::class, 'bulkAddToCart'])
    ->name('wishlist.bulkAddToCart');



Route::get('/deals',       [CarzyDealController::class, 'index'])->name('crazy.index');
Route::get('/deals/{slug}',  [CarzyDealController::class, 'show'])->name('crazy.show');

Route::post('/cart/add-bundle', [CartController::class, 'addBundle'])
    ->name('cart.addBundle');

// contact us
Route::get('/contact-us', [ContactController::class, 'contactus'])->name('contact.us');
Route::post('/contact-us', [ContactController::class, 'sendContact'])->name('contact.send');
// Thank You page after submission
Route::get('/thank-you', function () {
    return view('frontend.thank_you');
})->name('Thank.you');

Route::get('/test-shiprocket', function () {

    $creds = [
        ['email' => 'smrita@matrixbricks.com', 'password' => 'n9*DRd52M1eXB6gh'],
        ['email' => 'shweta@matrixbricks.com', 'password' => 'Dz1AkDSNn6Z^e2$A'],
    ];

    $results = [];

    foreach ($creds as $cred) {
        $response = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login', $cred);
        $data = $response->json();

        $results[] = [
            'email' => $cred['email'],
            'status' => isset($data['token']) ? 'SUCCESS ✅' : 'FAILED ❌',
            'response' => $data
        ];
    }

    return response()->json($results);
});
// contact us
Route::get('/about-us', [AboutController::class, 'index'])->name('about.us');
Route::get('/return-policy', [PoliciesController::class, 'index'])->name('frontend.return');
Route::get('/privacy-policy', [PoliciesController::class, 'privacy'])->name('frontend.privacy');
Route::get('/terms-services', [PoliciesController::class, 'termsconditions'])->name('frontend.termsconditions');
Route::post('/subscribe/send', [SubscribeController::class, 'send'])->name('subscribe.send');


//Add to wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::delete('/wishlist/remove/{id}', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::post('/wishlist/bulk-delete', [WishlistController::class, 'bulkDelete'])->name('wishlist.bulkDelete');

//checkout
Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('show.checkout');
Route::post('/cart/store-checkout-data', [CheckoutController::class, 'storeCheckoutData'])->name('cart.storeCheckoutData');


//======== Send OTP
Route::post('/send-otp', [CheckoutController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [CheckoutController::class, 'verifyOtp'])->name('verify.otp');


// Step 0: Store temporary order in session (AJAX)
Route::post('/payment/store-temp-order', [PaymentController::class, 'storeTempOrder'])
    ->name('payment.storeTempOrder');



// ===== Payment Integration URL
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/verify-payment', [PaymentController::class, 'verifyPayment'])->name('payment.verify');
Route::get('/admin/order/{id}/ship', [PaymentController::class, 'shiprocket'])->name('admin.shiprocket.ship');

  
//===== Order confirmation
Route::get('/order-confirmation', [PaymentController::class, 'order_confirmation'])->name('order.confirm');


Route::post('/signage/order/cancel/{orderId}',         [PaymentController::class, 'cancelOrder'])       ->name('order.cancel');
Route::post('/signage/order/refund/{orderId}',         [PaymentController::class, 'refundOrder'])       ->name('order.refund');
Route::post('/signage/order/payment-status/{orderId}', [PaymentController::class, 'updatePaymentStatus'])->name('order.payment.update');


 


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

// TEMPORARY diagnostic route — visit once on the live server, then tell Claude.
// Clears all caches and shows the actual .env values the server is using.
Route::get('/fix-google-cache', function () {
    \Artisan::call('optimize:clear');

    $lines = [];
    $envPath = base_path('.env');
    if (is_readable($envPath)) {
        foreach (file($envPath) as $l) {
            $t = trim($l);
            if (str_starts_with($t, 'APP_URL') || str_starts_with($t, 'GOOGLE_REDIRECT_URI')) {
                $lines[] = $t;
            }
        }
    }

    return response('<pre>Caches cleared.

Actual values in the server .env file:
' . e(implode("\n", $lines)) . '

config(services.google.redirect): ' . e(config('services.google.redirect')) . '
</pre>');
});

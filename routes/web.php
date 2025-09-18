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
Route::get('/', [HomeController::class, 'home'])->name('frontend.index');

// Master category products
Route::get('/category/{slug}', [CategoryProductsListingDetailsController::class, 'index'])->name('product.category');

// Subcategory products
Route::get('/products/{slug}', [ProductsListingDetailsController::class, 'index'])->name('product.details');

// Coming soon page
Route::get('/coming-soon', function () {
    return view('coming-soon');
})->name('coming.soon');


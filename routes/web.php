<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
////    return view('welcome');
//    return view('frontend.index');
//});

Route::get('/',[\App\Http\Controllers\FrontendController::class,'index'])->name('index');
Route::get('wishlist',[\App\Http\Controllers\FrontendController::class,'wishlist'])->name('wishlist');
Route::get('cart',[\App\Http\Controllers\FrontendController::class,'cart'])->name('cart');
Route::post('cart/add',[\App\Http\Controllers\FrontendController::class,'addToCart'])->name('cart.add');
Route::post('cart/remove',[\App\Http\Controllers\FrontendController::class,'removeFromCart'])->name('cart.remove');
Route::post('cart/update',[\App\Http\Controllers\FrontendController::class,'updateCart'])->name('cart.update');
Route::post('cart/update-variation',[\App\Http\Controllers\FrontendController::class,'updateVariation'])->name('cart.update-variation');
Route::post('cart/clear',[\App\Http\Controllers\FrontendController::class,'clearCart'])->name('cart.clear');
Route::post('cart/apply-coupon',[\App\Http\Controllers\FrontendController::class,'applyCoupon'])->name('cart.apply-coupon');
Route::post('cart/remove-coupon',[\App\Http\Controllers\FrontendController::class,'removeCoupon'])->name('cart.remove-coupon');
Route::get('checkout',[\App\Http\Controllers\FrontendController::class,'checkout'])->name('checkout');
Route::post('place-order',[\App\Http\Controllers\FrontendController::class,'placeOrder'])->name('place.order');
Route::get('contact',[\App\Http\Controllers\FrontendController::class,'contact'])->name('contact');
Route::get('about',[\App\Http\Controllers\FrontendController::class,'about'])->name('about');
Route::get('shop',[\App\Http\Controllers\FrontendController::class,'shop'])->name('shop');
Route::get('today-deal',[\App\Http\Controllers\FrontendController::class,'deal'])->name('deal');
Route::get('category-product/{slug}',[\App\Http\Controllers\FrontendController::class,'shop'])->name('category.products');
Route::get('/product/quick-view', [App\Http\Controllers\FrontendController::class, 'quickView'])->name('quick-view');
Route::get('product/{id}',[\App\Http\Controllers\FrontendController::class,'details'])->name('product.details');
Route::post('product/review/submit',[\App\Http\Controllers\FrontendController::class,'submitReview'])->name('review.submit');

// Wishlist AJAX routes
Route::post('wishlist/toggle',[\App\Http\Controllers\FrontendController::class,'toggleWishlist'])->name('wishlist.toggle');
Route::post('wishlist/remove',[\App\Http\Controllers\FrontendController::class,'removeWishlist'])->name('wishlist.remove');

Route::get('/pos/sale', [\App\Http\Controllers\Admin\AdminController::class, 'pos'])->middleware('auth:admin')->name('admin.pos');
Route::get('/pos/get-products', [\App\Http\Controllers\Admin\AdminController::class, 'getPosProducts'])->middleware('auth:admin')->name('admin.pos.getProducts');
Route::get('/pos/get-subcategories/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'getPosSubcategories'])->middleware('auth:admin')->name('admin.pos.getSubcategories');
Route::get('clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    return redirect('/');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';

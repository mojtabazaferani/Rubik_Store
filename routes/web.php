<?php

use App\Http\Controllers\RubikController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LoginCheck;
use App\Http\Middleware\Error404;
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

Route::controller(RubikController::class)->group(function() {

    Route::get('/', 'orginalHome')->name('orginal.home');

    Route::get('/home/two', 'orginalHomeTwo')->name('orginal.home2');

    Route::get('/products', 'products')->name('products');

    Route::get('/products/list', 'productsList')->name('products.list');

    Route::get('/product', 'product')->name('product');

    Route::post('/buy', 'buy')->name('buy')->middleware(LoginCheck::class);

    Route::get('/register', 'register')->name('register');

    Route::post('/store', 'store')->name('store');

    Route::get('/login', 'login')->name('login');

    Route::post('/check/{reset_password?}', 'check')->name('check');

    Route::get('/reset/password', 'resetPassword')->name('reset.password')->middleware(LoginCheck::class);

    Route::post('/update/password', 'updatePassword')->name('update.password');

    Route::get('/change/password', 'changePassword')->name('change.password');

    Route::put('/change/password', 'editPassword')->name('edit.password');

    Route::get('/profile', 'profile')->name('profile')->middleware(LoginCheck::class);

    Route::get('/panel/admin', 'panelAdmin')->name('panel.admin')->middleware(LoginCheck::class);

    Route::get('/panel/member', 'panelMember')->name('panel.member')->middleware(LoginCheck::class);

    Route::get('/notifications', 'notifications')->name('notifications')->middleware(LoginCheck::class);

    Route::post('/mark/all', 'markAll')->name('mark.all')->middleware(Error404::class);

    Route::get('/factors', 'factors')->name('factors')->middleware(LoginCheck::class);

    Route::get('/favorites', 'favorites')->name('favorites')->middleware(LoginCheck::class);

    Route::get('/cart', 'cart')->name('cart')->middleware(LoginCheck::class);

    Route::post('/purchase/invoice', 'purchaseInvoice')->name('purchase.invoice')->middleware(Error404::class);

    Route::get('/about', 'about')->name('about');

    Route::get('/faq', 'faq')->name('faq');

    Route::get('/blog', 'blog')->name('blog');

    Route::get('/blog/post', 'blogPost')->name('blog.post');

    Route::get('/compare', 'compare')->name('compare');

    Route::get('/checkout', 'checkout')->name('checkout')->middleware(LoginCheck::class);

    Route::get('/create/address', 'addresses')->name('addresses')->middleware(LoginCheck::class);

    Route::post('/create/address', 'createAddress')->name('create.address')->middleware(Error404::class);

    Route::get('/delete/address/{location?}/{state?}', 'deleteAddress')->name('delete.address')->middleware(LoginCheck::class);

    Route::delete('/delete/address', 'deletedAddress')->name('deleted.address')->middleware(Error404::class);

    Route::get('/change/address/{location?}/{state?}', 'addressChange')->name('address.change')->middleware(LoginCheck::class);

    Route::put('/address/update', 'addressUpdate')->name('address.update')->middleware(Error404::class);

    Route::get('/edit/address/{location}/{state}', 'editAddress')->name('edit.address')->middleware(LoginCheck::class);

    Route::put('/edit/address', 'editedAddress')->name('edited.address')->middleware(Error404::class);

    Route::get('/contact', 'contact')->name('contact')->middleware(LoginCheck::class);

    Route::post('/send/message', 'sendMessage')->name('send.message')->middleware(Error404::class);

    Route::get('/404', 'error404')->name('error.404');

    Route::post('/comment', 'comment')->name('comment')->middleware(Error404::class);

    Route::get('/message', 'message')->name('message')->middleware(LoginCheck::class);

    Route::post('/message', 'send')->name('send')->middleware(Error404::class);

    Route::get('/user/messages', 'userMessage')->name('user.message')->middleware(LoginCheck::class);

    Route::get('/messages', 'Messages')->name('messages')->middleware(LoginCheck::class);

    Route::post('/logout', 'logout')->name('logout')->middleware(LoginCheck::class);

});

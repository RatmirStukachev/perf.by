<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;

if (request()->ip() == '82.209.222.116') {
    Debugbar::enable();
}


Route::get('/', [HomeController::class, 'index'])->name('index');

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('/', [CategoryController::class, 'getCatalog'])->name('index');

    Route::get('/{category:slug}', [CategoryController::class, 'showLevel1'])->name('level1');

    Route::get('/{parent:slug}/{category:slug}', [CategoryController::class, 'showLevel2'])->name('level2');
});

Route::get('/search', [SearchController::class, 'search'])->name('search');

Route::get('/product/{product:slug}', [ProductController::class, 'getProduct'])->name('product');

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'getCart'])->name('cart.index');
    Route::post('/add', [CartController::class, 'addProduct'])->name('cart.add');
    Route::put('/update', [CartController::class, 'updateCart'])->name('cart.update');
    Route::delete('/remove', [CartController::class, 'removeCart'])->name('cart.remove');
});

Route::post('/order/create', [OrderController::class, 'createOrder'])->name('order.create');
Route::get('/order/success/{order:id}', [OrderController::class, 'getOrderSuccess'])->name('order.success');

Route::get('/contacts', [PageController::class, 'getContacts'])->name('contacts');

Route::get('/news-list', [PageController::class, 'getNews'])->name('news-list');
Route::get('/news-list/{slug}', [PageController::class, 'getOneNews'])->name('one-news');

Route::get('/{slug}', [PageController::class, 'getPage'])->name('page');

Route::post('/send-callback', [PageController::class, 'sendCallback'])->name('send-callback');

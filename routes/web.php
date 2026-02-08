<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [ArticleController::class, 'index'])->name('home');
Route::redirect('/home', '/');
Route::get('/article/{article:slug}', [ArticleController::class, 'show'])->name('article.show');
Route::get('/category/{category:slug}', [ArticleController::class, 'category'])->name('category.show');
Route::get('/search', [ArticleController::class, 'search'])->name('search');

// Authentication Routes
Auth::routes();

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Crawl
    Route::post('/crawl', [DashboardController::class, 'crawl'])->name('crawl');

    // Articles
    Route::get('/articles', [DashboardController::class, 'articles'])->name('articles');
    Route::get('/articles/create', [DashboardController::class, 'createArticle'])->name('articles.create');
    Route::post('/articles/classify', [DashboardController::class, 'classifyArticle'])->name('articles.classify');
    Route::post('/articles', [DashboardController::class, 'storeArticle'])->name('articles.store');
    Route::get('/articles/{article}/edit', [DashboardController::class, 'editArticle'])->name('articles.edit');
    Route::put('/articles/{article}', [DashboardController::class, 'updateArticle'])->name('articles.update');
    Route::delete('/articles/{article}', [DashboardController::class, 'deleteArticle'])->name('articles.delete');

    // Categories
    Route::get('/categories', [DashboardController::class, 'categories'])->name('categories');
    Route::get('/categories/create', [DashboardController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [DashboardController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}/edit', [DashboardController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{category}', [DashboardController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [DashboardController::class, 'deleteCategory'])->name('categories.delete');

    // Sources
    Route::get('/sources', [DashboardController::class, 'sources'])->name('sources');
    Route::get('/sources/create', [DashboardController::class, 'createSource'])->name('sources.create');
    Route::post('/sources', [DashboardController::class, 'storeSource'])->name('sources.store');
    Route::get('/sources/{source}/edit', [DashboardController::class, 'editSource'])->name('sources.edit');
    Route::put('/sources/{source}', [DashboardController::class, 'updateSource'])->name('sources.update');
    Route::delete('/sources/{source}', [DashboardController::class, 'deleteSource'])->name('sources.delete');

    // Test AI
    Route::get('/test-ai', [DashboardController::class, 'testAI'])->name('test-ai');
    Route::post('/test-ai', [DashboardController::class, 'processAI'])->name('test-ai.process');
});

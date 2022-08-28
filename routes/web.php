<?php

use App\Http\Livewire\ArticlesTable;
use App\Http\Livewire\ArticleForm;
use App\Http\Livewire\ArticleShow;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/blog/{article}', ArticleShow::class)
    ->name('article.show');

//Dashboard routes
Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified'])
    ->prefix('dashboard')->group(function(){

    Route::get('/blog', ArticlesTable::class)
        ->name('articles.index');

    Route::get('/blog/create', ArticleForm::class)
        ->name('article.create');

    Route::get('/blog/{article:id}/edit', ArticleForm::class)
        ->name('article.edit');

    Route::view('/','dashboard')
        ->name('dashboard');
});



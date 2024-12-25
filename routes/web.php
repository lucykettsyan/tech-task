<?php

use App\Http\Middleware\ValidateSearchQuery;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/search', [SearchController::class, 'search'])->middleware(ValidateSearchQuery::class);
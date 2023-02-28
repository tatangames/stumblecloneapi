<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\Login\LoginController;


Route::get('/', [LoginController::class,'index'])->name('login');

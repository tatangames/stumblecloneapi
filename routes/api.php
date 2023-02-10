<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Backend\Registro\RegistroController;




Route::post('/usuarios/registro', [RegistroController::class, 'registroUsuario']);

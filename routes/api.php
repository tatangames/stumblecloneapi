<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Registro\RegistroController;
use App\Http\Controllers\Api\Usuario\UsuarioInformacionController;
use App\Http\Controllers\Api\Login\LoginRedSocialController;

// registro de un nuevo usuario
Route::post('/usuario/registro', [RegistroController::class, 'registroUsuario']);

// login con redes sociales
Route::post('/usuario/registro/redsocial', [LoginRedSocialController::class, 'crearUsuarioRedSocial']);

// retorno de informacion de usuario local
Route::post('/usuario/informacion', [UsuarioInformacionController::class, 'informacionUsuarioLocal']);

// retorno de informacion de usuario con redes sociales
Route::post('/usuario/redsocial/informacion', [UsuarioInformacionController::class, 'informacionUsuarioRedSocial']);

// cambio de nombre de usuario
Route::post('/usuario/cambio/nombre', [UsuarioInformacionController::class, 'cambiarNombrePlayer']);




<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Registro\RegistroController;
use App\Http\Controllers\Api\Usuario\UsuarioInformacionController;


// registro de un nuevo usuario
Route::post('/usuario/registro', [RegistroController::class, 'registroUsuario']);

// retorno de informacion de usuario, cuando inicia el juego
Route::post('/usuario/informacion', [UsuarioInformacionController::class, 'informacionGlobalUsuario']);






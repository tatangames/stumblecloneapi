<?php

namespace App\Http\Controllers\Controles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // verifica que usuario inicio sesión y redirecciona a su vista según ROL
    public function indexRedireccionamiento(){

        $user = Auth::user();

        // ADMINISTRADOR SISTEMA
        $ruta = 'admin.roles.index';



        $titulo = "Battle Game";

        return view('backend.index', compact( 'ruta', 'user', 'titulo'));
    }

    // redirecciona a vista sin permisos
    public function indexSinPermiso(){
        return view('errors.403');
    }

}

<?php

namespace App\Http\Controllers\Api\Usuario;

use App\Http\Controllers\Controller;
use App\Models\UsuarioRecursos;
use Illuminate\Http\Request;

class UsuarioInformacionController extends Controller
{

    public function __construct(){
        $this->middleware('auth:sanctum');
    }

    // cambio de nombre de usuario
    // se verifica que haya gemas suficientes.
    public function informacionGlobalUsuario(Request $request){

        // La informacion por seguridad se obtendra con el Token
        $user = $request->user();

        if($user != null){

            // informacion de recursos del usuario
            $recursos = UsuarioRecursos::where('id_users', $user->id)->first();

            // obtener nivel de experiencia de usuario


            return ['usuario' => $user, 'recursos' => $recursos];

        }else{
            return ['success' => 99];
        }
    }


    private function miNivelExperiencia($id){




    }



}

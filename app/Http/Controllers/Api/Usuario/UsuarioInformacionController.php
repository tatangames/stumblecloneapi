<?php

namespace App\Http\Controllers\Api\Usuario;

use App\Http\Controllers\Controller;
use App\Models\NivelExperiencia;
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
            $nivelXP = $this->miNivelExperiencia($recursos);

            return ['nivelxp' => $nivelXP,'usuario' => $user, 'recursos' => $recursos];

        }else{
            return ['success' => 99];
        }
    }


    private function miNivelExperiencia($dataUser){

        $infoNivelExp = NivelExperiencia::orderBy('id', 'ASC')->get();

        $ultimoNivel = 0;
        $ultimaExperiencia = 0;

        foreach ($infoNivelExp as $info){

            $ultimoNivel = $info->nivel;
            $ultimaExperiencia = $info->experiencia;

            $siguienteXP = $info->experiencia;
            if($data = NivelExperiencia::where('id', $info->id + 1)->first()){
                $siguienteXP = $data->experiencia;
            }

            if(($dataUser->experiencia < $info->experiencia) &&
                ($dataUser->experiencia <= $siguienteXP)){
                return ['minivelxp' => $info->nivel, 'nextxp' => $info->experiencia, 'ultimonivel' => 0];
            }
        }

        return ['minivelxp' => $ultimoNivel, 'nextxp' => $ultimaExperiencia, 'ultimonivel' => 1];



        /*if(($dataUser->experiencia < 200) &&
            ($dataUser->experiencia <= 500)){
            return ['miXP' => 1, 'nextxp' => 200];
        }

        else if(($dataUser->experiencia < 300) &&
            ($dataUser->experiencia <= 800)){
            return ['miXP' => 2, 'nextxp' => 300];
        }

        else if(($dataUser->experiencia < 500) &&
            ($dataUser->experiencia <= 800)){
            return ['miXP' => 3, 'nextxp' => 500];
        }

        else{
            // he llegado al ultimo nivel
            return ['miXP' => 3, 'nextxp' => 500];
        }*/
    }



}

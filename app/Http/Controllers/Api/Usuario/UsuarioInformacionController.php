<?php

namespace App\Http\Controllers\Api\Usuario;

use App\Http\Controllers\Controller;
use App\Models\NivelExperiencia;
use App\Models\PanelNovedades;
use App\Models\PanelVideos;
use App\Models\User;
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

        $infoUsuario = User::where('id', $request->idusuario)->first();

        // informacion de recursos del usuario
        $recursos = UsuarioRecursos::where('id_users', $request->idusuario)->first();

        // obtener nivel de experiencia de usuario
        $nivelXP = $this->miNivelExperiencia($recursos);

        // obtener noticias como novedades
        $novedades = $this->getNoticiasNovedades();

        // obtener noticias como videos
        $videos = $this->getNoticiasVideos();

        return ['success' => 1, 'nivelxp' => [$nivelXP], 'usuario' => [$infoUsuario], 'recursos' => [$recursos],
                'novedades' => $novedades, 'videos' => $videos];
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

                if($info->nivel == 1){
                    $minimocurrent = 0;
                }else{
                    $minimocurrent = ($siguienteXP - $info->experiencia);
                }

                // retorna nivel en el que se encuentra el usuario
                return ['minivelxp' => $info->nivel, 'nextxp' => $info->experiencia, 'minimocurrent' => $minimocurrent, 'ultimonivel' => false];
            }
        }

        // retorno significa: que estamos en el ultimo nivel
        // el minimo current quedaria en 0, asi se llena la barra completa
        return ['minivelxp' => $ultimoNivel, 'nextxp' => $ultimaExperiencia, 'minimocurrent' => 0, 'ultimonivel' => true];
    }


    // informacion de las noticias como novedades
    private function getNoticiasNovedades(){

        $lista = PanelNovedades::orderBy('posicion', 'ASC')->get();
        return $lista;
    }

    // informacion de las noticias como videos
    private function getNoticiasVideos(){

        $lista = PanelVideos::orderBy('posicion', 'ASC')->get();
        return $lista;
    }


}

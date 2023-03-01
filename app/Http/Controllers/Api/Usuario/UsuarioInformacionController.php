<?php

namespace App\Http\Controllers\Api\Usuario;

use App\Http\Controllers\Controller;
use App\Models\Configuraciones;
use App\Models\NivelExperiencia;
use App\Models\NoticiaImagen;
use App\Models\NoticiaNovedades;
use App\Models\NoticiaVideos;
use App\Models\RegionesApp;
use App\Models\User;
use App\Models\UsuarioRecursos;
use App\Models\UsuarioRedSocial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsuarioInformacionController extends Controller
{

    public function __construct(){
        $this->middleware('auth:sanctum');
    }


    public function informacionUsuarioLocal(Request $request){

        $infoUsuario = User::where('id', $request->idusuario)->first();

        // informacion de recursos del usuario
        $recursos = UsuarioRecursos::where('id_users', $request->idusuario)->first();

        // obtener nivel de experiencia de usuario
        $nivelXP = $this->miNivelExperiencia($recursos);

        // obtener noticias como novedades por region
        $novedades = $this->getNoticiasNovedades();

        // obtener noticias como videos por region
        $videos = $this->getNoticiasVideos();

        // obtener imagenes de las noticias
        $imagenes = $this->getNoticiasImagenes();

        // codigo bundle para saver si debe actualizar el usuario
        $infoConfig = Configuraciones::where('id', 1)->first();

        return ['success' => 1, 'nivelxp' => [$nivelXP], 'usuario' => [$infoUsuario], 'recursos' => [$recursos],
                'novedades' => $novedades, 'videos' => $videos, 'notiimagen' => $imagenes, 'codeandroid' => $infoConfig->version_android,
                'codeapple' => $infoConfig->version_apple, 'nuevanoticia' => $infoConfig->nueva_noticia];
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

        $lista = NoticiaNovedades::orderBy('posicion', 'ASC')->get();

        // darle formato de fecha segun idioma actual del juego
        foreach ($lista as $dd){

            $infoRegiones = RegionesApp::where('id', $dd->id_regionesapp)->first();
            $dd->fecha = date($infoRegiones->fecha, strtotime($dd->fecha));
            $dd->region = $infoRegiones->nombre;
        }

        return $lista;
    }

    // informacion de las noticias como videos
    private function getNoticiasVideos(){

        $lista = NoticiaVideos::orderBy('posicion', 'ASC')->get();

        // darle formato de fecha segun idioma actual del juego
        foreach ($lista as $dd){

            $infoRegiones = RegionesApp::where('id', $dd->id_regionesapp)->first();
            $dd->fecha = date($infoRegiones->fecha, strtotime($dd->fecha));
            $dd->region = $infoRegiones->nombre;
        }

        return $lista;
    }

    // informacion de array de imagenes para noticias
    private function getNoticiasImagenes(){

        $lista = NoticiaImagen::orderBy('id', 'ASC')->get();

        return $lista;
    }


    public function informacionUsuarioRedSocial(Request $request){

        // request: idredsocial, tiporedsocial

        $infoRedSocial = UsuarioRedSocial::where('id_tiporedsocial', $request->tiporedsocial)
            ->where('id_redsocial', $request->idredsocial)
            ->first();

        $infoUsuario = User::where('id', $infoRedSocial->id_users)->first();

        // informacion de recursos del usuario
        $recursos = UsuarioRecursos::where('id_users', $infoRedSocial->id_users)->first();

        // obtener nivel de experiencia de usuario
        $nivelXP = $this->miNivelExperiencia($recursos);

        return ['success' => 1, 'idusuario' => $infoUsuario->id, 'token' => $infoRedSocial->token_redsocial,
            'nivelxp' => [$nivelXP], 'usuario' => [$infoUsuario], 'recursos' => [$recursos] ];
    }

    // retorno de trofeos y cornoas LOCAL
    private function getTrofeoCoronaLocal($region){

        /*$listado = DB::table('p_presup_unidad_detalle AS pre')
            ->join('p_materiales AS pm', 'pre.id_material', '=', 'pm.id')
            ->select('pm.id_objespecifico', 'pre.cantidad', 'pre.precio', 'pre.periodo')
            ->whereIn('pre.id_presup_unidad', $pilaArrayPresuUnidad)
            ->where('pm.id_objespecifico', $dd->id) // todos los materiales con este id obj específico
            ->get();*/

        $listado = User::where('pais', $region)->take(100)->get();


    }


}

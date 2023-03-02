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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        // obtener posicion de coronas Local
        $coronasLocal = $this->getPosicionCoronasLocal($infoUsuario->pais);

        // obtener posicion de copas Local
        $copasLocal = $this->getPosicionCopasLocal($infoUsuario->pais);

        // obtener posicion de coronas global
        $coronasGlobal = $this->getPosicionCoronasGlobal();

        // obtener posicion de coronas global
        $copasGlobal = $this->getPosicionCopasGlobal();


        // codigo bundle para saver si debe actualizar el usuario
        $infoConfig = Configuraciones::where('id', 1)->first();

        return ['success' => 1,
                'nivelxp' => [$nivelXP],
                'usuario' => [$infoUsuario],
                'recursos' => [$recursos],
                'novedades' => $novedades,
                'videos' => $videos,
                'notiimagen' => $imagenes,
                'codeandroid' => $infoConfig->version_android,
                'codeapple' => $infoConfig->version_apple,
                'nuevanoticia' => $infoConfig->nueva_noticia,
                'coronaslocal' => $coronasLocal,
                'copaslocal' => $copasLocal,
                'coronasglobal' => $coronasGlobal,
                'copasglobal' => $copasGlobal,
        ];
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

        return ['success' => 1,
                'idusuario' => $infoUsuario->id,
                'token' => $infoRedSocial->token_redsocial,
                'nivelxp' => [$nivelXP],
                'usuario' => [$infoUsuario],
                'recursos' => [$recursos] ];
    }

    // retorno de coronas Local
    private function getPosicionCoronasLocal($region){

        $listado = DB::table('users AS uu')
            ->join('usuario_recursos AS re', 're.id_users', '=', 'uu.id')
            ->select('uu.nombre', 'uu.pais', 're.coronas', 'uu.id')
            ->where('uu.pais', $region) // si region es null, pues lista estara vacia
            ->orderBy('re.coronas', 'DESC')
            ->take(100)
            ->get();

        $conteo = 0;
        foreach ($listado as $dd){
            $conteo++;
            $dd->conteo = $conteo;
        }

        return $listado;
    }

    // retorno de copas Local
    private function getPosicionCopasLocal($region){

        $listado = DB::table('users AS uu')
            ->join('usuario_recursos AS re', 're.id_users', '=', 'uu.id')
            ->select('uu.nombre', 'uu.pais', 're.copas', 'uu.id')
            ->where('uu.pais', $region) // si region es null, pues lista estara vacia
            ->orderBy('re.copas', 'DESC')
            ->take(100)
            ->get();

        $conteo = 0;
        foreach ($listado as $dd){
            $conteo++;
            $dd->conteo = $conteo;
        }

        return $listado;
    }


    // retorno de coronas Global
    private function getPosicionCoronasGlobal(){

        $listado = DB::table('users AS uu')
            ->join('usuario_recursos AS re', 're.id_users', '=', 'uu.id')
            ->select('uu.nombre', 'uu.pais', 're.coronas', 'uu.id')
            ->orderBy('re.coronas', 'DESC')
            ->take(100)
            ->get();

        $conteo = 0;
        foreach ($listado as $dd){
            $conteo++;
            $dd->conteo = $conteo;
        }

        return $listado;
    }


    // retorno de copas Global
    private function getPosicionCopasGlobal(){

        $listado = DB::table('users AS uu')
            ->join('usuario_recursos AS re', 're.id_users', '=', 'uu.id')
            ->select('uu.nombre', 'uu.pais', 're.copas', 'uu.id')
            ->orderBy('re.copas', 'DESC')
            ->take(100)
            ->get();

        $conteo = 0;
        foreach ($listado as $dd){
            $conteo++;
            $dd->conteo = $conteo;
        }

        return $listado;
    }

    public function cambiarNombrePlayer(Request $request){

        $rules = array(
            'id' => 'required',
            'nombre' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {
            if(User::where('id', $request->id)->first()){

                $infoRecursos = UsuarioRecursos::where('id_users', $request->id)->first();

                $conteo = Str::length($request->nombre);

                if($infoRecursos->nombre_cambio == 1){
                    // ya cambio nombre, verificar que haya gemas
                    $infoConfig = Configuraciones::where('id', 1)->first();

                    if($infoRecursos->recursos < $infoConfig->precio_nombre){
                        // no alcanzan las gemas
                        return ['success' => 1];
                    }


                    if($conteo < 4 || $conteo > 12){
                        return ['success' => 2];
                    }

                    if(User::where('nombre', $request->nombre)->first()){
                        return ['success' => 3];
                    }

                    // cambiar nombre
                    User::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                    ]);

                    UsuarioRecursos::where('id', $infoRecursos->id)->update([
                        'nombre_cambio' => 1,
                    ]);

                    DB::commit();
                    return ['success' => 4];
                }else{
                    // no ha cambiado el nombre.
                    // verificar reglas
                    // nombre debe tener minimo 4 y 12 caracteres maximo
                    // nombre unico


                    if($conteo < 4 || $conteo > 12){
                        return ['success' => 2];
                    }

                    if(User::where('nombre', $request->nombre)->first()){
                        return ['success' => 3];
                    }

                    // cambiar nombre
                    User::where('id', $request->id)->update([
                        'nombre' => $request->nombre,
                    ]);

                    UsuarioRecursos::where('id', $infoRecursos->id)->update([
                        'nombre_cambio' => 1,
                    ]);

                    DB::commit();
                    return ['success' => 4];
                }

            }else{
                return ['success' => 99];
            }
        } catch (\Throwable $e) {
            DB::rollback();
            return ['success' => 99];
        }
    }



}

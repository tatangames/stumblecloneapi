<?php

namespace App\Http\Controllers\Api\Registro;

use App\Http\Controllers\Controller;
use App\Models\Configuraciones;
use App\Models\NivelExperiencia;
use App\Models\NoticiaImagen;
use App\Models\NoticiaNovedades;
use App\Models\NoticiaVideos;
use App\Models\RegionesApp;
use App\Models\User;
use App\Models\UsuarioRecursos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegistroController extends Controller
{

    // registro de un usuario nuevo
    public function registroUsuario(Request $request){

        $clientIP = "190.86.190.54"; // sv
        //$clientIP = request()->ip();

        // el pais puede ser null
        $pais = "us";

        // obtiene el pais de donde se hace la consulta http
        /*if ($position = Location::get($clientIP)) {
             $pais = $position->countryCode;
        }*/

        // pasar a minuscula
        $pais = strtolower($pais);

        // generar random nombre aleatorio
        do {
            $randomPlayer = "Player_" . Str::random(9);
            $data = User::where('nombre', $randomPlayer)->get();
        }
        while ($data->count());

        DB::beginTransaction();

        try {

            // crear usuario
            $dataUser = new User();
            $dataUser->fecha_creacion = Carbon::now('America/El_Salvador');
            $dataUser->nombre = $randomPlayer;
            $dataUser->pais = $pais;
            $dataUser->save();

            // generar recursos
            $dataRecurso = new UsuarioRecursos();
            $dataRecurso->id_users = $dataUser->id;
            $dataRecurso->gemas = 5; // gemas por defecto
            $dataRecurso->monedas = 0;
            $dataRecurso->copas = 0;
            $dataRecurso->coronas = 0;
            $dataRecurso->experiencia = 0;
            $dataRecurso->nombre_cambio = false;
            $dataRecurso->save();

            // token de jugador
            $token = $dataUser->createToken('auth_token')->plainTextToken;

            // informacion de recursos del usuario
            $recursos = UsuarioRecursos::where('id_users', $dataUser->id)->first();

            // obtener nivel de experiencia de usuario
            $nivelXP = $this->miNivelExperiencia();

            // obtener noticias como novedades por region
            $novedades = $this->getNoticiasNovedades();

            // obtener noticias como videos por region
            $videos = $this->getNoticiasVideos();

            // obtener imagenes de las noticias
            $imagenes = $this->getNoticiasImagenes();

            // obtener posicion de coronas Local
            $coronasLocal = $this->getPosicionCoronasLocal($pais);

            // obtener posicion de copas Local
            $copasLocal = $this->getPosicionCopasLocal($pais);

            // obtener posicion de coronas global
            $coronasGlobal = $this->getPosicionCoronasGlobal();

            // obtener posicion de coronas global
            $copasGlobal = $this->getPosicionCopasGlobal();

            // codigo bundle para saver si debe actualizar el usuario
            $infoConfig = Configuraciones::where('id', 1)->first();

            DB::commit();
            // el idusaurio retornado sera para datos locales
            return ['success' => 1,
                    'token' => $token,
                    'usuario' => [$dataUser],
                    'nivelxp' => [$nivelXP],
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
                    'copasglobal' => $copasGlobal
                ];

        } catch (\Throwable $e) {
            DB::rollback();
            return ['success' => 99];
        }
    }


    private function miNivelExperiencia(){

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

            // es 0: porque el usuario no tiene experiencia aun
            if((0 < $info->experiencia) &&
                (0 <= $siguienteXP)){

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


}

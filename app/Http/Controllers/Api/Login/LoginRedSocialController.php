<?php

namespace App\Http\Controllers\Api\Login;

use App\Http\Controllers\Controller;
use App\Models\NivelExperiencia;
use App\Models\User;
use App\Models\UsuarioRecursos;
use App\Models\UsuarioRedSocial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginRedSocialController extends Controller
{

    // login con redes sociales
    public function crearUsuarioRedSocial(Request $request){

        // reuqest: tiporedsocial, idredsocial

        // se buscar al usuario, si existe se retona preguntandole al usuario si quiere cargar
        // los datos de esta cuenta encontrada
        // 1- facebook, 2- android, 3-apple

        if($infoRed = UsuarioRedSocial::where('id_tiporedsocial', $request->tiporedsocial)
            ->where('id_redsocial', $request->idredsocial)
            ->first()){
                // usuario encontrado, retornar para preguntar a usuario si quiere recuperar datos

            $infoUsuario = User::where('id', $infoRed->id_users)->first();

            return ['success' => 1, 'usuario' => [$infoUsuario]];
        }

        // CREAR USUARIO NUEVO CON LA RED SOCIAL

        $clientIP = "190.86.190.54"; // sv
        //$clientIP = request()->ip();

        // pais por DEFECTO (estados unidos)
        $pais = "US";

        // obtiene el pais de donde se hace la consulta http
        /*if ($position = Location::get($clientIP)) {
             $pais = $position->countryCode;
        }*/

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

            // guardar registro de red social
            $dataRed = new UsuarioRedSocial();
            $dataRed->id_users = $dataUser->id;
            $dataRed->id_tiporedsocial = $request->tiporedsocial;
            $dataRed->id_redsocial = $request->idredsocial;
            $dataRed->token_redsocial = $token;
            $dataRed->save();

            // informacion de recursos del usuario
            $recursos = UsuarioRecursos::where('id_users', $dataUser->id)->first();

            // obtener nivel de experiencia de usuario
            $nivelXP = $this->miNivelExperiencia($dataRecurso);


            DB::commit();
            // el idusaurio retornado sera para datos locales
            return ['success' => 2, 'idusuario' =>$dataUser->id, 'token' => $token, 'usuario' => [$dataUser], 'nivelxp' => [$nivelXP], 'recursos' => [$recursos] ];

        } catch (\Throwable $e) {
            DB::rollback();
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


}

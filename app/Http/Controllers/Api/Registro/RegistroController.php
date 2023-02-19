<?php

namespace App\Http\Controllers\Api\Registro;

use App\Http\Controllers\Controller;
use App\Models\NivelExperiencia;
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
            $dataUser->id_facebook = null;
            $dataUser->id_android = null;
            $dataUser->id_apple = null;
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
            $nivelXP = $this->miNivelExperiencia($recursos);

            DB::commit();
            // el idusaurio retornado sera para datos locales
            return ['success' => 1, 'idusuario' => $dataUser->id, 'token' => $token, 'nivelxp' => [$nivelXP], 'usuario' => [$dataRecurso], 'recursos' => [$recursos]];

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
                // retorna nivel en el que se encuentra el usuario
                return ['minivelxp' => $info->nivel, 'nextxp' => $info->experiencia, 'ultimonivel' => false];
            }
        }

        // retorno significa: que estamos en el ultimo nivel
        return ['minivelxp' => $ultimoNivel, 'nextxp' => $ultimaExperiencia, 'ultimonivel' => true];
    }



}

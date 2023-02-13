<?php

namespace App\Http\Controllers\Backend\Registro;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsuarioRecursos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

class RegistroController extends Controller
{

    // registro de un usuario nuevo
    public function registroUsuario(Request $request){

        $ip = "190.86.190.54"; // sv
        //$clientIP = request()->ip();

        // pais por DEFECTO (estados unidos)
        $pais = "US";

        /*if ($position = Location::get($ip)) {
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
            $dataUser->experiencia = 0;
            $dataUser->nombre_cambio = false;
            $dataUser->id_facebook = null;
            $dataUser->id_android = null;
            $dataUser->id_apple = null;
            $dataUser->pais = $pais;
            $dataUser->save();

            // generar recursos
            $dataRecurso = new UsuarioRecursos();
            $dataRecurso->id_users = $dataUser->id;
            $dataRecurso->gemas = 5; // gemas por defecto
            $dataRecurso->copas = 0;
            $dataRecurso->coronas = 0;

            // token de jugador
            $token = $dataUser->createToken('auth_token')->plainTextToken;

            $arrayUsuario[] = [
                'success' => 1,
                'idusuario' => $dataUser->id,
                'token' => $token,
                'gemas' => 5,
                'copas' => 0,
                'coronas' => 0
            ];

            return ['success' => 1, 'usuario' => $arrayUsuario];



            //DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            return ['success' => 99];
        }



       /* $regla = array(
            'nombre' => 'required',
            'password' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(User::where('nombre', $request->nombre)->first()){
            return "nombre usuario ya registrado";
        }

        do
        {
            $token = $this->getToken(6, $application_id);
            $code = 'EN'. $token . substr(strftime("%Y", time()),2);
            $user_code = User::where('user_code', $code)->get();
        }
        while(!$user_code->isEmpty());



        $dato = new User();
        $dato->nombre = $request->nombre;
        $dato->password = bcrypt($request->password);
        $dato->save();

        $token = $dato->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);*/

    }


}

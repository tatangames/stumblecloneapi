<?php

namespace App\Http\Controllers\Backend\Registro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Stevebauman\Location\Facades\Location;
class RegistroController extends Controller
{

    public function registroUsuario(Request$request){

        //$ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));

        //$ip =  \Request::getClientIp(true);

        $clientIP = \Request::ip();


        return $clientIP;

        //return $ip;

        if ($position = Location::get($ip)) {
            // Successfully retrieved position.
            return $position->countryName;
        } else {
            // Failed retrieving position.
        }






       // $clientIP = request()->ip();
       // dd($clientIP);
       /* public function index(Request $request){
            dd($request->ip());
        }*/

       // $clientIP = \Request::getClientIp(true);
       // dd($clientIP);










        return "fallo";


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

<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class LoginController extends Controller
{
    public function __construct(){
        $this->middleware('auth:sanctum');
    }

    public function registroUsuario(Request $request){

        return "sfsdfsdf";

        $regla = array(
            'nombre' => 'required',
            'password' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(User::where('nombre', $request->nombre)->first()){
            return "nombre usuario ya registrado";
        }

        $dato = new User();
        $dato->nombre = $request->nombre;
        $dato->password = bcrypt($request->password);
        $dato->save();

        $token = $dato->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function infoUsuario(Request $request){



    }


    public function pruebaTest(){

        return ['title' => 'holiss'];
    }
}

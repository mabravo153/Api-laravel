<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;

class userController extends Controller
{


    public function registro(Request $request)
    {

        //recoger los datos del usuario por post 

        $json = $request->input('json', null); //null es el default 
        $params = json_decode($json, true); //true es para que nos retorne un array, si no lo hacemos nos retornara un objeto

        if (!empty($params)) {

            //limpiar datos
            $params = array_map('trim', $params);

            //validar datos 

            $validate = Validator::make($params, [
                'name'       => 'required|alpha',
                'lastName'   => 'required|alpha',
                'userName'   => 'required|alpha_dash|unique:users',
                'email'      => 'required|email|unique:users',
                'password'   => 'required'
            ]);

            // de esta manera damos respuesta en caso de no validar los campos
            if ($validate->fails()) {
                $respuesta = array(
                    'estado' => 'error',
                    'codigo' => 400,
                    'descripcion' => 'hubo un problema',
                    'errors' => $validate->errors()
                );
            } else {
                $respuesta = array(
                    'estado' => 'completado',
                    'codigo' => 200,
                    'descripcion' => 'se valido correctamente'
                );
            }

            //cifrar contraseña 
            $contrasena = password_hash($params['password'],PASSWORD_BCRYPT,array('cost' => 12)); 


            //crear el usuario 
            $user = new User();

            $user->name = $params['name'];
            $user->lastName = $params['lastName'];
            $user->userName = $params['userName'];
            $user->email = $params['email'];
            $user->password = $contrasena;
            $user->role = "USER";

            //guardar en la base de datos
            $user->save();

        } else {
            $respuesta = array(
                'estado' => 'error',
                'codigo' => 404,
                'descripcion' => 'los campos enviados no son correctos'
            );
        }

        return response()->json($respuesta, $respuesta['codigo']);
    }

    public function login(Request $request)
    {
        return "prueba login";
    }
}

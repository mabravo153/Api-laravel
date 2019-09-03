<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\helpers\jwtAuth;


class userController extends Controller
{


    public function registro(Request $request) {

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
                    'descripcion' => 'se creo correctamente'
                );
            }

            //cifrar contraseña 
            $contrasena = hash('sha256', $params['password']); 


            //instancias el modelo usuario 
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

    public function login(Request $request){
        
        //recibir los datos por post

        $json = $request->input('json', null);
        $jsonDecode = json_decode($json, true);
    
       if(!empty($jsonDecode)){

         //validar los datos 

        $validate = Validator::make($jsonDecode, array(
            'userName'  => 'required|alpha_dash',
            'password'   => 'required'
        ));

        if($validate->fails()){
            $jwt = array(
                'estado' => 'error',
                'codigo' => 406,
                'mensaje' => $validate->errors() 
            ); 
        }else{

            $signUp = new jwtAuth();

            //cifrar la contraseña 
            $contrasena = hash('sha256', $jsonDecode['password']);
            

            //retornar datos o token 
            $jwt = $signUp->signUp($jsonDecode['userName'], $contrasena); // me regresa el token 

            if(!empty($jsonDecode['getToken'])){
                $jwt = $signUp->signUp($jsonDecode['userName'], $contrasena, true); //me retorna los datos sacados de la base de datos
            }

        }

        

       }
          
        return response()->json($jwt, 200); 
    }


    //actualizar datos de usuario 
    public function update(Request $request){
        //necesitamos recoger la cabecera que contendra el token

        
        $token = $request->header('Auth');

        //instanciamos la clase para poder ingresar al metodo 
        $jwt = new jwtAuth();
        $verificarToken = $jwt->checkToken($token);

        if($verificarToken){
            //actualziar el usuario
        }else{
            $data = array(
                'estado' => 'error',
                'codigo' => 
            );
        }
         
         
      return $data;

        
    }

}//final clase 

<?php 

//creamos el namespace para acceder a cualquier lado mas rapido 
namespace App\helpers; 

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User; 


class jwtAuth {

    public $key; 

    public function __construct(){
        $this->key = "esta_es_una_clave_super_secreta*125";
    }



    //funcion para loguear al usuario 
    public function signUp($userName, $password, $getToken = null){
        /*buscar si existe el usuario y contraseña */

        //esto me retornara el primer resultado, podemos usar get. podemos usar all que nos trae todos los datos 
        $user = User::where([
            'userName' => $userName,
            'password' => $password
        ])->first();

        /*comprobar si existen */
        //inicializa en false. si encuentra algo, este lo retornara como objeto, si es correcto ingresa
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }

        /*generar el token con los datos del usuario identificado */
        //en caso de ser true generaremos el token
        if($signup){

            $token = array(
                'sub'       => $user->id,
                'email'     => $user->email,
                'name'      => $user->name,
                'lastName'  => $user->lastName,
                'iat'       => time(), //iat es el tiempo de creacion del token, time sirve para creaar el tiempo en el momento
                'exp'       => time() + (7 * 24 * 60 * 60) // el token tendra una vida util de una semana 
            );

            $jwt = JWT::encode($token,$this->key, 'HS256'); // token codificado 

            $jwtDecode = JWT::decode($jwt, $this->key,['HS256']); //token decodificado 


            //devolver los datos decodificados o el token en funcion de un parametro 

            if(is_null($getToken)){
                $data = $jwt;
            }else{
                $data =$jwtDecode;
            }

        }else{
            $data = array(
                'respuesta' => 'error',
                'mensaje' => 'login incorrecto'
            );
        }

        return $data;  

    }



    //funcion para verificar el token.
    public function checkToken($jwt, $getIdentity  = false){
        $auth = false; //por defecto la autenticacion del token sera falso 

        //este codigo es propenso a errores, por eso lo agregamos en un try 
        try {
            //eliminados las comillas del token 
            str_replace('"', '', $jwt);

           $tokenDeco = JWT::decode($jwt, $this->key, ['HS256']); 
        } catch (\UnexpectedValueException $th) {
            $auth = false;
        }catch (\DomainException $th) {
            $auth = false;
        }


    (!empty($tokenDeco) && is_object($tokenDeco) && isset($tokenDeco->sub))? $auth = true: $auth = false;

    if($getIdentity){
        return $tokenDeco;
    }

    return $auth;

    }//final funcion token 
   
}//final clase
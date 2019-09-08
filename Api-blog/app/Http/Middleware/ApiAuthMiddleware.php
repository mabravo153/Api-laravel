<?php

namespace App\Http\Middleware;

use Closure;
use App\helpers\jwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){

         //necesitamos recoger la cabecera que contendra el token
         $token = $request->header('Auth');

         //instanciamos la clase para poder ingresar al metodo 
         $jwt = new jwtAuth();
         $verificarToken = $jwt->checkToken($token); //esto nos retornar que el usuario esta autorizado 


         if ($verificarToken) {
            return $next($request);
         } else {
             
            $data = array(
                'estado' => 'error',
                'codigo' => 400,
                'mensaje'=> 'usuario no identificado'
            );

            return response()->json($data, $data['codigo']);
         }
         

        
    }
}

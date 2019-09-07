<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\helpers\jwtAuth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;

class userController extends Controller
{


    public function registro(Request $request) {

        //recoger los datos del usuario por post 

        $json = $request->input('json', null); //null es el default 
        $params = json_decode($json, true); //true es para que nos retorne un array, si no lo hacemos nos retornara un objeto

        if (!empty($params)) {

            //limpiar datos de espacios
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

                //cifrar contraseña 
            $contrasena = hash('sha256', $params['password']); 


            //instancias el modelo usuario 
            $user = new User();
            
            //tomamos los atrubutos del usuario y los pasamos 
            $user->name = $params['name'];
            $user->lastName = $params['lastName'];
            $user->userName = $params['userName'];
            $user->email = $params['email'];
            $user->password = $contrasena;
            $user->role = "USER";

            //guardar en la base de datos
            $user->save();

            //enviamos respuesta
            $respuesta = array(
                    'estado' => 'completado',
                    'codigo' => 200,
                    'descripcion' => 'el usuario se creo correctamente'
            );

            }

        } else {
            $respuesta = array(
                'estado' => 'error',
                'codigo' => 404,
                'descripcion' => 'los campos enviados no son correctos'
            );
        }

        return response()->json($respuesta, $respuesta['codigo']);
    }//final registro 

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
    }//final login 


    /*actualizar datos de usuario */
    public function update(Request $request){
       
        //necesitamos recoger la cabecera que contendra el token
        $token = $request->header('Auth');

        //instanciamos la clase para poder ingresar al metodo 
        $jwt = new jwtAuth();
        $verificarToken = $jwt->checkToken($token); //esto nos retornar que el usuario esta autorizado 

        //recoger los datos del post 
        $json =$request->input('json', null);
        $parametros = json_decode($json, true);

        if($verificarToken && !empty($parametros)){
            /*actualziar el usuario*/
           
     
            $usuarioToken = $jwt->checkToken($token, true);//nos retorna el token con los datos del usuario traidos desde la base de datos 

            //validar los datos 
            $validate = Validator::make($parametros,[
                'name'       => 'required|alpha',
                'lastName'   => 'required|alpha',
                'userName'   => ['required','alpha_dash',  Rule::unique('users')->ignore($usuarioToken->sub)],
                'email'      => ['required', 'email', Rule::unique('users')->ignore($usuarioToken->sub)],//esto es la informacion que nos trae el token creado en jwtAuth      
            ]);

            if($validate->fails()){
                $data = array(
                    'estado' => 'error',
                    'codigo' => 400,
                    'mensaje'=> $validate->errors()
                );
            }else{

            /*quitar los campos que no quiero actualizar */
                //con unset eliminamos los campos que no queramos
            
                unset($parametros['id']);
                unset($parametros['password']);
                unset($parametros['create_at']);//parametros que en este caso no queramos actualizar 
                unset($parametros['rememberToken']);
                unset($parametros['role']);


            // actualizar datos en la bd 

            $user = User::where('id', $usuarioToken->sub)->update($parametros);


            // retornar array con la respuesta 

            $data = array(
                'estado' => 'correcto',
                'codigo' => 200,
                'mensaje'=> 'se actualizo correctamente'
            );

            }
        }else{
            //devolver un error en caso de no autenticar 
            $data = array(
                'estado' => 'error',
                'codigo' => 401,
                'mensaje'=> 'el usuario no esta identificado o no se envio parametros'
            );
        }
         
         //RECUERDA SIEMPRE RETORNAR LA RESPUESTA COMO UN JSON
      return response()->json($data, $data['codigo']);

        
    }//final update 

    public function uploadFoto(Request $request){
        
        //gracias al middleware nos evitamos tener que autenticar el token en todas las funciones 

        //recibir el fichero 
        $image = $request->file('file0'); // los llamremos de esta manera por el front, qu ellamara los archovos file 0 , 1 , 2

        //validar la imagen 

        $validate = Validator::make($request->all(),[
            'file0' => 'required|image'
        ]); 

        //guardar la imagen 

        if(!$image || $validate->fails()){
            
            $data = array(
                'estado' => 'error',
                'codigo' => 400,
                'mensaje' => 'no hay imagen disponible o no es un formato valido'
            ); 

        }else{

            $imageName = time().$image->getClientOriginalName(); 

            \Storage::disk('users')->put($imageName, \File::get($image));

            $data = array(
                'estado' => 'correcto',
                'codigo' => 200,
                'nombreImage' => $imageName,
                'mensaje' => 'la foto se subio correctamente'
            );
        }

        return response()->json($data, $data['codigo']);
    }


    public function getImage($imagen){
        
        $exists = \Storage::disk('users')->exists($imagen);
        

        if ($exists) {
            $file = \Storage::disk('users')->get($imagen);

           return new Response($file, 200);


        }else {
         
            $data = array(
                'estado' => 'error', 
                'codigo' => 404
            );

            return response()->json($data, $data['codigo']);

        } 
        

    }

}//final clase 

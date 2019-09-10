<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Posts;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\helpers\jwtAuth;

class postsController extends Controller
{

    private function optenerIdentidad($request){
        $header = $request->header('Auth');
        $jwt = new jwtAuth();
        $token = $jwt->checkToken($header, true);

        return $token;
    }

    public function __construct(){
        $this->middleware('apiauth', ['except' => ['index', 'show']]);
    }


    public function index(){
        $posts = Posts::all()->load('categories');

        if ($posts) {

            $data = array(
                'estado'    => 'completo',
                'codigo'    => 200,
                'mensaje'   => $posts
            );
        } else {

            $data = array(
                'estado'    => 'error',
                'codigo'    => 400,
                'mensaje'   => 'no hay posts disponibles'
            );
        }

        return response()->json($data, $data['codigo']);
    }

    public function show($id){

        $result = Posts::find($id)->load('categories');

        if (is_object($result)) {

            $data = array(
                'estado'    => 'correcto',
                'codigo'    => 200,
                'mensaje'   => $result
            );
        } else {

            $data = array(
                'estado'    => 'error',
                'codigo'    => 400,
                'mensaje'   => 'el post solicitado no existe'
            );
        }

        return response()->json($data, $data['codigo']);
    }

    public function store(Request $request){

        //recoger los datos 

        $json = $request->input('json', null);

        $param = json_decode($json, true);

        if (!empty($param)) {

            //conseguir usuario autenticado 

           $user = $this->optenerIdentidad($request);

            //validar 

            $validate = Validator::make($param, [

                'title'     => 'required',
                'content'   => 'required',
                'category'  => 'required', 
                'image'     => 'required'

            ]);

            if($validate->fails()){
                $data = array(
                    'estado'    => 'error',
                    'codigo'    => 400,
                    'mensaje'   => $validate->errors()
                );
            }else{

                //guardar 

                $post = new Posts();

                $post->title = $param['title'];
                $post->content = $param['content'];
                $post->fk_idcategories = $param['category'];
                $post->fk_idusers = $user->sub; 
                $post->image = $param['image'];

                $post->save();

                $data = array(
                    'estado'    => 'correcto',
                    'codigo'    => 200,
                    'mensaje'   => $post
                );

            }

            

        }else{
            $data = array(
                'estado'    => 'error',
                'codigo'    => 404,
                'mensaje'   => 'los parametros estan vacios'
            );
        }

        return response()->json($data, $data['codigo']);
    }

    public function update($id, Request $request){
        
        $json = $request->input('json', null); 
        $param = json_decode($json, true); 

        if(!empty($param)){
            
            $validate = Validator::make($param, [
                'title'     => 'required',
                'content'   => 'required',
                'image'     => 'required',
                'fk_idcategories'  => 'required'
            ]); 


            if($validate->fails()){
                
                $data = array(
                    'estado'    => 'error',
                    'codigo'    => 400,
                    'mensaje'   => $validate->errors()
                );

            }else{

                unset($param['id']);
                unset($param['created_at']);
                unset($param['user']);
                unset($param['id_user']);//por si nos envian informacion que pueda afectar el id del usuario 

                $update = Posts::where('id', $id)->update($param);

                $data = array(
                    'estado'    => 'correcto',
                    'codigo'    => 200,
                    'mensaje'   => $param
                );

            }

        }else{
            $data = array(
                'estado'    => 'error',
                'codigo'    => 404,
                'mensaje'   => 'el elemento enviado se encuantra vacio'
            );

        }

        return response()->json($data, $data['codigo']); 

    }


    public function destroy($id, Request $request){
        
        $user = $this->optenerIdentidad($request);

        $destroy = Posts::where('id', $id)
                        ->where('fk_idusers', $user->sub)
                        ->first();

        if(is_object($destroy)){
            $destroy->delete();

            $data = array(
                'estado'    => 'correcto',
                'codigo'    => 200,
                'mensaje'   => "el elemento {$destroy->title} se elimino correctamente"
            );

        }else{
            $data = array(
                'estado'    => 'error',
                'codigo'    => 400,
                'mensaje'   => 'el elemento no se encuentra o no tienes permiso de eliminarlo'
            );
        }
        return response()->json($data,$data['codigo']);

    }

    
}

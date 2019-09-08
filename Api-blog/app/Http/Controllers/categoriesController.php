<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Categories;
use App\helpers\jwtAuth;

class categoriesController extends Controller
{

    //aplicar middleware 
    public function __construct()
    {
        $this->middleware('apiauth', ['except' => ['index', 'show']]);
    }

    //retornar todas las categorias 
    public function index()
    {

        $categories = Categories::all();

        if ($categories) {
            $data = array(
                'estado'    => 'correcto',
                'codigo'    => 200,
                'mensaje'   => $categories
            );
        } else {
            $data = array(
                'estado'    => 'error',
                'codigo'    => 404,
                'mensaje'   => 'no hay categorias disponibles'
            );
        }


        return response()->json($data, $data['codigo']);
    }

    //retornar una categoria 
    public function show($idCategorie)
    {

        $categorie = Categories::find($idCategorie);

        if (is_object($categorie)) {
            $data = array(
                'estado' => 'correcto',
                'codigo' => 200,
                'mensaje' => $categorie
            );
        } else {
            $data = array(
                'estado' => 'error',
                'codigo' => 404,
                'mensaje' => 'la categoria no existe'
            );
        }

        return response()->json($data, $data['codigo']);
    }

    //ingresar nueva categoria

    public function store(Request $request)
    {

        /*RECIBIR LOS DATOS Y CODIGICARLOS */
        $input = $request->input('json', null);
        $param = json_decode($input, true);

        /*VALIDAR LOS DATOS*/

        if (!empty($param)) {

            $validate = Validator::make($param, [
                'name' => 'required|string'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'estado' => 'error',
                    'codigo' => 400,
                    'mensaje' => $validate->errors()
                );
            } else {

                /*GUARDAR LA CATEGORIA*/

                //instancia el modelo 
                $insertCategory = new Categories();

                $insertCategory->name = $param['name'];

                $insertCategory->save();


                $data = array(
                    'estado' => 'correcto',
                    'codigo' => 200,
                    'menssaje' => "la categoria {$param['name']} ha sido creada correctamente"
                );
            }
        }else{
            $data = array(
                'estado' => 'error',
                'codigo' => 400,
                'menssaje' => "se han enviado datos vacios o erroneos"
            ); 
        }

        return response()->json($data, $data['codigo']);
    }
}

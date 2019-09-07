<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Categories;
use App\helpers\jwtAuth;

class categoriesController extends Controller{
   

    public function index(){
        
        $categories = Categories::all();

        if($categories){
            $data = array(
                'estado'    => 'correcto',
                'codigo'    => 200,
                'mensaje'   => $categories
            );
        }else{
            $data = array(
                'estado'    => 'error',
                'codigo'    => 404,
                'mensaje'   => 'no hay categorias disponibles'
            );
        }
        

        return response()->json($data, $data['codigo']);

    }


    public function show($idCategorie){
        
        $categorie = Categories::find($idCategorie);

        if(is_object($categorie)){
            $data = array(
                'estado' => 'correcto',
                'codigo' => 200,
                'mensaje' => $categorie
            );   
        }else{
            $data = array(
                'estado' => 'error',
                'codigo' => 404,
                'mensaje' => 'la categoria no existe'
            ); 
        }

        return response()->json($data, $data['codigo']);

    }


}

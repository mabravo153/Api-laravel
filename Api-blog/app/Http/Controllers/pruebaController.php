<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categories;
use App\Posts;

class pruebaController extends Controller{

    public function pruebaoms(){
       
        //creamos una varialbe con lo que nos traemos de post por ejemplo, esto nos retornara un array 
        //debemos especificar la variable que sea diferente a alguna ya usada por el el lenguaje, luego llamamos la clase. 
        //al usar los dos puntos como en este caso, podemos acceder a sus metodos. esto nos devuelve un array como antes dije 
        $posts = Posts::all(); 
        
        foreach($posts as $post){
            echo "<p>{$post->title}</p>";
            echo "<p>{$post->user->name} {$post->categories->name}</p>";
        }


        

        die(); 
    }

}

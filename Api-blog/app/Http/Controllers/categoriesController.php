<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class categoriesController extends Controller
{
    public function pruebaCategorie(Request $request){
        return "prueba desde controler categories"; 
    }
}

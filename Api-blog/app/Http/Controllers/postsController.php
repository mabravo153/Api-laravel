<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class postsController extends Controller
{
    public function pruebaPost(Request $request){
        return "prueba desde controler posts"; 
    }
}

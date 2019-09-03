<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories'; // de esta manera le indicamos que usara una tabla en concreto 


    //metodo que usaremos para devolver todos los post de la categoria en concreto 
    public function posts(){
        return $this->hasMany('App\Posts', 'fk_idcategories', 'id'); //de esta manera especificamos la relacion con el modelo post
    }



}

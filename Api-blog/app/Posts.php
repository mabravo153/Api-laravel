<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model{
    protected $table = 'posts';


//relacion de muchos a uno 
//esta funcion nos relaciona de muchos a uno, y hay que pasarle como parametro, el fk
public function user(){
    return $this->belongsTo('App\User', 'fk_idusers');
}

public function categories(){
    return $this->belongsTo('App\Categories','fk_idcategories');
}



}

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Middleware\ApiAuthMiddleware;



//RUTAS DE PRUEBA 
Route::get('/', function () {
    return view('welcome');
});


//RUTAS DEL API 
    
    //rutas controlador usuario 
    Route::post('/api/register', 'userController@registro');//la ruta podemos ponerle como queramos. pero el metodo del controlador debe ser como esta escrito
    Route::post('/api/login', 'userController@login');
    Route::post('/api/upload', 'userController@uploadFoto')->middleware(ApiAuthMiddleware::class);//verificacion del token antes de ejecutar 
    Route::put('/api/update', 'userController@update');
    Route::get('/api/user/picture/{imagen}', 'userController@getImage');
    Route::get('/api/user/detail/{idUser}', 'userController@infoUser');
   
    //rutas controlador categorias
    Route::resource('api/categories', 'categoriesController');

    //rutas controlador posts 
    Route::resource('api/posts', 'postsController'); 
    Route::post('api/posts/upload', 'postsController@uploadFoto');
    Route::get('api/posts/image/{image}', 'postsController@getImage');
    Route::get('api/posts/categories/{id}', 'postsController@returnByCategory');
    Route::get('api/posts/id/{id}', 'postsController@returnById');
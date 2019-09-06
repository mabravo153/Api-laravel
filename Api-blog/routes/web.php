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


Route::get('/test', 'pruebaController@pruebaoms');


//RUTAS DEL API 
    //rutas prueba 
    Route::get('/usuario/prueba', 'userController@pruebaUser');
    Route::get('/categorias/prueba', 'categoriesController@pruebaCategorie');
    Route::get('/post/prueba', 'postsController@pruebaPost');

    //rutas controlador usuario 
    Route::post('/api/register', 'userController@registro');//la ruta podemos ponerle como queramos. pero el metodo del controlador debe ser como esta escrito
    Route::post('/api/login', 'userController@login');
    Route::put('/api/auth', 'userController@update');
    Route::post('/api/upload', 'userController@uploadFoto')->middleware(ApiAuthMiddleware::class);
   
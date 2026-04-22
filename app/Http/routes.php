<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'Controller@index');

Route::group(['middleware' => 'guest'], function () {

    Route::get('/login_merchant', function () {return view('login_merchant');});
    Route::post('/login_user', 'Controller@login_user');

    Route::get('/login_customer', function () {return view('login_customer');});
    Route::post('/login_user', 'Controller@login_user');

    Route::get('/register', function () {return view('register');});
    Route::post('/register_user', 'Controller@register_user');

    Route::get('/register_customer', function () {return view('register_customer');});
    Route::post('/register_user_customer', 'Controller@register_user_customer');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('/update_user', 'Controller@update_user');
    Route::get('/logout_user', 'Controller@logout_user');

    Route::get('/dashboard', 'Controller@dashboard');

    Route::post('/input_menu', 'Controller@input_menu');
    Route::get('/menu/delete/{id}', 'Controller@delete');
    
    Route::post('/tambah_pesanan', 'Controller@tambah_pesanan');
    Route::post('/cart/delete/{id}', 'Controller@delete_cart');
    
    Route::post('/print_invoice', 'Controller@print_invoice');
    Route::get('/print/invoice/{id}', 'Controller@print_invoice_merchant');
    

});

<?php

use App\Jobs\SendEmailJob;
use App\Mail\NewPostFromFavoriteUserMail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Testing send email
Route::get('/testing', function(){
    $data = [
        'title' => 'The Title One',
        'body' => 'The Body One',
        'name' => 'Julio',
        'email' => 'juliornellas@gmail.com',
    ];

    SendEmailJob::dispatch($data);
    // Mail::send(new NewPostFromFavoriteUserMail($data));
});
<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/test', function () {

    $user = User::findOrFail(1);

    return $user->destinations;
});

Route::get('/', function () {
    return view('welcome');
});

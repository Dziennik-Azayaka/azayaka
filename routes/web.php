<?php

use App\Http\Controllers\ActivationCodeController;
use App\Http\Controllers\SessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::get('/authentication{any?}', function () {
    return view('authentication');
})->where('any', '.*');

Route::get('/myaccount{any?}', function () {
    return view('myaccount');
})->where('any', '.*');
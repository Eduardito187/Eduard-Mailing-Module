<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Eduard\Mailing\Http\Controllers\Api\Mailing\Mail;
use Eduard\Account\Http\Middleware\CustomValidateToken;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware([CustomValidateToken::class])->group(function () {
    Route::controller(Mail::class)->group(function() {
        Route::post('mailing/create-masive', 'createMailMasive');
        Route::post('mailing/all-mail-sender', 'getAllMailSender');
        Route::post('mailing/get-mail', 'getMailQuery');
        Route::post('mailing/all-mail-customer', 'getAllCustomerMailing');
    });
});
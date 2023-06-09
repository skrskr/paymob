<?php

use Illuminate\Support\Facades\Route;
use Skrskr\Paymob\Http\Controllers\WebhookController;

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

Route::post('paymob/webhook', [WebhookController::class, 'handleWebhook'])->name('paymob.webhook');

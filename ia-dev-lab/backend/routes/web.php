<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['ok' => true, 'service' => 'ia-dev-lab-api'];
});

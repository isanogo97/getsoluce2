<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('getsirarh:status', function () {
    $this->comment('GETSIRARH API routes are ready.');
})->purpose('Check GETSIRARH API status');

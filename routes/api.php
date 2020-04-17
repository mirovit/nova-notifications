<?php

use Illuminate\Support\Facades\Route;

 Route::get('/unread', \Mirovit\NovaNotifications\Http\Controllers\GetAllUnreadController::class);
 Route::patch('/{notification}', \Mirovit\NovaNotifications\Http\Controllers\MarkAsReadController::class);
 Route::patch('/', \Mirovit\NovaNotifications\Http\Controllers\MarkAllAsReadController::class);

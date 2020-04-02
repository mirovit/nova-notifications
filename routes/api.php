<?php

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

 Route::get('/unread', function (Request $request) {
     $notifications = $request->user()->unreadNotifications->mapWithKeys(function (DatabaseNotification $notification) {
         return [$notification->id => $notification];
     });

     return Response::json([
         'count' => $notifications->count(),
         'notifications' => $notifications,
         'user_id' => $request->user()->id,
     ]);
 });


 Route::patch('/{notification}', function (Request $request, $notification) {
     $markRead = $request
         ->user()
         ->unreadNotifications()
         ->find($notification)
         ->markAsRead();

     return Response::json([
         'notification' => $request
             ->user()
             ->notifications()
             ->find($notification),
     ]);
 });

 Route::patch('/', function (Request $request) {
     return Response::json([
         'status' => $request
             ->user()
             ->unreadNotifications
             ->markAsRead(),
     ]);
 });

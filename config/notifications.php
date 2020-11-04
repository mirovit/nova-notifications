<?php

return [
    // The Echo namespaced path to the User model
    'user_model'  => 'App.Models.User',
    // Either a controller with an __invoke() method or controller@method
    'controllers' => [
        'list_unread_notifications' => \Mirovit\NovaNotifications\Http\Controllers\GetAllUnreadController::class,
        'mark_as_read'              => \Mirovit\NovaNotifications\Http\Controllers\MarkAsReadController::class,
        'mark_all_as_read'          => \Mirovit\NovaNotifications\Http\Controllers\MarkAllAsReadController::class,
    ],
];

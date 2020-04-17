<?php


namespace Mirovit\NovaNotifications\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Response;

class GetAllUnreadController
{
	public function __invoke(Request $request)
	{
		$notifications = $request->user()
			->unreadNotifications
			->mapWithKeys(function(DatabaseNotification $notification) {
				return [$notification->id => $notification];
			});

		return Response::json([
			'count'         => $notifications->count(),
			'notifications' => $notifications,
			'user_id'       => $request->user()->id,
		]);
	}
}
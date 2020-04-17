<?php


namespace Mirovit\NovaNotifications\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MarkAllAsReadController
{
	public function __invoke(Request $request)
	{
		return Response::json([
			'status' => $request
				->user()
				->unreadNotifications
				->markAsRead(),
		]);
	}
}
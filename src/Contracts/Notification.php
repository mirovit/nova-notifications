<?php


namespace Mirovit\NovaNotifications\Contracts;


interface Notification
{
	public static function make(string $title = null, string $content = null);
}
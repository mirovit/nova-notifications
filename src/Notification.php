<?php

namespace Mirovit\NovaNotifications;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Mirovit\NovaNotifications\Contracts\Notification as NotificationContract;

class Notification implements NotificationContract, Arrayable
{
    const LEVELS = ['info', 'success', 'error'];

    protected $notification = [];

    public function __construct($title = null, $subtitle = null)
    {
        if (!empty($title)) {
            $this->title($title);
        }

        if (!empty($subtitle)) {
            $this->subtitle($subtitle);
        }

        $this
            ->showMarkAsRead()
            ->showCancel()
            ->createdAt(Carbon::now());
    }

    public static function make(string $title = null, string $subtitle = null): Notification
    {
        return new static($title, $subtitle);
    }

    public function title(string $value): Notification
    {
        $this->notification['title'] = $value;
        return $this;
    }

    public function subtitle(string $value): Notification
    {
        $this->notification['subtitle'] = $value;
        return $this;
    }

    public function link(string $value, bool $external = false): Notification
    {
        $this->notification['url'] = $value;
        $this->notification['external'] = $external;
        return $this;
    }

    public function route(string $name, string $resourceName, $resourceId = null): Notification
    {
        $this->notification['route'] = [
            'name' => $name,
            'params' => ['resourceName' => $resourceName]
        ];

        if (!empty($resourceId)) {
            $this->notification['route']['params']['resourceId'] = $resourceId;
        }

        return $this;
    }

    public function routeIndex(string $resourceName): Notification
    {
        return $this->route('index', $resourceName);
    }

    public function routeCreate(string $resourceName): Notification
    {
        return $this->route('create', $resourceName);
    }

    public function routeEdit(string $resourceName, $resourceId): Notification
    {
        return $this->route('edit', $resourceName, $resourceId);
    }

    public function routeDetail(string $resourceName, $resourceId): Notification
    {
        return $this->route('detail', $resourceName, $resourceId);
    }

    public function level(string $value): Notification
    {
        if (!in_array($value, Notification::LEVELS)) {
            $value = 'info';
        }

        $this->notification['level'] = $value;
        return $this;
    }

    public function info(string $value): Notification
    {
        return $this
            ->title($value)
            ->level('info');
    }

    public function success(string $value): Notification
    {
        return $this
            ->title($value)
            ->level('success');
    }

    public function error(string $value): Notification
    {
        return $this
            ->title($value)
            ->level('error');
    }

    public function createdAt(Carbon $value): Notification
    {
        $this->notification['created_at'] = $value->toAtomString();
        return $this;
    }

    public function icon(string $value): Notification
    {
        $this->notification['icon'] = $value;
        return $this;
    }

    public function showMarkAsRead(bool $value = true): Notification
    {
        $this->notification['show_mark_as_read'] = $value;
        return $this;
    }

    public function showCancel(bool $value = true): Notification
    {
        $this->notification['show_cancel'] = $value;
        return $this;
    }

    public function toArray()
    {
        return $this->notification;
    }
}
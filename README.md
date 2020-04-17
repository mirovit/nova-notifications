# Nova Notifications

[![Build Status](https://travis-ci.org/mirovit/nova-notifications.svg?branch=master)](https://travis-ci.org/mirovit/nova-notifications)

[![Maintainability](https://api.codeclimate.com/v1/badges/b8a180684b5d64b2beac/maintainability)](https://codeclimate.com/github/mirovit/nova-notifications/maintainability)

[![Test Coverage](https://api.codeclimate.com/v1/badges/b8a180684b5d64b2beac/test_coverage)](https://codeclimate.com/github/mirovit/nova-notifications/test_coverage)

## Prerequisites
- Laravel Nova app
- Laravel Broadcasting configured
- Laravel Echo
- Laravel Notifications

## Installation

Install via composer `composer require mirovit/nova-notifications`.

Laravel will auto-register the Service Provider. You'll need to register the tool with Laravel Nova.

```php
    public function tools()
    {
        return [
            // ...
            \Mirovit\NovaNotifications\NovaNotifications::make(),
        ];
    }
```

Then publish the configuration file - `php artisan vendor:publish` and make sure the path to your user model is correct. Note that this will be the Echo style namespace, so if your User model is located at App\User, you need to pass App.User as value.

Make sure you have the user channel authenticated in `routes/channels.php` or where you store this logic:
```php
Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});
```

The last step is to publish manually Nova's layout file if you haven't done so. 
`cp vendor/laravel/nova/resources/views/layout.blade.php resources/views/vendor/nova/layout.blade.php`

Then place the partial view that displays the bell icon in the nav bar, somewhere aroud the user partial from nova - `@include('nova-notifications::dropdown')`.

## Usage

Trigger a notification from Laravel. Sample notification:
```php
<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Created extends Notification
{
    use Queueable;

    private $user;


    /**
     * Create a new notification instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return \Mirovit\NovaNotifications\Notification::make()
            ->info('A new user was created.')
            ->subtitle('There is a new user in the system - ' . $this->user->name . '!')
            ->routeDetail('users', $this->user->id)
            ->toArray();

    }
}

```

## Available methods


```php
Notification::make($title = null, $subtitle = null)
    // Sets title
    ->title(string $value)
    // Sets subtitle
    ->subtitle(string $subtitle)
    // Link and route work similarly. Route has precedence over link, if you define both on an instance. You should generally use a one of them.
    ->link(string $url, bool $external = false)
    // Route to internal resource
    ->route(string $name, string $resourceName, $resourceId = null)
    // Helper methods for resource routing
    ->routeIndex(string $resourceName)
    ->routeCreate(string $resourceName)
    ->routeEdit(string $resourceName, $resourceId)
    ->routeDetail(string $resourceName, $resourceId)
    // Notification level - info, success or errro
    ->level(string $value)
    // Helpers to set title and level with one call
    ->info(string $value)
    ->success(string $value)
    ->error(string $value)
    // Set custom date for notification, defaults to current datetime
    ->createdAt(Carbon $value)
    // Add icon classes to be applied, ex: fas fa-info
    ->icon(string $value)
    ->toArray();
```

## Icons
In order to show the icons, you need to make sure they are imported in your project. You can use any icon font like [Font Awesome](https://fontawesome.com).

Example usage of FA:
In `layout.blade.php` add the CSS for FA.

Then just add the`->icon()` method on your notification and specify the classes for rendering the icon `fas fa-info`.

## Demo

### No notifications
![No notifications](https://raw.githubusercontent.com/mirovit/nova-notifications/master/images/no-notifications.png)

### No notifications opened
![No notifications open](https://raw.githubusercontent.com/mirovit/nova-notifications/master/images/no-notifications-open.png)

### Notifications count
![Notifications count](https://raw.githubusercontent.com/mirovit/nova-notifications/master/images/notification-buble.png)

### Notification success
![Notification success](https://raw.githubusercontent.com/mirovit/nova-notifications/master/images/notification-success.png)

### Notification info
![Notification info](https://raw.githubusercontent.com/mirovit/nova-notifications/master/images/notification-info.png)

### Notification error
![Notification error](https://raw.githubusercontent.com/mirovit/nova-notifications/master/images/notification-error.png)

### Notifications open
![Notifications open](https://raw.githubusercontent.com/mirovit/nova-notifications/master/images/notifications-open.png)

## ToDos

- [x] Add translations
- [ ] Add docs for customizing the Vue layout
- [x] Allow for external links in notifications
- [x] Add support for icons
- [ ] Actions customization

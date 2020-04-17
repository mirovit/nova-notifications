# Nova Notifications

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
        return [
            'title' => 'A new user was created. ,
            'subtitle' => 'There is a new user in the system - ' . $this->user->name . '!',
            'level' => 'info',
            'created_at' => now()->toAtomString(),
            'route' => ['name' => 'detail', 'params' => ['resourceName' => 'users', 'resourceId' => $this->user->id]],
//            'url' => '/resources/users/' . $this->user->id,
        ];
    }
}

```

Supported levels are `info`, `success` and `error`.
You can add a link to an internal resource by using eiter the route key, passing the parameters in the example above or use a url.

Only title and created_at are required for the notification to be displayed.

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
- [ ] Allow for external links in notifications
- [ ] Add support for icons
- [ ] Actions customization

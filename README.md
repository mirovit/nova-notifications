# Nova Notifications

[![Build Status](https://travis-ci.org/mirovit/nova-notifications.svg?branch=master)](https://travis-ci.org/mirovit/nova-notifications)
[![Maintainability](https://api.codeclimate.com/v1/badges/b8a180684b5d64b2beac/maintainability)](https://codeclimate.com/github/mirovit/nova-notifications/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b8a180684b5d64b2beac/test_coverage)](https://codeclimate.com/github/mirovit/nova-notifications/test_coverage)

## Quick Links
* [Prerequisites](https://github.com/mirovit/nova-notifications#prerequisites)
* [Installation](https://github.com/mirovit/nova-notifications#installation)
* [Usage](https://github.com/mirovit/nova-notifications#usage)
* [Available methods](https://github.com/mirovit/nova-notifications#available-methods)
* [Icons](https://github.com/mirovit/nova-notifications#icons)
* [Configuration](https://github.com/mirovit/nova-notifications#configuration)
* [Translation](https://github.com/mirovit/nova-notifications#translation)
* [Demo](https://github.com/mirovit/nova-notifications#demo)
* [ToDos](https://github.com/mirovit/nova-notifications#todos)

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

Make sure you have the user channel authenticated in `routes/channels.php` or where you store this logic:
```php
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});
```

Laravel Echo is not bundled with Nova by defult, so you will need to setup that for your front end. To do that follow these steps below:

- Install [Echo](https://laravel.com/docs/7.x/broadcasting#installing-laravel-echo)
- `npm install`
- create an admin.js file in resources/js
```js
import Echo from 'laravel-echo';

// Sample instance with Pusher
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});
```
- add to your webpack.mix.js
```js
mix.js('resources/js/admin.js', 'public/js');
```
- add in your Nova layout.blade.php
```html
// ...
<script src="{{ mix('app.js', 'vendor/nova') }}"></script>
<script src="{{ mix('js/admin.js') }}"></script>
```

Additionally, the package assumes that models are namespaced as App\Models, if that is not correct for your project, the authentication between the front & back end will not work and notifications will not show without refreshing the page. Go to the [Configuration](https://github.com/mirovit/nova-notifications#configuration) section to see how to fix this.

The last step is to publish manually Nova's layout file if you haven't done so. 
`cp vendor/laravel/nova/resources/views/layout.blade.php resources/views/vendor/nova/layout.blade.php`

Then place the partial view that displays the bell icon in the nav bar:

Find in `views/vendor/nova/layout.blade.php`:
```php
<dropdown class="ml-auto h-9 flex items-center dropdown-right">
    @include('nova::partials.user')
</dropdown>
```

Replace with:
```php
@include('nova-notifications::dropdown')

<dropdown class="ml-8 h-9 flex items-center dropdown-right">
    @include('nova::partials.user')
</dropdown>
```

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
    // Notification level - info, success or error
    
    ->level(string $value)
    // Helpers to set title and level with one call
    ->info(string $value)
    ->success(string $value)
    ->error(string $value)
    // Set custom date for notification, defaults to current datetime
    ->createdAt(Carbon $value)
    // Add icon classes to be applied, ex: fas fa-info
    ->icon(string $value)
    ->showMarkAsRead(bool $value = true)
    ->showCancel(bool $value = true)
    // URL to the sound that the notification should make
    ->sound('https://url-to-a-sound-file')
    // If `play_sound` is set to true in your config, every notification will play the default sound. You can disable the sound per notification here.
    ->playSound(bool $value = true)
    // Whether to show the toasted popup notification
    ->displayToasted(bool $value = true)
    // Alias to invoke the displayToasted() with false
    ->hideToasted()
    ->toArray();
```

## Icons
In order to show the icons, you need to make sure they are imported in your project. You can use any icon font like [Font Awesome](https://fontawesome.com).

Example usage of FA:
In `layout.blade.php` add the CSS for FA.

Then just add the`->icon()` method on your notification and specify the classes for rendering the icon `fas fa-info`.

## Configuration
There is an optional config file published by the package. If you use a different convention for model namespaces or you want to override the default controllers provided by the package, then you'll need to publish the configuration into your project.

Note that the default model namespace that the package assumes is App\Models, so if you're using another namespace, this will have to be adjusted for the authentication between the API and the front end.

`php artisan vendor:publish` and select the number corresponding to Mirovit\NovaNotifications\NovaNotificationsServiceProvider or publish all.

## Translation
The package has been translated into English, if you require additional translations, you can add them as documented in the Laravel Nova docs.

An item that has come up a few times is that the difference for humans is displayed only in English, regardless of the application locale. You need to set the moment.js locale in your application to the appropriate locale, this is not a responsibility of this package.

Locate your layout file - `resources/views/vendor/nova/layout.blade.php`:

Find:
```html
<!-- Build Nova Instance -->
<script>
    window.Nova = new CreateNova(config)
</script>
```

and replace with:
```html
<!-- Build Nova Instance -->
<script>
    window.Nova = new CreateNova(config)
    moment.locale('es-es') // this can come from a config setting, just an example of how to set it
</script>
```

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

<?php

namespace Mirovit\NovaNotifications;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Mirovit\NovaNotifications\Http\Middleware\Authorize;

class NovaNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nova-notifications');

        $this->publishes([
            __DIR__.'/../config/notifications.php' => config_path('nova-notifications.php'),
        ]);


		$this->publishes([
			__DIR__.'/../resources/lang/' => resource_path('lang/vendor/nova-notifications'),
		]);

		$this->registerTranslations();

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::provideToScript([
                'user_model_namespace' => config('nova-notifications.user_model'),
            ]);
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
                ->prefix('nova-vendor/nova-notifications')
                ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/notifications.php', 'nova-notifications'
        );
    }

	protected function registerTranslations()
	{
		$locale = app()->getLocale();

		Nova::translations(__DIR__.'/../resources/lang/' . $locale . '.json');
		Nova::translations(resource_path('lang/vendor/nova-notifications/' . $locale . '.json'));

		$this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'NovaNotifications');
		$this->loadJSONTranslationsFrom(__DIR__.'/../resources/lang');
		$this->loadJSONTranslationsFrom(resource_path('lang/vendor/nova-notifications'));
    }
}

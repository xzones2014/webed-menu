<?php namespace WebEd\Base\Menu\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use WebEd\Base\Menu\Facades\DashboardMenuFacade;
use WebEd\Base\Menu\Facades\MenuManagementFacade;
use WebEd\Base\Menu\Http\Middleware\BootstrapModuleMiddleware;

class ModuleProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /*Load views*/
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'webed-menus');
        /*Load translations*/
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'webed-menus');
        /*Load migrations*/
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes([
            __DIR__ . '/../../resources/views' => config('view.paths')[0] . '/vendor/webed-menus',
        ], 'views');
        $this->publishes([
            __DIR__ . '/../../resources/lang' => base_path('resources/lang/vendor/webed-menus'),
        ], 'lang');
        $this->publishes([
            __DIR__ . '/../../resources/assets' => resource_path('assets'),
        ], 'webed-assets');
        $this->publishes([
            __DIR__ . '/../../resources/public' => public_path(),
        ], 'webed-public-assets');

        app()->booted(function () {
            $this->app->register(BootstrapModuleServiceProvider::class);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Load helpers
        load_module_helpers(__DIR__);

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(RepositoryServiceProvider::class);

        //Register related facades
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('MenuManagement', MenuManagementFacade::class);
        $loader->alias('DashboardMenu', DashboardMenuFacade::class);

        /**
         * @var Router $router
         */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', BootstrapModuleMiddleware::class);
    }
}

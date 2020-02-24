<?php namespace WebEd\Base\Menu\Providers;

use Illuminate\Support\ServiceProvider;

class InstallModuleServiceProvider extends ServiceProvider
{
    protected $module = 'webed-menus';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        app()->booted(function () {
            $this->booted();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }

    protected function booted()
    {
        acl_permission()
            ->registerPermission('View menus', 'view-menus', $this->module)
            ->registerPermission('Delete menus', 'delete-menus', $this->module)
            ->registerPermission('Create menus', 'create-menus', $this->module)
            ->registerPermission('Edit menus', 'edit-menus', $this->module);
    }
}

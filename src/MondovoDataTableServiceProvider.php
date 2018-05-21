<?php

namespace Mondovo\DataTable;

use Illuminate\Support\ServiceProvider;
use Mondovo\DataTable\Contracts\DataTableAdapterInterface;
use Mondovo\DataTable\Contracts\DataTableFilterInterface;
use Mondovo\DataTable\Contracts\DataTableJsInterface;
use Mondovo\DataTable\Contracts\DrawTableInterface;
use Mondovo\DataTable\Contracts\KeywordGroupPluginServiceInterface;
use Mondovo\DataTable\Contracts\KeywordHelperServiceInterface;

class MondovoDataTableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
	    $this->loadViewsFrom(__DIR__.'/components', 'datatable');
	    $this->publishes([
		    __DIR__.'/components' => base_path('resources/views/mondovo/datatable'),
	    ]);

	    $this->publishes([
		    __DIR__.'/config/mondovo-datatable.php' => \config_path('mondovo-datatable.php'),
	    ], 'config');

	    $this->publishes([
		    __DIR__ . '/config/mondovo-helpers.php' => config_path('mondovo-helpers.php'),
	    ], 'config');

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
	    $this->mergeConfigFrom(__DIR__.'/config/mondovo-datatable.php', 'mondovo-datatable');

	    foreach (config('helpers.package_helpers', []) as $activeHelper) {
		    $file = __DIR__ . '/helpers/' . $activeHelper . '.php';
		    if (file_exists($file)) {
			    require_once($file);
		    }
	    }

	    $this->app->bind(DrawTableInterface::class, 'Mondovo\DataTable\DrawTable');
	    $this->app->bind(DataTableFilterInterface::class, 'Mondovo\DataTable\DataTableFilter' );
	    $this->app->bind(DataTableAdapterInterface::class, 'Mondovo\DataTable\DataTableAdapter' );
	    $this->app->bind(DataTableJsInterface::class, 'Mondovo\DataTable\DataTableJs');
	    $this->app->bind(KeywordGroupPluginServiceInterface::class, 'Mondovo\DataTable\KeywordGroupPluginService');
	    $this->app->bind(KeywordHelperServiceInterface::class, 'Mondovo\DataTable\KeywordHelperService');
    }
}

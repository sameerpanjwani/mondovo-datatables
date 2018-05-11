<?php

namespace Mondovo\Datatable;

use Illuminate\Support\ServiceProvider;

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
    }
}

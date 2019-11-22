<?php
return [
	/*
	|--------------------------------------------------------------------------
	| Package Helpers
	|--------------------------------------------------------------------------
	|
	| This package comes with some pre-built helpers. Activate the ones
	| you wish to use by selecting them here. Valid options include:
	|
	| translation-helpers
	|
	*/
	'package_helpers' => [
		'translation-helpers',
		'array-helpers',
		'custom-str-helpers',
	],

	/*
	|--------------------------------------------------------------------------
	| Directory
	|--------------------------------------------------------------------------
	|
	| By default this package will look in the application's 'helpers'
	| directory. However, you may choose to override the directory.
	|
	*/
	'directory' => 'helpers',
	/*
	|--------------------------------------------------------------------------
	| Default Timezone
	|--------------------------------------------------------------------------
	|
	| The 'datetime' helper provides an easy way to format all of your Datetime
	| instances according to a runtime timezone. As a fallback, you may
	| enter your applications's default timezone here.
	|
	*/
	'default_timezone' => 'america/chicago',
	/*
	|--------------------------------------------------------------------------
	| Default Image
	|--------------------------------------------------------------------------
	|
	| The 'image' helper provides a simple default image when no image is
	| passed to it. Here you can define which image you would like to
	| use as the fallback image. The path is relative to the
	| resource root, or 'public' directory.
	|
	*/
	'default_image' => 'img/mondovo-logo.png',
];
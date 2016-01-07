<?php
	use Cake\Routing\Router;

	Router::plugin('SimilarContent', ['path' => '/SimilarContent'], function ($routes) {
		$routes->fallbacks('DashedRoute');
	});

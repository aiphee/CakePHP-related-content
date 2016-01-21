<?php
	use Cake\Routing\Router;

	Router::plugin('RelatedContent', ['path' => '/RelatedContent'], function ($routes) {
		$routes->fallbacks('DashedRoute');
	});

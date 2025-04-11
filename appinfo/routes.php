<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Machbarkeit\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
	'resources' => [
		'note' => ['url' => '/notes'],
		'note_api' => ['url' => '/api/0.1/notes'],
		'machbarkeit' => ['url' => '/machbarkeit']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		[
			'name' => 'note_api#preflighted_cors',
			'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS',
			'requirements' => ['path' => '.+']
		],
		['name' => 'machbarkeit#getMetadata', 'url' => '/machbarkeit/metadata', 'verb' => 'GET'],
		['name' => 'machbarkeit#getOntology', 'url' => '/machbarkeit/ontology', 'verb' => 'GET'],
		// ['name' => 'machbarkeit#getUiProfile', 'url' => '/machbarkeit/ui_profile', 'verb' => 'GET'],

		// TODO
		// get module names
		['name' => 'machbarkeit#getModules', 'url' => '/machbarkeit/modules', 'verb' => 'GET'],
		// ontology tree
		['name' => 'machbarkeit#getOntologyTree', 'url' => '/machbarkeit/ontology/{textSearch}/{module_id}', 'verb' => 'GET'],
		// find ontology from termCodes
		['name' => 'machbarkeit#findOntology', 'url' => '/machbarkeit/findOntologyFromUploadFile/{code}', 'verb' => 'GET'],
		// get all filters
		['name' => 'machbarkeit#getFilters', 'url' => '/machbarkeit/filters/{filter_options_ids}', 'verb' => 'GET'],
		// get filters (for concepts)
		['name' => 'machbarkeit#getRequest', 'url' => '/machbarkeit/get_request', 'verb' => 'POST'],

		['name' => 'machbarkeit#selectSql', 'url' => '/machbarkeit/select_sql', 'verb' => 'GET'],
	]
];

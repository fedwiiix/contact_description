<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\People\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
       ['name' => 'contact#search', 'url' => '/search/{search}', 'verb' => 'GET'],
       ['name' => 'contact#list', 'url' => '/list/{id}', 'verb' => 'GET'],

       ['name' => 'contact#index', 'url' => '/contact', 'verb' => 'GET'],
       ['name' => 'contact#show', 'url' => '/contact/{id}', 'verb' => 'GET'],
       ['name' => 'contact#create', 'url' => '/contact', 'verb' => 'POST'],
       ['name' => 'contact#update', 'url' => '/contact/{id}', 'verb' => 'PUT'],
       ['name' => 'contact#destroy', 'url' => '/contact/{id}', 'verb' => 'DELETE'],
       
       ['name' => 'contact#export', 'url' => '/export', 'verb' => 'GET'],

       ['name' => 'tag#index', 'url' => '/tag', 'verb' => 'GET'],
       ['name' => 'tag#create', 'url' => '/tag', 'verb' => 'POST'],
       ['name' => 'tag#favorite', 'url' => '/tagfavorite/{id}', 'verb' => 'PUT'],
       ['name' => 'tag#update', 'url' => '/tag/{id}', 'verb' => 'PUT'],
       ['name' => 'tag#destroy', 'url' => '/tag/{id}', 'verb' => 'DELETE'],

       ['name' => 'tagassign#show', 'url' => '/tagassign/{id}', 'verb' => 'GET'],
       ['name' => 'tagassign#create', 'url' => '/tagassign', 'verb' => 'POST'],
       ['name' => 'tagassign#destroy', 'url' => '/tagassign/{id}', 'verb' => 'DELETE'],

    ]
];
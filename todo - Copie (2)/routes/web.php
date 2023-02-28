<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Création du groupr api : http://localhost:8000/api/
$router->group(['prefix' => 'api'], function () use ($router) {

    //inscription
    $router->post('register', 'AuthController@register');
    // api/login
    $router->post('login', 'AuthController@login');
    // api/logout
    $router->post('logout', 'AuthController@logout');
    // api/refresh
    $router->post('refresh', 'AuthController@refresh');
    // api/me
    $router->post('me', 'AuthController@me');

    $router->group(['prefix' => 'taches'], function () use ($router) {

        // Toutes les tâches
        $router->get('', ['uses' => 'TacheController@showAllTasks']);

        // Détail d'une tâche, doit être propriétaire
        $router->get('/{id}', ['middleware' => 'mustBeOwnerOfTache', 'uses' => 'TacheController@showOneTask']);

        // Ajout d'une tâche
        $router->post('', ['uses' => 'TacheController@create']);

        // Suppression d'une tâche, doit être propriétaire
        $router->delete('/{id}', ['middleware' => 'mustBeOwnerOfTache', 'uses' => 'TacheController@delete']);

        // Modification d'une tâche, doit être propriétaire
        $router->put('/{id}', ['middleware' => 'mustBeOwnerOfTache', 'uses' => 'TacheController@update']);

        // Fermeture ou Ouverture d'une tâche, doit être proprétaire
        $router->put('/{id}/complet', ['middleware' => 'mustBeOwnerOfTache', 'uses' => 'TacheController@completed']);

    });
});



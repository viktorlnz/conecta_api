<?php

namespace app\routes;

use app\controllers\exercicio\Exercicio;
use Slim\Routing\RouteCollectorProxy;

class ExercicioRoutes{
    public function __invoke(RouteCollectorProxy $group)
    {
        $group->group('/exercicio', function(RouteCollectorProxy $group){
            $group->post('', Exercicio::class . ':create');
            $group->get('', Exercicio::class . ':list');
            $group->options('', Exercicio::class . ':options');
        });
    }
}
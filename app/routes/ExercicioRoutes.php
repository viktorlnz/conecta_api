<?php

namespace app\routes;

use app\controllers\exercicio\Exercicio;
use app\controllers\instituicao\Materia;
use Slim\Routing\RouteCollectorProxy;

class ExercicioRoutes{
    public function __invoke(RouteCollectorProxy $group)
    {
        $group->group('/exercicio', function(RouteCollectorProxy $group){
            $group->post('', Exercicio::class . ':create');
            $group->get('', Exercicio::class . ':list');
            $group->options('', Exercicio::class . ':options');

            $group->get('/materia/{id}/professor/{idProfessor}', Exercicio::class . ':getExerciciosMateria');
            $group->options('/materia/{id}/professor/{idProfessor}', Exercicio::class . ':options');
        });

        $group->get('/turma/materia/{id}', Materia::class . ':listTurmaMaterias');
        $group->options('/turma/materia/{id}', Materia::class . ':options');
    }
}
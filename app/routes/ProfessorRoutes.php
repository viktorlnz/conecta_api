<?php

namespace app\routes;

use app\controllers\instituicao\Aluno;
use app\controllers\instituicao\Materia;
use app\controllers\instituicao\Turma;
use Slim\Routing\RouteCollectorProxy;

class ProfessorRoutes{
    public function __invoke(RouteCollectorProxy $group){

        $group->group('/list/professor', function(RouteCollectorProxy $group){
            $group->get('/aluno/{id}', Aluno::class . ':listProfessorAlunos');
            $group->get('/materia/{id}', Materia::class . ':listProfessorMaterias');
            $group->get('/turma/{id}', Turma::class . ':listProfessorTurmas');
        });
    }
}
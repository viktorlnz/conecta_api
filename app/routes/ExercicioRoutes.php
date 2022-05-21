<?php

namespace app\routes;

use app\controllers\exercicio\Exercicio;
use app\controllers\exercicio\Tarefa;
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

        $group->group('/tarefa', function(RouteCollectorProxy $group){
            $group->post('', Tarefa::class . ':create');
            $group->options('', Tarefa::class . ':options');

            $group->post('/submissao', Tarefa::class . ':submissao');
            $group->options('/submissao', Tarefa::class . ':options');

            $group->get('/get/{id}', Tarefa::class . ':get');
            $group->options('/get/{id}', Tarefa::class . ':options');

            $group->get('/aluno/{id}/get', Tarefa::class . ':listAlunoTarefas');
            $group->options('/aluno/{id}/get', Tarefa::class . ':options');
            
            $group->get('/aluno/{id}/get_atuais', Tarefa::class . ':listAlunoTarefasAtuais');
            $group->options('/aluno/{id}/get_atuais', Tarefa::class . ':options');

            $group->get('/listar/concluidas/{idProfessor}', Tarefa::class . ':listTarefasConcluidas');
            $group->options('/listar/concluidas/{idProfessor}', Tarefa::class . ':options');

        });

        $group->get('/turma/materia/{id}', Materia::class . ':listTurmaMaterias');
        $group->options('/turma/materia/{id}', Materia::class . ':options');
    }
}
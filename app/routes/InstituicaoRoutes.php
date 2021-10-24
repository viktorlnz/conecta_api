<?php

namespace app\routes;

use app\controllers\instituicao\Aluno;
use app\controllers\instituicao\Instituicao;
use app\controllers\instituicao\Materia;
use app\controllers\instituicao\Professor;
use app\controllers\instituicao\Turma;
use app\controllers\instituicao\Usuario;
use Slim\Routing\RouteCollectorProxy;

class InstituicaoRoutes{
    public function __invoke(RouteCollectorProxy $group)
    {
        $group->group('/instituicao', function(RouteCollectorProxy $group){
            $group->post('', Instituicao::class . ':create');
            $group->get('', Instituicao::class . ':list');
            $group->options('', Instituicao::class . ':options');
        });

        $group->group('/professor', function(RouteCollectorProxy $group){
            $group->post('', Professor::class . ':create');
            $group->get('', Professor::class . ':list');
            $group->options('', Professor::class . ':options');
        });

        $group->group('/aluno', function(RouteCollectorProxy $group){
            $group->post('', Aluno::class . ':create');
            $group->get('', Aluno::class . ':list');
        });

        $group->group('/materia', function(RouteCollectorProxy $group){
            $group->post('', Materia::class . ':create');
            $group->get('', Materia::class . ':list');
        });

        $group->group('/turma', function(RouteCollectorProxy $group){
            $group->post('', Turma::class . ':create');
            $group->get('', Turma::class . ':list');
        });

        //ROTAS DE AUTENTICAÇÃO
        $group->post('/login', Usuario::class . ':login');
        $group->options('/login', Usuario::class . ':options');
        $group->post('/logoff', Usuario::class . ':logoff');
        $group->get('/check_login', Usuario::class . ':checkLogin');
    }
}
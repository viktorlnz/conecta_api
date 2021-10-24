<?php

namespace app\controllers\exercicio;

use app\controllers\Controller;

use app\models\exercicio\Tarefa as Model;
use app\models\instituicao\Materia;
use app\models\instituicao\Professor;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Tarefa extends Controller{

    public function create(Req $req, Res $res){
        $args = $req->getParsedBody();

        $tarefa = new Model(
            0,
            $args['titulo'],
            $args['desc'],
            $args['pontos'],
            $args['dtComeco'],
            $args['dtFim']
        );

        $idMateriaTurma = $args['idMateriaTurma'];

        $id = $tarefa->create($idMateriaTurma);

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res){
        $tarefas = Model::list();

        $res->getBody()->write( json_encode($tarefas) );
        return $res->withHeader('Content-type', 'application/json');
    }
}
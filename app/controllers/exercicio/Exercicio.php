<?php

namespace app\controllers\exercicio;

use app\controllers\Controller;

use app\models\exercicio\Exercicio as Model;
use app\models\instituicao\Materia;
use app\models\instituicao\Professor;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Exercicio extends Controller{

    public function create(Req $req, Res $res){
        $args = $req->getParsedBody();

        $exercicio = new Model(
            0,
            $args['desc'],
            $args['categoria'],
            new Professor($args['idProfessor']),
            new Materia($args['idMateria'])
        );

        $id = $exercicio->create();

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res){
        $exercicios = Model::list();

        $res->getBody()->write( json_encode($exercicios) );
        return $res->withHeader('Content-type', 'application/json');
    }
}
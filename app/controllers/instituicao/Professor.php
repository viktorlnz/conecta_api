<?php

namespace app\controllers\instituicao;

use app\controllers\Controller;
use app\models\instituicao\Professor as Model;
use Psr\Http\Message\RequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Professor extends Controller{

    public function create(Req $req, Res $res){
        $args = $this->getArgs($req);

        $professor = new Model(
            0,
            $args['nome'],
            $args['identificacao'],
            0,
            $args['usuario'],
            $args['senha'],
            $args['email']
        );

        $id = $professor->create($args['idInstituicao']);

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res){
        $professores = Model::list();

        $res->getBody()->write( json_encode($professores) );
        return $res->withHeader('Content-type', 'application/json');
    }
}
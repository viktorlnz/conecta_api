<?php

namespace app\controllers\instituicao;

use app\controllers\Controller;
use app\models\instituicao\Aluno as Model;
use Psr\Http\Message\RequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Aluno extends Controller{

    public function create(Req $req, Res $res){
        $args = $this->getArgs($req);

        $aluno = new Model(
            0,
            $args['nome'],
            $args['identidade'],
            $args['dataNasc'],
            0,
            $args['usuario'],
            $args['senha'],
            $args['email']
        );

        $id = $aluno->create($args['idInstituicao']);

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res){
        $alunos = Model::list();

        $res->getBody()->write( json_encode($alunos) );
        return $res->withHeader('Content-type', 'application/json');
    }
}
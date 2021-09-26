<?php

namespace app\controllers\instituicao;

use app\controllers\Controller;
use app\models\instituicao\Instituicao as Model;
use Psr\Http\Message\RequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Instituicao extends Controller{

    public function create(Req $req, Res $res){
        $args = $this->getArgs($req);

        $instituicao = new Model(
            0,
            $args['cnpj'],
            $args['razaoSocial'],
            $args['nomeFantasia'],
            true,
            [],
            [],
            [],
            [],
            null,
            0,
            $args['usuario'],
            $args['senha'],
            $args['email']
        );

        $id = $instituicao->create();

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res){
        $instituicoes = Model::list();

        $res->getBody()->write( json_encode($instituicoes) );
        return $res->withHeader('Content-type', 'application/json');
    }
}
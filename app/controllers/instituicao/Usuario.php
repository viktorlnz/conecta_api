<?php

namespace app\controllers\instituicao;

use app\controllers\Controller;
use app\models\instituicao\Usuario as Model;
use Psr\Http\Message\RequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Usuario extends Controller{

    public function login(Req $req, Res $res){
        $args = $this->getArgs($req);

        $usuario = new Model(
            0,
            $args['usuario'],
            '',
            $args['senha']
        );

        $logged = $usuario->login();

        $res->getBody()->write( json_encode( $logged ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function logoff(Req $req, Res $res){

        $usuario = new Model();

        $usuario->logoff();

        $res->getBody()->write( json_encode( true ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function checkLogin(Req $req, Res $res){
        $usuario = new Model();

        $res->getBody()->write( json_encode( $usuario->checkLogin() ));

        return $res->withHeader('Content-type', 'application/json');
    }

}
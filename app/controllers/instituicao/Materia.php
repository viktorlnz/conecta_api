<?php

namespace app\controllers\instituicao;

use app\controllers\Controller;
use app\models\instituicao\Materia as Model;
use Psr\Http\Message\RequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Materia extends Controller{

    public function create(Req $req, Res $res){
        $args = $this->getArgs($req);

        $materia = new Model(
            0,
            $args['nome']
        );

        $id = $materia->create($args['idInstituicao']);

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res, array $params){
        $materias = Model::list($params['id']);

        $res->getBody()->write( json_encode($materias) );
        return $res->withHeader('Content-type', 'application/json');
    }
}
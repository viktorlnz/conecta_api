<?php

namespace app\controllers\exercicio;

use app\controllers\Controller;
use app\models\exercicio\TarefaSubmissao as ExercicioTarefaSubmissao;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class TarefaSubmissao extends Controller{

    public function get(Req $req, Res $res, array $args){
        $tarefa = new ExercicioTarefaSubmissao(null, null, $args['idSubmissao']);

        $tarefa = $tarefa->get();

        $res->getBody()->write(json_encode($tarefa));
        return $res->withHeader('Content-type', 'application/json');
    }

}
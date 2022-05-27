<?php

namespace app\controllers\exercicio;

use app\controllers\Controller;
use app\models\exercicio\ExercicioSubmissao;
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

    public function corrigir(Req $req, Res $res){
        $args = $req->getParsedBody();

        $tarefa = new ExercicioTarefaSubmissao(
            null,
            null,
            $args['id'],
            '',
            '',
            0,
            null,
            null,
            null,
            null,
            null
        );

        foreach ($args['exercicios'] as $e) {
            $exercicio = new ExercicioSubmissao($e['correcao'], '', $e['id']);

            array_push($tarefa->exercicios, $exercicio);
        }


        $tarefa->corrigir();

        $res->getBody()->write(json_encode($tarefa));
        return $res->withHeader('Content-type', 'application/json');
    }
}
<?php

namespace app\controllers\exercicio;

use app\controllers\Controller;
use app\models\exercicio\Exercicio;
use app\models\exercicio\ExercicioSubmissao;
use app\models\exercicio\ExercicioTarefa;
use app\models\exercicio\Tarefa as Model;
use app\models\exercicio\TarefaSubmissao;
use app\models\instituicao\Aluno;
use app\models\instituicao\Materia;
use app\models\instituicao\MateriaTurma;
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
            $args['dtFim'],
            new MateriaTurma($args['idMateriaTurma']),
            new Professor($args['idProfessor'])
        );

        $tarefa->professor->id = $tarefa
            ->professor
            ->getProfessorMateriaId($tarefa->materiaTurma->id);

        $exercicios = [];

        foreach ($args['exercicios'] as $key => $exercicio) {
            $e = new ExercicioTarefa(
                $exercicio['numerador'], $exercicio['pontos'], 
                $exercicio['id'], '', '', ''
            );

            array_push($exercicios, $e);
        }

        $tarefa->exercicios = $exercicios;

        $idMateriaTurma = $args['idMateriaTurma'];

        $id = $tarefa->create($idMateriaTurma);

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function submissao(Req $req, Res $res){
        $args = $req->getParsedBody();

        $tarefa = new TarefaSubmissao(
            $args['dtInicio'], $args['dtSubmissao'], $args['id'],
            '', '', 0, null, null, null, null,
            new Aluno($args['idAluno'])
        );

        foreach($args['exercicios'] as $e){
            $e = new ExercicioSubmissao(null, $e['resposta'], $e['id']);

            array_push($tarefa->exercicios, $e);
        }

        $tarefa->submissao();

        $res->getBody()->write( json_encode( true ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function get(Req $req, Res $res, array $args){
        $tarefa = new Model($args['id']);

        $tarefa = $tarefa->get();

        $res->getBody()->write(json_encode($tarefa));
        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res){
        $tarefas = Model::list();

        $res->getBody()->write( json_encode($tarefas) );
        return $res->withHeader('Content-type', 'application/json');
    }

    public function listAlunoTarefas(Req $req, Res $res, array $args){
        $tarefas = Model::listAlunoTarefas($args['id']);

        $res->getBody()->write(json_encode($tarefas));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function listTarefasConcluidas(Req $req, Res $res, array $args){
        $idProfessor = $args['idProfessor'];

        $tarefas = Model::listTarefasConcluidas($idProfessor);

        $res->getBody()->write(json_encode($tarefas));
        return $res->withHeader('Content-type', 'application/json');
    }

    public function listAlunoTarefasAtuais(Req $req, Res $res, array $args){
        $tarefas = Model::listAlunoTarefasAtuais($args['id']);

        $res->getBody()->write(json_encode($tarefas));

        return $res->withHeader('Content-type', 'application/json');
    }
}
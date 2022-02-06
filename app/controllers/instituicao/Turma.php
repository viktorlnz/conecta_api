<?php

namespace app\controllers\instituicao;

use app\controllers\Controller;
use app\models\instituicao\Aluno;
use app\models\instituicao\Turma as Model;
use app\models\instituicao\Materia;
use app\models\instituicao\MateriaTurma;
use app\models\instituicao\Professor;
use Psr\Http\Message\RequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Turma extends Controller{

    public function create(Req $req, Res $res){
        $args = $this->getArgs($req);

        $materiasTurma = [];

        foreach ($args->materiasTurma as $materia) {
            $professores = [];

            foreach ($materia->professores as $professor) {
                $p = new Professor($professor['id']);
                
                array_push($professores, $p);
            }

            $mt = new MateriaTurma(
                0,
                new Materia($materia['id']),
                $professores
            );

            array_push($materiasTurma, $mt);
        }

        $alunos = [];

        foreach ($args->alunos as $aluno) {
            $a = new Aluno(
                $aluno['id']
            );

            array_push($alunos, $a);
        }

        $turma = new Model(
            0,
            $args['nome'],
            $args['periodo'],
            $args['grau'],
            $args['dataInicio'],
            $args['duracaoMes'],
            $materiasTurma,
            $alunos
        );

        $id = $turma->create($args['idInstituicao']);

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res, array $params){
        $turmas = Model::list( $params['id'] );

        $res->getBody()->write( json_encode($turmas) );
        return $res->withHeader('Content-type', 'application/json');
    }
}
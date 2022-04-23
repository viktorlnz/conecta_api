<?php

namespace app\controllers\exercicio;

use app\controllers\Controller;

use app\models\exercicio\Exercicio as Model;
use app\models\exercicio\ExercicioAlternativa;
use app\models\instituicao\Materia;
use app\models\instituicao\Professor;
use Psr\Http\Message\ServerRequestInterface as Req;
use Psr\Http\Message\ResponseInterface as Res;

class Exercicio extends Controller{

    public function create(Req $req, Res $res){
        $args = $req->getParsedBody();

        $exercicio = new Model(
            0,
            $args['titulo'],
            $args['desc'],
            $args['categoria'],
            $args['respostaCerta'] ?? '',
            new Professor($args['idProfessor']),
            new Materia($args['idMateria'])
        );

        if($exercicio->categoria === 'ALTERNATIVA'){
            foreach ($args['exercicioAlternativas'] as $alternativa) {
                $alternativa = new ExercicioAlternativa(0, $alternativa['resposta'], $alternativa['numerador']);

                array_push($exercicio->exerciciosAlternativa, $alternativa);
            }
        }

        $id = $exercicio->create();

        $res->getBody()->write( json_encode( $id ));

        return $res->withHeader('Content-type', 'application/json');
    }

    public function list(Req $req, Res $res){
        $exercicios = Model::list();

        $res->getBody()->write( json_encode($exercicios) );
        return $res->withHeader('Content-type', 'application/json');
    }

    public function getExerciciosMateria(Req $req, Res $res, array $params){
        $exercicios = Model::getExerciciosMateria($params['id'], $params['idProfessor']);

        $res->getBody()->write( json_encode($exercicios) );
        return $res->withHeader('Content-type', 'application/json');
    }
}
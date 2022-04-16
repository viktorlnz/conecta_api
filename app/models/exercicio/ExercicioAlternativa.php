<?php

namespace app\models\exercicio;

use app\daos\Dao;

class ExercicioAlternativa{

    public function __construct(
        public int $id = 0,
        public string $resposta = '',
        public int $numerador = 0
    ){}

    public function create(int $idExercicio){
        $dao = new Dao();

        try {

            $this->id = $dao->insert(
                'exercicio_alternativa',
                [
                    'id_exercicio' => $idExercicio,
                    'numerador' => $this->numerador,
                    'resposta' => $this->resposta
                ]
            );

        } catch (\Throwable $th) {

            throw $th;
        }

        return $this->id;
    }

}
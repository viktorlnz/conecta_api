<?php

namespace app\models\instituicao;

use app\daos\Dao;

class MateriaTurma{
    public function __construct(
        public int $id = 0,
        public ?Materia $materia = null,
        public array $professores = []
    ){}

    public function create(int $idTurma){
        
        $dao = new Dao();

        $this->id = $dao->insert(
            'materia_turma',
            [
                'id_turma' => $idTurma,
                'id_materia' => $this->materia->id
            ]
        );

        foreach ($this->professores as $professor) {
            $dao->insert(
                'professor_materia_turma',
                [
                    'id_professor' => $professor->id,
                    'id_materia_turma' => $this->id
                ]
            );
        }

        return $this->id;
    }
}
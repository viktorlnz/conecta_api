<?php

namespace app\models\instituicao;

use app\daos\Dao;

class Materia{
    public function __construct(
        public int $id = 0,
        public string $nome = ''
    ){}

    public function create(int $idInstituicao){
        $dao = new Dao();

        return $dao->insert('materia',
            [
                'id_instituicao' => $idInstituicao,
                'nome' => $this->nome
            ]
        );
    }

    public static function list(int $idInstituicao){
        $dao = new Dao();

        return $dao->get('materia',
        [ 'id_instituicao' => ['compare' => '=', 'value' => $idInstituicao] ]
    );
    }

    public static function listProfessorMaterias(int $idInstituicao){
        $dao = new Dao();

        return $dao->getSql('
        SELECT materia.id, materia.nome FROM materia
        LEFT JOIN materia_turma ON materia.id = materia_turma.id_materia
        LEFT JOIN professor_materia_turma ON materia_turma.id_materia = professor_materia_turma.id_materia_turma
        LEFT JOIN professor ON professor_materia_turma.id_professor = professor.id
        WHERE professor.id = :id
        ');

        
    }
}
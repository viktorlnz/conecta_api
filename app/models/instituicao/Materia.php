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

    public static function listProfessorMaterias(int $idProfessor){
        $dao = new Dao();

        return $dao->getSql('
        SELECT DISTINCT materia.nome, materia.id FROM professor_materia_turma
	    JOIN materia_turma ON professor_materia_turma.id_materia_turma = materia_turma.id
	    JOIN materia ON materia_turma.id_materia = materia.id
        WHERE professor_materia_turma.id_professor = :id
        ', ['id' => $idProfessor]);

        
    }

    public static function listTurmaMaterias(int $idTurma){
        $dao = new Dao();

        return $dao->getSql('
        SELECT materia.id, materia.nome, materia_turma.id as materia_turma_id FROM materia_turma
        JOIN materia ON materia.id = materia_turma.id_materia
        WHERE id_turma = :id
        ', ['id' => $idTurma]);

    }
}
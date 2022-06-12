<?php

namespace app\models\exercicio;

use app\daos\Dao;
use app\models\instituicao\Materia;
use app\models\instituicao\Professor;

class Exercicio{

    public function __construct(
        public int $id = 0,
        public string $titulo = '',
        public string $desc = '',
        public string $categoria = '',
        public string $respostaCerta = '',
        public ?Professor $professor = null,
        public ?Materia $materia = null,
        public array $exerciciosAlternativa = []
    ){}

    public function create(){
        $dao = new Dao();

        try {
            //$dao->beginTransaction();

            $this->id = $dao->insert(
                'exercicio',
                [
                    'id_professor' => $this->professor->id,
                    'id_materia' => $this->materia->id,
                    'resposta_certa' => $this->respostaCerta,
                    'titulo' => $this->titulo,
                    '"desc"' => $this->desc,
                    'categoria' => $this->categoria
                ]
            );

            foreach ($this->exerciciosAlternativa as $alternativa) {
                $alternativa->create($this->id);
            }

            //$dao->commit();
        } catch (\Throwable $th) {
            //$dao->rollback();

            throw $th;
        }

        return $this->id;
    }

    public static function list(int $idProfessor){
        $dao = new Dao();

        return $dao->getSql(
            'SELECT exercicio.id, id_materia, titulo, nome as materia FROM exercicio 
            LEFT JOIN materia ON exercicio.id_materia = materia.id
            WHERE id_professor = :id',
            ['id' => $idProfessor]
        );
    }

    public static function getExerciciosMateria(int $idMateria, int $idProfessor){
        $dao = new Dao();

        return $dao->getSql('
        SELECT id, titulo, "desc" FROM exercicio
        WHERE id_materia = :id AND id_professor = '.$idProfessor.'
        ', ['id' => $idMateria]);
    }
}
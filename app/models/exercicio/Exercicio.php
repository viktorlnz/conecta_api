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

    public static function list(){
        $dao = new Dao();

        return $dao->get(
            'exercicio'
        );
    }
}
<?php

namespace app\models\exercicio;

use app\models\instituicao\Materia;
use app\models\instituicao\Professor;

class ExercicioTarefa extends Exercicio{

    public function __construct(
        public int $numerador = 0,
        public float $pontos = 0.0,
        int $id = 0,
        string $desc = '',
        string $categoria = '',
        ?Professor $professor = null,
        ?Materia $materia = null
    ){
        parent::__construct($id, $desc, $categoria, $professor, $materia);
    }
}
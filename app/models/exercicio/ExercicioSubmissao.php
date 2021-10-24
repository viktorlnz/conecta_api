<?php

namespace app\models\exercicio;

use app\models\instituicao\Materia;
use app\models\instituicao\Professor;

class ExercicioSubmissao extends Exercicio{

    public function __construct(
        public ?string $correcao = null,
        int $id = 0,
        string $desc = '',
        string $categoria = '',
        ?Professor $professor = null,
        ?Materia $materia = null
    ){
        parent::__construct($id, $desc, $categoria, $professor, $materia);
    }
}
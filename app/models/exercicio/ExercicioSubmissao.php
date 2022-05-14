<?php

namespace app\models\exercicio;

use app\models\instituicao\Materia;
use app\models\instituicao\Professor;

class ExercicioSubmissao extends Exercicio{

    public function __construct(
        public ?string $correcao = null,
        public string $resposta = '',
        int $id = 0,
        string $titulo = '',
        string $desc = '',
        string $categoria = '',
        string $respostaCerta = '',
        ?Professor $professor = null,
        ?Materia $materia = null
    ){
        parent::__construct($id, $titulo, $desc, $categoria, $respostaCerta, $professor, $materia);
    }
}
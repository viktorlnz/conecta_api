<?php

namespace app\models\exercicio;

use app\models\instituicao\Aluno;
use app\models\instituicao\Materia;
use app\models\instituicao\Turma;

class TarefaSubmissao extends Tarefa{

    public function __construct(
        public ?string $dtInicio = null,
        public ?string $dtSubmissao = null,
        int $id = 0,
        string $titulo = '',
        string $desc = '',
        float $pontos = 0.0,
        ?string $dtComeco = null,
        ?string $dtFim = null,
        ?Materia $materia = null,
        ?Turma $turma = null,
        public ?Aluno $aluno = null
    ){}
}
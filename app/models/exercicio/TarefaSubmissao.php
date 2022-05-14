<?php

namespace app\models\exercicio;

use app\daos\Dao;
use app\models\instituicao\Aluno;
use app\models\instituicao\Materia;
use app\models\instituicao\MateriaTurma;
use app\models\instituicao\Professor;
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
        ?MateriaTurma $materiaTurma = null,
        ?Professor $professor = null,
        public ?Aluno $aluno = null,
        array $exercicios = []
    ){
        parent::__construct(
            $id, $titulo, $desc, $pontos, $dtComeco,
            $dtFim, $materiaTurma, $professor, $exercicios
        );
    }

    public function submissao(){
        $dao = new Dao();

        $dao->beginTransaction();

        try {
            $this->id = $dao->insert(
                'tarefa_submissao',
                [
                    'id_tarefa' => $this->id,
                    'id_aluno' => $this->aluno->id,
                    'dt_inicio' => $this->dtInicio,
                    'dt_submissao' => $this->dtSubmissao
                ]
            );

            foreach ($this->exercicios as $exercicio) {
                $dao->insert(
                    'exercicio_submissao',
                    [
                        'id_tarefa_submissao' => $this->id,
                        'id_exercicio' => $exercicio->id,
                        'resposta' => $exercicio->resposta
                    ],
                    false
                );
            }

            $dao->commit();

            return $this->id;

        } catch (\Throwable $th) {
            $dao->rollback();

            throw $th;
        }
    }
}
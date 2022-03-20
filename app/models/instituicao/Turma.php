<?php

namespace app\models\instituicao;

use app\daos\Dao;

class Turma{
    public function __construct(
        public int $id = 0,
        public string $nome = '',
        public ?string $periodo = null,
        public int $grau = 0,
        public ?string $dataInicio = null,
        public int $duracaoMes = 0,
        public array $materiasTurma = [],
        public array $alunos = []
    ){}

    public function create(int $idInstituicao){
        $dao = new Dao();

        $dao->beginTransaction();

        try {
            $this->id = $dao->insert(
                'turma',
                [
                    'id_instituicao' => $idInstituicao,
                    'nome' => $this->nome,
                    'periodo' => $this->periodo,
                    'grau' => $this->grau,
                    'data_inicio' => $this->dataInicio,
                    'duracao_mes' => $this->duracaoMes,
                ]
            );

            foreach ($this->materiasTurma as $materia) {
                //$materia->create($this->id);

                $materia->id = $dao->insert(
                    'materia_turma',
                    [
                        'id_turma' => $this->id,
                        'id_materia' => $materia->materia->id
                    ]
                );
        
                foreach ($materia->professores as $professor) {
                    $dao->insert(
                        'professor_materia_turma',
                        [
                            'id_professor' => $professor->id,
                            'id_materia_turma' => $materia->id
                        ]
                    );
                }
            }

            foreach ($this->alunos as $aluno) {
                $dao->insert(
                    'aluno_turma',
                    [
                        'id_turma' => $this->id,
                        'id_aluno' => $aluno->id
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

    public static function list(int $idInstituicao){
        $dao = new Dao();
    
        return $dao->get(    
            'turma',
            ['id_instituicao' => [ 'compare' => '=', 'value' => $idInstituicao] ]
        );
    }
}
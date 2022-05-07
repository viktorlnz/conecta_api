<?php

namespace app\models\exercicio;

use app\daos\Dao;
use app\models\instituicao\Materia;
use app\models\instituicao\MateriaTurma;
use app\models\instituicao\Professor;
use app\models\instituicao\Turma;

class Tarefa{

    public function __construct(
        public int $id = 0,
        public string $titulo = '',
        public string $desc = '',
        public float $pontos = 0.0,
        public ?string $dtComeco = null,
        public ?string $dtFim = null,
        public ?MateriaTurma $materiaTurma = null,
        public ?Professor $professor = null,
        public array $exercicios = []
    ){}

    public function create(int $idMateriaTurma){
        $dao = new Dao();

        try {
            $dao->beginTransaction();
            
            $this->id = $dao->insert(
                'tarefa',
                [
                    'id_professor' => $this->professor->id,
                    'titulo' => $this->titulo,
                    '"desc"' => $this->desc,
                    'pontos' => $this->pontos,
                    'dt_comeco' => $this->dtComeco,
                    'dt_fim' => $this->dtFim
                ]
            );

            foreach ($this->exercicios as $exercicio) {
        
                $dao->insert('exercicio_tarefa', 
                [
                    'id_exercicio' => $exercicio->id,
                    'id_tarefa' => $this->id,
                    'numerador' => $exercicio->numerador,
                    'pontos' => $exercicio->pontos
                ]
                , false);
            }

            $dao->commit();
        } catch (\Throwable $th) {
            $dao->rollback();

            throw $th;
        }

        return $this->id;
    }

    public static function list(){
        $dao = new Dao();

        return $dao->get(
            'tarefa'
        );
    }

    public static function listAlunoTarefas(int $id){
        $dao = new Dao();

        return $dao->getSql(
            'SELECT tarefa.id, tarefa.titulo, tarefa.pontos, tarefa.dt_fim, 
            professor_materia_turma.id_professor AS id_professor,
            materia.id AS id_materia, materia.nome AS materia FROM tarefa
            LEFT JOIN professor_materia_turma ON tarefa.id_professor = professor_materia_turma.id
            LEFT JOIN materia_turma ON professor_materia_turma.id_materia_turma = materia_turma.id
            LEFT JOIN turma ON materia_turma.id_turma = turma.id
            LEFT JOIN aluno_turma ON turma.id = aluno_turma.id_turma
            JOIN materia ON materia_turma.id_materia = materia.id
            WHERE aluno_turma.id_aluno = :id',
            [
                'id' => $id
            ]
        );
    }
}
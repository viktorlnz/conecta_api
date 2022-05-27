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

    public function get(){
        $dao = new Dao();

        $t = $dao->getSql(
            'SELECT dt_submissao, tarefa.titulo, tarefa.desc,
            tarefa.pontos, aluno.id AS id_aluno, aluno.nome FROM tarefa_submissao
            LEFT JOIN tarefa ON tarefa_submissao.id_tarefa = tarefa.id
            LEFT JOIN aluno ON tarefa_submissao.id_aluno = aluno.id
            WHERE tarefa_submissao.id = :id',
            ['id' => $this->id]
        );

        //caso não haja tarefa retornada
        if(sizeof($t) === 0){
            return null;
        }

        $t = $t[0];

        $this->dtSubmissao = $t['dt_submissao'];
        $this->titulo = $t['titulo'];
        $this->desc = $t['desc'];
        $this->pontos = $t['pontos'];

        $this->aluno = new Aluno($t['id_aluno'], $t['nome']);


        $exercicios = $dao->getSql(
            'SELECT id_exercicio, resposta, "desc", categoria, titulo FROM exercicio_submissao
            LEFT JOIN exercicio ON exercicio_submissao.id_exercicio = exercicio.id
            WHERE id_tarefa_submissao = :id',
            [
                'id' => $this->id
            ]
        );
        
        foreach ($exercicios as $e) {
            $ex = new ExercicioSubmissao(
                null,
                $e['resposta'],
                $e['id_exercicio'],
                $e['titulo'],
                $e['desc'],
                $e['categoria']
            );

            if($ex->categoria === 'ALTERNATIVA'){
                $alts = $dao->getSql(
                    'SELECT * FROM exercicio_alternativa
                    WHERE id_exercicio = :id',
                    [
                        'id' => $ex->id
                    ]
                );

                $alternativas = [];

                foreach ($alts as $alt) {
                    $alternativa = new ExercicioAlternativa($alt['id'], $alt['resposta'], $alt['numerador']);

                    array_push($alternativas, $alternativa);
                }

                $ex->exerciciosAlternativa = $alternativas;
            }

            array_push($this->exercicios, $ex);
        }

        return $this;
    }

    public function corrigir(){
        $dao = new Dao();

        foreach ($this->exercicios as $exercicio) {
            $dao->execSql(
                'UPDATE exercicio_submissao SET correcao = :correcao
                WHERE id_exercicio = '.$exercicio->id.' AND id_tarefa_submissao = '.$this->id,
                [
                    'correcao' => $exercicio->correcao
                ]
            );

        }
    }
}
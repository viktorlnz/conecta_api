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

    public function get(){
        $dao = new Dao();

        $res = $dao->getSql(
            'SELECT tarefa.titulo, tarefa.desc, tarefa.pontos, tarefa.dt_comeco, 
            tarefa.dt_fim, professor.id AS id_professor, professor.nome FROM tarefa
            LEFT JOIN professor_materia_turma ON tarefa.id_professor = professor_materia_turma.id
            LEFT JOIN professor ON professor_materia_turma.id_professor = professor.id
            WHERE tarefa.id = :id',
            ['id' => $this->id]
        )[0];

        $this->titulo = $res['titulo'];
        $this->desc = $res['desc'];
        $this->pontos = $res['pontos'];
        $this->dtComeco = $res['dt_comeco'];
        $this->dtFim = $res['dt_fim'];

        $this->professor = new Professor($res['id_professor'], $res['nome']);

        $res = $dao->getSql(
            'SELECT "id", "desc", categoria, titulo from exercicio
            inner join exercicio_tarefa ON exercicio.id = exercicio_tarefa.id_exercicio
            WHERE exercicio_tarefa.id_tarefa = :id',
            ['id' => $this->id]
        );

        foreach ($res as $e) {
            $exercicio = new Exercicio(
                $e['id'], $e['titulo'], $e['desc'], $e['categoria'],
                '', null, null, []
            );

            if($e['categoria'] === 'ALTERNATIVA'){
                $alt = $dao->getSql(
                    'SELECT * from exercicio_alternativa
                    WHERE id_exercicio = :id',
                    ['id' => $e['id']]
                );

                foreach($alt as $a){
                    $alternativa = new ExercicioAlternativa($a['id'], $a['resposta'], $a['numerador']);
                    array_push($exercicio->exerciciosAlternativa, $alternativa);
                }
            }

            array_push($this->exercicios, $exercicio);
        }

        return $this;
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

    public static function listAlunoTarefasAtuais(int $id){
        $dao = new Dao();

        return $dao->getSql(
            'SELECT tarefa.id, tarefa.titulo, tarefa.pontos, tarefa.dt_fim, 
            professor_materia_turma.id_professor AS id_professor, tarefa.desc,
            materia.id AS id_materia, materia.nome AS materia FROM tarefa
            LEFT JOIN professor_materia_turma ON tarefa.id_professor = professor_materia_turma.id
            LEFT JOIN materia_turma ON professor_materia_turma.id_materia_turma = materia_turma.id
            LEFT JOIN turma ON materia_turma.id_turma = turma.id
            LEFT JOIN aluno_turma ON turma.id = aluno_turma.id_turma
            JOIN materia ON materia_turma.id_materia = materia.id
            WHERE aluno_turma.id_aluno = :id AND dt_fim > CURRENT_TIMESTAMP
            AND CURRENT_TIMESTAMP > dt_comeco
            AND tarefa.id NOT IN (SELECT id_tarefa FROM tarefa_submissao WHERE id_aluno = :id)
            ORDER BY tarefa.dt_fim ASC',
            [
                'id' => $id
            ]
        );
    }

    public static function listTarefasConcluidas(int $idProfessor){
        $dao = new Dao();
        
        return $dao->getSql(
            'SELECT tarefa.id, tarefa.titulo, tarefa_submissao.id_tarefa,
            tarefa_submissao.id_aluno, aluno.nome, tarefa_submissao.dt_submissao,
            tarefa_submissao.id AS id_submissao 
            FROM tarefa
            RIGHT JOIN tarefa_submissao ON tarefa.id = tarefa_submissao.id_tarefa
            LEFT JOIN aluno ON tarefa_submissao.id_aluno = aluno.id
            LEFT JOIN professor_materia_turma ON tarefa.id_professor = professor_materia_turma.id
            WHERE professor_materia_turma.id_professor = :id
            AND tarefa_submissao.id NOT IN (SELECT DISTINCT id_tarefa_submissao FROM exercicio_submissao WHERE correcao is not null)',
            [
                'id' => $idProfessor
            ]
        );
    }
}
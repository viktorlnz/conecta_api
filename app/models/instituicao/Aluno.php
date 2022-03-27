<?php

namespace app\models\instituicao;

use app\daos\Dao;
use app\utils\Password;

class Aluno extends Usuario{
    public function __construct(
        public int $id = 0,
        public string $nome = '',
        public string $identidade = '',
        public ?string $dataNasc = null,
        int $idUsuario = 0,
        string $usuario = '',
        string $senha = '',
        string $email = '',
        ?string $dtRegistro = null
    ){
        parent::__construct(
            $idUsuario,
            $usuario,
            $email,
            $senha,
            $dtRegistro,
            'ALUNO'
        );
    }

    public function create(int $idInstituicao){
        $dao = new Dao();

        try {
            $dao->beginTransaction();

            $this->idUsuario = $dao->insert(
                'usuario',
                [
                    'usuario' => $this->usuario,
                    'email' => $this->email,
                    'senha' => Password::make($this->senha),
                    'categoria_usuario' => $this->categoriaUsuario
                ]
            );
    
            $this->id = $dao->insert(
                'aluno',
                [
                    'nome' => $this->nome,
                    'identidade' => $this->identidade,
                    'data_nasc' => $this->dataNasc,
                    'id_instituicao' => $idInstituicao,
                    'id_usuario' => $this->idUsuario
                ]
            );

            $dao->commit();

        } catch (\Throwable $th) {
            $dao->rollback();

            throw $th;
        }

        return $this->id;
    }

    public static function list(int $idInstituicao){
        $dao = new Dao();

        return $dao->get(
            'aluno',
            [
                'id_instituicao' => ['compare' => '=', 'value' => $idInstituicao]
            ]
        );
    }

    public static function listProfessorAlunos(int $idProfessor){
        $dao = new Dao();

        return $dao->getSql('
        SELECT DISTINCT aluno.nome, aluno.id, aluno.data_nasc, aluno.identidade FROM aluno
	    JOIN aluno_turma ON aluno.id = aluno_turma.id_aluno
		JOIN turma ON aluno_turma.id_turma = turma.id
	    JOIN materia_turma ON turma.id = materia_turma.id_turma
		JOIN professor_materia_turma ON materia_turma.id = professor_materia_turma.id_materia_turma
        WHERE professor_materia_turma.id_professor = :id
        ', ['id' => $idProfessor]);

    }
}
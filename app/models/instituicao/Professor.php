<?php

namespace app\models\instituicao;

use app\daos\Dao;
use app\utils\Password;

class Professor extends Usuario{
    public function __construct(
        public int $id = 0,
        public string $nome = '',
        public string $identificacao = '',
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
            $senha ,
            $dtRegistro,
            'PROFESSOR'
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
                'professor',
                [
                    'id_instituicao' => $idInstituicao,
                    'nome' => $this->nome,
                    'id_usuario' => $this->idUsuario,
                    'identificacao' => $this->identificacao
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
            'professor',
            [
                'id_instituicao' => ['compare' => '=', 'value' => $idInstituicao]
            ]
        );
    }

    public function getProfessorMateriaId(int $idMateriaTurma){
        $dao = new Dao();

        $ret = $dao->getSql(
            'SELECT id FROM professor_materia_turma WHERE id_professor = '.$this->id.
            ' AND id_materia_turma = '.$idMateriaTurma
        )[0]['id'];

        return $ret;
    }
}
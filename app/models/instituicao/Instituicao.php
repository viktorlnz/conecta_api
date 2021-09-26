<?php

namespace app\models\instituicao;

use app\daos\Dao;
use app\utils\Password;

class Instituicao extends Usuario{
    public function __construct(
        public int $id = 0,
        public string $cnpj = '',
        public string $razaoSocial = '',
        public string $nomeFantasia = '',
        public ?bool $ativo = null,
        public array $alunos = [],
        public array $professores = [],
        public array $materias = [],
        public array $turmas = [],
        private ?string $dataRegistro = null,
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
            'INSTITUICAO'
        );
    }

    public function create(){

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
                'instituicao',
                [
                    'cnpj' => $this->cnpj,
                    'razao_social' => $this->razaoSocial,
                    'nome_fantasia' => $this->nomeFantasia,
                    'ativo' => $this->ativo,
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

    public function read(){

    }

    public static function list(){
        $dao = new Dao();

        return $dao->get('instituicao');
    }

    public function update(){

    }

    public function delete(){

    }
}
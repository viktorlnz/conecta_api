<?php

namespace app\models\instituicao;

use app\daos\Dao;

class Materia{
    public function __construct(
        public int $id = 0,
        public string $nome = ''
    ){}

    public function create(int $idInstituicao){
        $dao = new Dao();

        return $dao->insert('materia',
            [
                'id_instituicao' => $idInstituicao,
                'nome' => $this->nome
            ]
        );
    }

    public static function list(int $idInstituicao){
        $dao = new Dao();

        return $dao->get('materia',
        [ 'id_instituicao' => ['compare' => '=', 'value' => $idInstituicao] ]
    );
    }
}
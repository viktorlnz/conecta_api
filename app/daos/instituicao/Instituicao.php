<?php

namespace app\daos\instituicao;

use app\daos\Dao;
use app\models\instituicao\Instituicao as InstituicaoModel;

class Instituicao extends Dao{
    public static function create(InstituicaoModel $instituicao){
        $sql = 'INSERT into instituicao 
        (cnpj, razao_social, nome_fantasia, ativo) VALUES (:cnpj, :razao, :nome, true)';

        try {
            $stmt = Dao::$conn->prepare($sql);

            $stmt->bindParam(':cnpj', $instituicao->cpnj);
            $stmt->bindParam(':razao', $instituicao->razaoSocial);
            $stmt->bindParam(':nome', $instituicao->nomeFantasia);

            $stmt->execute();

            $instituicao->id = Dao::$conn->lastInsertId('instituicao_id_seq');

        } catch (\Throwable $th) {
            throw $th;
        }

        return $instituicao;
    }

    public static function read(int $id):InstituicaoModel | null{
        $sql = 'SELECT cnpj, razao_social, nome_fantasia, ativo FROM instituicao WHERE
        id = :id';

        $retorno = null;

        try {
            $stmt = Dao::$conn->prepare($sql);

            $stmt->bindParam(':id', $id);

            $stmt->execute();

            $res = $stmt->fetch(\PDO::FETCH_ASSOC);

            $retorno = new InstituicaoModel(
                $id,
                $res['cnpj'],
                $res['razao_social'],
                $res['nome_fantasia'],
                $res['ativo']
            );

        } catch (\Throwable $th) {
            throw $th;
        }

        return $retorno;
    }

    public static function list():array{
        $sql = 'SELECT id, cnpj, razao_social, nome_fantasia, ativo FROM instituicao';

        $retorno = [];

        try {
            $stmt = Dao::$conn->prepare($sql);

            $stmt->execute();

            while($res = $stmt->fetch(\PDO::FETCH_ASSOC)){
                $instituicao = new InstituicaoModel(
                    $res['id'],
                    $res['cnpj'],
                    $res['razao_social'],
                    $res['nome_fantasia'],
                    $res['ativo']
                );

                array_push($retorno, $instituicao);
            }

        } catch (\Throwable $th) {
            throw $th;
        }

        return $retorno;
    }

    public static function update(InstituicaoModel $instituicao){
        $sql = 'UPDATE instituicao SET '; 
        
        $columns = [
            'cnpj' => $instituicao->cpnj
        ];

        try {
            $stmt = Dao::$conn->prepare($sql);

            $stmt->execute();

            while($res = $stmt->fetch(\PDO::FETCH_ASSOC)){
                $instituicao = new InstituicaoModel(
                    $res['id'],
                    $res['cnpj'],
                    $res['razao_social'],
                    $res['nome_fantasia'],
                    $res['ativo']
                );

                array_push($retorno, $instituicao);
            }

        } catch (\Throwable $th) {
            throw $th;
        }

        return $retorno;
    }

    public static function delete(){

    }
}
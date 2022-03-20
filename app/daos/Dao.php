<?php

namespace app\daos;

use app\db\Db;


class Dao{
    protected static \PDO $conn;
    protected static string $table;

    public function __construct()
    {
        Dao::$conn = Db::get()->connect();
    }

    public function beginTransaction(){
        Dao::$conn->beginTransaction();
    }

    public function commit(){
        Dao::$conn->commit();
    }

    public function rollback(){
        Dao::$conn->rollBack();
    }

    public function insert(string $table, array $item, bool $getLastId = true){
        $retorno = 0;

        $sql = 'INSERT INTO '.$table;
        
        $keys = array_reduce(array_keys($item), function($carry, $item){
            return $carry . $item . ', ';
        }, '( ');

        $keys = rtrim($keys, ', ');
        $keys .= ') ';

        $columns = array_reduce(array_keys($item), function($carry, $item){
            return $carry . '?, ';
        }, '( ');

        $columns = rtrim($columns, ', ');
        $columns .= ') ';
        
        $sql .= $keys. ' VALUES ' . $columns;

        try {
            $stmt = Dao::$conn->prepare($sql);
            $c = [];
            foreach ($item as $row) {
                array_push($c, $row);
            }

            $stmt->execute($c);

            if($getLastId){
                $retorno = Dao::$conn->lastInsertId($table.'_id_seq');
            }
            
        } catch (\Throwable $th) {
            throw new \Error($th->getMessage() . $sql);
        }

        return $retorno;
    }

    public function getSql(string $sql, array $params = null){
        $retorno = [];
        

        try {
            $stmt = Dao::$conn->prepare($sql);

            if (isset($params)) {
                foreach ($params as $key => $where) {
                    $stmt->bindParam(':'.$key, $where);                
                }
            }

            $stmt->execute();

            while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                array_push($retorno, $row);
            }
        } catch (\Throwable $th) {
            throw new \Error($th->getMessage().$sql);
        }

        return $retorno;
    }


    /**
     * @param string $table
     * @param mixed $filter
     * @param mixed $columns
     * [
     *  'coluna_teste1',
     *  'tabela_teste' => [
     *      'column' => 'coluna_teste'
     *      'as' => 'outro_nome' //opcional
     *      'join' => 'LEFT JOIN',
     *      'from' => 'nome_da_tabela',
     *      
     *  ]
     * ]
     */
    public function get(string $table, array | null $filter = null, array | null $columns = null){
        $retorno = [];
        
        $sql = 'SELECT ';

        $sqlColumns = is_null($columns) ? '*' :
        $this->mountSelectColumns($columns);

        $sql .= $sqlColumns;

        $sql .= ' FROM '.$table;

        if (!is_null($columns)) {
            $sql .= $this->mountJoins($table, $columns);
        }

        if( !is_null($filter)){
            $sql .= $this->mountWhere($filter);
        }

        try {
            $stmt = Dao::$conn->prepare($sql);

            if ($filter !== null) {
                foreach ($filter as $key => $where) {
                    $stmt->bindParam(':v'.$key, $where['value']);                
                }
            }

            $stmt->execute();

            while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                array_push($retorno, $row);
            }
        } catch (\Throwable $th) {
            throw new \Error($th->getMessage().$sql);
        }

        return $retorno;
    }

    /**
     * Undocumented function
     *
     * @param array $columns 
     * [
     *  'coluna_teste1',
     *  'tabela_teste' => [
     *      'column' => 'coluna_teste'
     *      'as' => 'outro_nome' //opcional
     *  ]
     * ]
     * @return string
     */
    private function mountSelectColumns(array $columns):string{
        $sql = '';
        $rows = [];

        //['tabela_teste' => 'teste', []]
        foreach ($columns as $key => $column) {
            if(is_array($column)){ //formato: 'tabela' => []

                foreach ($column as $c) {
                    $str = $key . '.' . $c['column'] . (isset($c['as']) ? ' AS '.$c['as'] : '');
                    array_push($rows, $str);
                }
                
            }
            else{ // apenas coluna ['teste', 'teste2']
                array_push($rows, $column);
            }
        }

        if ( sizeof($rows) > 0) {
            $sql .= implode(', ', $rows);
        }

        return $sql;
    }

    /**
     * Undocumented function
     *
     * @param string $table nome da tabela a fazer join
     * @param array $columns
     * [
     *  'coluna_teste1',
     *  'tabela_teste' => [
     *      'column' => 'coluna_teste'
     *      'as' => 'outro_nome' //opcional
     *      'join' => 'LEFT JOIN',
     *      'from' => 'nome_da_tabela',
     *      
     *  ]
     * ]
     * @return string
     */
    private function mountJoins(string $table, array $columns):string{
        $sql = '';
        $joins = [];

        foreach ($columns as $key => $c) {
            
            if(!is_array($c)){
                return '';
            }

            elseif ($key === $table) {
                continue;
            }
            else{
                $column = $c[0];
              
                $str = ' ' . ( isset($column['join']) ? $column['join'] : 'JOIN' ) . ' ' . $key .
                ' ON ' . ( isset($column['from']) ? $column['from'] : $table) . '.id = '.
                $key . '.' . $column['column'];

                array_push($joins, $str);
            }
        }

        if( sizeof($joins) > 0 ){
            $sql .= implode(', ', $joins);
        }

        return $sql;
    }

    private function mountWhere($wheres){
        $sql = ' WHERE ';

        foreach ($wheres as $key => $where) {
            $sql .= ' '. (isset($where['table']) ? $where['table'] . '.' . $key : $key) . ' '.$where['compare'].' :v'.$key.' '. $where['next'] ?? '';
        }

        return $sql;
    }

    /**
     * Executa uma query de update no banco
     *
     * @param array $updateFieldsAndValues um array que contem o seguinte formato:
     * [
     *      'fields' => ['column1' => 'Value1', 'column2' => 'Value2'],
     *      'where' => ['conditionColumn' => 'value']
     * ]
     * @return void
     */
    public function update( string $table , array $updateFieldsAndValues){
        $fields = $updateFieldsAndValues['fields'];
        $where = $updateFieldsAndValues['where'];

        $updateFields = '';
        foreach (array_keys($fields) as $field) {
            $updateFields .= "{$field} = :{$field}, ";
        }

        $updateFields = rtrim($updateFields, ',');
        $whereUpdate = array_keys($where);

        $bind = array_merge($fields, $where);
        $sql = sprintf('UPDATE %s SET %s WHERE %s', $table, $updateFields, "{$whereUpdate[0]} = :{$whereUpdate[0]}");


        try {
            $prepare = Dao::$conn->prepare($sql);

            return $prepare->execute($bind);
        } catch (\PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function delete(string $table, $field, $value){
        try {
            $prepare = Dao::$conn->prepare("DELETE FROM $table WHERE {$field} = :{$field}");
            $prepare->bindValue($field, $value);

            return $prepare->execute();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
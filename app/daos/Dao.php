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

    public function insert(string $table, array $item){
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

            $retorno = Dao::$conn->lastInsertId($table.'_id_seq');

        } catch (\Throwable $th) {
            throw new \Error($th->getMessage() . $sql);
        }

        return $retorno;
    }

    /**
     * Teste
     * @param string 
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

    private function mountSelectColumns(array $columns, bool $join = false):string{
        $sql = '';
        $rows = [];

        foreach ($columns as $key => $column) {
            if(is_array($column)){
                $sql .= $this->mountSelectColumns($column, true);
            }
            else{
                array_push($rows, $join ? $key.'.'.$column : $column);
            }
        }

        if ( sizeof($rows) > 0) {
            $sql .= implode(', ', $rows);
        }

        return $sql;
    }

    private function mountJoins(string $table, array $columns):string{
        $sql = '';
        $joins = [];

        foreach ($columns as $key => $column) {
            if(!is_array($column)){
                return '';
            }

            elseif ($key === $table) {
                continue;
            }

            else{
                array_push($joins, 
                    ( is_null(  $column['join']) ? 'JOIN' : $column['join'] ) 
                    .' '.$column['column']. ' ON '
                    .( is_null($column['from']) ? $table : $column['from'] ). '.id'
                    .' = '
                    . $column['column']. '.id'
                );
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
            $sql .= ' '. $key . ' '.$where['compare'].' :v'.$key.' '. $where['next'] ?? '';
        }

        return $sql;
    }
}
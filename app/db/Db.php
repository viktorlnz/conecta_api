<?php

namespace app\db;

class Db{

    private static $conn;

    public function connect()
    {
        $params = parse_ini_file('database.ini');

        if($params === false){
            throw new \Exception("Erro ao ler ao arquivo de configuração.");
        }

        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
        $params['host'],
        $params['port'],
        $params['database'],
        $params['user'],
        $params['password']
    );

    $pdo = new \PDO($conStr);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    return $pdo;

    }

    public static function get() {
        if(null === static::$conn){
            static::$conn = new static();
        }

        return static::$conn;
    }

    protected function __construct(){

    }

    private function __clone(){

    }

}
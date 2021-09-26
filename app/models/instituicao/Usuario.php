<?php

namespace app\models\instituicao;

use app\daos\Dao;
use app\utils\Password;

class Usuario{

    public function __construct(
        public int $idUsuario = 0,
        public string $usuario = '',
        public string $email = '',
        public string $senha = '',
        public ?string $dtRegistro = null,
        public ?string $categoriaUsuario = null
    ){}

    public function login(){
        $dao = new Dao();

        $usuario = $dao->get(
            'usuario',
            [
                'usuario' => ['compare' => '=', 'value' => $this->usuario, 'next' => 'OR'],
                'email' => ['compare' => '=', 'value' => $this->usuario]
            ]
        )[0];

        if (Password::verify($this->senha, $usuario['senha'])) {
            $idLogin = 0;
            
            try {
                $idLogin = $dao->insert(
                    'login',
                    [
                        'id_usuario' => $usuario['id'],
                        'ip' => $this->getClientIp()
                    ]
                );

            } catch (\Throwable $th) {
                throw $th;
            }

            $_SESSION['idUsuario'] = $usuario['id'];
            $_SESSION['id'] = $idLogin;
            $_SESSION['categoria'] = $usuario['categoria_usuario'];
            $_SESSION['usuario'] = $usuario['usuario'];
        
            return $usuario;
        }

        else{
            return false;
        }
    }

    public static function logoff(){
        $_SESSION = array();
    }

    public function checkLogin(){
        $ip = $this->getClientIp();

        $dao = new Dao();

        $ipLogin = $dao->get(
            'login',
            ['id' => ['compare' => '=', 'value' => $_SESSION['id'] ] ],
            ['ip']
        )[0];

        if ($ip == $ipLogin['ip']) {
            return true;
        }
        else{
            return false;
        }
    }

    private function getClientIp(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            
            //ip da internet compartilhada
            $ip = $_SERVER['HTTP_CLIENT_IP'];

        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            
            //ip passado pelo proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
}
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
            
            // bin2hex(random_bytes($length))
            $token = bin2hex(random_bytes(64));
            
            try {
                $dao->insert(
                    'login',
                    [
                        'id_usuario' => $usuario['id'],
                        'ip' => $this->getClientIp(),
                        'token' => $token
                    ]
                );

            } catch (\Throwable $th) {
                throw $th;
            }

            $usuarioTipo = null;

            switch($usuario['categoria_usuario']){
                case 'ALUNO':
                    $usuarioTipo = $dao->get(
                        'usuario',
                        [
                            'id' => ['compare' => '=', 'value' => $usuario['id'], 'table' => 'usuario']
                        ],
                        [
                            'aluno' => [
                                [
                                    'column' => 'id_usuario',
                                    'join' => 'RIGHT JOIN'
                                ],
                                [
                                    'column' => 'id'
                                ]
                            ]
                        ]
                        
                    )[0];
                break;

                case 'PROFESSOR':
                    $usuarioTipo = $dao->get(
                        'usuario',
                        [
                            'id' => ['compare' => '=', 'value' => $usuario['id'], 'table' => 'usuario']
                        ],
                        [
                            'professor' => [
                                [
                                    'column' => 'id_usuario',
                                    'join' => 'RIGHT JOIN'
                                ],
                                [
                                    'column' => 'id'
                                ]
                            ]
                        ]
                        
                    )[0];
                break;

                case 'INSTITUICAO':
                    $usuarioTipo = $dao->get(
                        'usuario',
                        [
                            'id' => ['compare' => '=', 'value' => $usuario['id'], 'table' => 'usuario']
                        ],
                        [
                            'instituicao' => [
                                [
                                    'column' => 'id_usuario',
                                    'join' => 'RIGHT JOIN'
                                ],
                                [
                                    'column' => 'id'
                                ]
                            ]
                        ]
                        
                    )[0];
                break;
            }
            $usuario = $dao->get(
                'usuario',
                [
                    'usuario' => ['compare' => '=', 'value' => $this->usuario, 'next' => 'OR'],
                    'email' => ['compare' => '=', 'value' => $this->usuario]
                ]
            )[0];

            $_SESSION['id'] = $usuarioTipo['id'];
            $_SESSION['idUsuario'] = $usuario['id'];
            $_SESSION['categoria'] = $usuario['categoria_usuario'];
            $_SESSION['usuario'] = $usuario['usuario'];

            return [
                'id' => $usuarioTipo['id'],
                'token' => $token,
                'usuario' => $usuario['usuario'],
                'categoria' => $usuario['categoria_usuario']
            ];
        }

        else{
            return [
                'error' => 'Login ou senha não conferem!'
            ];
        }
    }

    public static function logoff(){
        $_SESSION = array();
    }

    public function checkLogin(string $token){
        $ip = $this->getClientIp();

        $dao = new Dao();
        

        $ipLogin = $dao->get(
            'login',
            ['id' => ['compare' => '=', 'value' => $_SESSION['id'] ] ],
            ['ip', 'token']
        )[0];

        if ($ip === $ipLogin['ip'] && $token === $ipLogin['token']) {
            return [
                'id' => $_SESSION['idUsuario'],
                'categoria' => $_SESSION['categoria'],
                'usuario' => $_SESSION['usuario']
            ];
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
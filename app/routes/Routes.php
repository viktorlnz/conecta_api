<?php 

namespace app\routes;

use Slim\App;

class Routes{

    private static $routes;


    public static function get(){
        if(null === static::$routes){
            static::$routes = new Static();
        }

        return static::$routes;
    }

    public function ligarRotas(App $app){
        //SESSÃO INSTITUIÇÃO
        $app->group('', new InstituicaoRoutes);

        //SESSÃO EXERCÍCIOS
        $app->group('', new ExercicioRoutes);
    }

    protected function __construct(){}

}

?>
<?php

namespace app\utils;

class Password{

    public static function make($pass){

        $options = [
            'cost' => 12
        ];

        return password_hash($pass, PASSWORD_BCRYPT, $options);
    }

    public static function verify($pass, $hash):bool{
        return password_verify($pass, $hash);
    }
}
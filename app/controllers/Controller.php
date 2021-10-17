<?php

namespace app\controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class Controller{
    public function __invoke(Request $request)
    {

    }

    public function getArgs(Request $request){
        $contentType = $request->getHeaderLine('Content-Type');
  
        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);

            
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody($contents);
            }
        }
    
        $args = $request->getParsedBody();
        return $args;
    }

    public function options(
        Request $request,
        ResponseInterface $response
    ): ResponseInterface {
        // Do nothing here. Just return the response.
        return $response;
    }
}
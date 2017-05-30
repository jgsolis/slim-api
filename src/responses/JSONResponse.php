<?php

namespace SlimApi\Responses;

use \Slim\Http\Response as Response;

class JSONResponse extends Response
{


    public function __construct()
    {
        parent::__construct();

        $this->setHeaders();

    }

    private function setHeaders()
    {
        $this->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization');
        $this->headers->set('Content-Type', 'application/json');
        $this->headers->set('Access-Control-Allow-Origin', '*');
        $this->headers->set('Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, OPTIONS');      
    }

    public function set( $data = NULL )
    {
        if ( !isset($data) || !is_array($data) )
        {
            throw new \Exception('La respuesta siempre debe ser un arreglo');
        }

        if ( isset($data['error']['code']) )
        { 
            $this->status = $data['error']['code']; 
        }

        $count = 1;

        if ( $this->status == 200 )
        {
            $key        = array_keys($data)[0];
            $array      = $data[$key];

            if ( is_array($array) )
            {
                $arrayKey   = array_keys($array)[0];
                $count      = ( is_array( $data[$key][$arrayKey]) )? count($data[$key]) : 1;
            }
            
        }

        $statusName    = ( $this->isSuccessful() || $this->isOk() )? 'SUCCESS' : 'ERROR';

        $response = [
            'status'    => $statusName,
            'count'     => $count,
            'results'   => $data
        ];
   

        $jsonResponse = json_encode($response);

        return $this->write($jsonResponse);

    }

    public function write($data)
    {

        $this->getBody()->write($data);

        return $this;
    }

    public function getCodeMessage($code)
    {
        return static::$messages[$code];
    }


}

?>
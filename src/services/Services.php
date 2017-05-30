<?php

return [
	
	'logger' => function($c){

		if ( !isset($c) ){ throw new \Exception('No existe el contenedor de dependencias'); }

		$classLogger   = '\\Katzgrau\\KLogger\\Logger';
		$classLogLevel = '\\Psr\\Log\\LogLevel';

		if ( !class_exists($classLogger) ) { throw new \Exception("La clase  {$classLogger}  no existe"); }

		if ( !class_exists($classLogLevel) ){ throw new \Exception("La clase {$classLogLevel} no existe"); }

		if ( empty($c->get('settings')['logger']) || empty($c->get('settings')['logger']['extension']) || empty($c->get('settings')['logger']['prefix']) || empty($c->get('settings')['logger']['path']) )
		{ 
			throw new \Exception("No se encuentra la configuración del logger"); 
		}
		$path 		= (trim($config['path']) != '')? $config['path'] : 'logs/';
		$extension 	= (trim($config['extension']) != '')? $config['extension'] : 'log';
		$prefix 	= (trim($config['prefix']) != '')? $config['prefix'] : 'app_';
		$config     = $c->get('settings')['logger'];

		$logger 	= new $classLogger( $path, $classLogLevel::ERROR, array (
		    'extension' => $extension,
		    'prefix'	=> $prefix
		));

		return $logger;

	},

	'jwt' => function($c){

		$class = '\\Firebase\\JWT\\JWT';

		if ( !class_exists($class) )
		{
	    	throw new \Exception("La clase {$class} no existe");
		}

		$jwt = new $class;
	    return $jwt;


	},

	'response' => function($c){

		$response = new \SlimApi\Responses\JSONResponse();

		return $response;

	},

	'debugLogger' => function($c){

		if ( !isset($c) ){ throw new \Exception('No existe el contenedor de dependencias'); }

		$classLogger   = '\\Katzgrau\\KLogger\\Logger';
		$classLogLevel = '\\Psr\\Log\\LogLevel';

		if ( !class_exists($classLogger) ) { throw new \Exception("La clase  {$classLogger}  no existe"); }

		if ( !class_exists($classLogLevel) ){ throw new \Exception("La clase {$classLogLevel} no existe"); }

		if ( empty($c->get('settings')['debugLogger']) || empty($c->get('settings')['debugLogger']['extension']) || empty($c->get('settings')['debugLogger']['prefix']) || empty($c->get('settings')['debugLogger']['path']) )
		{ 
			throw new \Exception("No se encuentra la configuración del debugLogger"); 
		}

		$path 		= (trim($config['path']) != '')? $config['path'] : 'logs/debug/';
		$extension 	= (trim($config['extension']) != '')? $config['extension'] : 'log';
		$prefix 	= (trim($config['prefix']) != '')? $config['prefix'] : 'debug_';
		$config     = $c->get('settings')['debugLogger'];

		$logger 	= new $classLogger( $path, $classLogLevel::INFO, array (
		    'extension' => $extension,
		    'prefix'	=> $prefix
		));

		return $logger;

	}

];

?>
<?php

return [
	
	'logger' => function($c){

		if ( !isset($c) )
		{ 
			throw new \Exception('No existe el contenedor de dependencias'); 
		}

		$classLogger   = '\\Katzgrau\\KLogger\\Logger';
		$classLogLevel = '\\Psr\\Log\\LogLevel';

		if ( !class_exists($classLogger) ) 
		{ 
			throw new \Exception("La clase  {$classLogger}  no existe"); 
		}

		if ( !class_exists($classLogLevel) )
		{ 
			throw new \Exception("La clase {$classLogLevel} no existe"); 
		}

		if ( empty($c->get('settings')['logger']) )
		{ 
			throw new \Exception("No se encuentra la configuración del logger"); 
		}

		$extension  = ( $c->get('settings')['logger']['extension'] == NULL || trim($c->get('settings')['logger']['extension']) === '' )? 'log' : $c->get('settings')['logger']['extension'];

		$path		= ( $c->get('settings')['logger']['path'] == NULL || trim($c->get('settings')['logger']['path']) === '' )? 'logs/' : $c->get('settings')['logger']['path'];

		$prefix 	= ( $c->get('settings')['logger']['prefix'] == NULL || trim($c->get('settings')['logger']['prefix']) === '' )? 'app_' : $c->get('settings')['logger']['prefix'];

		$config     = $c->get('settings')['logger'];

		$logger 	= new $classLogger( $path, $classLogLevel::ERROR, array (
		    'extension' => $extension,
		    'prefix'	=> $prefix
		));

		return $logger;

	},

	'response' => function($c){

		$class = '\\SlimApi\\Responses\\JSONResponse';

		if ( !class_exists($class) )
		{
			throw new \Exception("La clase {$class} no existe");
		}

		$response = new $class();

		return $response;

	},

	'debugLogger' => function($c){

		if ( !isset($c) ){ throw new \Exception('No existe el contenedor de dependencias'); }

		$classLogger   = '\\Katzgrau\\KLogger\\Logger';
		$classLogLevel = '\\Psr\\Log\\LogLevel';

		if ( !class_exists($classLogger) ) { throw new \Exception("La clase  {$classLogger}  no existe"); }

		if ( !class_exists($classLogLevel) ){ throw new \Exception("La clase {$classLogLevel} no existe"); }

		if ( empty($c->get('settings')['debugLogger']) )
		{ 
			throw new \Exception("No se encuentra la configuración del debugLogger"); 
		}

		$extension  = ( $c->get('settings')['debugLogger']['extension'] == NULL || trim($c->get('settings')['debugLogger']['extension']) === '' )? 'log' : $c->get('settings')['debugLogger']['extension'];

		$path		= ( $c->get('settings')['debugLogger']['path'] == NULL || trim($c->get('settings')['debugLogger']['path']) === '' )? 'logs/debug/': $c->get('settings')['debugLogger']['path'];

		$prefix 	= ( $c->get('settings')['debugLogger']['prefix'] == NULL || trim($c->get('settings')['debugLogger']['prefix']) === '' )? 'debug_' : $c->get('settings')['debugLogger']['prefix'];

		$config     = $c->get('settings')['debugLogger'];

		$logger 	= new $classLogger( $path, $classLogLevel::INFO, array (
		    'extension' => $extension,
		    'prefix'	=> $prefix
		));

		return $logger;

	}

];

?>
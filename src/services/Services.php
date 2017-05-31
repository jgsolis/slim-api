<?php

return [
	
	'logger' => function($c){

		if ( !isset($c) ){ throw new \Exception('No se pudo obtener el contenedor de dependencias'); }

		$classLogger   = '\\Katzgrau\\KLogger\\Logger';
		$classLogLevel = '\\Psr\\Log\\LogLevel';
		$config 	   = $c->get('settings');

		if ( !class_exists($classLogger) ) { throw new \Exception("La clase  {$classLogger}  no existe"); }

		if ( !class_exists($classLogLevel) ){ throw new \Exception("La clase {$classLogLevel} no existe"); }

		if ( !isset($config['logger']) ){ throw new \Exception("No se encuentra la configuración [logger] en el archivo [config.php]"); }

		if ( !is_array($config['logger']) ){ throw new \Exception("El elemento [logger] de [config.php] debe ser un arreglo"); }
		
		$extension  = ( empty($config['logger']['extension']) )? 'log' : $config['logger']['extension'];

		$path		= ( empty($config['logger']['path']) )? 'logs/': $config['logger']['path'];

		$prefix 	= ( empty($config['logger']['prefix']) )? 'app_' : $config['logger']['prefix'];

		$logger 	= new $classLogger( $path, $classLogLevel::INFO, array (
		    'extension' => $extension,
		    'prefix'	=> $prefix
		));

		return $logger;

	},

	'response' => function($c){

		$class = '\\SlimApi\\Responses\\JSONResponse';

		if ( !class_exists($class) ){ throw new \Exception("La clase {$class} no existe"); }

		$response = new $class();

		return $response;

	},

	'debugLogger' => function($c){

		if ( !isset($c) ){ throw new \Exception('No se pudo obtener el contenedor de dependencias'); }

		$classLogger   = '\\Katzgrau\\KLogger\\Logger';
		$classLogLevel = '\\Psr\\Log\\LogLevel';
		$config 	   = $c->get('settings');

		if ( !class_exists($classLogger) ) { throw new \Exception("La clase  {$classLogger}  no existe"); }

		if ( !class_exists($classLogLevel) ){ throw new \Exception("La clase {$classLogLevel} no existe"); }

		if ( !isset($config['debugLogger']) ){ throw new \Exception("No se encuentra la configuración [debugLogger] en el archivo [config.php]"); }

		if ( !is_array($config['debugLogger']) ){ throw new \Exception("El elemento [debugLogger] de [config.php] debe ser un arreglo"); }
		
		$extension  = ( empty($config['debugLogger']['extension']) )? 'log' : $config['debugLogger']['extension'];

		$path		= ( empty($config['debugLogger']['path']) )? 'logs/debug/': $config['debugLogger']['path'];

		$prefix 	= ( empty($config['debugLogger']['prefix']) )? 'debug_' : $config['debugLogger']['prefix'];

		$logger 	= new $classLogger( $path, $classLogLevel::INFO, array (
		    'extension' => $extension,
		    'prefix'	=> $prefix
		));

		return $logger;

	}

];

?>
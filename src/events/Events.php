<?php

return [

	'errorHandler' => function(){
		return function($request, $response, $ex){

			$path   	= $request->getUri()->getPath();
			$method 	= $request->getMethod();

			$data = [
				'code'  	  => 500,
				'codeMessage' => $response->getCodeMessage(500),
				'userMessage' => method_exists($ex, 'getUserMessage')? $ex->getUserMessage() : NULL ,
				'devMessage'  => method_exists($ex, 'getDevMessage')? $ex->getDevMessage() : NULL,
				'resource'	  => isset($path)? $path : NULL,
				'method'	  => isset($method)? $method : NULL
			];

			return $response->set(['error' => $data]);
	
		};
	},
	
	'notFoundHandler' => function(){

		return function($request, $response){

			$path   	= $request->getUri()->getPath();
			$method 	= $request->getMethod();
			
			$data = [
				'code'  	  => 404,
				'codeMessage' => $response->getCodeMessage(404),
				'userMessage' => 'Recurso no encontrado',
				'devMessage'  => 'No existe la ruta proporcionada, verifique la URI',
				'resource' 	  => isset($path)? $path : NULL,
				'method'	  => isset($method)? $method : NULL
			];

			return $response->set(['error' => $data]);

		};
	},

	'phpErrorHandler' => function(){

		return function($request, $response, $error){


			$path   	= $request->getUri()->getPath();
			$method 	= $request->getMethod();

			$data = [
				'code'  	  => 500,
				'codeMessage' => $response->getCodeMessage(500),
				'userMessage' => 'No se pudo completar la operación, intente nuevamente',
				'devMessage'  => isset($error)? $error : 'PHP Error',
				'resource'	  => isset($path)? $path : NULL,
				'method'	  => isset($method)? $method : NULL
			];

			return $response->set(['error' => $data]);

		};
		
	},

	'notAllowedHandler' => function(){
		return function($request, $response, $methods){
			
			$path   	= $request->getUri()->getPath();
			$method 	= $request->getMethod();

			$data = [
				'code'  	  => 405,
				'codeMessage' => $response->getCodeMessage(500),
				'userMessage' => 'No se pudo completar la operación, intente nuevamente',
				'devMessage'  => 'El método utilizado no está permitido para obtener el recurso',
				'resource'	  => isset($path)? $path : NULL,
				'method'	  => isset($method)? $method : NULL
			];

			return $response->set(['error' => $data]);

		};
	}

];

?>
<?php

namespace SlimApi;

use \SlimApi\Exceptions\HTTPException;

class Application
{

	private $APP_PATH;

	private $ROUTES_PATH;

	private $LIBS_PATH;

	private $config;

	private $services;

	private $events;

	private $container;

	private $app;


	public function __construct()
	{
		try
		{
			define('ROOT_PATH', dirname(dirname(dirname(dirname(__DIR__)))));

			$this->APP_PATH  	= ROOT_PATH	. DIRECTORY_SEPARATOR . 'app';

			$this->ROUTES_PATH 	= $this->APP_PATH 	. DIRECTORY_SEPARATOR . 'routes';

			$this->LIBS_PATH 	= $this->APP_PATH 	. DIRECTORY_SEPARATOR . 'libs';

			$this->registerConfig();

			$this->setDisplayPhpErrors();

			$this->registerContainer();

			$this->registerApplication();

			$this->registerServices();

			$this->registerEvents();

			$this->registerRoutes();

			$this->registerDatabases();

			
		}
		catch(\Exception $e)
		{
			
			echo $this->handleErrors($e);
			die;
		}

	}

	private function handleErrors($e)
	{
		$error = new \stdClass();

		$error->status  = 'ERROR';

		$error->count   = 1;

		$error->results = [
			'error' => [
				'code' => 500,
				'codeMessage' => 'Internal Server Error',
				'userMessage' => 'Servicio no disponible, intente de nuevo',
				'devMessage'  => $e->getMessage(),
				'resource' 	  => NULL,
				'method' 	  => NULL
			]
		];

		header('Access-Control-Allow-Origin: *');

		header('Content-Type: application/json');

		return $response = json_encode($error);
	}

	private function setDisplayPhpErrors()
	{

		if ( $this->config['settings']['displayPhpErrors'] )
		{
			ini_set('display_startup_errors', 1);
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		}
		else
		{
			ini_set('display_startup_errors', 0);
			ini_set('display_errors', 0);
			error_reporting(0);
		}

	}

	private function registerDatabases()
	{
		$c = $this->app->getContainer();
		$databases = $c->get('settings')['databases'];

		foreach ( $databases as $db )
		{
			$c[$db['database']] = function ($c) use ($db) {

		        $pdo = new \PDO($db['driver'] . ":host=" . $db['host'] . ";dbname=" . $db['database'], $db['username'], $db['password']);
		        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		        return $pdo;
		        
		    };
		}
	}

	private function registerEvents()
	{
		if ( $this->loadEvents() )
		{
			$c = $this->app->getContainer();

			$c['errorHandler'] 	  	= $this->events['errorHandler'];

			$c['notFoundHandler'] 	= $this->events['notFoundHandler'];

			$c['phpErrorHandler'] 	= $this->events['phpErrorHandler'];

			$c['notAllowedHandler'] = $this->events['notAllowedHandler'];

		}
	}

	private function loadEvents()
	{
		$file = __DIR__ . DIRECTORY_SEPARATOR . "events" . DIRECTORY_SEPARATOR . "Events.php";

		if ( !file_exists($file) )
		{
			throw new \Exception('No ese encuentra el archivo de eventos');
		}

		if ( $this->events = require_once $file ) 
			return true;
		else
			return false;
	}

	private function registerServices()
	{
		
		if ( $this->loadServices() )
		{
			$c = $this->app->getContainer();

			$c['logger'] = $this->services['logger'];

			$c['jwt']  	 = $this->services['jwt'];

			$c['response'] = $this->services['response'];

			$c['debugLogger'] = $this->services['debugLogger'];

		}

	}

	private function loadServices()
	{
		$file = __DIR__ . DIRECTORY_SEPARATOR . "services" . DIRECTORY_SEPARATOR . "Services.php";

		if ( !file_exists($file) )
		{
			throw new \Exception('No ese encuentra el archivo de servicios');
		}

		if ( $this->services = require_once $file ) 
			return true;
		else
			return false;
	}


	private function registerRoutes()
	{

		$file = $this->ROUTES_PATH . DIRECTORY_SEPARATOR . 'Routes.php';

		if ( !file_exists($file) )
		{
			throw new HTTPException( 'No se pudo realizar la operación. Intente de nuevo.', 
									 'El archivo de rutas [/routes/routes.php] no existe, favor de verificar', 
									  500 );
		}

		require_once $file;

	}

	private function registerContainer()
	{
		$this->container = new \Slim\Container($this->config);
	}

	private function registerApplication()
	{
		$this->app = new \Slim\App($this->container);
	}

	private function registerConfig()
	{
		$file = ROOT_PATH . "/config.php";

		if ( !file_exists($file) )
		{
			throw new \Exception('No se encuentra su config.php');
		}
		
		$this->config = require_once $file;
		
	}

	public function run()
	{
		$this->app->run();
	}
}

?>
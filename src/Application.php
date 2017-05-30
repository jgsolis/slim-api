<?php

namespace SlimApi;

use \SlimApi\Exceptions\HTTPException;

class Application
{
	private $ROOT_PATH;

	private $APP_PATH;

	private $ROUTES_PATH;

	private $config;

	private $services;

	private $events;

	private $container;

	private $app;


	public function __construct($app)
	{
		try
		{
			$this->ROOT_PATH    = dirname(dirname(dirname(dirname(__DIR__))));

			$this->APP_PATH  	= $this->ROOT_PATH . DIRECTORY_SEPARATOR . $app;

			$this->ROUTES_PATH 	= $this->APP_PATH . DIRECTORY_SEPARATOR . 'routes';

			$this->loadConfig();

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
	/*------------------------------------------------*/
	/*- Carga el archivo de configuración config.php  */
	/*------------------------------------------------*/
	private function loadConfig()
	{
		$file = "{$this->ROOT_PATH}/config.php";

		if ( !file_exists($file) )
		{
			throw new \Exception('No se encuentra el archivo de configuración config.php');
		}
		
		$this->config = require_once $file;
	}

	/*-------------------------------------------------*/
	/*- se configura si se muestran los errores de php */
	/*-------------------------------------------------*/
	private function setDisplayPhpErrors()
	{
		if ( !isset($this->config) || empty($this->config['settings']) )
		{
			throw new \Exception('Se necesita el archivo de configuración config.php');
		}

		if ( !isset($this->config['settings']['displayPhpErrors']) || trim($this->config['settings']['displayPhpErrors']) === '' )
		{
			throw new \Exception('Necesita configurar la opción displayPhpErrors en el archivo config.php');
		}

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

	/*-------------------------------------------------*/
	/*- se crea el contenedor de dependencias          */
	/*-------------------------------------------------*/	
	private function registerContainer()
	{
		if ( !isset($this->config) )
		{
			throw new \Exception('Verifique que exista el archivo config.php');
		}

		$class = '\\Slim\\Container';

		if ( !class_exists($class) )
		{
			throw new \Exception("No existe la clase {$class}");
		}
		$this->container = new $class($this->config);
	}

	/*-------------------------------------------------*/
	/*- se crea la aplicación Slim                     */
	/*-------------------------------------------------*/	
	private function registerApplication()
	{
		if ( !isset($this->container) )
		{
			throw new \Exception('El contenedor de dependencias no ha sido creado');
		}

		$class = '\\Slim\\App';

		if ( !class_exists($class) )
		{
			throw new \Exception("No existe la clase {$class}");
		}

		$this->app = new $class($this->container);
	}

	/*-------------------------------------------------*/
	/*- se registran los servicios                     */
	/*-------------------------------------------------*/	
	private function registerServices()
	{
		if ( !isset($this->app) )
		{
			throw new \Exception('No se ha creado una instancia de Slim App');
		}

		if ( $this->app->getContainer() == NULL )
		{
			throw new \Exception('No se encuentra el contenedor de dependencias');
		}
		
		if ( $this->loadServices() )
		{
			$c = $this->app->getContainer();

			$c['logger'] = $this->services['logger'];

			$c['response'] = $this->services['response'];

			$c['debugLogger'] = $this->services['debugLogger'];

		}
	}

	/*-------------------------------------------------*/
	/*- se carga el archivo de servicios               */
	/*-------------------------------------------------*/	
	private function loadServices()
	{
		$file = __DIR__ . DIRECTORY_SEPARATOR . "services" . DIRECTORY_SEPARATOR . "Services.php";

		if ( !file_exists($file) )
		{
			throw new \Exception('No ese encuentra el archivo services/Services.php');
		}

		if ( $this->services = require_once $file ) 
			return true;
		else
			return false;
	}

	/*-------------------------------------------------*/
	/*- se registran los manejadores de eventos        */
	/*-------------------------------------------------*/	
	private function registerEvents()
	{
		if ( !isset($this->app) )
		{
			throw new \Exception('No se ha creado una instancia de Slim App');
		}

		if ( $this->app->getContainer() == NULL )
		{
			throw new \Exception('No se encuentra el contenedor de dependencias');
		}
		
		if ( $this->loadEvents() )
		{
			$c = $this->app->getContainer();

			$c['errorHandler'] 	  	= $this->events['errorHandler'];

			$c['notFoundHandler'] 	= $this->events['notFoundHandler'];

			$c['phpErrorHandler'] 	= $this->events['phpErrorHandler'];

			$c['notAllowedHandler'] = $this->events['notAllowedHandler'];

		}
	}

	/*-------------------------------------------------*/
	/*- se carga el archivo de eventos                 */
	/*-------------------------------------------------*/
	private function loadEvents()
	{
		$file = __DIR__ . DIRECTORY_SEPARATOR . "events" . DIRECTORY_SEPARATOR . "Events.php";

		if ( !file_exists($file) )
		{
			throw new \Exception('No ese encuentra el archivo events/Events.php');
		}

		if ( $this->events = require_once $file ) 
			return true;
		else
			return false;
	}

	/*-------------------------------------------------*/
	/*- se carga el archivo de rutas                   */
	/*-------------------------------------------------*/
	private function registerRoutes()
	{
		$file = $this->ROUTES_PATH . DIRECTORY_SEPARATOR . 'Routes.php';

		if ( !file_exists($file) )
		{
			throw new \Exception('El archivo /routes/Routes.php no existe, favor de verificar');
		}

		require_once $file;
	}

	/*-------------------------------------------------*/
	/*- se cargan las bases de datos                   */
	/*-------------------------------------------------*/
	private function registerDatabases()
	{
		if ( !isset($this->app) )
		{
			throw new \Exception('No se ha creado una instancia de Slim App');
		}

		if ( $this->app->getContainer() == NULL )
		{
			throw new \Exception('No se ha creado el contenedor de dependencias');
		}

		if ( $this->app->getContainer()->get('settings') == NULL )
		{
			throw new \Exception('Error al obtener la configuración del archivo config.php');
		}

		if ( $this->app->getContainer()->get('settings')['databases'] == NULL )
		{
			throw new \Exception('Error al obtener la configuración de la bases de datos');
		}

		$c 		= $this->app->getContainer();
		$dbs 	= $c->get('settings')['databases'];

		foreach ( $dbs as $db )
		{
			if ( empty($db['database']) || empty($db['driver']) || empty($db['host']) || empty($db['username']) )
			{
				throw new \Exception('Error al obtener la configuración de la base de datos');
			}

			$c[$db['database']] = function ($c) use ($db) {

		        $pdo = new \PDO($db['driver'] . ":host=" . $db['host'] . ";dbname=" . $db['database'], $db['username'], $db['password']);
		        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
		        return $pdo;
		        
		    };
		}
	}

	/*-------------------------------------------------*/
	/*- Se manejan errores que no generan excepciones  */
	/*-------------------------------------------------*/
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

	/*-------------------------------------------------*/
	/*- Run app 									   */
	/*-------------------------------------------------*/	
	public function run()
	{
		$this->app->run();
	}

}


?>
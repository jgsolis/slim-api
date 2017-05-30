<?php

namespace SlimApi\Exceptions;


class HTTPException extends \Exception
{


	private $userMessage;

	private $devMessage;

	private $httpCode;

	public function __construct( $userMessage = '', $devMessage = '', $code = NULL )
	{
		parent::__construct($userMessage);

		$this->userMessage = $userMessage;

		$this->devMessage  = $devMessage;

		$this->httpCode    = $code;
	}

	public function getUserMessage()
	{
		return $this->userMessage;
	}

	public function getDevMessage()
	{
		return $this->devMessage;
	}



}

?>
<?php 

namespace Wormhole;

use Wormhole\behaviors\ClientAuthInspector;
use Wormhole\lib\behaviors\BehaviorManager;
use Pest;
use PestXML;
use PestJSON;

class LMSAPI
{
	private $url;
	private $behaviorManager;
	
	private $language;
	private $replyFormat;

	private $apiSecretKey;
	private $apiAccessKey;
	
	private $client = null;
	private $lastResponse = null;
	
	public $debug;
	
	function __construct($apiSecretKey, $apiAccessKey, 
		$language=Config::LANGUAGE_DEFAULT, $replyFormat=Config::REPLYFORMAT_DEFAULT) 
	{
		$this->url = Config::API_URL;
		$this->debug = Config::DEBUG;
		
		$this->apiSecretKey = $apiSecretKey;
		$this->apiAccessKey = $apiAccessKey;
		$this->language = $language;
		$this->replyFormat = $replyFormat;
		
		// Initialize REST Client
		$this->client = $this->createRestClient();
		
		// Initialize Behaviours
		$this->behaviorManager = $this->createBehaviorManager();
	}
	
	public function getSecretKey() 
	{
		return $this->apiSecretKey;
	}
	
	public function getAccessKey() 
	{
		return $this->apiAccessKey;
	}
  
	/*
	 * Invokes an API's method with given parameters.
	 * @param $method string API's method name.
	 * @param $params array Associative array of method parameters (optional).
	 * @param $file array Associative array with data of file to be sent (optional).
	 *		This array must have two keys, one named "path" or "tmp_name" with file's full path and other named "type" with its Content-Type.
	 *		You can use $_FILES php variable directly.
	 */
	public function call($method, $params=null, $file=null)
	{
		// Add language header
		$this->addHeader("Accept-Language", $this->language);
		
		$this->behaviorManager->beforeSendRequest($this, $method, $params, $file);
		$methodWithParams = empty($params) ? $method : $method."?".http_build_query($params);
		if(empty($file))
			$this->lastResponse = $this->client->get($methodWithParams);
		else
			$this->lastResponse = $this->client->post($methodWithParams, array(), array(), $file);
			
		$this->behaviorManager->afterReceiveReply($this, $method, $params, $file);
		
		return $this->lastResponse;
	}
	
	public function addHeader($name, $value)
	{
		if(isset($this->client->curl_opts[CURLOPT_HTTPHEADER]) == false)
			$this->client->curl_opts[CURLOPT_HTTPHEADER] = array();
			
		$this->client->curl_opts[CURLOPT_HTTPHEADER][] = ($name . ": " . $value);
	}

	public function debug($title, $value)
	{
		if($this->debug)
			echo ("*DEBUG* " . $title . ": " . $value . "<br/>");
	}
	
	private function createRestClient() 
	{
		switch($this->replyFormat) {
			case LMSAPI_ReplyFormat::XML:
				return new PestXML($this->url);
				break;
			case LMSAPI_ReplyFormat::JSON:
				return new PestJSON($this->url);
				break;
			default:
				throw new Exception("Unsupported ReplyFormat");
		}
	}
	
	private function createBehaviorManager() 
	{
		$inspectors = array();
		
		$inspectors[] = new ClientAuthInspector();
		$response 	  = new BehaviorManager($inspectors)

		return $response;
	}
}
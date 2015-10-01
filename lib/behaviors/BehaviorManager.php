<?php

namespace Wormhole\lib\behaviors;

class BehaviorManager
{
	private $inspectors;
	
	/*
	 * @param array $inspectors List of IClientInspectors
	 */
	function __construct($inspectors = array()) 
	{
		$this->inspectors = $inspectors;
	}

	public function beforeSendRequest($api, $method, $params, $file) {
		foreach($this->inspectors as $inspector)
			$inspector->beforeSendRequest($api, $method, $params, $file);
	}
  
	public function afterReceiveReply($api, $method, $params, $file) {
		foreach($this->inspectors as $inspector)
			$inspector->afterReceiveReply($api, $method, $params, $file);
	}
}
<?php 

namespace Wormhole\behaviors;

use Wormhole\lib\behaviors\IClientInspector.php';

class ClientAuthInspector implements IClientInspector
{
	public function beforeSendRequest($api, $method, $params, $file) {
		// Add authorization header
		$api->addHeader("Authorization", $this->getAuthenticationSignatureHeader($api, $params));
	}
	
	public function afterReceiveReply($api, $method, $params, $file) {}

	private function getAuthenticationSignatureHeader($api, $params)
	{
		// Normalize params in a string
		$normParams = $this->normalizeRequestParameters($params);
		$api->debug("Normalized params",$normParams);
		
		// Create a signed version of the normalized query parameters using the secret key
		$signature = base64_encode(pack('H*', 
			hash_hmac("sha1" , $normParams, $api->getSecretKey())
		));

		// We construct the Authorization header to send in the request using the shared api access key 
		// and the signature made with the secret key. Finally we add the header value to the request.
		$headerSignature = sprintf("%s:%s", $api->getAccessKey(), $signature); 
		$api->debug("Header Signature",$headerSignature);
		
		return $headerSignature;
	}

	/// Normalizes a sequence of key/value pair parameters as per the OAuth core specification.
	private function normalizeRequestParameters($parameters)
	{
		$keys = array_keys($parameters);
		natcasesort($keys);
		$orderedParameters = array();
		foreach($keys as $key){
			$orderedParameters[$key] = $parameters[$key];
		}
		//ksort($parameters);
		return http_build_query($orderedParameters);
	}
}
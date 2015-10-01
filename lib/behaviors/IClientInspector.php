<?php

namespace Wormhole\lib\behaviors;

interface IClientInspector
{
	public function beforeSendRequest($api, $method, $params, $file);
	public function afterReceiveReply($api, $method, $params, $file);
}
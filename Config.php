<?php 

namespace Wormhole;

abstract class Config
{
	//const API_URL = "http://wit.local/wlms.services/LMSAPI.svc/API/";
	const API_URL = "http://services.lms.wormholeit.com/LMSAPI.svc/API/";
	
	const DEBUG = false;
	
	const LANGUAGE_DEFAULT 		= LMSAPI_Language::EN_US;
	const REPLYFORMAT_DEFAULT 	= LMSAPI_ReplyFormat::JSON;
}
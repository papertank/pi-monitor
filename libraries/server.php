<?php namespace Monitor\Libraries;

class Server {

       public static function online($domain, $port = 80)
       {
       	if ( strpos($domain, 'http') === false )
       	{
       		$domain = 'http://'.$domain;
       	}

       	//check, if a valid url is provided
              if(!filter_var($domain, FILTER_VALIDATE_URL))
              {
       			return false;
              }

              //initialize curl
              $curlInit = curl_init($domain);
              curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
              curl_setopt($curlInit,CURLOPT_PORT,$port);
              curl_setopt($curlInit,CURLOPT_HEADER,true);
              curl_setopt($curlInit,CURLOPT_NOBODY,true);
              curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

              //get answer
              $response = curl_exec($curlInit);

              curl_close($curlInit);

              if ($response) return true;

              return false;

       }

}
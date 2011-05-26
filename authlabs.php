<?php

/**
 * Authority Labs Partner API client interface.
 *
 * @category Services
 * @package  Authority Labs Partner API PHP Library
 * @author   Jonathan Kressaty <jonathan.kressaty@gmail.com>
 * @license  http://creativecommons.org/licenses/MIT/ MIT
 * @link     https://github.com/kressaty/Authority-Labs-Partner-API-PHP-Library
 */
 
class Authoritylabspartner{

	/**
	 * POST to the resource at the specified path.
	 *
	 * @param string $keyword   	Keyword to query against
	 * @param string  $domain		Domain to check for keyword placement
	 * @param string  $auth_token	Authority Labs Partner Auth Token
	 * @param string  $engine		OPTIONAL Search engine to query
	 * @param string  $locale		OPTIONAL Language/Country code
	 * @param string  $pages_from	OPTIONAL Default is false and only works with Google
	 *
	 * @return object Returns formatted result
	 */

	public function partnerKeyword($keyword, $domain, $auth_token, $engine="google", $locale="en-US",$pages_from="false",$callback=null)
	{
		$subdomain = 'api';
		$path = 'keywords/';
		$method = 'POST';
		$vars = array(
			'auth_token' => $auth_token,
			'keyword' => $keyword,
			'engine' => $engine,
			'locale' => $locale,
			'pages_from' => $pages_from,
			
		);
		
		if($callback!=null)
		{
			$vars['callback'] = $callback;
		}
		
		return $this->_request($path, $domain, $subdomain, $method, $vars);
	}
	
	/**
	 * POST to the resource at the specified path.
	 *
	 * @param string $keyword   	Keyword to query against
	 * @param string  $domain		Domain to check for keyword placement
	 * @param string  $auth_token	Authority Labs Partner Auth Token
	 * @param string  $engine		OPTIONAL Search engine to query
	 * @param string  $locale		OPTIONAL Language/Country code
	 * @param string  $pages_from	OPTIONAL Default is false and only works with Google
	 *
	 * @return object Returns formatted result
	 */
	
	public function priorityPartnerKeyword($keyword, $domain, $auth_token, $engine="google", $locale="en-US",$pages_from="false",$callback=null)
	{
		$subdomain = 'api';
		$path = 'keywords/priority/';
		$method = "POST";
		$vars = array(
			'auth_token' => $auth_token,
			'keyword' => $keyword,
			'engine' => $engine,
			'locale' => $locale,
			'pages_from' => $pages_from,
			
		);
		
		if($callback!=null)
		{
			$vars['callback'] = $callback;
		}
		return $this->_request($path, $domain, $subdomain, $method="POST", $vars);
	}
	
	/**
	 * POST to the resource at the specified path.
	 *
	 * @param string $keyword   	Keyword to query against
	 * @param string  $domain		Domain to check for keyword placement
	 * @param string  $auth_token	Authority Labs Partner Auth Token
	 * @param string  $engine		OPTIONAL Search engine to query
	 * @param string  $locale		OPTIONAL Language/Country code
	 * @param string  $pages_from	OPTIONAL Default is false and only works with Google
	 *
	 * @return object Returns formatted result
	 */
	
	public function getPartnerKeyword($keyword, $domain, $auth_token, $engine="google", $locale="en-US",$pages_from="false",$callback=null)
	{
		$subdomain = 'api';
		$path = 'keywords/get';
		$method = "GET";
		$vars = array(
			'auth_token' => $auth_token,
			'keyword' => $keyword,
			'engine' => $engine,
			'locale' => $locale,
			'pages_from' => $pages_from,
			
		);
		
		if($callback!=null)
		{
			$vars['callback'] = $callback;
		}
		
		return $this->_request($path, $domain, $subdomain, $method, $vars);
		
	}

	private function _request($path, $domain, $subdomain, $method = "POST", $vars = array()) 
	{
	
		$endpoint = "http://$subdomain.authoritylabs.com/";
        $encoded = "";
        foreach($vars AS $key=>$value)
            $encoded .= "$key=".urlencode($value)."&";
        $encoded = substr($encoded, 0, -1);
        $tmpfile = "";
        $fp = null;
        
        // construct full url
        $url = $endpoint.$path;
        
        // if GET and vars, append them
        if($method == "GET") 
            $url .= (FALSE === strpos($path, '?')?"?":"&").$encoded;

        // initialize a new curl object            
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
       // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        switch(strtoupper($method)) {
            case "GET":
                curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
                break;
            case "POST":
                curl_setopt($curl, CURLOPT_POST, TRUE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
                break;
            case "PUT":
                // curl_setopt($curl, CURLOPT_PUT, TRUE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                file_put_contents($tmpfile = tempnam("/tmp", "put_"),
                    $encoded);
                curl_setopt($curl, CURLOPT_INFILE, $fp = fopen($tmpfile,
                    'r'));
                curl_setopt($curl, CURLOPT_INFILESIZE, 
                    filesize($tmpfile));
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
               return "Unknown method $method";
                break;
        }
        
       
        
        // do the request. If FALSE, then an exception occurred    
        if(FALSE === ($result = curl_exec($curl)))
           
           return "Curl failed with error " . curl_error($curl);
        
        // get result code
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // unlink tmpfiles
        if($fp)
            fclose($fp);
        if(strlen($tmpfile))
            unlink($tmpfile);
            
        $final_data->response_code = $responseCode;
        if($result == "OK")
        {
        	$final_data->result = $result;
        }
        else
        {
        	$final_data->result = json_decode($result);
        }

        return $this->_parsed_response($final_data, $domain);
    }
    
    private function _parsed_response($final_data, $domain)
    {
    	if($final_data->response_code != 200)
    	{
    		$final_data->error = 'Data Unavailable';
    		return $final_data;
    	}
    	else
    	{
    		if(isset($final_data->result) && is_object($final_data->result))
    		{
    			$serps = get_object_vars($final_data->result->serp);
    			
    			foreach($serps as $k=>$v)
    			{
    				if($v->base_url == $this->_stripit($domain))
    				{
    					$v->rank = $k;
    					$final_data->domain_rank = $serps[$k];
    					return $final_data;
    				}
    			}
    			
    			$final_data->rank_unavailable = 'Rank not found in result set';
    			
    		}
    		return $final_data;
    	}
    		
    }
    
    private function _stripit($url) 
    { 
       $url = trim($url);
       $url = preg_replace("/^(http:\/\/)*(www.)*/is", "", $url); 
       $url = preg_replace("#/$#" , "" ,$url); 
       return $url; 
    }
    
}
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
class Authoritylabspartner {

	public $endpoint = 'http://api.authoritylabs.com/';

	/**
	 * POST to the resource at the specified path.
	 *
	 * @param string $keyword   	Keyword to query against
	 * @param string  $auth_token	Authority Labs Partner Auth Token
	 * @param string  $engine		OPTIONAL Search engine to query
	 * @param string  $locale		OPTIONAL Language/Country code
	 * @param string  $pages_from	OPTIONAL Default is false and only works with Google
	 *
	 * @return object Returns formatted result
	 */
	public function partnerKeyword($keyword, $auth_token, $engine = "google", $locale = "en-US",$pages_from = "false",$callback = null)
	{
		$path = 'keywords/';
		$method = 'POST';
		$vars = array(
			'auth_token' => $auth_token,
			'keyword' => $keyword,
			'engine' => $engine,
			'locale' => $locale,
			'pages_from' => $pages_from,
		);

		if ($callback != null)
		{
			$vars['callback'] = $callback;
		}

		return $this->_request($path, $this->endpoint, $method, $vars);
	}

	/**
	 * POST to the resource at the specified path.
	 *
	 * @param string $keyword   	Keyword to query against
	 * @param string  $auth_token	Authority Labs Partner Auth Token
	 * @param string  $engine		OPTIONAL Search engine to query
	 * @param string  $locale		OPTIONAL Language/Country code
	 * @param string  $pages_from	OPTIONAL Default is false and only works with Google
	 *
	 * @return object Returns formatted result
	 */
	public function priorityPartnerKeyword($keyword, $auth_token, $engine = "google", $locale = "en-US", $pages_from = "false", $callback = null)
	{
		$path = 'keywords/priority/';
		$method = "POST";
		$vars = array(
			'auth_token' => $auth_token,
			'keyword' => $keyword,
			'engine' => $engine,
			'locale' => $locale,
			'pages_from' => $pages_from,
		);

		if ($callback != null)
		{
			$vars['callback'] = $callback;
		}

		return $this->_request($path, $this->endpoint, $method = "POST", $vars);
	}

	/**
	 * POST to the resource at the specified path.
	 *
	 * @param string $keyword   	Keyword to query against
	 * @param string  $url   		URL being queried for ranking
	 * @param string  $auth_token	Authority Labs Partner Auth Token
	 * @param string  $engine		OPTIONAL Search engine to query
	 * @param string  $locale		OPTIONAL Language/Country code
	 * @param string  $pages_from	OPTIONAL Default is false and only works with Google
	 *
	 * @return object Returns formatted result
	 */
	public function getPartnerKeyword($keyword, $url, $auth_token, $engine = "google", $locale = "en-US", $pages_from = "false")
	{
		$path = 'keywords/get';
		$method = "GET";
		$vars = array(
			'auth_token' => $auth_token,
			'keyword' => $keyword,
			'engine' => $engine,
			'locale' => $locale,
			'pages_from' => $pages_from,
		);

		$result = $this->_request($path, $this->endpoint, $method, $vars);

		if (isset($result->result))
		{
			return $this->_parseit($url, $result->result);
		}
		else
		{
			return 'serps not available';
		}

	}

	/**
	 * Interpret SERP for specified URL from given JSON data.
	 *
	 * @param string  $url   		URL being queried for ranking
	 * @param string  $json_url		URL for JSON response from Authority Labs
	 * @param string  $auth_token	Authority Labs Partner Auth Token
	 *
	 * @return array Returns formatted result
	 */
	public function parseRanks($url, $json_url, $auth_token)
	{
		$arr_rankings = '';

		$json_result = $this->_request('', $json_url, 'GET', array('auth_token' => $auth_token));

		$json_data = $json_result->result;

		$arr_rankings = $this->_parseit($url, $json_data);

		return $arr_rankings;
	}

	private function _parseit($url, $json_data)
	{
		$url = str_ireplace('http://','',$url);

		if (is_object($json_data)) {

			$serp = get_object_vars($json_data->serp);

			$arr_rankings = array();

			foreach ($serp as $key => $val) {

				$match = $val->href;

				if (stristr($match, '.' . $url))
					$arr_rankings[$key] = $val->href;
					//$arr_rankings[] = $key;

				if (stristr($match, '/' . $url))
					$arr_rankings[$key] = $val->href;
					//$arr_rankings[] = $key;
			}

			ksort($arr_rankings);

			return $arr_rankings;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Send the CURL request
	 * @param  string $path     Path to request
	 * @param  string $endpoint URL endpoint
	 * @param  string $method   HTTP Method
	 * @param  array  $vars     Variables to include in the request
	 * @return array            Response data
	 */
	private function _request($path, $endpoint, $method = "POST", $vars = array())
	{
		$encoded = "";
		foreach ($vars AS $key => $value)
			$encoded .= "$key=".urlencode($value)."&";
		$encoded = substr($encoded, 0, -1);
		$tmpfile = "";
		$fp = null;

		// construct full url
		$url = $endpoint . $path;

		// if GET and vars, append them
		if ($method == "GET")
			$url .= (FALSE === strstr($url, '?') ? "?" : "&") . $encoded;

		// initialize a new curl object
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		// curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
		switch (strtoupper($method)) {
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
		if (FALSE === ($result = curl_exec($curl)))

			return "Curl failed with error " . curl_error($curl);

		// get result code
		$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		// unlink tmpfiles
		if ($fp)
			fclose($fp);
		if (strlen($tmpfile))
			unlink($tmpfile);

		$final_data->response_code = $responseCode;
		if ($result == "OK")
		{
			$final_data->result = $result;
		}
		else
		{
			$final_data->result = json_decode($result);
		}

		return $final_data;
	}

}
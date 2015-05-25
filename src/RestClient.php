<?php

namespace pavelmics\SypexGeoIp;

/**
 * Class RestClient
 * It is simply REST API client for sypex geo ip service
 *
 * @link https://sypexgeo.net/ru/api/
 */
class RestClient
{
	/**
	 * Sypex JSON API url
	 * @var string
	 */
	protected $_apiUrl = 'http://api.sypexgeo.net/';

	/**
	 * Key for REST api.
	 * You can get your key in your account information at @link http://sypexgeo.net/
	 * @var string|null
	 */
	protected $_apiKey = null;

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $_client;

	/**
	 * Last error
	 * @var bool|array
	 */
	public $lastError = false;

	/**
	 * @constructor
	 * @param array $params
	 */
	public function __construct(array $params = [])
	{
		$this->_client = new \GuzzleHttp\Client();

		isset($params['key'])
			? $this->_apiKey = $params['key']
			: null;
	}

	/**
	 * Returns data provided by sypexgeo service
	 *
	 * @param $ip
	 * @return array
	 */
	public function get($ip)
	{
		$url = $this->_getApiUrl() . $ip;
		$jsonResult = $this->_req($url);

		return $jsonResult
			? json_decode($jsonResult, true)
			: false;
	}

	/**
	 * Builds API url
	 *
	 * @return string
	 */
	protected function _getApiUrl()
	{
		empty($this->_apiKey)
			? $path = 'json/'
			: $path = $this->_apiKey . '/json/';

		return $this->_apiUrl . $path;
	}

	/**
	 * Makes http GET request
	 *
	 * @param $url
	 * @return string|bool
	 */
	protected function _req($url)
	{
		try {
			$result = $this->_client->get($url);
		} catch (\GuzzleHttp\Exception\TransferException $e) {
			$result = false;
			$this->lastError = [
				'errorType' => 'http_client_error',
				'request' => method_exists($e, 'getRequest') ? $e->getRequest() : '',
			];
		}

		return $result->getBody();
	}
}
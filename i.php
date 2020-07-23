<?php
/**
 * Plugin Name: wp_redirect_register
 * Description:       autherize ur user by otp 
 * Version:           1.0
 * Author:            umesh
 * @license           GPL-2.0-or-later
 * 
 */

defined( 'ABSPATH' ) or die( 'No script !' );

/**
	 * Send an SMS to one or more comma separated numbers
	 * @param       $numbers
	 * @param       $message
	 * @param       $sender
	 * @param null  $sched
	 * @param false $test
	 * @param null  $receiptURL
	 * @param numm  $custom
	 * @param false $optouts
	 * @param false $simpleReplyService
	 * @return array|mixed
	 * @throws Exception
	 */

	public function sendSms($numbers, $message, $sender, $sched = null, $test = false, $receiptURL = null, $custom = null, $optouts = false, $simpleReplyService = false)
	{

		if (!is_array($numbers))
			throw new Exception('Invalid $numbers format. Must be an array');
		if (empty($message))
			throw new Exception('Empty message');
		if (empty($sender))
			throw new Exception('Empty sender name');
		if (!is_null($sched) && !is_numeric($sched))
			throw new Exception('Invalid date format. Use numeric epoch format');

		$params = array(
			'message'       => rawurlencode($message),
			'numbers'       => implode(',', $numbers),
			'sender'        => rawurlencode($sender),
			'schedule_time' => $sched,
			'test'          => $test,
			'receipt_url'   => $receiptURL,
			'custom'        => $custom,
			'optouts'       => $optouts,
			'simple_reply'  => $simpleReplyService
		);

		return $this->_sendRequest('send', $params);
	}


	/**
	 * Send an SMS to a Group of contacts - group IDs can be retrieved from getGroups()
	 * @param       $groupId
	 * @param       $message
	 * @param null  $sender
	 * @param false $test
	 * @param null  $receiptURL
	 * @param numm  $custom
	 * @param false $optouts
	 * @param false $simpleReplyService
	 * @return array|mixed
	 * @throws Exception
	 */
	public function sendSmsGroup($groupId, $message, $sender = null, $sched = null, $test = false, $receiptURL = null, $custom = null, $optouts = false, $simpleReplyService = false)
	{

		if (!is_numeric($groupId))
			throw new Exception('Invalid $groupId format. Must be a numeric group ID');
		if (empty($message))
			throw new Exception('Empty message');
		if (empty($sender))
			throw new Exception('Empty sender name');
		if (!is_null($sched) && !is_numeric($sched))
			throw new Exception('Invalid date format. Use numeric epoch format');

		$params = array(
			'message'       => rawurlencode($message),
			'group_id'      => $groupId,
			'sender'        => rawurlencode($sender),
			'schedule_time' => $sched,
			'test'          => $test,
			'receipt_url'   => $receiptURL,
			'custom'        => $custom,
			'optouts'       => $optouts,
			'simple_reply'  => $simpleReplyService
		);

		return $this->_sendRequest('send', $params);
	}


	/**
	 * Send an MMS to a one or more comma separated contacts
	 * @param       $numbers
	 * @param       $fileSource - either an absolute or relative path, or http url to a file.
	 * @param       $message
	 * @param null  $sched
	 * @param false $test
	 * @param false $optouts
	 * @return array|mixed
	 * @throws Exception
	 */
	public function sendMms($numbers, $fileSource, $message, $sched = null, $test = false, $optouts = false)
	{

        class Textlocal
{
	private $username;
	private $hash;
	private $apiKey;

	private $errorReporting = false;

	public $errors = array();
	public $warnings = array();

	public $lastRequest = array();

	/**
	 * Instantiate the object
	 * @param $username
	 * @param $hash
	 */
	function __construct($username, $hash, $apiKey = false)
	{
		$this->username = $username;
		$this->hash = $hash;
		if ($apiKey) {
			$this->apiKey = $apiKey;
		}

	}

	/**
	 * Private function to construct and send the request and handle the response
	 * @param       $command
	 * @param array $params
	 * @return array|mixed
	 * @throws Exception
	 * @todo Add additional request handlers - eg fopen, file_get_contacts
	 */
	private function _sendRequest($command, $params = array())
	{
		if ($this->apiKey && !empty($this->apiKey)) {
			$params['apiKey'] = $this->apiKey;

		} else {
			$params['hash'] = $this->hash;
		}
		// Create request string
		$params['username'] = $this->username;

		$this->lastRequest = $params;

		if (self::REQUEST_HANDLER == 'curl')
			$rawResponse = $this->_sendRequestCurl($command, $params);
		else throw new Exception('Invalid request handler.');

		$result = json_decode($rawResponse);
		if (isset($result->errors)) {
			if (count($result->errors) > 0) {
				foreach ($result->errors as $error) {
					switch ($error->code) {
						default:
							throw new Exception($error->message);
					}
				}
			}
		}

		return $result;
	}


		if (!is_array($numbers))
			throw new Exception('Invalid $numbers format. Must be an array');
		if (empty($message))
			throw new Exception('Empty message');
		if (empty($fileSource))
			throw new Exception('Empty file source');
		if (!is_null($sched) && !is_numeric($sched))
			throw new Exception('Invalid date format. Use numeric epoch format');

		$params = array(
			'message'       => rawurlencode($message),
			'numbers'       => implode(',', $numbers),
			'schedule_time' => $sched,
			'test'          => $test,
			'optouts'       => $optouts
		);

		/** Local file. POST to service */
		if (is_readable($fileSource))
			$params['file'] = '@' . $fileSource;
		else $params['url'] = $fileSource;

		return $this->_sendRequest('send_mms', $params);
	}

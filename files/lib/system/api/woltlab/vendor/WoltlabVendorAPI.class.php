<?php
namespace wcf\system\api\woltlab\vendor;
use wcf\system\exception\HTTPServerErrorException;
use wcf\system\exception\HTTPUnauthorizedException;
use wcf\system\exception\SystemException;
use wcf\system\io\RemoteFile;
use wcf\system\SingletonFactory;
use wcf\util\HTTPRequest;
use wcf\util\JSON;

/**
 * Easy to use class for the WoltLab GmbH vendor API in version 1.2.
 * 
 * @author	Dennis Kraffczyk, Matthias Kittsteiner
 * @copyright	2021 KittMedia
 * @license	LGPL <http://www.gnu.org/licenses/lgpl.html>
 * @package	com.kittmedia.wcf.woltlabapi
 */
class WoltlabVendorAPI extends SingletonFactory {
	/**
	 * API url
	 * @var		string
	 */
	const API_URL_VENDOR_CUSTOMER = 'https://api.woltlab.com/1.2/customer/vendor/list.json';
	const API_URL_VENDOR_FILE = 'https://api.woltlab.com/1.1/vendor/file/list.json';
	
	/**
	 * List of cached results for each api
	 * @var		mixed[]
	 */
	private $cachedResults = [
		'vendorCustomer' => [],
		'vendorFile' => []
	];
	
	/**
	 * Active request
	 * @var		HTTPRequest
	 */
	protected $request = null;
	
	/**
	 * @inheritdoc
	 */
	protected function init() { 
		if (empty(WOLTLAB_VENDOR_ID) || empty(WOLTLAB_VENDOR_API_KEY)) {
			throw new SystemException('Could not initialize "WoltLab Vendor API" due to invalid vendorID or API key');
		}
		
		// check if PHP has enabled ssl support
		// WoltLab only allows API calls over HTTPS
		if (!RemoteFile::supportsSSL()) {
			throw new SystemException('Could not initialize "WoltLab Vendor API" due to missing SSL support');
		}
	}
	
	/**
	 * Generates a new HTTP request with the given `$postParameter` against
	 * the given `$apiURL`.
	 * If `$executeRequestAutomatically` is set to `true` then request will be
	 * automatically performed.
	 * @param	string			$apiURL
	 * @param	string[]		$postParameter
	 * @param	boolean			$executeRequestAutomatically
	 * @return	HTTPRequest
	 */
	private function getHTTPRequest($apiURL, $postParameter = [], $executeRequestAutomatically = false) {
		static $defaultPOSTParameter = [
			'apiKey' => WOLTLAB_VENDOR_API_KEY,
			'vendorID' => WOLTLAB_VENDOR_ID
		];
		
		$__postParameter = array_merge($defaultPOSTParameter, $postParameter);
		
		$request = new HTTPRequest(
			$apiURL,
			[
				'method' => 'POST'
			],
			$__postParameter
		);
		
		if ($executeRequestAutomatically) {
			$request->execute();
		}
		
		return $request;
	}
	
	/**
	 * Returns a list of all own plugin store file ids.
	 * @return	integer[]
	 */
	public function getOwnPluginStoreFileIDs() {
		if (!empty(WOLTLAB_ID) && !empty(WOLTLAB_API_KEY)) {
			return $this->getPurchasedPluginStoreFileIDsByUser(WOLTLAB_ID, WOLTLAB_API_KEY);
		}
		
		return [];
	}
	
	/**
	 * Returns a list of the purchased plugin store file ids
	 * of the WoltLab user with the given credentials.
	 * @param	integer		$woltlabID
	 * @param	string		$pluginStoreApiKey
	 * @return	integer[]
	 */
	public function getPurchasedPluginStoreFileIDsByUser($woltlabID, $pluginStoreApiKey) {
		if (isset($this->cachedResults['vendorCustomer'][$woltlabID])) {
			return $this->cachedResults['vendorCustomer'][$woltlabID]['fileIDs'];
		}
		
		$this->request = $this->getHTTPRequest(static::API_URL_VENDOR_CUSTOMER, [
			'pluginStoreApiKey' => $pluginStoreApiKey,
			'woltlabID' => $woltlabID
		], true);
		
		$reply = $this->getReplyAsArray();
		
		switch ($reply['status']) {
			case 200:
				break;
			case 401:
			case 403:
				throw new HTTPUnauthorizedException(
					($reply['errorMessage'] ?? ''),
					$reply['status']
				);
			default:
				$errorMessage = (isset($reply['errorMessage']) ? $reply['errorMessage'] : '');
				throw new HTTPServerErrorException(
					'Received status code '.$reply['status'].' from server'.($errorMessage ? ' with message: '.$errorMessage : '.')
				);
		}
		
		// cache reply
		$this->cachedResults['vendorCustomer'][$woltlabID] = $reply;
		unset($reply);
		
		return $this->cachedResults['vendorCustomer'][$woltlabID]['fileIDs'];
	}
	
	/**
	 * Returns a list of the plugin store files
	 * of the WoltLab user with the given credentials.
	 * @return	array
	 */
	public function getPluginStoreFilesByUser() {
		if (empty(WOLTLAB_ID) || empty(WOLTLAB_API_KEY)) {
			return [];
		}
		
		if (isset($this->cachedResults['vendorFile'][WOLTLAB_ID])) {
			return $this->cachedResults['vendorFile'][WOLTLAB_ID]['files'];
		}
		
		$this->request = $this->getHTTPRequest(static::API_URL_VENDOR_FILE, [
			'pluginStoreApiKey' => WOLTLAB_API_KEY,
			'woltlabID' => WOLTLAB_ID
		], true);
		
		$reply = $this->getReplyAsArray();
		
		switch ($reply['status']) {
			case 200:
				break;
			case 401:
			case 403:
				throw new HTTPUnauthorizedException(
					($reply['errorMessage'] ?? ''),
					$reply['status']
				);
			default:
				$errorMessage = (isset($reply['errorMessage']) ? $reply['errorMessage'] : '');
				throw new HTTPServerErrorException(
					'Received status code '.$reply['status'].' from server'.($errorMessage ? ' with message: '.$errorMessage : '.')
				);
		}
		
		// cache reply
		$this->cachedResults['vendorFile'][WOLTLAB_ID] = $reply;
		unset($reply);
		
		return $this->cachedResults['vendorFile'][WOLTLAB_ID]['files'];
	}
	
	/**
	 * Decodes the JSON string into an array and return it.
	 * If `$reply` is empty the `body` of the active request reply will be used.
	 * @param	string		$reply
	 * @return	string[]
	 */
	private function getReplyAsArray($reply = '') {
		if (empty($reply)) {
			$requestReplyData = $this->request->getReply();
			$reply = $requestReplyData['body'];
		}
		
		return JSON::decode($reply);
	}
	
	/**
	 * Returns true if the plugin store product with the given file id
	 * is purchased by the customer with the given credentials.
	 * @param	integer		$pluginStoreFileID
	 * @param	integer		$woltlabID
	 * @param	string		$pluginStoreApiKey
	 * @return	boolean
	 * @throws	HTTPServerErrorException
	 * @throws	HTTPUnauthorizedException
	 */
	public function isPurchasedPluginStoreProduct($pluginStoreFileID, $woltlabID, $pluginStoreApiKey) {
		return in_array($pluginStoreFileID, $this->getPurchasedPluginStoreFileIDsByUser($woltlabID, $pluginStoreApiKey));
	}
}

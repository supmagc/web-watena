<?php

/**
 * This class hold the request data for the current request.
 * Before using it, you should make sure to call Request::init().
 * If this class is used as part of Watena, this will be done for you.
 * 
 * @author Jelle Voet
 * @version 0.1.0
 */
class Request {
	
	private static $s_aData = array(
		'useragent' => 'watena',
		'http' => true,
		'https' => false,
		'protocol' => 'http',
		'user' => '',
		'password' => '',
		'host_http' => 'localhost',
		'host_server' => 'localhost',
		'port' => 80,
		'offset' => '',
		'path' => '/',
		'method' => 'GET',
		'mapping' => array(),
		'base' => 'http://localhost',
		'root' => 'http://localhost',
		'url' => 'http://localhost/',
		'detail' => '[GET] http://localhost',
	);
	
	/**
	 * Initialize and cache the correct values for the current request.
	 */
	public final static function init() {
		if(!empty($_SERVER['HTTP_USER_AGENT'])) {
			if(empty($_SESSION['HTTP_USER_AGENT'])) {
				self::$s_aData['useragent'] = $_SERVER['HTTP_USER_AGENT'];
			}
			else {
				self::$s_aData['useragent'] = $_SESSION['HTTP_USER_AGENT'];
			}
			if($_SERVER['HTTP_USER_AGENT'] !== '*/*') {
				$_SESSION['HTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			}
		}
		
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			self::$s_aData['http'] = false;
			self::$s_aData['https'] = true;
			self::$s_aData['protocol'] = 'https';
			self::$s_aData['port'] = 443;
		}
		
		if(!empty($_SERVER['PHP_AUTH_USER'])) {
			self::$s_aData['user'] = $_SERVER['PHP_AUTH_USER'];
		}
		
		if(!empty($_SERVER['PHP_AUTH_PW'])) {
			self::$s_aData['user'] = $_SERVER['PHP_AUTH_PW'];
		}
		
		if(!empty($_SERVER['HTTP_HOST'])) {
			self::$s_aData['host_http'] = $_SERVER['HTTP_HOST'];
			if(empty( $_SERVER['SERVER_NAME']))
				self::$s_aData['host_server'] = $_SERVER['HTTP_HOST'];
		}
		
		if(!empty($_SERVER['SERVER_NAME'])) {
			self::$s_aData['host_server'] = $_SERVER['SERVER_NAME'];
			if(empty( $_SERVER['HTTP_HOST']))
				self::$s_aData['host_server'] = $_SERVER['SERVER_NAME'];
		}
		
		if(!empty($_SERVER['SERVER_PORT'])) {
			self::$s_aData['port'] = $_SERVER['SERVER_PORT'];
		}
		
		if(!empty($_SERVER['DOCUMENT_ROOT'])) {
			self::$s_aData['offset'] = Encoding::replace('\\', '/', Encoding::substring(PATH_ROOT, Encoding::length($_SERVER['DOCUMENT_ROOT'])));
		}

		$sPath = '';
		if(isset($_SERVER['REDIRECT_URL'])) {
			$sPath = $_SERVER['REDIRECT_URL'];
		}
		else if(isset($_SERVER['REQUEST_URI'])) {
			$sPath = Encoding::substring($_SERVER['REQUEST_URI'], 0, Encoding::indexOf($_SERVER['REQUEST_URI'], '?'));
		}
		else if(isset($_SERVER['PHP_SELF'])) {
			$sPath = $_SERVER['PHP_SELF'];
		}
		self::$s_aData['path'] = Encoding::substring($sPath, Encoding::length(self::offset()));
		self::$s_aData['mapping'] = explode_trim('/', self::$s_aData['path']);
		
		if(!empty($_SERVER['REQUEST_METHOD'])) {
			self::$s_aData['method'] = Encoding::toUpper($_SERVER['REQUEST_METHOD']);
		}
		
		$sBuilder = self::$s_aData['protocol'] . '://' . self::$s_aData['host_http'];
		if(self::$s_aData['https']) {
			if(self::$s_aData['port'] != 443)
				$sBuilder .= ':' . self::$s_aData['port'];
		}
		else {
			if(self::$s_aData['port'] != 80)
				$sBuilder .= ':' . self::$s_aData['port'];
		}
		self::$s_aData['base'] = $sBuilder;
		
		$sBuilder .= self::$s_aData['offset'];
		self::$s_aData['root'] = $sBuilder;
		
		$sBuilder .= self::$s_aData['path'];
		self::$s_aData['url'] = $sBuilder;
		
		$sBuilder = '[' . self::$s_aData['method'] . ']' . $sBuilder . '?' . http_build_query($_GET, null, '&') . ' (' . self::$s_aData['useragent'] . ')';
		self::$s_aData['detail'] = $sBuilder;
	}

	/**
	 * Determine if the current request is not using a secured protocol.
	 * This method is the opposite of Request::isHttps().
	 * The return value is based on $_SERVER['HTTPS'] and will influence
	 * the behaviour of Request::protocol() and Request::port().
	 * 
	 * @see Request::isHttps()
	 * @see Request::protocol()
	 * @see Request::port()
	 * @return boolean Indicates if the current request is not using https. (default: true)
	 */
	public final static function isHttp() {
		return self::$s_aData['http'];
	}
	
	/**
	 * Determine if the current request is using a secured protocol.
	 * This method is the opposite of Request::isHttp().
	 * The return value is based on $_SERVER['HTTPS'] and will influence
	 * the behaviour of Request::protocol() and Request::port().
	 * 
	 * @see Request::isHttps()
	 * @see Request::protocol()
	 * @see Request::port()
	 * @return boolean Indicates of the current request is using https. (default: false)
	 */
	public final static function isHttps() {
		return self::$s_aData['https'];
	}
	
	/**
	 * Retrieve the scheme/protocol of the current request.
	 * Currently the only two protocols supported are 'http' and 'https'.
	 * The return value is based on Request::isHttp() and Request::isHttps().
	 * 
	 * @see Request::isHttp()
	 * @see Request::isHttps()
	 * @return string Return value should be 'http' or 'https'. (default: 'http')
	 */
	public final static function protocol() {
		return self::$s_aData['protocol'];
	}

	/**
	 * Retrieve the http-authentication user of the current request.
	 * The return value is based on $_SERVER['PHP_AUTH_USER'].
	 * 
	 * @return string The user portion of the current request. (default: '')
	 */
	public final static function user() {
		return self::$s_aData['user'];
	}

	/**
	 * Retrievethe http-authentication password of the current request.
	 * The return value is based on $_SERVER['PHP_AUTH_PASS'].
	 * 
	 * @return string The password portion of the current request. (default: '')
	 */
	public final static function password() {
		return self::$s_aData['password'];
	}
	
	/**
	 * Retrieve the lowercase hostname of the current request.
	 * Optionally you can try to retrieve the actual server-name as 
	 * specified on the server config. If not required, the value returned 
	 * will be the host-portion of the request.
	 * The return value is based on $_SERVER['SERVER_NAME'] or $_SERVER['HTTP_POST'].
	 *
	 * @param boolean $bServer Set to true if you want the internal server-name.
	 * @return string Returns lowercase $_SERVER['HTTP_HOST'] or $_SERVER['SERVER_NAME']. (default: localhost)
	 */
	public final static function host($bServer = false) {
		return $bServer ? self::$s_aData['host_server'] : self::$s_aData['host_http'];
	}
	
	/**
	 * Retrieve the portnumber of the current request.
	 * The return value is based on $_SERVER[SERVER_PORT].
	 * If no port is specified, this will default to 80 for http and 443 for https.
	 *
	 * @see Request::isHttp()
	 * @see Request::isHttps()
	 * @return int The port number of the current request. (default based on protocol: 80 or 443)
	 */
	public final static function port() {
		return self::$s_aData['port'];
	}
	
	/**
	 * Retrieve the base portion of the current request.
	 * 
	 * @see Request::protocol()
	 * @see Request::user()
	 * @see Request::password()
	 * @see Request::host()
	 * @see Request::port()
	 * @return string http[s]://[user[:password]@]example.com[:80]
	 */
	public final static function base() {
		return self::$s_aData['base'];
	}

	/**
	 * Retrieve the offset portion of the current request.
	 * The offset is defined as the difference between the base and the root url
	 * for the current request and is not fixed 'per install'.
	 *
	 * @return string The path to the webroot for this install. /path-to-install
	 */
	public final static function offset() {
		return self::$s_aData['offset'];
	}

	/**
	 * Retrieve the root portion of the current request.
	 * This is the concatenation of Request::base() and Request::offset().
	 * 
	 * @see Request::base()
	 * @see Request::offset()
	 * @return string http[s]://[user[:password]@]example.com[:80][/path-to-install]
	 */
	public final static function root() {
		return self::$s_aData['root'];
	}
	
	/**
	 * Retrieve the path portion of the current request.
	 * The path is defined as the as the posfix after the offset and thus contains
	 * the actual mapping with the install starting from the root.
	 * When no mapping is found, this will always return '/'.
	 * 
	 * @return string The mapping for the current request. /path/mapping (default: '/')
	 */
	public final static function path() {
		return self::$s_aData['path'];
	}
	
	/**
	 * Retrieve the full url of the current request.
	 * This is the concatenation of Request::root() and Request::path().
	 * 
	 * @see Request::root()
	 * @see Request::path()
	 * @return string http[s]://[user[:password]@]example.com[:80][/path-to-install]/[path/mapping]
	 */
	public final static function url() {
		return self::$s_aData['url'];
	}
	
	/**
	 * Retrieve the uppercase request-method of the current request.
	 * If no request-method is specified, this will default to GET.
	 * The return values is based on $_SERVER['REQUEST_METHOD'].
	 *
	 * @return string De method used for the data of the current request. (default: 'GET')
	 */
	public final static function method() {
		return self::$s_aData['method'];
	}
	
	/**
	 * Retrieve the useragent of the current request.
	 * This will automatically save the useragent to a session.
	 * If a subsequent request with useragent * / * should occur, the session-value will be used instead.
	 * If no useragent is specified, this will default to 'Unknown'.
	 * The return valies is based 
	 *
	 * @return string The persistent useragent data from the previous and current requests.
	 */
	public final static function useragent() {
		return self::$s_aData['useragent'];
	}
	
	/**
	 * Retrieve the specified value from the current $_GET.
	 * You can specify as you would when using array_value().
	 * If the specified index does not exist, $mDefault will be returned
	 * 
	 * @see array_value()
	 * @param mixed $mIndex The index of the required value.
	 * @param mixed $mDefault Default value when index does not exist.
	 * @return mixed The value specified by $mIndex in $_GET, of $mDefault.
	 */
	public final static function get($mIndex, $mDefault = null) {
		return array_value($_GET, $mIndex, $mDefault);
	} 

	/**
	 * Retrieve the specified value from the current $_POST.
	 * You can specify as you would when using array_value().
	 * If the specified index does not exist, $mDefault will be returned
	 * 
	 * @see array_value()
	 * @param mixed $mIndex The index of the required value.
	 * @param mixed $mDefault Default value when index does not exist.
	 * @return mixed The value specified by $mIndex in $_POST, of $mDefault.
	 */
	public final static function post($mIndex, $mDefault = null) {
		return array_value($_POST, $mIndex, $mDefault);
	}

	/**
	 * Retrieve the specified value from the current $_SESSION.
	 * You can specify as you would when using array_value().
	 * If the specified index does not exist, $mDefault will be returned
	 * 
	 * @see array_value()
	 * @param mixed $mIndex The index of the required value.
	 * @param mixed $mDefault Default value when index does not exist.
	 * @return mixed The value specified by $mIndex in $_SESSION, of $mDefault.
	 */
	public final static function session($mIndex, $mDefault = null) {
		return array_value($_SESSION, $mIndex, $mDefault);
	}

	/**
	 * Retrieve the specified value from the current $_COOKIE.
	 * You can specify as you would when using array_value().
	 * If the specified index does not exist, $mDefault will be returned
	 * 
	 * @see array_value()
	 * @param mixed $mIndex The index of the required value.
	 * @param mixed $mDefault Default value when index does not exist.
	 * @return mixed The value specified by $mIndex in $_COOKIE, of $mDefault.
	 */
	public final static function cookie($mIndex, $mDefault = null) {
		return array_value($_COOKIE, $mIndex, $mDefault);
	}
	
	/**
	 * Retrieve the full detailed string containing the current request state.
	 * Currently only the $_GET parameters are appended. (not $_POST, $_COOKIE, $_SESSION)
	 * This is the concatenation of Request::method(), Request::url(), Request::get(), 
	 * Request::useragent().
	 * 
	 * @see Request::method()
	 * @see Request::url()
	 * @see Request::get()
	 * @see Request::useragent()
	 * @return string [GET] http[s]://[user[:password]@]example.com[:80][/path-to-install]/[path/mapping]?[param0=foo&param1=bar] (useragent)
	 */
	public final static function detail() {
		return self::$s_aData['detail'];
	}
}

?>
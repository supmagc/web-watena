<?php namespace Watena\Core;

class Object {
	
	private static $s_oSingleton;
	
	private $m_oLogger;
	
	protected function __construct() {
		if(get_class($this) == __NAMESPACE__."\Watena") self::$s_oSingleton = $this;
	}
	
	/**
	 * @return Watena
	 */
	public static final function getWatena() {
		return self::$s_oSingleton;
	}
	
	/**
	 * Retrurn the logger-instance linked to this class
	 * 
	 * @return Logger
	 */
	public final function getLogger() {
		if($this->m_oLogger === null) {
			$this->m_oLogger = Logger::getInstance(get_class($this));
		}
		return $this->m_oLogger;
	}
	
	public function toString() {
		return get_class($this);
	}
	
	public final function __toString() {
		return $this->toString();
	}
}

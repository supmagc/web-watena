<?php
require_plugin('DatabaseManager');

class UserManager extends Plugin {
	
	public static function create() {
	
	}
	
	public static function login($sUsername, $sPassword) {
	
	}
	
	public static function authenticate($mIdentifier/* ?? SYSTEM ?? */) {
	
	}
	
	/**
	* Retrieve version information of this plugin.
	* The format is an associative array as follows:
	* 'major' => Major version (int)
	* 'minor' => Minor version (int)
	* 'build' => Build version (int)
	* 'state' => Naming of the production state
	*/
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}
}

?>
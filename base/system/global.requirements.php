<?php
define('REQERROR_EXTENSIONNOTFOUND', 1);
define('REQERROR_EXTENSIONUNLOADABLE', 2);
define('REQERROR_FILENOTFOUND', 3);

function require_error($nCode, $sName) {
	$oInstance = Logger::getInstance('Requirements');
	$sMessage = 'Requirement error occured';
	switch($nCode) {
		
	}
	require_logger()->warning($sMessage, array('code' => $nCode, 'name' => $sName));
}

function require_logger() {
	return Logger::getInstance('Requirement');
}

function require_extension($mName) {
	if(is_array($mName)) return array_all('require_extension', $mName);
	else {
		if(!extension_loaded($mName)) {
			if(function_exists('dl')) if(!@dl($mName)) require_error(REQERROR_EXTENSIONUNLOADABLE, $mName);
			else require_error(REQERROR_EXTENSIONNOTFOUND, $mName);
		}
		return true;
	}
}

function require_plugin($mName) {
	if(is_array($mName)) return array_all('require_plugin', $mName);
	else {
		return watena()->getContext()->loadPlugin($mName);
	}
}

function require_pear($mName) {
	if(is_array($mName)) return array_all('require_pear', $mName);
	else {
		$nOld = error_reporting(E_ERROR);
		$bReturn = @include_once('PEAR.php') && @include_once($sPear.'.php');
		error_reporting($nOld);
		return $bReturn;
	}
}

function require_include($mName) {
	if(is_array($mName)) return array_all('require_include', $mName);
	else {
		if(is_file($mName)) {
			return @include_once $mName;
		}
		else return false;
	}
}

function require_file($mName) {
	if(is_array($mName)) return array_all('require_file', $mName);	
	else {
		if(is_file($mName)) return true;
		else {
			require_error(REQERROR_FILENOTFOUND, $mName);
			return false;
		}
	}
}

function require_directory($mName) {
	if(is_array($mName)) return array_all('require_directory', $mName);
	else {
		return is_dir($mName);
	}
}

function require_define($mName) {
	if(is_array($mName)) return array_all('require_define', $mName);
	else {
		return defined($mName);
	}
}

function require_library($mName) {
	if(is_array($mName)) return array_all('require_library', $mName);
	else {
		return file_exists(PATH_LIBS . '/' . $mName);
	}
}

function require_data($mName) {
	if(is_array($mName)) return array_all('require_data', $mName);
	else {
		return file_exists(PATH_DATA . '/' . $mName);
	}
}

function require_model($mName) {
	if(is_array($mName)) return array_all('require_model', $mName);
	else {
		if($sPath = watena()->getContext()->getLibraryFilePath('models', 'model.'.Encoding::toLower($mName).'.php')) {
			@include_once $sPath;
			return class_exists($mName);
		}
		else return false;
	}
}

function require_view($mName) {
	if(is_array($mName)) return array_all('require_view', $mName);
	else {
		if($sPath = watena()->getContext()->getLibraryFilePath('views', 'view.'.Encoding::toLower($mName).'.php')) {
			@include_once $sPath;
			return class_exists($mName);
		}
		else return false;
	}
}

function require_controller($mName) {
	if(is_array($mName)) return array_all('require_controller', $mName);
	else {
		if($sPath = watena()->getContext()->getLibraryFilePath('controllers', 'controller.'.Encoding::toLower($mName).'.php')) {
			@include_once $sPath;
			return class_exists($mName);
		}
		else return false;
	}
}

?>
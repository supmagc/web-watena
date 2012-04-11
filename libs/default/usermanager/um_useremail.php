<?php

class UserEmail extends DbObject {
	
	public function getUserId() {
		return $this->getDataValue('userId');
	}
	
	public function getEmail() {
		return $this->getDataValue('email');
	}
	
	public function getHash() {
		return $this->getDataValue('hash');
	}
	
	public function getTimestamp() {
		return $this->getDataValue('timestamp');
	}
	
	public function isVerified() {
		return (bool)$this->getDataValue('verified');
	}
	
	public static function load($mData) {
		return DbObject::loadObject('UserEmail', UserManager::getDatabaseConnection()->getTable('user_email'), $mData);
	}
	
	public static function create(User $oUser, $sEmail, $bVerified) {
		return !UserManager::getUserIdByEmail($sEmail) ? DbObject::createObject('UserEmail', UserManager::getDatabaseConnection()->getTable('user_email'), array(
			'userId' => $oUser->getId(),
			'email' => $sEmail,
			'verified' => $bVerified ? 1 : 0,
			'hash' => md5($oUser->getId() . $sEmail . microtime(true) . mt_rand())
		)) : false;
	}
}

?>
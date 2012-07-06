<?php
require_model('HtmlModel');
require_plugin('UserManager');
require_plugin('ToeVla');

class MainModel extends HtmlModel {
	
	private $m_sHash = null;
	private $m_nHubId = null;
	private $m_nFestivalId = null;
	private $m_sEmail = null;
	
	public function showVerifier($sEmail) {
		$this->m_sEmail = $sEmail;
	}
	
	public function getEmail() {
		return $this->m_sEmail;
	}
	
	public function setHash($sHash) {
		$this->m_sHash = $sHash;
	}
	
	public function setHubId($nId) {
		$this->m_nHubId = $nId;
	}
	
	public function setFestivalId($nId) {
		$this->m_nFestivalId = $nId;
	}
	
	public function hasHash() {
		return $this->m_sHash !== null;
	}
	
	public function getHash() {
		return $this->m_sHash;
	}
	
	public function getHubId() {
		return $this->m_nHubId;
	}
	
	public function getFestivalId() {
		return $this->m_nFestivalId;
	}
	
	public function getAnalytics() {
		return $this->getWatena()->getContext()->getPlugin('ToeVla')->getConfig('analytics');
	}
	
	public function getUrl() {
		return $this->getWatena()->getMapping()->getRoot();
	}

	public function getTwitterParams() {
		$aData = array(
			'hashtags' => 'fiaf12',
			'url' => urlencode('' . new Mapping('/')),
		);
		if(isset($_GET['hash']) && $_GET['hash'])
			$aData['hashtags'] .= ',' . $_GET['hash'];
		if(isset($_GET['name']) && $_GET['name'])
			$aData['related'] = $_GET['name'];
		return http_build_query($aData, null, '&');
	}
	
	public function hasTwitterLogin() {
		return UserManager::getProviderTwitter(); 
	}
		
	public function getTwitterLoginUrl() {
		return UserManager::getProviderTwitter()->getConnectUrl(new Mapping('/twitter/callback'));
	}
	
	public function hasFacebookLogin() {
		return UserManager::getProviderFacebook();
	}
	
	public function getFacebookLoginUrl() {
		return UserManager::getProviderFacebook()->getConnectUrl(new Mapping('/facebook/callback'));
	}
}

?>
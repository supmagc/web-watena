<?php

class TemplateView extends View implements IPCO_IContentParser {
	
	public function headers(Model $oModel = null) {
		$this->headerContentType('text/plain');
	}
	
	public function render(Model $oModel = null) {
		$oPlugin = parent::getWatena()->getContext()->getPlugin('TemplateLoader');
		$oTemplate = $oPlugin->load(parent::getConfig('template', 'index.tpl'));
		$oGenerator = $oTemplate->createTemplateClass();
		$oGenerator->componentPush($oModel);
		echo $oGenerator->getContent(true);
	}
	
	public static function getRequirements() {
		return array('plugins' => 'TemplateLoader');
	}
}

?>
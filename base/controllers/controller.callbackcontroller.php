<?php

class CallbackController extends Controller {
	
	private $m_oCallback;
	private $m_oModel;
	private $m_oView;
	
	public final function process(Model $oModel = null, View $oView = null) {
		$this->m_oCallback = Callback::loadFromRequest();
		
		if(!empty($this->m_oCallback)) {
			$this->getLogger()->info('Callback-data loaded: ADD DEBUG DATA');
			$this->m_oCallback->process($this);
		}
		else {
			$this->getLogger()->error('CallbackController is unable to trigger any callback when no callback-data could be loaded.');
		}
	}
	
	public final function tester() {
		$this->display("Hello Callback !");
	}
}

?>
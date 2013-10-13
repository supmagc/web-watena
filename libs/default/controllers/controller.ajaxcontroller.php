<?php

class AjaxController extends Controller {
	
	public function process(Model $oModel = null, View $oView = null) {
		if(is_a($oModel, 'AjaxModel')) {
			$this->getLogger()->error('An AjacController needs an AjaxModel, but a {model} was given.', array('modem' => get_class($oModel)));
		}
		else if(is_a($oView, 'AjaxView')) {
			$this->getLogger()->error('An AjacController needs an AjaxView, but a {view} was given.', array('view' => get_class($oView)));
		}
		else {
			try {
				$oModel->getAjax()->process($this);
			}
			catch(Exception $e) {
				$oModel->addException($e);
			}
		}
	}
}

?>
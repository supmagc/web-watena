<?php

class ModelTester extends Model {
	
	var $continue = true;
	var $count = 10;
	
	function init() {
		echo "test: ";
		print_r(parent::getWatena()->getContext()->getPlugin('DatabaseManager'));
	}
	
	function next() {
		$this->count = $this->count - 1;
		return $this->count > 0;
	}
}

?>
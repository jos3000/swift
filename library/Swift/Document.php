<?php

class Swift_Document {
	private $_domdocument;
	
	public function loadHTML($doc){
		if(is_a($doc,'DomDocument')) $this->_domdocument = $doc;
	}
	
	public function saveDom(){
		return $this->_domdocument;
	}
	
	public function process(){

	}
}
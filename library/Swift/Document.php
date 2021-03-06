<?php

class Swift_Document {
	private $_domdocument;
	private $_config;
	
	public $seperator = ',';
	
	private $_used_libraries = array();
	
	public function __construct($config = array()){
		
		$this->_config = $config;
		
		if(empty($this->_config['version_number'])) $this->_config['version_number'] = 0;
		
	}
	
	public function loadHTML($doc){
		if($doc instanceof DomDocument) $this->_domdocument = $doc;
		elseif(is_file($doc)) {
			$this->_domdocument = new DomDocument();
			$this->_domdocument->loadHTMLFile($doc);
		} else {
			$this->_domdocument = new DomDocument();
			$this->_domdocument->loadHTML($doc);
		}
	}
	
	public function saveDom(){
		return $this->_domdocument;
	}
	
	public function process(){
		
		$this->deferJS();
		$this->loadDependencies();
		$this->promoteCSS();
		
		$this->serveJSinline();
		$this->serveCSSinline();
		
		$this->serveJSexternally();
		$this->serveCSSexternally();
		
		$this->combineJS();
		$this->combineCSS();
		
	}
	
	private function combineJS(){
		$this->combineTags('script','src');
	}
	
	private function combineCSS(){
		$this->combineTags('link','href','rel','stylesheet');
	}
	
	private function serveJSinline(){
		$this->serveInline('script','src');
	}
	
	private function serveCSSinline(){
		$this->serveInline('link','href','rel','stylesheet');
	}
	
	private function serveJSexternally(){
		$this->serveExternally('script');
	}
	
	private function serveCSSexternally(){
		$this->serveExternally('style');
	}
	
	private function extractModulename($url,$id = false){
		# if id matches a module name in the config use that
		
		if(!empty($id)){
			if(!empty($this->_config['modules'][$id])) return $id;
		}
		
		if('swift://' === substr($url,0,8)) {
			$url = substr($url,8); # remove 'swift://'
			if(strpos($url,'#')) $url = substr($url,0,strpos($url,'#')); # chop of the hash if there is one
			return $url;
		}
		
		return false;

	}
	
	private function loadDependencies(){
		$targettags = $this->_domdocument->getElementsByTagName('script');
		
		$removeelements = array();
		
		# copy targets into a new array to prevent iterating over newly added nodes
		$targetnodes = array();
		foreach($targettags AS $target_node){
			$targetnodes[] = $target_node;
		}
		
		foreach($targetnodes AS $target_node){
			
			$src = (string)$target_node->getAttribute('src');
			$id = (string)$target_node->getAttribute('id');
			
			$modulename = $this->extractModulename($src,$id);
			
			# skip inline targets or ones without swift modules source files
			if(!$modulename) {
				# no module - let's move on
				continue;
			}
			
			# if this module has been loaded already 

			if(in_array($modulename,$this->_used_libraries)) {
				# if a module has already been loaded further up the page, remove the node so that it's not loaded again
				$target_node->parentNode->removeChild($target_node);
				
			} else {
				
				# load requirements from a list of dependent modules
				
				$requirements = $this->getDependencyNames($modulename);
				
				foreach($requirements AS $required_module){
					if(!in_array($required_module,$this->_used_libraries)) {
						# if we haven't already put it in the page add it before this node
						
						$new_node = $this->_domdocument->createElement('script');
				
						$new_node->setAttribute('src','swift://'.$required_module);
								
						$target_node->parentNode->insertBefore($new_node,$target_node);
						
						$this->_used_libraries[] = $required_module;
					}
				}
			
				$this->_used_libraries[] = $modulename;
			}	
		}
		
	}
	
	private function getDependencyNames($modulename){
		
		if(empty($this->_config['modules'][$modulename]['requires'])) return array();
		else {
			if(is_string($this->_config['modules'][$modulename]['requires'])) $this->_config['modules'][$modulename]['requires'] = array($this->_config['modules'][$modulename]['requires']);
			
			$requires = array();
			
			foreach($this->_config['modules'][$modulename]['requires'] AS $req){
				
				$sub_dependencies = $this->getDependencyNames($req);
				
				foreach($sub_dependencies AS $sub){
					$requires[] = $sub;
				}
				
				$requires[] = $req;				
			}

			return $requires;
		}

	}
	
	private function promoteCSS(){
		
		$bodylist = $this->_domdocument->getElementsByTagName('body');
		$bodynode = $bodylist->item(0);
		
		$headlist = $this->_domdocument->getElementsByTagName('head');
		$headnode = $headlist->item(0);
		
		# only select from body node - leave CSS in the head if it's there already
		
		$linktags = $bodynode->getElementsByTagName('link');
		
		foreach($linktags AS $target_node){
			if($target_node->getAttribute('rel') != 'stylesheet') continue;
			# move it to the end of the head tag
			$headnode->appendChild($target_node);
		}
		
		$styletags = $bodynode->getElementsByTagName('style');
		
		foreach($styletags AS $target_node){
			# move it to the end of the head tag
			$headnode->appendChild($target_node);
		}
	}
	
	private function deferJS(){
		
		$bodylist = $this->_domdocument->getElementsByTagName('body');
		$bodynode = $bodylist->item(0);
		
		# only select from body node - leave CSS in the head if it's there already
		
		$scripttags = $this->_domdocument->getElementsByTagName('script');
		
		# can't iterate over tags when moving them so make an array before working on them
		$tagstomove = array();
		
		foreach($scripttags AS $target_node){
			
			if(!$target_node->hasAttribute('defer')) continue;
			# move it to the end of the body tag
			$tagstomove[] = $target_node;
			
			$target_node->removeAttribute('defer');
		}
		
		foreach($tagstomove AS $tn){
			$bodynode->appendChild($tn);
		}
		
	}
	
	
	# also rewrites URLs
	
	private function combineTags($tagname, $attributename, $filterattribute=false, $filtervalue=false){
		
		if(empty($this->_config['swift_url'])) throw new Swift_Document_Exception('Cannot combine URLs if the swift combine is not set');
		
		$targettags = $this->_domdocument->getElementsByTagName($tagname);

		$combine_node = false;
		
		$combine_group = false;
		
		$removeelements = array();
		
		foreach($targettags AS $target_node){

			$src = (string)$target_node->getAttribute($attributename);
			
			if(!empty($filterattribute)) {
				if($target_node->getAttribute($filterattribute) != $filtervalue) continue;
			}
			
			$modulename = $this->extractModulename($src);
			
			# skip inline targets or ones without swift modules source files
			if(!$modulename) {
				$combine_node = false;
				continue; 
			}
			
			# stop combining if the group of the current module is not the same as the previous
			if(!empty($this->_config['modules'][$modulename]['group'])){
				$current_group = $this->_config['modules'][$modulename]['group'];
			} else {
				$current_group = false;
			}
			
			if($combine_group != $current_group){
				$combine_node = false;
			}
			
			if(empty($combine_node)) {
				$combine_node = $target_node;
				$combine_group = $current_group;
				$combine_node->setAttribute($attributename,$this->_config['swift_url'].$this->_config['version_number'].$this->seperator.$modulename);
			} else {
				$removeelements[] = $target_node;
				$combine_node->setAttribute($attributename,$combine_node->getAttribute($attributename).$this->seperator.$modulename);
			}
			
			$lookup = array('script'=>'js','link'=>'css');
			
			if(	
				!empty($this->_config['debug_combine_'.$lookup[$tagname].'_off']) ||
				$target_node->nextSibling == NULL || 
				$target_node->nextSibling->nodeName != $tagname
			) {
				$combine_node = false;
			}
		}
		
		foreach($removeelements as $rm){ 
			$rm->parentNode->removeChild($rm); 
		}
		
	}
	
	
	# Pulls files in files from outside
	
	private function serveInline($tagname, $attributename, $filterattribute=false, $filtervalue=false){
		
		$targettags = $this->_domdocument->getElementsByTagName($tagname);
		
		$combine_node = false;
		
		$removeelements = array();
		
		foreach($targettags AS $target_node){
			
			$src = (string)$target_node->getAttribute($attributename);
			
			# skip targets that are already inline or ones without swift modules source files
			if(empty($src) || strpos($src,'swift://') !== 0 || substr($src,-7) !== '#inline') continue; 
			
			if(!empty($filterattribute)) {
				if($target_node->getAttribute($filterattribute) != $filtervalue) continue;
			}
			
			$modulename = substr(substr($src,8),0,-7);
			
			$content = file_get_contents($this->_config['modules'][$modulename]['path']);
			
			if($tagname == 'script'){
				# JavaScript
				$target_node->appendChild($this->_domdocument->createTextNode($content));
				$target_node->removeAttribute($attributename);
				
			} elseif($tagname == 'link') {
				# CSS
				
				$newtag = $this->_domdocument->createElement('style',$content);
				
				$target_node->parentNode->insertBefore($newtag, $target_node);
				
				$newtag->setAttribute('type','text/css');
				
				$target_node->parentNode->removeChild($target_node);
				
			}
		}
	}
	
	# Pulls files in files from outside
	
	private function serveExternally($tagname){
		
		$targettags = $this->_domdocument->getElementsByTagName($tagname);
		
		$combine_node = false;
		
		$removeelements = array();
		
		foreach($targettags AS $target_node){
			
			$modulename = $this->extractModulename(false,(string)$target_node->getAttribute('id'));
			
			if(empty($modulename) || empty($this->_config['modules'][$modulename]['serve_externally'])) continue;
			
			$content = trim($target_node->nodeValue);
			
			if($tagname == 'script'){
				# JavaScript
				file_put_contents($this->_config['cache_dir'].'/'.$modulename.'.js', $content);
				$target_node->removeChild($target_node->firstChild);
				$target_node->setAttribute('src','swift://'.$modulename);
				
			} elseif($tagname == 'style') {
				# CSS
				
				file_put_contents($this->_config['cache_dir'].'/'.$modulename.'.css', $content);
				
				$newtag = $this->_domdocument->createElement('link');
				
				$headlist = $this->_domdocument->getElementsByTagName('head');
				$headnode = $headlist->item(0);
				
				$headnode->appendChild($newtag);
				
				$newtag->setAttribute('rel','stylesheet');
				$newtag->setAttribute('href','swift://'.$modulename);
				
				$target_node->parentNode->removeChild($target_node);
				
			}
		}
	}
}

class Swift_Document_Exception extends Exception {}

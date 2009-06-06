<?php
require_once('simpletest/autorun.php');
require_once('../library/Swift/Document.php');

class TestOfDocument extends UnitTestCase {
	
	public function testDocumentSetup() {
		$swiftdoc = new Swift_Document((require('_files/config.php')));
		$this->assertIsA($swiftdoc,'Swift_Document');
	}
	
	public function testUnchangedDocument() {
		
		$input = new DomDocument();
		$input->loadHTMLFile('_files/example1.html');
		
		$swiftdoc = new Swift_Document(array());
		$swiftdoc->loadHTML($input);
		$output = $swiftdoc->saveDom();
		
		$this->assertEqual($input->saveHTML(),$output->saveHTML());
		
	}
	
	public function testBasicDocument() {
		
		$input = new DomDocument();
		$input->loadHTMLFile('_files/example1.html');
		
		$swiftdoc = new Swift_Document(array());
		$swiftdoc->loadHTML($input);
		$swiftdoc->process();
		$output = $swiftdoc->saveDom();
		
		#should change document
		$this->assertNotEqual($input->saveHTML(),$output->saveHTML());
				
		foreach($output->getElementsByTagName('script') AS $scriptnode){
			# all script tags using the swift protocol should be changed
			$src = (string)$scriptnode->getAttribute('src');
			$this->assertNoPattern('/swift:\/\//',$src);
		}
		
	}
}

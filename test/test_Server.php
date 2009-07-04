<?php
require_once '_inc.php';

require_once 'Swift/Server.php';

function testSwift_Server()
{
	
	$passed = assertTrue(defined('HTTP_TEST_SERVER_PATH'), 'Check server Define');
	
	if(!$passed) {
		echo "HTTP_TEST_SERVER_PATH must be defined in local_test_config in order to run the tests over HTTP\n";
	} else {
		foreach (glob("Document_files/config*.php") as $filename) {
		
			$code = substr($filename,21,-4);
			$num = substr($filename,21,-5);
			
			$swiftdoc = new Swift_Document(require('Document_files/config'.$code.'.php'));
			$swiftdoc->loadHTML('Document_files/input'.$num.'.html');
			$swiftdoc->process();
			$output = $swiftdoc->saveDom();
			$output->formatOutput = true;
			$outputstring = $output->saveHTML();
			$expectedoutputstring = file_get_contents('Document_files/output'.$code.'.html');
		
			$passed = assertTrue($expectedoutputstring == $outputstring, 'Swift_Document - example '.$code);
		
			if(!$passed) {
				echo "Actual output saved to /tmp/output".$code.".html\n";
				file_put_contents('/tmp/output'.$code.'.html',$outputstring);
			}
		
		}
	}
	
}

testSwift_Server();

<?php
require_once '_inc.php';

require_once 'Swift/Server.php';

function testSwift_Server()
{
	
	$passed = assertTrue(defined('HTTP_TEST_SERVER_PATH'), 'Swift_Server - Check server Define');
	
	if(!$passed) {
		echo "HTTP_TEST_SERVER_PATH must be defined in local_test_config in order to run the tests over HTTP\n";
	} else {
		
		$code = '1a';
		
		$outputstring = file_get_contents(HTTP_TEST_SERVER_PATH . '?config='.$code.'&code=0,script1,script2');
		$expectedoutputstring = file_get_contents('Server_files/output'.$code.'.txt');
		
		
		$passed = assertTrue($outputstring === $expectedoutputstring, 'Swift_Server - Output of combined minified script');
		
	}
	
}

testSwift_Server();

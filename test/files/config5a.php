<?php

// test dependency loading

return array(
	'swift_url' => 'http://www.example.com/swift/',
	'cache_dir' => 'cache/',
	'version_number' => '123',
	'modules' => array(
		'extscript1' => array(
			'serve_externally' => true
		),
		
		'extstyles1' => array(
			'serve_externally' => true
		)
	
	)
);
<?php

// test dependency loading

return array(
	'swift_url' => 'http://www.example.com/swift/',
	'modules' => array(
		'library1' => array(),
		'library2' => array(),
		'script1' => array(
			'requires' => 'library1'
		),
		'script2' => array(
			'requires' => 'library1'
		),
		'script3' => array(
			'requires' => 'library2'
		)
	)
);
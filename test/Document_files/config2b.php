<?php

// test dependency loading

return array(
	'swift_url' => 'http://www.example.com/swift/',
	'modules' => array(
		'library1' => array(
		),
		'script1' => array(
			'path' => 'Document_files/scripts/example-script1.js',
			'requires' => array('library1')
		),
		'styles1' => array(
			'path' => 'Document_files/styles/example-styles1.css',
		)
	)
);
<?php

// test dependency loading

return array(
	'swift_url' => 'http://www.example.com/swift/',
	'modules' => array(
		'examplescript' => array(
			'requires' => array(
				'library1'
			)
		),
		'library' => array()
	)
);
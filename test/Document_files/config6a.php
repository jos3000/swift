<?php

// test dependency loading

return array(
	'swift_url' => 'http://www.example.com/swift/',
	'modules' => array(
		'putOnGlasses' => array(
			'requires' => array()
		),
		'findSoap' => array(
			'requires' => array('putOnGlasses')
		),
		'findSocks' => array(
			'requires' => array('putOnGlasses')
		),
		'findShoes' => array(
			'requires' => array('putOnGlasses')
		),
		'findTrousers' => array(
			'requires' => array('putOnGlasses')
		),
		'washFeet' => array(
			'requires' => array('findSoap')
		),
		'tieShoeLaces' => array(
			'requires' => array('putOnShoes')
		),
		'putOnSocks' => array(
			'requires' => array('washFeet','findSocks')
		),
		'putOnShoes' => array(
			'requires' => array('putOnSocks','putOnTrousers','findShoes')
		),
		'putOnTrousers' => array(
			'requires' => array('washFeet','findTrousers')
		)
	)
);
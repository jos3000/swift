<?php

// request combining grouping

return array(
	'swift_url' => 'http://www.example.com/swift/',
	'modules' => array(
		'putOnGlasses' => array(
			'requires' => array()
		),
		'findSoap' => array(
			'group' => 'washing',
			'requires' => array('putOnGlasses')
		),
		'findSocks' => array(
			'group' => 'dressing',
			'requires' => array('putOnGlasses')
		),
		'findShoes' => array(
			'group' => 'dressing',
			'requires' => array('putOnGlasses')
		),
		'findTrousers' => array(
			'group' => 'dressing',
			'requires' => array('putOnGlasses')
		),
		'washFeet' => array(
			'group' => 'washing',
			'requires' => array('findSoap')
		),
		'tieShoeLaces' => array(
			'group' => 'dressing',
			'requires' => array('putOnShoes')
		),
		'putOnSocks' => array(
			'group' => 'dressing',
			'requires' => array('washFeet','findSocks')
		),
		'putOnShoes' => array(
			'group' => 'dressing',
			'requires' => array('putOnSocks','putOnTrousers','findShoes')
		),
		'putOnTrousers' => array(
			'group' => 'dressing',
			'requires' => array('washFeet','findTrousers')
		)
	)
);
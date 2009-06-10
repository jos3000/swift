<?php
require_once '_inc.php';

require_once 'Swift/Document.php';

function testSwift_Document()
{
	$swiftdoc = new Swift_Document(require('files/config1d.php'));
	$swiftdoc->loadHTML('files/input1.html');
	$swiftdoc->process();
	$output = $swiftdoc->saveDom();
	
	$passed = assertTrue($minExpected == $minOutput, 'Minify_Javascript');

}

testSwift_Document();

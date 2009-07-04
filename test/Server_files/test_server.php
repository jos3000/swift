<?php

set_include_path(dirname(__FILE__) . '/../../library' . PATH_SEPARATOR . get_include_path());

// add the local copy on Minify to the include path
require('../local_test_config.php');

require('Swift/Server.php');

$config = require('config'.$_GET['config'].'.php');
$server = new Swift_Server($config);
$server->setCode($_GET['code']);
$server->serve();

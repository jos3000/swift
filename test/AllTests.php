<?php

if(!file_exists('simpletest/VERSION') || file_get_contents('simpletest/VERSION') != '1.0.1'){
        die('simpletest 1.0.1 is missing. Try:

curl http://kent.dl.sourceforge.net/sourceforge/simpletest/simpletest_1.0.1.tar.gz -o /tmp/simpletest_1.0.1.tar.gz;
tar zxvf /tmp/simpletest_1.0.1.tar.gz -C .
'        );
}

require('simpletest/autorun.php');

class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('All tests');
        $this->addFile('AllDocumentTests.php');
        $this->addFile('AllServerTests.php');
    }
}


/*
$included_files = array();

if(isset($argv[1])) {
	$included_files = array('root/'.$argv[1].'_test.php');
} else {

	$dir  = new DirectoryIterator('root'); 

	foreach ($dir as $file) { 
		$filename = $file->getFilename();
		if(strpos($filename,'_test.php')){
			$included_files[] = 'root/'.$filename;
		}
	}

}

$test = &new GroupTest('All tests');

foreach($included_files AS $file){
	$test->addTestFile($file);
}

$test->run(new TextReporter());
*/
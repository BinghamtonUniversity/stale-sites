<?php
//file to test the new average age of directory fuction
include_once 'SiteScanner.class.php';
include_once 'config.php';

if($basePath !== null) {
	$ss = new SiteScanner($basePath, $ignoredSites);
	echo $ss->_avgModified("/var/www/");
}
?>
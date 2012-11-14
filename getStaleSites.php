<?php
include_once 'SiteScanner.class.php';
include_once 'config.php';

if($basePath !== null) {
	try {
		$ss = new SiteScanner($basePath, $ignoredSites, $ignoredFileNames);
		$ss->scanSites();
		//$ss->displayReport($basePath); //Bug https://github.com/BinghamtonUniversity/stale-sites/issues/10
		$ss->displayReport();
	}
	catch(Exception $e) {
		echo $e->getMessage();
	}

}
?>
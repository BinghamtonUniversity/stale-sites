<?php
include_once 'SiteScanner.class.php';
include_once 'config.php';

if($basePath !== null) {
	$ss = new SiteScanner($basePath, $ignoredSites);
	$ss->scanSites();
	$ss->displayReport($basePath);
}
?>
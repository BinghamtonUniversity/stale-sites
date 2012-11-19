<?php
date_default_timezone_set('America/New_York'); //to supress mktime() warning
include_once 'Database.php';

$basePath = null;
$ignoredSites = null;

$ignoredFileNames = array (
	'logo.html',
	'hnav.html',
	'nav.html'
	);

try {
	$db = new Database;
	$basePath = $db->getBaseDir();
	$ignoredSites  = $db->getExcludeDir();
}
catch(Exception $e) {
	$basePath = null;
}

?>
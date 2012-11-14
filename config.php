<?php
date_default_timezone_set('America/New_York'); //to supress mktime() warning
include_once 'Database.php';

$basePath = null;

$ignoredSites = array(
    'images',
    'magazine',
    ".git"
    );
try {
	$db = new Database;
	$basePath = $db->getBaseDir();
}
catch(Exception $e) {
	$basePath = null;
}

?>
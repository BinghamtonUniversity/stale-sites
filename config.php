<?php
include_once 'Database.php';
$basePath = null; //load base path
$errorMsg = null;
$db = new Database();

try {
	$basePath = $db->getBaseDir();
}
catch(Exception $e) {
	$db = null;
	$errorMsg = $e->getMessage();
}

$ignoredSites = array(
    'bedupako/',
    'newBedupako/',
    'textApp/'
    );
?>
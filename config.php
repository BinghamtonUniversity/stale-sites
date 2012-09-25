<?php
include_once 'Database.php';

$basePath = null;

$ignoredSites = array(
    'images',
    'magazine',
    );
try {
	$db = new Database;
	$basePath = $db->getBaseDir();
}
catch(Exception $e) {
	$basePath = null;
}

?>
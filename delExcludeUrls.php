<?php
include_once 'Database.php';
include_once 'config.php';
if(!isset($_GET['url']) ) {
	echo "Please check your provided URL";	
}
$db = new Database;
$url = trim($_GET['url']);
try {
	$db->delExcludePathDir($url);
	//exit;
	header("Location: index.php");
	exit;
}
catch(Exception $e) {
	exit;
	header("Location: index.php?error=".urlencode($e->getMessage()));
}
?>
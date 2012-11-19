<?php
include_once 'Database.php';
include_once 'config.php';
if(!isset($_POST['path_urls']) ) {
	echo "Please check your provided URL";	
}
$db = new Database;
$urls = explode(",", $_POST['url']);

foreach ($urls as $key => $value) {
	$urls[$key] = trim($value);
}
try {
	$db->replaceBaseDir($urls);
	echo "success";
	exit;
}
catch(Exception $e) {
	echo $e->getMessage();
}
?>
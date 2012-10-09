<?php
include_once 'Database.php';
include_once 'config.php';
if(!isset($_POST['url']) ) {
	echo "Please check your provided URL";	
}
$db = new Database;
$url = trim($_POST['url']);

try {
	if(strlen($url) > 0) {
		if($url != $basePath)
			$db->replaceBaseDir($url);
		echo "success";
		exit;
	}
	else {
		$db->cleanDir();
		echo "Sucessfully reset the base-path";
	}
}
catch(Exception $e) {
	echo $e->getMessage();
}
?>
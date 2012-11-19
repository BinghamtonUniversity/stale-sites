<?php
include_once 'Database.php';
include_once 'config.php';
if(!isset($_POST['url']) && !isset($_GET['url']) ) {
	echo "Please check your provided URL";	
}
$db = new Database;
$ech = true;
if(isset($_POST['url'])) {
	$url = trim($_POST['url']);
}
else {
	$url = trim($_GET['url']);
	$ech = false;
}

try {
	$urls = explode(",", $url);

	$db->addExcludePathDir($urls);
	if($ech) {
		echo "success";
		exit;
	}
	else {
		header("Location: index.php");
	}
}
catch(Exception $e) {

	if($ech) {
		
		echo $e->getMessage();
	}
	else {
		header("Location: index.php?error=".urlencode($e->getMessage()));
	}
}
?>
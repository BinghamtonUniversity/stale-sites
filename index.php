<?php  

include_once 'config.php';

//unlink('cache.html')
//set_time_limit(5);
//xdebug_enable();
?>
<!DOCTYPE html>
<html>
<head>
<title>Binghamton University - Stale Sites</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script src="jquery-1.8.2.min.js" type="text/javascript"></script>
<script src="custom.js" type="text/javascript"></script>
</head>
<body>

<div id="content">
	<h1> Stale sites v1.6 </h1>
	<h2>Binghamton University</h2>
	<p>
		<lable> Base Path:
			<input type="text" name="base-dir" id="base-dir" value="<?php echo $basePath; ?>"/>
			<input type="button" name="update-button" id="update-button" value="Update"/>
		</lable>
	</p>
	<h2 id="status-indicator">
	<?php if($basePath === null) {
		?>
		 Please setup your base-path first. 
		<?php
	}
	else {
		?>
		Below are your stale-sites.

		<?php
	}
	?>
	</h2>
	<div id="main-text">
		<?php
		if($basePath !== null) include_once("getStaleSites.php");
		?>
	</div>
</div>
</body>
</html>

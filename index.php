<?php  

/**
 * Short description goes here
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category Stale_Sites
 * @package  Stale_Sites
 * @author   Patrick Lewis <plewis@binghamton.edu>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     http://www2.binghamton.edu/
**/
include_once 'config.php';

//unlink('cache.html')
//set_time_limit(5);
//xdebug_enable();
?>
<!DOCTYPE html>
<html>
<head>
<title>Stale Site - Binghamton university.</title>
<link href="style.css" rel="stylesheet" type="text/css">
<script src="jquery-1.8.2.min.js" type="text/javascript"></script>
<script src="custom.js" type="text/javascript"></script>
</head>
<body>

<div id="content">
	<h1> Stale sites v1.6 </h1>
	<h2> SUNY Binghamton </h2>
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
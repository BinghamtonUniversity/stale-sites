<?php  
include_once 'config.php';

//unlink('cache.html')
//set_time_limit(5);
//xdebug_enable();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Stale Sites</title>
  	<script src="js/jquery.js"></script>
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

  </head>
  <body>
  	<div class="container-fluid">
  		
  		<div class="row-fluid">	
  			<h1 class="span12" style="text-align: center"> Stale sites <small>v1.6</small> </h1>
		</div>
		<div class="row-fluid" style="text-align:center">
  			<img src="img/bulogo.png" width="168px" height="55px" class="img-polaroid">
  		</div>

		<div class="row-fluid"><div class="span12"></div></div>	<!-- Extra space -->
	  <div class="row-fluid">
	    <div class="span2">
	     
			
				<lable> <strong>Base Path</strong>:<br>
					<div class="input-append">
					  <input class="span8" name="base-dir" id="base-dir" value="<?php echo $basePath; ?>" type="text"/>
					  <button type="button" name="update-button" id="update-button" value="Update" class="btn btn-primary">Update</button>
					</div>
				</lable>
			
			<br><br>
				<lable> <strong>Add exclude Path/s : </strong>

					<div class="input-append">
						<input type="text" name="exclude-dir" id="exclude-dir" value="" placeholder="comma seperated input" class="span8"/>
						<button type="button" name="exclude-button" id="exclude-button" class="btn btn-danger">Exclude</button>
					</div>
				</lable>
				<small>Note: Relative paths are ignored from base path</small>

			<br><br>
				<strong>Current Excluded paths</strong>: 
				<div id = "ignoredSites">
					<?php include_once("getIgnoredSites.php"); ?>
				</div>

			
	    </div>
	    <div class="well span10">
	    	<div id="status-indicator">

			<?php 
			if(isset($_GET['error']))  {
				?>
				<span class="alert alert-block alert-error">
					<?=$_GET['error'];?>
				</span>
				<?php
			}

			if($basePath === null) {
				?>
				<span class="alert alert-block alert-error">
				 Please setup your base-path first. 
				</span>
				<?php
			}
			?>
			</div>
	      <!--Body content-->
	      	<div class="offset2" id="main-text">
	      	<?php
				if($basePath !== null) include_once("getStaleSites.php");
			?>
			</div>
	    </div>
	  </div>
	</div>
  	<script src="js/bootstrap.min.js"></script>
	<script src="js/custom.js" type="text/javascript"></script>
  </body>
  
</html>

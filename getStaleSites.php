<?php
include_once 'SiteScanner.class.php';
include_once 'config.php';

if($basePath !== null) {
	try {
		$ss = new SiteScanner($basePath, $ignoredSites, $ignoredFileNames);
		$ss->scanSites();
		//$ss->displayReport($basePath); //Bug https://github.com/BinghamtonUniversity/stale-sites/issues/10
		$ans = $ss->getReport("http://".$_SERVER['HTTP_HOST']);

		?>
		<table class="table table-striped table-hover table-bordered">
			<tr>
				<td>X</td>
				<td>Name</td>
				<td>Days</td>
			</tr>
			<?php
			foreach ($ans as $key => $value) {
			?>
			<tr>
				<td><a class="icon-remove" href="addExcludeUrls.php?url=<?=urlencode($key)?>"></a></td>
				<td><a href="/<?=$key?>"><?=$key?></a></td>
				<td><?=$value?></td>
			</tr>
			<?php
			}
			?>
		</table>
	<?php
	}
	catch(Exception $e) {
		echo $e->getMessage();
	}
}
?>
<?php
include_once 'SiteScanner.class.php';
include_once 'config.php';

if($basePath !== null) {
	try {
		$ss = new SiteScanner($basePath, $ignoredSites, $ignoredFileNames);
		$ss->scanSites();
		//$ss->displayReport($basePath); //Bug https://github.com/BinghamtonUniversity/stale-sites/issues/10
		$ans = $ss->getReport();
		asort($ans);

        $intervals = array(
            "24+ Months Old" => 730,
            "18+ Months Old" => 545,
            "12+ Months Old" => 365,
            "6+ Months Old" => 180,
            "3+ Months Old" => 90
            );
		?>
		<table class="table table-striped table-hover table-bordered" width="50%">
			<tr>
				<td width="10%"><strong>Exclude</strong></td>
				<td width="80%"><strong>Name</strong></td>
				<td width="10%"><strong>Days</strong></td>
			</tr>
			<?php
			foreach ($intervals as $interval_key => $interval_value) {
				$started = false;
				foreach ($ans as $key => $value) {
						if(intval($value) > $interval_value) {
							if(!$started) { $started = true;
								?>
								<tr>
									<td colspan="3" style="text-align:center;"><?=$interval_key?></td>
								</tr>
							<?php } ?>	
						<tr>
							<td><a class="icon-remove" href="addExcludeUrls.php?url=<?=urlencode($key)?>">X</a></td>
							<td><a href="<?="http://".$_SERVER['HTTP_HOST'].'/'.$key?>"><?=$key?></a></td>
							<td><?=$value?></td>
						</tr>
					<?php
					unset($ans[$key]);
					}
				}
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
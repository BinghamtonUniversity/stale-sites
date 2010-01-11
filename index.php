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

$basePath = '/opt/local/apache2/htdocs/www2.binghamton.edu';

chdir($basePath) or die("Could not get new working directory $basePath");

$sites = glob('*', GLOB_ONLYDIR);

foreach ($sites as $dir) {
    echo "Most recent file in $dir: ", date(DATE_RSS, lastModified($dir)), "\n";
}

?>
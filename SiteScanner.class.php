<?php
include_once 'config.php';
/**
 * Implements a class that will scan top-level subdirectories within a given
 * directory, filtering them to remove unwanted directories. Generates an HTML 
 * report (cached for 1 hour) listing directories that have no .html files that have
 * been edited in the last 30 days or less. The results are grouped by specified
 * intervals.
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
 * @link     http://www2.binghamton.edu/patrick/stale-sites/
**/
 
/**
 * SiteScanner class
 * 
 * @category Stale_Sites
 * @package  Stale_Sites
 * @author   Patrick Lewis <plewis@binghamton.edu>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     http://www2.binghamton.edu/patrick/stale-sites/
**/
class SiteScanner
{
    // flag used to track if a report has been generated in the past hour
    private $_cacheOutdated = true;
    
    // array of directories to be scanned
    private $_sites         = array();    

    // associative array of directories and their 'last-modified' timestamps
    private $_siteAges      = array();
    
    // directory that will contain the cached HTML output
    private $_outputDir;
    
    /**
     * Class constructor
     *
     * If a report has been cached within the past hour, a flag is set that skips
     * the directory processing and the cached file will be output.
     *
     * @param string $basePath     the site root
     * @param array  $ignoredSites array of directories to ignore
     *
     * @return null
    **/
    function __construct($basePath, Array $ignoredSites)
    {   
        $this->_outputDir = getcwd();
        
        // determine if a report has been cached in the past hour or not
        if(file_exists('./cache.html')) {
            if (mktime() - filemtime('./cache.html') < 3600) {
                $this->_cacheOutdated = false;
            }
        }
        
        if (!is_dir($basePath)) {
            throw new Exception("$basePath is not a directory.");
        }
        
        // change to the path specified or throw an exception
        if (!chdir($basePath)) {
            throw new Exception("Could not get new working directory ${basePath}.");
        }
                
        // retrieve an array of all directories in the specified path
        $unfilteredSites = glob('*', GLOB_ONLYDIR);
        
        // remove 'ignored' directories from the array
        $this->_sites = array_diff($unfilteredSites, $ignoredSites);
    }
    
    /**
     * Return the timestamp of the most recently modified .html file in the specified
     * directory or any of its subdirectories.
     * 
     * @param string $dir directory to scan for .html files
     * 
     * @return int timestamp of the most recent .html file (default: 0)
    **/
    private function _lastModified($dir)
    {
        $newest = 0;
        
        // skip the directory if it can't be read due to filesystem permissions
        try {
            $it = new RecursiveDirectoryIterator($dir);            
        } catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), "<br />\n";
            return 0;
        }
        
        // scan through the directory and its subdirectories to determine the
        // most recent .html file contained within
        try {
            foreach (new RecursiveIteratorIterator($it) as $file) {
                if (fnmatch('*.php', $file)) {
                    if (filemtime($file) > $newest) {
                        $newest = filemtime($file);
                    }
                }
            }        
        } catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), "<br />\n";          
        }

        return $newest;
    }
    /**
     * Return the  average timestamp of the edited .html files in the specified
     * directory or any of its subdirectories.
     * 
     * @param string $dir directory to scan for .html files
     * 
     * @return int timestamp of the most recent .html file (default: 0)
    **/
    public function _avgModified($dir)
    {
        $avg = 0;
        $tmpTotal = 0;
        $tmpCnt = 0;
        $totalFilesScanned = 0;
        $totals = array();
       // $totalsCount = array();
        $numberOfTotals = 0;
//echo PHP_INT_MAX."\n";
        $maxIntVal = PHP_INT_MAX - time() - 1; //-1 just to ensure its still within range

        // skip the directory if it can't be read due to filesystem permissions
        try {
            $it = new RecursiveDirectoryIterator($dir);            
        } catch (Exception $e) {
            //echo 'Caught exception: ',  $e->getMessage(), "<br />\n";
            //echo "here";
            return 0;
        }
        
        // scan through the directory and its subdirectories to determine the
        // most recent .html file contained within
        try {
            foreach (new RecursiveIteratorIterator($it) as $dirLevel => $file) {

                if (fnmatch('*.html', $file)) {
                    if($tmpTotal < $maxIntVal) {
                        //echo filemtime($file)."\n";
                        $tmpTotal += filemtime($file);
                        $tmpCnt ++;
                        $totalFilesScanned++;
                    }                        
                    else { //echo "YESS\n";
                        $totals[$numberOfTotals] = $tmpTotal;
                        //$totalsCount[$numberOfTotals] = $tmpCnt;
                        $numberOfTotals++;
                        $tmpCnt = 0;
                        $tmpTotal = 0;
                    }
                }
            }
            $totals[$numberOfTotals] = $tmpTotal;
            //$totalsCount[$numberOfTotals] = $tmpCnt;
            $numberOfTotals++;
            $tmpCnt = 0;
            $tmpTotal = 0;

            //Time to calculate the average!!!
//var_dump($totals);
//var_dump($totalsCount);
//var_dump($totalFilesScanned);
            $avg = 0.0;

            if($totalFilesScanned != 0) {
                foreach ($totals as $i => $currentTotal) {
                    //echo "here $currentTotal $totalFilesScanned \n";
                    $avg += $currentTotal/$totalFilesScanned;
                }
            }

            return intval($avg);

        } catch (Exception $e) { //comes here if read permission denied
             //echo 'Caught exception: ',  $e->getMessage(), "<br />\n";          
        }

        //error
        return 0;
    }
    /**
     * Return the amount of days that have passed between the specified date and
     * today (ignoring time of day so that if it's 3pm today, a timestamp of 6pm
     * last night will still be reported as 1 day old).
     * 
     * @param int $timestamp timestamp that we're comparing to
     * 
     * @return int number of days that have passed since $timestamp
    **/    
    private function _daysOld($timestamp)
    {   
        // to compare dates and ignore time-of-day, make the comparison
        // vs. midnight today by using mktime(24)
        $diff = mktime(24) - $timestamp;
        
        return floor($diff / (86400)); // 86400 seconds = 1 day
    }
    
    /**
     * Find the timestamp of each directory. 
     *
     * @return null
    **/
    public function scanSites()
    {           
        if ($this->_cacheOutdated) {
            foreach ($this->_sites as $dir) {
                //$siteAge = $this->_lastModified($dir);
                $siteAge = $this->_avgModified($dir);
                if ($siteAge > 0) {
                    $this->_siteAges[$dir] = $siteAge;
                }
            }
            asort($this->_siteAges);                
        }               
    }
            
    /**
     * Generate the report with results grouped by specified intervals.
     * 
     * The HTML is generated and stored in cache.html if needed (depending on the
     * age of the most recently cached version) and then the contents of cache.html
     * are output.
     *
     * @return null
    **/    
    public function displayReport($urlStart = NULL)
    {       
        chdir($this->_outputDir);

        if($urlStart !== NULL) {
            if(substr($urlStart, -1) !== "/") {
                $urlStart .="/";
            }
        }
        // var_dump($this->_cacheOutdated);
        // generate a new report if the currently cached one is outdated,
        // otherwise just output the cached version
        if ($this->_cacheOutdated) {
            $intervals = array(
                "6+ Months Old" => 180,
                "3+ Months Old" => 90,
                "60+ Days Old"  => 60,
                "30+ Days Old"  => 30,
                "1+ Days Old"  => 1
                );        

            $siteAge = $this->_daysOld(current($this->_siteAges));               
       
            $cache = fopen('cache.html', 'w');

            // iterate through each interval, printing a heading and then
            // listing all sites that fall under that interval before
            // moving on to the next one. sites newer than the shortest
            // interval will not be inlcuded in the report.
            //var_dump($this->_siteAges);
            while (current($intervals)) {

            //var_dump($siteAge);
            //var_dump(current($intervals));
                fwrite($cache, "<h2>" . key($intervals) . "</h2>\n");
                if ($siteAge >= current($intervals)) {
                    while ($siteAge >= current($intervals) && current($this->_siteAges) !==false) {
                        fwrite(
                            $cache, '<a href="' .$urlStart .
                            key($this->_siteAges) .
                            '/">' .
                            key($this->_siteAges) .
                            "</a> (" . 
                            $siteAge . 
                            " days old)<br/>\n"
                        );
                        if(next($this->_siteAges) !==false) {
                            $siteAge = $this->_daysOld(current($this->_siteAges));
                            //echo 'here - '.$siteAge.' ';   
                        }
                        else
                        {
                            break;
                        }
                    }
                }
                
                next($intervals);

                fwrite($cache, "<br/>\n");
            }

            fclose($cache);
        }
        else {
            echo "Cached output being displayed: <br/>";
        }
        echo file_get_contents('cache.html');                      
    }
}

?>
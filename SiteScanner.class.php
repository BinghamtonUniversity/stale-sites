<?php

/**
 * Class description here
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
 
/**
 * SiteScanner class
 * 
 * @category Stale_Sites
 * @package  Stale_Sites
 * @author   Patrick Lewis <plewis@binghamton.edu>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link     http://www2.binghamton.edu/
**/
class SiteScanner
{
    private $_cacheOutdated = true;
    private $_sites         = array();    
    private $_ignoredSites  = array();
    private $_siteAges      = array();
    private $_outputDir;
    
    /**
     * Class constructor
     *
     * @param string $basePath     the site root
     * @param array  $ignoredSites array of directories to ignore
     *
     * @return null
    **/
    function __construct($basePath, Array $ignoredSites)
    {   
        $this->_outputDir = getcwd();

        if (mktime() - filemtime('cache.html') < 3600) {
            $this->_cacheOutdated = false;
        }
        
        if (!is_dir($basePath)) {
            throw new Exception("$basePath is not a directory.");
        }
        
        // Change to the path specified or throw an exception
        if (!chdir($basePath)) {
            throw new Exception("Could not get new working directory ${basePath}.");
        }
                
        // Retrieve an array of all directories in the specified path
        $unfilteredSites = glob('*', GLOB_ONLYDIR);
        
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

        try {
            $it = new RecursiveDirectoryIterator($dir);            
        } catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), "<br />\n";
            return 0;
        }
        
        try {
            foreach (new RecursiveIteratorIterator($it) as $file) {
                if (fnmatch('*.html', $file)) {
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
     * Check every directory to find their timestamps
     *
     * @return null
    **/
    public function scanSites()
    {           
        if ($this->_cacheOutdated) {
            foreach ($this->_sites as $dir) {
                $siteAge = $this->_lastModified($dir);
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
     * @return null
    **/    
    public function displayReport()
    {       
        chdir($this->_outputDir);

        if ($this->_cacheOutdated) {
            $intervals = array(
                "6+ Months Old" => 180,
                "3+ Months Old" => 90,
                "60+ Days Old"  => 60,
                "30+ Days Old"  => 30
                );        

            $siteAge = $this->_daysOld(current($this->_siteAges));               
       
            $cache = fopen('cache.html', 'w');

            while (current($intervals)) {
                if ($siteAge >= current($intervals)) {
                    fwrite($cache, "<h2>" . key($intervals) . "</h2>\n");
                    while ($siteAge >= current($intervals)) {
                        fwrite(
                            $cache, '<a href="/' .
                            key($this->_siteAges) .
                            '/">' .
                            key($this->_siteAges) .
                            "</a> (" . 
                            $siteAge . 
                            " days old)<br/>\n"
                        );

                        $siteAge = $this->_daysOld(next($this->_siteAges));
                    }
                }
                next($intervals);
                fwrite($cache, "<br/>\n");
            }

            fclose($cache);
        }        

        echo file_get_contents('cache.html');                      
    }
}

?>

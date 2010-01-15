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
    /**
     * Class constructor
     *
     * @param string $basePath the site root
     *
     * @return null
    **/
    function __construct($basePath)
    {   
        if (!is_dir($basePath)) {
            throw new Exception("$basePath is not a directory.");
        }
        
        // Change to the path specified or throw an exception
        if (!chdir($basePath)) {
            throw new Exception("Could not get new working directory ${basePath}.");
        }
        
        // Retrieve an array of all directories in the specified path
        $this->sites = glob('*', GLOB_ONLYDIR);
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
            echo 'Caught exception: ',  $e->getMessage(), "\n";
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
            echo 'Caught exception: ',  $e->getMessage(), "\n";          
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
        $siteAges = array();

        echo "\n<pre>";
                
        foreach ($this->sites as $dir) {
            $siteAge = $this->_lastModified($dir);
            if ($siteAge > 0) {
                $siteAges[$dir] = $siteAge;
            }
        }

        asort($siteAges);                

        foreach ($siteAges as $site => $siteAge) {
            echo date('Y-m-d', $siteAge), 
                " => /${site}", 
                " (", 
                $this->_daysOld($siteAge), 
                " days old)\n";
        }

        echo "</pre>";
    }
}

?>

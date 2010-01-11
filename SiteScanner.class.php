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
     */
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

        $it = new RecursiveDirectoryIterator($dir);

        foreach (new RecursiveIteratorIterator($it) as $file) {
            if (fnmatch('*.html', $file)) {
                if (filemtime($file) > $newest) {
                    $newest = filemtime($file);
                }
            }
        }

        return $newest;
    }
    
    public function scan()
    {
        foreach ($this->sites as $dir) {
            $dirAge = $this->_lastModified($dir);
            echo "Most recent file in $dir: ",
                date(DATE_RSS, $dirAge), "\n";
        }                
    }
}

?>

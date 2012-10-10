# Stale Site Reporter

This package generates a report that identifies stale directories within a given website structure.

## Notes

* A directory is considered to be as new as the most-recently-edited .html file contained within that directory or its subdirectories.
* Top-level directories are included in the report by default. They can be excluded from the report, and subdirectories can be flagged for inclusion in the report as needed.
* Only sites older than the configured staleness threshold will be included in the report. 
* Currently it calculates the age of a directory based on the average age of the *.html files in it.


Commenting the new code is still left out.

Some important stuff:
The problem, as it turns out, is that the PDO SQLite driver requires that if you are going to do a write operation (INSERT,UPDATE,DELETE,DROP, etc), then the folder the database resides in must have write permissions, as well as the actual database file.

sqlite version 3.7.9 is used



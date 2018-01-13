# MultiPoolMiner Monitoring

This is designed to run on a webserver somewhere to monitor all your MultiPoolMiner workers.

## Installation
Install on a webserver with Apache and PHP.  Set the document root to the /web directory

On your workers, set `$MinerStatusURL = "http://your.website.com/miner.php"

## What information is reported?

* Your BTC address, which is needed to lookup your workers
* Worker name
* Time the worker last reported to the server
* Total profit for the worker
* For each running miner:
  * Name
  * Path (relative to the MultiPoolMiner directory so it won't include your computer username or anything)
  * Arguments
  * Type (NVIDIA, AMD, CPU)
  * Time it's been running
  * Current and estimated speeds
  * PID
  * Pool URL
  * Estimated BTC/day

## Where is the information stored?

Data is stored in MySQL.  Edit the web/private/config.php file to match your settings.

Make sure the .htaccess file blocks access to files in web/private.
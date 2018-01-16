# MultiPoolMiner Monitoring

This is designed to run on a webserver somewhere to monitor all your MultiPoolMiner workers.

Currently it only works with my version at https://github.com/grantemsley/MultiPoolMiner

## Installation
Install on a webserver with Apache and PHP.  Set the document root to the /web directory

On your workers, set 

    $MinerStatusURL = "http://your.website.com/miner.php"

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

In an SQLite3 database in the main directory.  The database file gets created automatically.  If you want to clear all data, simply delete miners.sqlite3.

I chose to use an SQLite database to simplify installation - no need to setup username/password for a MySQL database or anything.

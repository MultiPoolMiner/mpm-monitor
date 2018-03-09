<?php
// This file is intended to be run as a scheduled task/cron job on your server.
// It removes miners that have not been seen in the last 7 days.

require dirname(__FILE__).'/functions.php';
cleanup();
?>

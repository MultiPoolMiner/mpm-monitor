<?php
if(empty($_REQUEST['address'])) { echo "Failure"; exit; }
if(empty($_REQUEST['workername'])) { echo "Failure"; exit; }

// Prevent the reporting of ridiculously large profit amounts 
if($_REQUEST['profit'] > 0.02) { echo "Invalid Profit Amount!"; exit; }

//Force users to use valid addresses or status keys
if(strlen($_REQUEST['address']) < 32) { echo "Please use a valid address or generate a status key! Exiting..."; exit; }

require dirname(__FILE__).'/private/functions.php';
if(empty($_REQUEST['miners'])) { $_REQUEST['miners'] = ''; }
if(empty($_REQUEST['profit'])) { $_REQUEST['profit'] = 0; }

update_worker($_REQUEST['address'], $_REQUEST['workername'], $_REQUEST['miners'], $_REQUEST['profit']);
echo "Success";
?>

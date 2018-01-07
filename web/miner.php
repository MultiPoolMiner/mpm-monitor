<?php
if(empty($_REQUEST['address'])) { echo "Failure"; exit; }
if(empty($_REQUEST['workername'])) { echo "Failure"; exit; }

require dirname(__FILE__).'/private/functions.php';
if(empty($_REQUEST['miners'])) { $_REQUEST['miners'] = ''; }
if(empty($_REQUEST['profit'])) { $_REQUEST['profit'] = 0; }

update_worker($_REQUEST['address'], $_REQUEST['workername'], $_REQUEST['miners'], $_REQUEST['profit']);
echo "Success";
?>

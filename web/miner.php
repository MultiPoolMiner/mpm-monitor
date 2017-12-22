<?php
if(empty($_GET['address'])) { echo "Failure"; exit; }
if(empty($_GET['workername'])) { echo "Failure"; exit; }

include('../functions.php');
if(empty($_GET['miners'])) { $_GET['miners'] = ''; }
if(empty($_GET['profit'])) { $_GET['profit'] = 0; }

update_worker($_GET['address'], $_GET['workername'], $_GET['miners'], $_GET['profit']);
echo "Success";
?>

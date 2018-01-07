<?php
if(empty($_GET['address'])) { echo 'Error: Address not specified.';exit; }

require dirname(__FILE__).'/private/functions.php';

$workers = get_workers_json($_GET['address']);
if(empty($workers)) { echo "Error: No workers found."; exit;}

header('Content-Type: application/json');
echo $workers;
?>

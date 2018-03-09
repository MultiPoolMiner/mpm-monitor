<?php
header('Content-Type: application/json');

if(empty($_GET['address'])) { 
  $workers = json_encode(array("error" => 'Error: Address not specified.'));
} else {
  require dirname(__FILE__).'/private/functions.php';
  $workers = get_workers_json($_GET['address']);

  if(empty(json_decode($workers, true))) { 
    $statusurl = ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];
    if(dirname($_SERVER['PHP_SELF']) == "/") {
      $statusurl .= "miner.php";
    } else {
      $statusurl .= dirname($_SERVER['PHP_SELF']) . '/miner.php';
    }

    $workers = json_encode(array("error" => "<center><span style='color: #C00;'>Error: No workers found. Make sure your have set:<pre>-MinerStatusURL $statusurl
    -MinerStatusKey {$_GET['address']}</span></center>"));
  }
}

echo $workers;
?>

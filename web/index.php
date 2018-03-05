<?php ini_set('display_errors','On');?>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="style.css"/>
  <meta http-equiv="refresh" content="120"/>
</head>

<body>
<form method="GET">
  Address: <input type="text" name="address" value="<?=$_GET['address']?>"/><input type="submit" value="Submit"/>
</form>


<?php
if(empty($_GET['address'])) { exit; }

include('../functions.php');
date_default_timezone_set('UTC');

$workers = get_workers($_GET['address']);
if(empty($workers)) { echo "Nothing found."; exit;}

echo "<table class='workers'><tr><th>Worker Name</th><th>Status</th><th>Last Seen</th><th>BTC/Day</th><th>Active Miners</th></tr>";

foreach($workers as $worker) {
  $workerstatus = json_decode($worker['miners']);
  echo "<tr>";
  echo "<td>{$worker['workername']}</td>";

  # Set the class to running if it has been seen in the last 5 minutes, stopped if last seen within 24 hours, and unknown if seen more than 24 hours ago
  if($worker['lastseen'] >= strtotime('-5 minutes')) {
    echo "<td class='running'>Running</td>";
  } elseif ($worker['lastseen'] >= strtotime('-24 hours')) {
    echo "<td class='stopped'>Stopped</td>";
  } else {
    echo "<td class='unknown'>Not seen in 24h</td>";
  }

  echo "<td>" . date("Y-m-d H:i:s", $worker['lastseen']) . "</td>";
  echo "<td>" . number_format($worker['profit'],8) . "</td>";
  echo "<td>";

  if(count($workerstatus) > 0) {
    echo "<table class='miners'><tr><th>Name</th><th>Type</th><th>Pool</th><th>Path</th><th>Active</th><th>Algorithm</th><th>Current Speed</th><th>Estimated Speed</th><th>PID</th><th>BTC/day</th></tr>";

    foreach($workerstatus as $m) {
      $currentspeeds = array();
      foreach((array)$m->CurrentSpeed as $cs) {
        $currentspeeds[] = ConvertToHashrate($cs);
      }

      $estimatedspeeds = array();
      foreach((array)$m->EstimatedSpeed as $es) {
        $estimatedspeeds[] = ConvertToHashrate($es);
      }

      $mName = (array_key_exists('Name', $m)) ? $m->Name : "";
      $mType = (array_key_exists('Type', $m)) ? $m->Type : "";
      $mPool = (array_key_exists('Pool', $m)) ? $m->Pool : "";
      $mPath = (array_key_exists('Path', $m)) ? $m->Path : "";
      $mActive = (array_key_exists('Active', $m)) ? $m->Active : "";
      $mAlgorithm = (array_key_exists('Algorithm', $m)) ? $m->Algorithm : "";
      $mPID = (array_key_exists('PID', $m)) ? $m->PID : "";
      $mBTCperday = (array_key_exists('BTC/day', $m)) ? $m->{'BTC/day'} : "";

      echo "<tr>";
      echo "<td>{$mName}</td>";
      echo "<td>{$mType}</td>";
      echo "<td>{$mPool}</td>";
      echo "<td>{$mPath}</td>";
      echo "<td>{$mActive}</td>";
      echo "<td>{$mAlgorithm}</td>";
      echo "<td>" . implode(",",$currentspeeds) . "</td>";
      echo "<td>" . implode(",",$estimatedspeeds) . "</td>";
      echo "<td>{$mPID}</td>";
      echo "<td>{$mBTCperday}</td>";
      echo "</tr>";
    }
    echo "</table>";
  } else {
    echo "Not reported";
  }
  echo "</td>";
  echo "</tr>";
}
echo "</table>";

?>

</body></html>


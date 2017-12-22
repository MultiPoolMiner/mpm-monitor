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

$workers = get_workers($_GET['address']);
if(empty($workers)) { echo "Nothing found."; exit;}

echo "<table class='workers'><tr><th>Worker Name</th><th>Status</th><th>Last Seen</th><th>Estimated BTC/Day</th><th>Active Miners</th></tr>";

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
  echo "<td>{$worker['profit']}</td>";
  echo "<td>";

  if(count($workerstatus) > 0) {
    echo "<table class='miners'><tr><th>Name</th><th>Type</th><th>Pool</th><th>Path</th><th>Active</th><th>Current Speed</th><th>Estimated Speed</th><th>PID</th><th>BTC/day</th></tr>";

    foreach($workerstatus as $m) {
      echo "<tr>";
      echo "<td>{$m->Name}</td>";
      echo "<td>{$m->Type}</td>";
      echo "<td>{$m->Pool}</td>";
      echo "<td>{$m->Path}</td>";
      echo "<td>{$m->Active}</td>";
      echo "<td>{$m->CurrentSpeed}</td>";
      echo "<td>{$m->EstimatedSpeed}</td>";
      echo "<td>{$m->PID}</td>";
      echo "<td>{$m->{'BTC/day'}}</td>";
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


<?php

// Connect to the database, or create it if the file doesn't exist
function connect_database() {
  require dirname(__FILE__).'/config.php';

  try {
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  } catch(PDOException $e) {
    die("Datbase connection failed. Please try again later.");
  }
  
  # Create the tables if they don't exist
  $db->exec("CREATE TABLE IF NOT EXISTS workers (
      address VARCHAR(100),
      workername VARCHAR(100),
      lastseen INTEGER,
      miners TEXT,
      profit DOUBLE,
      PRIMARY KEY(address,workername)
    );");
  return $db;
}

// Get all the workers matching that address.
function get_workers($address) {
  $db = connect_database();
  $query = "SELECT * FROM workers WHERE address = :address ORDER BY workername";

  $stmt = $db->prepare($query);
  $stmt->bindParam(':address', $address);
  $result = $stmt->execute();

  $workers = [];
  while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $workers[] = $row;
  }
  return($workers);
}

function get_workers_json($address) {
  $db = connect_database();
  $query = "SELECT * FROM workers WHERE address = :address ORDER BY workername";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':address', $address);
  $result = $stmt->execute();

  $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Decode the miners field so it can be json encoded properly
  foreach($workers as $key => $worker) {
    $workers[$key]['miners'] = json_decode($workers[$key]['miners']);
  }

  return json_encode($workers, JSON_PRETTY_PRINT);
}

// Update a worker's last seen timestamp.
function update_worker($address, $workername, $miners = "", $profit = 0) {
  $now = time();

  $db = connect_database();
  $query = "REPLACE INTO workers (address, workername, miners, profit, lastseen)
    VALUES (:address, :workername, :miners, :profit, :lastseen);";

  $stmt = $db->prepare($query);
  $stmt->bindParam(':address', $address);
  $stmt->bindParam(':workername', $workername);
  $stmt->bindParam(':miners', $miners);
  $stmt->bindParam(':profit', $profit);
  $stmt->bindParam(':lastseen', $now);
  $stmt->execute();
}

// Remove any miners across all accounts that haven't been seen for 7 days
function cleanup() {
  $now = time();
  $olderthan = $now - (7 * 24 * 60 * 60);

  $db = connect_database();
  $query = "DELETE FROM workers WHERE lastseen < :olderthan;";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':olderthan', $olderthan);
  $stmt->execute();
}

function ConvertToHashrate($value) {
  # If value is not just a number, return it as is
  if(!is_numeric($value)) { return $value; }

  $units = array('H/s','KH/s','MH/s','GH/s','TH/s','PH/s');

  $pow = floor(($value ? log($value) : 0) / log(1000));
  $pow = min($pow, count($units) -1);

  $value /= pow(1000, $pow);
  return round($value, 2) . ' ' . $units[$pow];
}

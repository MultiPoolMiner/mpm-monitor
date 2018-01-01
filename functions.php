<?php

// Connect to the database, or create it if the file doesn't exist
function connect_database() {

  $dbpath = 'sqlite:' . dirname(__FILE__) . '/miners.sqlite3';
  $db = new PDO($dbpath);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  # Create the tables if they don't exist
  $db->exec("CREATE TABLE IF NOT EXISTS workers (
      address TEXT NOT NULL,
      workername TEXT NOT NULL,
      lastseen INTEGER,
      miners TEXT,
      profit REAL,
      PRIMARY KEY(address,workername)
    ) WITHOUT ROWID;");
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

// Update a worker's last seen timestamp.
function update_worker($address, $workername, $miners = "", $profit = 0) {
  $now = time();

  $db = connect_database();
  $query = "INSERT OR REPLACE INTO workers (address, workername, miners, profit, lastseen)
    VALUES (:address, :workername, :miners, :profit, :lastseen);";

  $stmt = $db->prepare($query);
  $stmt->bindParam(':address', $address);
  $stmt->bindParam(':workername', $workername);
  $stmt->bindParam(':miners', $miners);
  $stmt->bindParam(':profit', $profit);
  $stmt->bindParam(':lastseen', $now);
  $stmt->execute();
}

function ConvertToHashrate($value) {
  # If value is not just a number, return it as is
  if(!is_numeric($value)) { return $value; }

  $units = array('H/s','KH/s','MH/s','GH/s','TH/s','PH/s');

  $pow = floor(($value ? log($value) : 0) / log(1000));
  $pow = min($pow, count($units) -1);

  $value /= pow(1000, $pow);
  echo "VALUE = $value\n";
  return round($value, 2) . ' ' . $units[$pow];
}

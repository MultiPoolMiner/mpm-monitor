<?php
header('Content-Type: application/json');
require dirname(__FILE__).'/private/functions.php';

$key = generate_key();
echo json_encode($key);
?>

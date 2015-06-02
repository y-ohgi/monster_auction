<?php

require_once('db_connect.php');

$sql = "SELECT * FROM user_master ORDER BY um_id DESC;";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
var_dump($row);
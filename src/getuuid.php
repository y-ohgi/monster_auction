<?php
// getuuid.php

require_once('db_connect.php');

// uuidの作成
$uuid = uniqid();

echo $uuid;

<?php

$dsn = 'mysql:dbname=login_test; host=localhost; charset=utf8;';
$user = 'testuser';
$password = 'bear';

try {
    $dbh = new PDO($dsn, $user, $password);
    // エラーの表示                                                                               
    // あとでtry/catchで行う                                                                      
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    print('Error:'.$e -> getMessage());
    die();
}



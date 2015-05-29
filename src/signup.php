<?php

require_once('controller/Util.inc');
require_once('model/Userdb.inc');
require_once('controller/Page.inc');

// req:
$name = trim(Util::h(@$_POST['name']));
$uuid = Util::h(@$_POST['uuid']);

// res:
$response = array(
    "status"=>null
);
Page::setRes($response);

// validate
if($name == ''){
    Page::complete(450);
}

// db処理
try{
    // ユーザーの登録
    $register = Userdb::regUser($uuid, $name);
    if($register !== true){
        Page::complete(451);
    }
}catch(Exception $error){
    Dbh::get()->rollback();
    Page::complete(550);
}

Page::complete(200);

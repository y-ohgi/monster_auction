<?php

require_once('controller/Util.inc');
require_once('model/Userdb.inc');
require_once('controller/Page.inc');

// req:
$name = trim(Util::h(@$_POST['name']));
$uuid = Util::h(@$_POST['uuid']);

// res:
$response = array(
    "result"=>null,
    "message"=> null
);
Page::setRes($response);


if($name == ''){
    Page::complete(false, '名前が入力されていません');
}

// db処理
try{
    $register = Userdb::regUser($uuid, $name);
    if($register !== true){
        Page::complete(false, $register);
    }
}catch(Exception $error){
    Db::getDbh()->rollback();
    Page::complete(false, 'db error');
}

Page::complete(true, 'Success!!');

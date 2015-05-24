<?php

//require_once('init.php');
require_once('controller/Util.inc');
//require_once('db_connect.php');
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
    Page::complete(false, '何も入力されていません');
}

// db処理
try{
    Db::getDbh()->beginTransaction();

    if(Userdb::regUser($uuid, $name) !== true){
        Page::complete(false, '名前が存在しています');
    }
    Db::getDbh()->commit();
}catch(Exception $error){
    Db::getDbh()->rollback();
    Db::disconnect();
    Page::complete(false, 'db error');
}

Page::complete(true, 'Success!!');
//echo "登録完了";

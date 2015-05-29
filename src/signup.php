<?php

require_once('controller/Util.inc');
require_once('model/UM.inc');
require_once('model/UserDao.inc');
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

// ユーザーの登録
$code = UserDao::regUser($uuid, $name);
Page::complete($code);

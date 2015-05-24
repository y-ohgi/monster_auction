<?php


require_once('model/Userdb.inc');

// //Userdb::getRecord('5560e4afedf70');
// echo Userdb::regUser("5562018d6f8ef", "ねーむ");

require_once('controller/Page.inc');

//Page::setRes(array("message"=>"success"));
//Page::setRes(array("result"=>true, "hoge"=>"hoge", "fuga"=>array("hoge"=>"hoge", "fuga","fuga")));
//var_dump(Page::getRes());

$respons = array(
    "result"=>null,
    "message"=> null
);
Page::setRes($respons);
Page::incRes(false, "hoge");

Page::complete();
//Page::complete(false, "hoge");

//echo json_encode(Page::getRes());

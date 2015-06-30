<?php
// 新規登録処理

require_once('common/init.inc');

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');



$uuid = $_POST['uuid'];
$name = $_POST['name'];

$response = array(
    "status"=>null
);
Page::setResponse($response);

try{
    // 既存のuuid/nameではないか
    $sql = "SELECT * FROM user_master WHERE um_uuid = :uuid OR um_name = :name;";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(isset($row['um_id'])){
        Page::complete(EXISTING);
        return;
    }

    // 登録
    $sql = "INSERT INTO user_master(um_uuid, um_name) VALUES(:uuid, :name)";
    $stmt = Dbh::get()->prepare($sql);
    $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();

}catch(Exception $e){
    Page::complete(SERVER_ERROR);
    return;
}

Page::complete(SUCCESS);

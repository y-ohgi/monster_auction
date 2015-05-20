<?php
// roomcreate.php

/**
 * ルームを作成
 *
 * req:
 *  $_POST['uuid'];
 *  $_POST['room_title'];
 *  $_POST['room_max'];
 * res: 
 *  {
 *    "room_id": room_id,
 *    "message": message
 *  }
 * 
 */

require_once('init.php');
require_once('db_connect.php');

// req:
$uuid = h(@$_POST['uuid']);
$room_title = h(@$_POST['room_title']);
$room_max = h(@$_POST['room_max']);
// res:
$respons = array(
    "room_id"=> null,
    "message"=> null
);

// 終了処理
// どうしようもなかった。
function endProces($id, $msg){
    $dbh = null;
    $respons['room_id'] = $id;
    $respons['message'] = $msg;

    echo json_encode($respons);
    exit();
}

// postじゃなかった場合
if($_SERVER["REQUEST_METHOD"] != "POST"){
    endProces(null, "postじゃないです");
}


try{
    // uuidでroom_masterからユーザーをselectする
    // 存在しなかった場合はfalseを返しexit
    $sql = 'SELECT um_id FROM user_master WHERE um_uuid = :uuid;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();
    if(!$stmt->fetchColumn()){
        endProces(null, "uuidが存在しません");
    }

    // room_titleとroom_maxを登録し、登録ができた場合はtrue
    $sql = 'INSERT INTO room_master(rm_title, rm_max, rm_stat) VALUES(:title, :max, "wait");';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':title', $room_title, PDO::PARAM_STR);
    $stmt->bindValue(':max', $room_max, PDO::PARAM_STR);
    $stmt->execute();
    $id = $dbh->lastInsertId();
}catch(Exception $e){
    endProces(null, "エラーです");
}

endProces($id, "Success!!");
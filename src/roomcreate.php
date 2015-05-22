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
 *    "message": "Success"
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

    echo json_encode($respons, JSON_UNESCAPED_UNICODE);
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
    $room_id = $dbh->lastInsertId();

    // user_masterにuuidで指定されたユーザーにrm_idを登録
    // MEMO: room_joinでも同じことをするが、一時的に現在人数が0人にならないようにする為。
    $sql = 'UPDATE user_master SET um_rm_id = :rm_id WHERE um_uuid = :uuid;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':rm_id', $room_id, PDO::PARAM_INT);
    $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();
}catch(Exception $e){
    endProces(null, "エラーです");
}

endProces($room_id, "Success!!");
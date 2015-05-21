<?php
// gamelobby.php

/**
 * 現在どの状態に居るかを取得する。
 * 取得後、"game$room_stat.php"へ遷移する
 *
 * req:
 *  $_POST['uuid']
 * res:
 *  {
 *    room_stat: "wait", // ルームが待機中か、それとも別のシーンにいるかを返す。
 *    message: "Success!!"
 *  }
 *
 */

require_once('init.php');
require_once('db_connect.php');

// req:
$uuid = h(@$_POST['uuid']);
// res:
$respons = array(
    "room_stat"=> null,
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

try{
    // uuidでroom_masterからユーザーをselectする
    // 存在しなかった場合はfalseを返しexit
    $sql = 'SELECT um_rm_id FROM user_master WHERE um_uuid = :uuid;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();
    $room_id = $stmt->fetchColumn();
    if(!$room_id){
        endProces(null, "uuidが存在しません");
    }

    // rm_statを取得する
    $sql = 'SELECT rm_stat FROM room_master WHERE rm_id = :room_id;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':room_id', intval($room_id), PDO::PARAM_INT);
    $stmt->execute();
    $stat = $stmt->fetchColumn();
}catch(Exception $e){
    endProces(null, "エラーです");
}

endProces($stat, "Success!!");



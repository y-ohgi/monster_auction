<?php
// gamewait.php

/**
 * 現在のルーム内メンバーを取得し、
 *  rm_maxに達した場合
 * 
 *
 * req:
 *  $_POST['uuid']
 * res:
 *  {
 *    "maxflg"=>null,  // true/null
 *    "message"=>null, // エラーが有った場合メッセージを格納
 *    "memberlist"=>array() // ルーム内のメンバーを格納
 *  }
 *
 */

require_once('init.php');
require_once('db_connect.php');

// req:
$uuid = h(@$_POST['uuid']);
// res:
$respons = array(
    "maxflg"=>null,
    "message"=> null,
    "memberlist"=>array()
);


// 終了処理
// どうしようもなかった。
function endProces($msg, $flg=null, $user=null){
    $dbh = null;
    
    $respons['maxflg'] = $flg;
    $respons['message'] = $msg;
    $respons['memberlist'] = $user;

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
        endProces("uuidが存在しません");
    }
    
    // room_idを所持していて、指定時間応答が無い者のum_rm_idをnullにする
    $sql = 'UPDATE room_master SET ';
    
    // user_masterで現在のroom_idを持っているユーザーを取得
    $sql = 'SELECT um_id, um_name FROM user_master WHERE um_rm_id = :room_id;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
    $stmt->execute();
    $now = $stmt->rowCount();
    $member = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 現在のroom_idのrm_maxを取得し$maxflgを作成
    $sql = 'SELECT rm_max FROM room_master WHERE rm_id = :room_id;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
    $stmt->execute();
    $max = $stmt->fetchColumn();
    if($max <= $now){
        $maxflg = true;
    }else{
        $maxflg = false;
    }
    
}catch(Exception $e){
    //endProces(null, "エラーです");
    echo $e->getMessage();
}

endProces("Success!!", $maxflg, $member);


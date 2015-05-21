<?php
// roomjoin.php

/**
 * ルームへ参加
 * req:
 *   $_POST['uuid'];
 *   $_POST['room_id'];
 * res:
 *   {
 *     "result": true, // true/null
 *     "message": "Success" // successかエラーメッセージ
 *   }
 *
 */

require_once('init.php');
require_once('db_connect.php');

// req:
$uuid = h(@$_POST['uuid']);
$room_id = intval(h(@$_POST['room_id'])); // TODO:数字でなかった場合の処理
// res:
$respons = array(
    "result"=> null,
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


// TODO:トランザクション
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

    
    // TODO: 以下はもう少し短くなるはず
    // 指定されたroom_idが満員で無いかのチェック
    $sql = 'SELECT rm_ppl, rm_max FROM room_master WHERE rm_id = :rm_id;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':rm_id', $room_id, PDO::PARAM_INT);
    $stmt->execute();
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $ppl = intval($list[0]['rm_ppl']);
    $max = intval($list[0]['rm_max']);
    // 最大人数に達していた場合
    if($ppl >= $max){
        endProces(null, "最大人数です");
    }

    // user_masterにuuidで指定されたユーザーにrm_idを登録
    $sql = 'UPDATE user_master SET um_rm_id = :rm_id WHERE um_uuid = :uuid;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':rm_id', $room_id, PDO::PARAM_INT);
    $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();

    // user_masterでrm_idをcount()する
    $sql = 'SELECT um_id FROM user_master WHERE um_rm_id = :rm_id;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':rm_id', $room_id, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->rowCount();
    
    // room_masterをupdateし入室
    $sql = 'UPDATE room_master SET rm_ppl = :count WHERE rm_id = :rm_id';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':rm_id', $room_id, PDO::PARAM_INT);
    $stmt->bindValue(':count', $count, PDO::PARAM_INT);
    $stmt->execute();
}catch(Exception $e){
    endProces(null, "エラーです");
}

endProces(true, "Success!!");
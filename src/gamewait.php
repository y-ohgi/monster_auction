<?php
// gamewait.php

/**
 * 現在のルーム内メンバーを取得し、最大になった場合maxflgをtrueにする
 * 
 *
 * req:
 *  $_POST['uuid']
 * res:
 *  {
 *    "maxflg"=>null,  // true/null
 *    "message"=>null, // エラーが有った場合メッセージを格納
 *    "memberlist"=> // ルーム内のメンバーを格納
 *    [ 
 *      {
 *         "um_id":"1",
 *         "um_name":"あ"
 *       },
 *       {
 *         "um_id":"2",
 *         "um_name":"いえ"
 *       }
 *     ]
 *  }
 *
 */

require_once('init.php');
require_once('db_connect.php');

require_once('../lib/Carbon/Carbon.php');
use Carbon\Carbon;

// req:
$uuid = h(@$_POST['uuid']);
// res:
$respons = array(
    "maxflg"=>null,
    "message"=> null,
    "memberlist"=>array()
);

// 何秒前までをアクティブと認めるか.
/*
  $activetime = 30;
  $limittime = Carbon::now()->subSeconds($activetime);
*/


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
    $sql = 'SELECT um_rm_id, um_active FROM user_master WHERE um_uuid = :uuid;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();
    
    $tmp = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    $room_id = $tmp["um_rm_id"];
    $active = $tmp["um_active"];
    if(!$room_id){
        endProces("uuidが存在しません");
    }
    /*    if($active < $limittime || $active == null){
        endProces("タイムオーバーです");
    }

    
    // room_idを所持していて、指定時間応答が無い者のum_rm_idをnullにする
    $sql = 'UPDATE user_master SET um_active = null WHERE um_active > :limittime;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':limittime', $limittime, PDO::PARAM_STR);
    $stmt->execute();

    */
    
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
    echo $e->getMessage();
    endProces(null, "エラーです");
}

endProces("Success!!", $maxflg, $member);


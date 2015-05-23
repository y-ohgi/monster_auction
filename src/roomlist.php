<?php
// roomlist.php

/**
 * ルームへ参加
 * req:
 *   TODO:$_POST['uuid'];
 * res:
 *   {
 *     "rm_id": 1
 *     "rm_title": "部屋名"
 *     "rm_ppl": 1,   // 現在人数
 *     "rm_max": 8    //部屋の格納可能人数
 *   }
 *
 */

require_once('init.php');
require_once('db_connect.php');

require_once('../lib/Carbon/Carbon.php');
use Carbon\Carbon;

// room一覧の連想配列
$roomlist = array();

// 何分前までをルームを生かすか
$closelimit = 24;
$closetime = Carbon::now()->subHours($closelimit);
// 何秒前までをアクティブと認めるか.
$activelimit = 30;
//$activetime = Carbon::now()->subSeconds($activelimit);
$activetime = Carbon::now()->subMinutes($activelimit); // 一時的にn分にする

try{
    //////////////////// 一通り終わるまでroomlist.phpはuuidを必要としない ////////////////////////
    // uuidでroom_masterからユーザーをselectする
    // 存在しなかった場合はfalseを返しexit
    // $sql = 'SELECT um_rm_id, um_active FROM user_master WHERE um_uuid = :uuid;';
    // $stmt = $dbh->prepare($sql);
    // $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    // $stmt->execute();
    
    // $tmp = $stmt->fetch(PDO::FETCH_ASSOC);
    // $room_id = $tmp["um_rm_id"];
    // $active = $tmp["um_active"];
    // if(!$room_id){
    //     //endProces("uuidが存在しません");
    //     echo "uuidが存在しません";
    //     exit();
    // }


    // activelimitに達しているユーザーを検索し、room_masterを更新する
    $sql = 'UPDATE user_master SET um_active = null, um_rm_id = null WHERE um_active > :activetime;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':activetime', $activetime, PDO::PARAM_STR);
    $stmt->execute();
    
    /*
     *   オークションゲームの部屋を作っている
     *
     *  room_master: 部屋は全てここに格納している
     *  user_master: ユーザー情報は全てここに格納している
     * 
     *  user_masterでroom_master.idを持っている者をcountしupdateする
     *  全て一気にやりたいが、方法が分からない。
     *
     * room_masterからclosed以外の全てのレコードを取得し
     *  SELECT rm_id FROM room_master WHERE room_stat != "closed";
     * それを回し、user_masterでroom_master.rm_idを使いカウントし、room_master.rm_pplをUPDATE
     *  UPDATE room_master SET rm_ppl(
     *    SELECT count(um_id) FROM user_master WHERE um_rm_id = :room_id
     *  ) WHERE rm_id = :room_id;
     * 
     */
    $sql = 'SELECT rm_id FROM room_master WHERE rm_stat != "closed";';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $key=>$value){
        //var_dump($value['rm_id']);
        
        $sql = 'UPDATE room_master SET rm_ppl = (SELECT count(um_id) FROM user_master WHERE um_rm_id = :room_id)WHERE rm_id = :room_id;';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':room_id', $value['rm_id'], PDO::PARAM_INT);
        $stmt->execute();
    }

    
    // $closetime時間の間使用されていない物のステータスを更新
    // room_masterのrm_createdが3時間前の物を'closed'へupdate
    // TODO: 'UPDATE room_master SET rm_stat = "closed" WHERE rm_created < :closetime' AND rm_ppl = 0;';
    $sql = 'UPDATE room_master SET rm_stat = "closed" WHERE rm_created < :closetime OR rm_ppl = 0;';
    // $sql = 'UPDATE room_master SET rm_stat = "closed" WHERE rm_created < :closetime';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':closetime', $closetime, PDO::PARAM_STR);
    $stmt->execute();
    
    


    // room_masterからrm_stat = 'wait'の物をselect
    $sql = 'SELECT rm_id, rm_title, rm_ppl, rm_max FROM room_master WHERE rm_stat = "wait"';
    $stmt = $dbh->query($sql);
    $roomlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){
    echo $e->getMessage();
    //endProces(null, "エラーです");
    echo "errorです";
    exit();
}

//endProces("Success!!", $maxflg, $member);
$jlist = json_encode($roomlist, JSON_UNESCAPED_UNICODE);
echo $jlist;

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

// require_once('init.php');
//require_once('db_connect.php');

require_once('controller/Util.inc');
require_once('controller/Page.inc');
require_once('model/Userdb.inc');
require_once('model/Roomdb.inc');

require_once('../lib/Carbon/Carbon.php');
use Carbon\Carbon;

// req:
//==とりあえず使わない
// $name = trim(Util::h(@$_POST['name']));
// if(Userdb::authUser($uuid)){Page::complete(false)}
// res:
$response = array(
    "status"=>null,
    "roomlist"=>array()
);
Page::setRes($response);


// time系の名前とかどうにかしたい

// 何分前までをルームを生かすか
$closelimit = 24;
$closetime = Carbon::now()->subHours($closelimit);

// TODO:クラス分けする
// 何秒前までをユーザーをアクティブと認めるか.
$activelimit = 30;
//$activetime = Carbon::now()->subSeconds($activelimit);
$activetime = Carbon::now()->subMinutes($activelimit); // 一時的にn分にする


try{
    // ユーザー一定時間更新のないを削除
    UM::updUsers($activetime);
    
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
    //=== とらん
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
    //======

    
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

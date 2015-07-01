<?php

require_once('common/init.inc');
//echo ROOT_DIR;
require_once('model/Dbh.inc');

require_once(ROOT_DIR. 'controller/User.inc');
require_once(ROOT_DIR. 'controller/Auction.inc');
// $user = new User("559255e25f6d1");
// echo $user->getId();

require_once(ROOT_DIR. 'controller/Room.inc');

try{
    Dbh::get()->beginTransaction();

    $rm_id = 20;
    $um_id = 2;
    
    $room = new Room();

    $auction = new Auction();

    $monsters = $auction->getAuctionMonsters($rm_id);
    var_dump($monsters);
    
    $room->leave($ru_id);
    $room->delete($rm_id);

    // $sql = "SELECT * FROM room_master WHERE rm_id = :rm_id;";
    // $stmt = Dbh::get()->prepare($sql);
    // $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    // $stmt->execute();
    // $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // var_dump($rows);

    Dbh::get()->commit();
}catch(Exception $e){
    Dbh::get()->rollback();
    echo $e->getMessage();
    //Page::complete(SERVER_ERROR);
    return;
}

//Page::complete(SUCCESS, $rooms);





// monster_auctionに人数分オークション用モンスターを追加
// $sql = "SELECT * FROM monster_master;";
// $stmt = Dbh::get()->prepare($sql);
// $stmt->execute();
// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
// //var_dump($rows);
// //echo count($rows);
// shuffle($rows);
// //var_dump($rows);
// $max = 8;
// $monsters = array_slice($rows, 0, $max);

// var_dump($monsters);



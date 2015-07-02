<?php
require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class Auction{

    // オークションで使用する各種レコードを作成
    public function create($rm_id, $max){
        // monster_auctionと room_masterの 中間テーブルを作成
        $ra_id = $this->_createAuctionManager($rm_id);
        // monster_auctionに人数分のレコードを挿入
        $this->_createAuctionRecode($ra_id, $max);
    }

    public function getRAid($rm_id){
        $sql = "SELECT ra_id FROM room_auction WHERE ra_rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $ra_id = $stmt->fetchColumn();
        return $ra_id;
    }

    // オークションに参加するモンスターリストを取得
    public function getAuctionMonsters($rm_id){
        $ra_id = $this->getRAid($rm_id);

        $sql = "SELECT mm_id, mm_name, mm_price FROM monster_auction LEFT JOIN monster_master ON monster_auction.ma_mm_id = monster_master.mm_id WHERE monster_auction.ma_ra_id = :ra_id;";
        //$sql = "SELECT * FROM monster_auction WHERE ma_ra_id = :ra_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ra_id", $ra_id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $rows;
    }
    
    // Auction関連の削除処理
    // XXX: Room.incに今のところまとめてるが、分ける気がする
    // public function delete($rm_id){
    //     // monster_auctionを消す為にra_idを取得する
    //     $sql = "SELECT ra_id FROM room_auction WHERE ra_rm_id = :rm_id;";
    //     $stmt = Dbh::get()->prepare($sql);
    //     $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $ra_id = $stmt->fetchColumn();
        
    //     // monster_auctionの削除
    //     $sql = "DELETE FROM monster_auction WHERE ma_ra_id = :ra_id";
    //     $stmt = Dbh::get()->prepare($sql);
    //     $stmt->bindValue(":ra_id", $ra_id, PDO::PARAM_INT);
    //     $stmt->execute();
        
    //     // room_auctionの削除
    //     $sql = "DELETE FROM room_auction WHERE ra_rm_id = :rm_id";
    //     $stmt = Dbh::get()->prepare($sql);
    //     $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
    //     $stmt->execute();
    // }
    
    // ルームの現在開催中のオークションを格納するレコードの作成
    private function _createAuctionManager($rm_id){
        $sql = "INSERT INTO room_auction(ra_rm_id) VALUES(:rm_id);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $ra_id = Dbh::get()->lastInsertId();

        return $ra_id;
    }

    // monster_auctionに人数分オークション用モンスターレコードを作成
    private function _createAuctionRecode($ra_id, $max){
        $sql = "SELECT * FROM monster_master;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        shuffle($rows);
        $max = 8;
        $monsters = array_slice($rows, 0, $max);
        
        foreach($monsters as $mons){
            $mmid = $mons["mm_id"];
            $price = $mons["mm_price"];
            $sql = "INSERT INTO monster_auction(ma_ra_id, ma_mm_id, ma_price) VALUES(:ra_id, :mm_id, :ma_price);";
 
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(":ra_id", $ra_id, PDO::PARAM_INT);
            $stmt->bindValue(":mm_id", $mmid, PDO::PARAM_INT);
            $stmt->bindValue(":ma_price", $price, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    
}


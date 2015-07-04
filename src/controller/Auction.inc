<?php
require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class Auction{
    private $_rm_id;
    
    public function __construct($rm_id = null){
        if(isset($rm_id)){
            $this->_rm_id = $rm_id;
        }
    }

    // オークションで使用する各種レコードを作成
    public function create($max){
        // monster_auctionと room_masterの 中間テーブルを作成
        $ra_id = $this->_createAuctionManager($this->_rm_id);
        // monster_auctionに人数分のレコードを挿入
        $this->_createAuctionRecode($ra_id, $max);
    }

    public function getRAid(){
        $sql = "SELECT ra_id FROM room_auction WHERE ra_rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $ra_id = $stmt->fetchColumn();
        return $ra_id;
    }

    // オークションに参加するモンスターリストを取得
    public function getAuctionMonsters(){
        $ra_id = $this->getRAid($this->_rm_id);

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
    private function _createAuctionManager(){
        $sql = "INSERT INTO room_auction(ra_rm_id) VALUES(:rm_id);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
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

    // 現在設定されてるオークションは開催されているか
    //  時間外or NULL が入っていた場合false
    public function isAlive(){
        $sql = "SELECT * FROM room_auction WHERE ra_rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $maid = $row['ra_ma_id'];
        $time = $row['ra_time'];

        if(is_null($maid)){
            return false;
        }
        
        if(Carbon::parse($time)->addSeconds(TIMER_AUCTION)->isPast()){
            return false;
        }
        
        return true;
    }

    // 現在登録されているオークションが誰かに落札されているか
    //  オークションが登録されていない場合はtrue を返す
    public function isSold(){
        $sql = "SELECT ra_ma_id FROM room_auction WHERE ra_rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $maid = $stmt->fetchColumn();
        if(is_null($maid)){
            return true;
        }
        
        $sql = "SELECT ma_ru_id FROM monster_auction WHERE ma_id = (SELECT ra_ma_id FROM room_auction WHERE ra_rm_id = :rm_id);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $ruid = $stmt->fetchColumn();
        
        if(is_null($ruid)){
            return false;
        }else{
            return true;
        }
    }

    // 強制的に購入させる
    public function forceBuy(){
        // モンスターを落札していないユーザーをSELECTしてUPDATE
        // 現在落札していないユーザーを取得
        $sql = "SELECT room_user.ru_id FROM room_user LEFT JOIN monster_auction ON room_user.ru_id = monster_auction.ma_ru_id WHERE room_user.ru_rm_id = :rm_id AND monster_auction.ma_id IS NULL ORDER BY RAND() LIMIT 1;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $ruid = $stmt->fetchColumn();

        var_dump($ruid);
        var_dump($this->_rm_id);

        
        $sql = "UPDATE monster_auction SET ma_ru_id = :ru_id, ma_closeflg = true WHERE ma_id = (SELECT ra_ma_id FROM room_auction WHERE ra_rm_id = :rm_id);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->bindValue(":ru_id", $ruid, PDO::PARAM_INT);
        $stmt->execute();
        

        // 所持金を減らす
        //  XXX: どっかに分けるべき？ ex.Money.inc
        $sql = "UPDATE room_user SET ru_money = ru_money - (SELECT ma_price FROM monster_auction WHERE ma_ru_id = :ru_id) WHERE ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ru_id", $ruid, PDO::PARAM_INT);
        $stmt->execute();
        
    }

    // 新規登録 登録できるmonster_auctionが無かったらfalseを返す
    public function setAuction(){
        //$sql = "SELECT ma_id FROM monster_auction WHERE ma_ra_id = (SELECT ra_id FROM room_auction WHERE ra_rm_id = :rm_id LIMIT 1) AND ma_closeflg IS NULL;";
        $sql = "SELECT * FROM monster_auction WHERE ma_ru_id IS NULL;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $maid = $stmt->fetchColumn();

        if(isset($maid)){
            // maテーブルに レコードが残っていなかったら
            return false;
        }else{
            $sql = "UPDATE room_auction SET ra_ma_id = :ma_id, ra_time = :time WHERE ra_rm_id = :rm_id;";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(":ma_id", $maid, PDO::PARAM_INT);
            $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
            $stmt->bindValue(":time" , Carbon::now(), PDO::PARAM_STR);
            $stmt->execute();

            return true;
        }
        
    }
    

    public function getAuction($ma_id = null){
        if(isset($ma_id)){
            //$sql = "SELECT * FROM room_auction LEFT JOIN monster_auction ON room_auction.ra_ma_id = monster_auction.ma_id WHERE room_auction.ra_ma_id = :ma_id;";
            $sql = "SELECT * FROM monster_auction WHERE ma_id = :ma_id;";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(":ma_id", $ma_id, PDO::PARAM_INT);
        }else{
            $sql = "SELECT * FROM room_auction LEFT JOIN monster_auction ON room_auction.ra_ma_id = monster_auction.ma_id WHERE room_auction.ra_rm_id = :rm_id;";
            $stmt = Dbh::get()->prepare($sql);
            $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $res = array(
            "timer"=>null,
            "auction_id"=>null,
            "monster_id"=>null,
            "user_id"=>null,
            "price"=>null
        );

        if(isset($row["ra_time"])){
            $time = $row["ra_time"];
            $now = Carbon::now();
            $timer = TIMER_AUCTION - intval(Carbon::parse($time)->diffInSeconds($now));
        }else{
            $timer = 0;
        }
        $res["timer"] = $timer;

        $ruid = $row["ma_ru_id"];
        $sql = "SELECT ru_um_id FROM room_user WHERE ru_id = :ru_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ru_id", $ruid, PDO::PARAM_INT);
        $stmt->execute();
        $umid = $stmt->fetchColumn();
        $res["user_id"] = $umid;

        $res["auction_id"] = $row["ma_id"];
        $res["monster_id"] = $row["ma_mm_id"];
        $res["price"] = $row["ma_price"];

        return $res;
            
    }

}


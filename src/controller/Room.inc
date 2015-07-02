<?php
// XXX: $this->setRoom($rm_id);とかして他関数のrm_idをなくすべきか


require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');

require_once(ROOT_DIR . 'controller/Active.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class Room{
    private $_rm_id;
    
    public function __construct($rm_id = null){
        if(isset($rm_id)){
            $this->_rm_id = $rm_id;
        }
    }

    // 部屋作成処理
    public function create($title, $max, $um_id){
        // room_masterに追加
        $sql = "INSERT INTO room_master(rm_title, rm_stat, rm_max, rm_creater_id) VALUES(:title, :stat, :max, :creater);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->bindValue(":stat", ROOM_WAIT, PDO::PARAM_STR);
        $stmt->bindValue(":max", $max, PDO::PARAM_INT);
        $stmt->bindValue(":creater", $um_id, PDO::PARAM_INT);
        $stmt->execute();
        $rm_id = Dbh::get()->lastInsertId();

        $this->_rm_id = $rm_id;
        return $rm_id;
    }
    // 部屋削除処理
    public function delete(){
        // monster_auctionを消す為にra_idを取得する
        $sql = "SELECT ra_id FROM room_auction WHERE ra_rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $ra_id = $stmt->fetchColumn();
        
        // monster_auctionの削除
        $sql = "DELETE FROM monster_auction WHERE ma_ra_id = :ra_id";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ra_id", $ra_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // room_auctionの削除
        $sql = "DELETE FROM room_auction WHERE ra_rm_id = :rm_id";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // room_masterの削除
        $sql = "DELETE FROM room_master WHERE rm_id = :rm_id";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        
    }
    
    // 入室処理
    public function join($um_id){
        // room_userに追加
        $sql = "INSERT INTO room_user(ru_um_id, ru_rm_id, ru_money) VALUES(:um_id, :rm_id, :money);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":um_id", $um_id, PDO::PARAM_INT);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->bindValue(":money", USER_MONEY, PDO::PARAM_INT);
        $stmt->execute();

        $ru_id = Dbh::get()->lastInsertId();

        // roomのユーザー数を更新
        $this->updPpl($this->_rm_id);

        // TODO: activeの初期処理
        $active = new Active();
        $active->create($ru_id);
    }
    // 退室処理
    public function leave($um_id){
        // user_activeの削除
        $sql = "DELETE FROM user_active WHERE ua_ru_id = (SELECT ru_id FROM room_user WHERE ru_um_id = :um_id);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":um_id", $um_id, PDO::PARAM_INT);
        $stmt->execute();

        // room_userの削除
        $sql = "DELETE FROM room_user WHERE ru_um_id = :um_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":um_id", $um_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // rm_pplが0だった場合はdeleteを叩く
        $this->updPpl();
        if($this->getPpl() <= 0){
            $this->delete();
        }
    }

    // 現在の人数を更新
    public function updPpl(){
        $ppl = $this->getPpl();
        
        $sql = "UPDATE room_master SET rm_ppl = :ppl WHERE rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ppl", $ppl, PDO::PARAM_INT);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // XXX: rm_pplが0だった場合はdeleteを叩く
    }
    // 現在の人数を取得
    public function getPpl(){
        $sql = "SELECT count(*) FROM room_user WHERE ru_rm_id = :rm_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $ppl = $stmt->fetchColumn(PDO::FETCH_ASSOC);
        
        return $ppl;
    }
    
    public function getStat(){
        $sql = "SELECT rm_stat FROM room_master WHERE rm_id = :rm_id";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":rm_id", $this->_rm_id, PDO::PARAM_INT);
        $stmt->execute();
        $stat = $stmt->fetchColumn();

        return $stat;
    }
}

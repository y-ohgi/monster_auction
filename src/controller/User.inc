<?php
// １ユーザーを表すクラス
require_once(ROOT_DIR . "common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class User{
    private $_user = null;
    public function __construct($uuid){
        $sql = 'SELECT * FROM user_master WHERE um_uuid = :uuid';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        $this->_user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function authUser(){
        return isset($_user);
    }

    public function getId(){
        return $this->_user['um_id'];
    }
    public function getName(){
        return $this->_user['um_name'];
    }


    // room_master.idを返す
    public function getRMid(){
        $um_id = $this->_user['um_id'];
        $sql = 'SELECT * FROM room_user WHERE ru_um_id = :um_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':um_id', $um_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $rm_id = $row['ru_rm_id'];
        return $rm_id;
    }
    // room_user.idを返す
    public function getRUid(){
        $um_id = $this->_user['um_id'];
        $ruser = RoomDao::getUserAtUM($um_id);
        $sql = 'SELECT * FROM room_user WHERE ru_um_id = :um_id;';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':um_id', $um_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $ru_id = $row['ru_id'];
        return $ru_id;
    }
    // user_active.idを返す
    public function getUAid(){
        $ru_id = $this->getRUid();
        $sql = 'SELECT * FROM user_active WHERE ua_ru_id = :ru_id';
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(':ru_id', $ru_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $ua_id = $row['ua_id'];
        return $ua_id;
    }
}
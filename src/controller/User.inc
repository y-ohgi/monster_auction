<?php
// １ユーザーを表すクラス
require_once(dirname(__FILE__).'/../model/UserDao.inc');
require_once(dirname(__FILE__).'/../model/RoomDao.inc');
require_once(dirname(__FILE__).'/../model/ActiveDao.inc');

class User{
    private $user = null;
    public function __construct($uuid){
        $this->user = UserDao::getUser($uuid);
    }

    public function getUser(){
        return $this->user;
    }
    public function getId(){
        return $this->user['um_id'];
    }    
    public function getName(){
        return $this->user['um_name'];
    }


    // room_master.idを返す
    public function getRMid(){
        $um_id = $this->user['um_id'];
        $ruser = RoomDao::getUserAtUM($um_id);
        return $ruser['ru_rm_id'];
    }
    // room_user.idを返す
    public function getRUid(){
        $um_id = $this->user['um_id'];
        $ruser = RoomDao::getUserAtUM($um_id);
        return $ruser['ru_id'];
    }
    // user_active.idを返す
    public function getUAid(){
        $ru_id = $this->getRUid();
        $auser = ActiveDao::getUserAtRU($ru_id);
        return $auser['ua_id'];
    }
}


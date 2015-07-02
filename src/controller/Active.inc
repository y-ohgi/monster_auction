<?php

// ユーザーがルーム内に存在するかを確認するためのクラス

require_once(ROOT_DIR . "common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class Active{
    private $_ra_id;
    
    public function __construct($ra_id = null){
        if(isset($ra_id)){
            $this->_ra_id = $ra_id;
        }
    }

    // activeの参加
    public function create($ru_id){
        // TODO: timeを作成
        $time = Carbon::now();
        
        $sql = "INSERT INTO user_active(ua_ru_id, ua_time) VALUES(:ru_id, :ra_time);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ru_id", $ru_id, PDO::PARAM_INT);
        $stmt->bindValue(":ra_time", $time, PDO::PARAM_STR);
        $stmt->execute();
        $ra_id = Dbh::get()->lastInsertId();

        $this->_ra_id = $ra_id;
        return $ra_id;
    }

    // 生存確認
    public function isAlive(){
        $sql = "SELECT * FROM user_active WHERE ua_id = :ua_id";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ua_id", $this->_ua_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $time = Carbon::parse($row['ua_time']);
        // 取得時間に30分足して現在時刻を過ぎていればtrue
        if($time->addMinutes(TIMER_USER_TIMEOUT)->isPast()){
            return true;
        }else{
            return false;
        }
    }

    // 更新
    public function update(){
        $time = Carbon::now();
        $sql = "UPDATE user_active SET ua_time = :time WHERE ua_id = :ua_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":time", $time, PDO::PARAM_STR);
        $stmt->bindValue(":ua_id", $this->_ua_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // 削除
    public function delete(){

    }

}

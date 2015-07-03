<?php

// ユーザーがルーム内に存在するかを確認するためのクラス

require_once(ROOT_DIR . "common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;

class Active{
    private $_ua_id;
    
    public function __construct($ua_id = null){
        if(isset($ua_id)){
            $this->_ua_id = $ua_id;
        }
    }

    // activeの参加
    public function create($ru_id){
        // TODO: timeを作成
        $time = Carbon::now();
        
        $sql = "INSERT INTO user_active(ua_ru_id, ua_time) VALUES(:ru_id, :ua_time);";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ru_id", $ru_id, PDO::PARAM_INT);
        $stmt->bindValue(":ua_time", $time, PDO::PARAM_STR);
        $stmt->execute();
        $ua_id = Dbh::get()->lastInsertId();

        $this->_ua_id = $ua_id;
        return $ua_id;
    }

    // 生存確認
    public function isAlive(){
        $sql = "SELECT * FROM user_active WHERE ua_id = :ua_id";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ua_id", $this->_ua_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $time = Carbon::parse($row['ua_time']);
        // 取得時間に30分足して現在時刻を過ぎていれば30分以上ログインしていないことになるのでfalse
        if($time->addMinutes(TIMER_USER_TIMEOUT)->isPast()){
            return false;
        }else{
            return true;
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
        $sql = "DELETE FROM user_active WHERE ua_id = :ua_id;";
        $stmt = Dbh::get()->prepare($sql);
        $stmt->bindValue(":ua_id", $this->_ua_id, PDO::PARAM_INT);
        $stmt->execute();
    }

}

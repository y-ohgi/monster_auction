<?php
class Page{
    protected static $response = array();

    // responseするデータ配列の初期設定
    public static function setRes($ary){
        static::$response = $ary;
    }

    // $responseのvalueに値を設定
    public static function incRes(){
        $i = 0;
        foreach(static::$response as $key=>$value){
            static::$response[$key] = func_get_arg($i);
            $i++;
        }
    }

    // 現在のresponseデータを返す
    public static function getRes(){
        return static::$response;
    }

    // responseに配列を追加
    public static function mergeRes($ary){
        static::$response = array_merge(self::$response, $ary);
    }

    // 処理を終了
    //  引数にはresponseのkey順
    public static function complete(){
        if(func_num_args() === 1){
            static::$response = array("status"=> func_get_arg(0) );
        }else{
            $i = 0;
            foreach(static::$response as $key=>$value){
                static::$response[$key] = func_get_arg($i);
                $i++;

            }
        }

        Dbh::disconnect();

        //header("Content-Type: application/json; charset=utf-8");
        print(json_encode(static::$response, JSON_UNESCAPED_UNICODE));
    }
}
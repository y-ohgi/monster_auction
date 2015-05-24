<?php


class Page{
    protected static $response = array();
    
    public static function setRes($ary){
        //self::$response = array_merge(self::$response, $ary);
        self::$response = $ary;
    }

    // self::$responseの$valueに値を入れる
    public static function incRes(){
        $i = 0;
        foreach(self::$response as $key=>$value){
            self::$response[$key] = func_get_arg($i);
            $i++;
        }
    }

    public static function getRes(){
        return self::$response;
    }

    public static function mergeRes($ary){
        self::$response = array_merge(self::$response, $ary);
    }
    
    // 処理を終了
    public static function complete(){
        // XXX:complteに引数が渡された場合self::incResに飛ばしたい
        // if(func_num_args()){
        //     self::incRes(func_get_args());
        // }
        if(func_num_args()){
            $i = 0;
            foreach(self::$response as $key=>$value){
                self::$response[$key] = func_get_arg($i);
                $i++;
            }
        }
        
        //header("Content-Type: application/json; charset=utf-8");
        echo json_encode(self::$response, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
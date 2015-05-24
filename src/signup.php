<?php

require_once('init.php');
require_once('db_connect.php');

// 値受け取り
$name = trim(h(@$_POST['name']));
$uuid = h(@$_POST['uuid']);

if($name == ''){
    echo "空白文字です";
    exit();
}


// db処理
try{
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->beginTransaction();

    // nameが既存であるかの判定
    // TODO:uuidが既存であるかの判定
    $sql = 'SELECT um_name FROM user_master WHERE um_name = :name';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
    $stmt->execute();
    $exiname = $stmt->fetchColumn();

    // 名前が存在した場合
    if($exiname != false){
        echo "名前が存在しています";
        exit();
    }

    // ユーザーの登録
    $sql = 'INSERT INTO user_master(um_name, um_uuid) values(:name, :uuid)';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':name', (string)$name, PDO::PARAM_STR);
    $stmt->bindValue(':uuid', (string)$uuid, PDO::PARAM_STR);
    $stmt->execute();

    $dbh->commit();
}catch(Exception $error){
    $dbh->rollback();
    echo "REFUSAL";
    exit();
}
// dbとの接続を切断
$dbh = null;

echo "登録完了";
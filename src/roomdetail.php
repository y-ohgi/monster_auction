<?php

require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');
$uuid = $_POST['uuid'];

$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
$um_id = $user->getId();
$ru_id = $user->getRUid();
$rm_id = $user->getRMid();

$response = array(
    "status"=>null,
    "my_id"=>null,
    "room_id"=>null,
    "memberlist"=>null
);
Page::setResponse($response);

$sql = "SELECT ru_id AS user_id, um_name AS name FROM room_user LEFT JOIN user_master ON room_user.ru_um_id = user_master.um_id WHERE ru_rm_id = :rm_id;";
$stmt = Dbh::get()->prepare($sql);
$stmt->bindValue(":rm_id", $rm_id, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

Page::complete(SUCCESS, $ru_id, $rm_id, $rows);
<?php


require_once("common/init.inc");

require_once(ROOT_DIR . 'model/Dbh.inc');
require_once(ROOT_DIR . 'controller/Page.inc');
require_once(ROOT_DIR . 'controller/User.inc');

require_once(ROOT_DIR . 'controller/Room.inc');
require_once(ROOT_DIR . 'controller/Auction.inc');

require_once(ROOT_DIR . '../lib/Carbon/Carbon.php');
use Carbon\Carbon;

$uuid = $_POST['uuid'];


$user = new User($uuid);
if($user->authUser()){
    Page::complete(BAD_REQUEST);
    return;
}
$um_id = $user->getId();
$rm_id = $user->getRMid();

$response = array(
    "status"=>null
);
Page::setResponse($response);

$room = new Room($rm_id);
$room->leave($um_id);

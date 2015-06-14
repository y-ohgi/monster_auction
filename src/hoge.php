<?php

require_once('model/Dbh.inc');

$sql = "SELECT * FROM room_master WHERE rm_id = 100;";
$stmt = Dbh::get()->prepare($sql);
$stmt->execute();

var_dump($stmt->fetch(PDO::FETCH_ASSOC));


require_once('controller/Time.inc');
echo Time::getNow();


// require_once(dirname(__FILE__).'/../lib/Carbon/Carbon.php');
// use Carbon\Carbon;

// require_once('controller/Time.inc');


// function b(){
//     echo "<br />";
// }

// $time = "2015-06-03 17:25:57";

// $created = Carbon::parse($time);
// //$created = Carbon::now();
// $targettime = $created->copy()->addSeconds(Time::getAuctionStart());

// echo $diff = $created->diffInSeconds($targettime);
/*
// Time::AuctionStart()秒分経ったか
if($targettime->isPast()){
    echo "Past";
    // 残り秒を求める
}else{
    echo $diff = $created->diffInSeconds($targettime);
}

/*
$time = "2015-06-03 17:25:57";

$test = Carbon::parse($time);
echo $test->timestamp;
b();
$now = Carbon::now();
echo $test->diffInSeconds($now);
b();

echo $test;
b();
var_dump($test->addSecond(10)->isPast());
var_dump($now->addSecond(10)->isPast());
b();
/**/
/*
// //$dtOttawa = Carbon::createFromDate(2000, 1, 1, 'America/Toronto');
// $dtOttawa = Carbon::parse($time);
// //$dtVancouver = Carbon::createFromDate(2000, 1, 1, 'America/Vancouver');
// $dtVancouver = Carbon::now();
// echo $dtOttawa->diffInMinutes($dtVancouver);
// echo $dtOttawa->diffInSeconds($dtVancouver);

// b();

echo(Carbon::now());

*/
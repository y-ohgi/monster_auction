<?php
// statuslist.inc

//=========== room_master.rm_stat
// 待機状態
define("ROOM_WAIT", "wait");
// 




//============ jsonで返すstatus
// 正常
define("SUCCESS", 200);

// 別のページを参照してください
define('SEE_OTHER', 303);

// クライアント側のエラー
define('BAD_REQUEST', 400);
// 未入力の値が存在している
define('NOT_INPUT', 450);
// 既存の値(名前もしくはuuid)
define('EXISTING', 452);
// タイムアウト
define('TIMEOUT', 453);

// サーバーエラー
define('SERVER_ERROR', 500);

// ルームが満員
define('FULL_OF_PEOPLE', 600);
// ルームをTIME_ROOM_CANCREATE分以内に作っている
define('CANT_CREATEROOM', 601);
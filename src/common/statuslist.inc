<?php
// statuslist.inc
// jsonで返すstatusキーの値を格納

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
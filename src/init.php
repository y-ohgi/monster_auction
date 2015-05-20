<?php
// init.php

// エラーの表示
ini_set('display_errors', '1');

// 文字列のエスケープ
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

require_once('../lib/Carbon/Carbon.php');
use Carbon\Carbon;

/*
// 使用するディレクトリ
define("C_DIR", "/Users/owner/htdocs_s/MA/");


// smartyの読み込み
require_once('/opt/www/Smarty/libs/Smarty.class.php');
$smarty = new Smarty();
$smarty->setTemplateDir(C_DIR . 'templates');

*/



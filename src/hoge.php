<?php


require_once('controller/User.inc');

$user = new User("556848028678f");
var_dump($user->getUser());
var_dump($user->getName());
var_dump($user->getRUid());
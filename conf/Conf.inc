<?php

class Config{
    private static $data = array (
        'dsn'=> 'mysql:dbname=login_test; host=localhost; charset=utf8;',
        'user'=> 'testuser',
        'password'=> 'bear'
    );

    public static function get($k){
        return self::$data[$k];
    }
}
/*
// DB
'db_name' => 'furu_ec',
'db_user' => 'furu_ec',
'db_pass' => 'furu_ec',
'db_host' => 'localhost',
// smarty
'smarty_template_dir' => '/home/furu/smarty/ec2014/templates/',
'smarty_compile_dir'  => '/home/furu/smarty/ec2014/templates_c/',
//'' => '',
);
static public function get($name) {
return self::$data_[$name];
}

}

*/
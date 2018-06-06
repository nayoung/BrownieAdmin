<?php
class Account_auth_mapper
{
    private $data = array(
        'id'                => 0,
        'auth_id'           => 0,
        'menu_id'           => 0,
        'type'              => 'R',
    );
    private  $table = 'account_auth';

    public static $var_type = array(
        'R' => '읽기',
        'W' => '쓰기',
    );

}
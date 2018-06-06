<?php
class Menu
{
    private $data = array(
        'id'                => 0,
        'parent_id'         => 0,
        'menu_title'        => '',
    );
    private  $table = 'menu';

    public static $var_icon_class = array(
        0 => "icon-folder-open",
        1 => "icon-align-left",
        2 => "icon-wrench",
        3 => "icon-cog",
    );
}
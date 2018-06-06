<?php
require_once dirname(__FILE__) . '/../../conf/config.php';
if (Account::isLogin() == false) {
    include_once _VIEW_PATH . 'login.html';
} else {
    $url = FIRST_PAGE;
    include_once _VIEW_PATH . 'redirect.html';
}

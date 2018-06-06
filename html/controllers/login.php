<?php
require_once dirname(__FILE__) . '/../../conf/config.php';

switch ($request['act']) {
    case 'login' :
        $account = new Account;
        if ($account->doLogin($request) == true) {
            $url = FIRST_PAGE;
            include_once _VIEW_PATH . 'redirect.html';
        }

        $message = '회원정보가 없습니다.';
        $back = true;
        include_once _VIEW_PATH . 'redirect.html';

        break;

    case 'logout' :
        break;

    case 'default' :
        break;
}


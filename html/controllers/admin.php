<?php
require_once dirname(__FILE__) . '/../../conf/config.php';

switch ($act) {
    case 'register' :
        $admin = new Admin;
        if ($admin->doRegister($request)) {
            $message = "등록되었습니다.";
        } else {
            $message = "등록에 실패하였습니다.";
        }
        $back = true;
        include_once _VIEW_PATH . 'redirect.html';
        break;

    default :
        $request['scale'] = ((int) $request['scale'] > 0)? $request['scale']:50;
        $params = $request;
        unset($params['scale']);
        unset($params['page']);

        $admin = new Admin;
        $total = $admin->getCount($params);

        $url = $_SERVER['PHP_SELF'] ;
        $page = new Page($request['page'], $total, $url, $params, $request['scale']);
        list($page_html, $offset, $limit) = $page->init();

        $list = $admin->getList($params, $offset, $limit, array('name asc'));

        include_once _VIEW_PATH . 'admin.html';
        break;
}


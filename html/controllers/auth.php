<?php
require_once dirname(__FILE__) . '/../../conf/config.php';

switch ($act) {
    case 'registerPop' :
        if ((int) $request['id'] > 0) {
            $auth = new Auth;
            $auth_list = $auth->getList(array('id' => $request['id']));
        }
        include_once _VIEW_PATH . 'auth_detail_pop.html';
        break;

    case 'register' :
        $auth = new Auth;
        if ((int) $request['id'] > 0) {
            if ($auth->doModify($request)) {
                $message = "수정되었습니다.";
                $close = true;
            } else {
                $message = "수정에 실패하였습니다.";
                $back = true;
            }
        } else {
            if ($auth->doRegister($request)) {
                $message = "등록되었습니다.";
                $close = true;
            } else {
                $message = "등록에 실패하였습니다.";
                $back = true;
            }
        }

        include_once _VIEW_PATH . 'redirect.html';
        break;

    default :
        $params = $request;

        $auth = new Auth;
        $list = $auth->getList(array(), 0,0, array("id ASC"));

        $request['auth'] = ((int) $request['auth'] == 0 )? $list[0]['id']: $request['auth'];
        $auth_menu = $auth->getAuthMenu($request['auth']);

        include_once _VIEW_PATH . 'auth.html';
        break;
}


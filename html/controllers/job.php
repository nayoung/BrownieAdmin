<?php
require_once dirname(__FILE__) . '/../../conf/config.php';

switch ($act) {
    case 'registerPop' :
        Auth::getAuth('15', 'read');

        include_once _VIEW_PATH . 'job_detail_pop.html';
        break;

    case 'register' :
        Auth::getAuth('15', 'write');

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
        Auth::getAuth('5', 'read');

        $request['scale'] = ((int)$request['scale'] > 0) ? $request['scale'] : 50;
        $params = $request;
        unset($params['scale']);
        unset($params['page']);

        $job = new Job;
        $total = $job->getCount($params);

        $url = $_SERVER['PHP_SELF'];
        $page = new Page($request['page'], $total, $url, $params, $request['scale']);
        list($page_html, $offset, $limit) = $page->init();

        $list = $job->getList($params, $offset, $limit, array('jobid DESC'));

        include_once _VIEW_PATH . 'job.html';
        break;
}
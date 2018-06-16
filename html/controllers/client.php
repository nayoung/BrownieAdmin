<?php
require_once dirname(__FILE__) . '/../../conf/config.php';

switch ($act) {
    case 'software' :
        Auth::getAuth('10', 'read');

        $request['scale'] = ((int)$request['scale'] > 0) ? $request['scale'] : 50;
        $params = $request;
        unset($params['scale']);
        unset($params['page']);

        $client = new Client;
        $total = $client->getCount($params);

        $url = $_SERVER['PHP_SELF'];
        $page = new Page($request['page'], $total, $url, $params, $request['scale']);
        list($page_html, $offset, $limit) = $page->init();

        $list = $client->getList($params, $offset, $limit, array('idx DESC'));

        include_once _VIEW_PATH . 'client_software_setting.html';
        break;

    case 'common' :
        Auth::getAuth('11', 'read');

        $request['scale'] = ((int)$request['scale'] > 0) ? $request['scale'] : 50;
        $params = $request;
        unset($params['scale']);
        unset($params['page']);

        $common = new Common;
        $total = $common->getCount($params);

        $url = $_SERVER['PHP_SELF'];
        $page = new Page($request['page'], $total, $url, $params, $request['scale']);
        list($page_html, $offset, $limit) = $page->init();

        $list = $common->getList($params, $offset, $limit, array('idx DESC'));
        include_once _VIEW_PATH . 'client_common_setting.html';
        break;

    case 'oslang' :
    default :
        Auth::getAuth('9', 'read');

        $request['scale'] = ((int)$request['scale'] > 0) ? $request['scale'] : 50;
        $params = $request;
        unset($params['scale']);
        unset($params['page']);

        $os_lang = new OsLang;
        $total = $os_lang->getCount($params);

        $url = $_SERVER['PHP_SELF'];
        $page = new Page($request['page'], $total, $url, $params, $request['scale']);
        list($page_html, $offset, $limit) = $page->init();

        $list = $os_lang->getList($params, $offset, $limit, array('code DESC'));

    include_once _VIEW_PATH . 'client_os_language_setting.html';
        break;

}
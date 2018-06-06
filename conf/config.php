<?php
error_reporting(E_ALL);

$rootPath = dirname(__FILE__) . '/../';
define('_DOCUMENT_ROOT',	$rootPath);
define('_CONFIG_PATH',		_DOCUMENT_ROOT . 'conf/');
define('_LIB_PATH',			_DOCUMENT_ROOT . 'lib/');
define('_HOME_PATH',		_DOCUMENT_ROOT . 'html/');
define('_MODEL_PATH',		_HOME_PATH . 'models/');
define('_VIEW_PATH',		_HOME_PATH . 'views/');

define('_SCRIT_PATH',		'/html/views/script/');
define('_CSS_PATH',		    '/html/views/css/');
define('_IMAGE_PATH',		'/html/views/image/');

$httpHost = $_SERVER['HTTP_HOST'];
define('_HTTP_HOST', 'http://' . $httpHost);
$domain = explode('.', $httpHost);
define('_COOKIE_DOMAIN', '.' . $domain[1] . '.' . $domain[2]);
define('SERVER_REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

spl_autoload_register(function ($class) {
    try {
        if (is_file(_LIB_PATH . strtolower($class) . '.php')) {
            require_once _LIB_PATH . strtolower($class) . '.php';
        } else {
            require_once _MODEL_PATH . strtolower($class) . '.php';
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
        echo $error_message; exit;
    }
});

if (isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] == 'development') {
    define('_CONFIG_DB_INC', _CONFIG_PATH . 'inc/dev.database.inc');
} else {
    define('_CONFIG_DB_INC', _CONFIG_PATH . 'inc/database.inc');
}
define('DB_SECTION', 'BROWNIE_MASTER');
$db_con = DbCon::getInstance(_CONFIG_DB_INC, DB_SECTION);

$http_request = new Request($_GET, $_POST, $_FILE, $_SERVER, $_COOKIE);
$request = $http_request->getRequest();

session_start();
define('FIRST_PAGE', $_SESSION['first_page']);
//echo "<pre>";print_r($_SERVER);
//exit;

// session없을경우 로그인으로 이동
if (!in_array($_SERVER['PHP_SELF'], array("/index.php", "/login.php")) && !in_array($request['act'], array('login', 'logout'))
    && Account::isLogin() == false) {
    $url = '/index.php';
    include_once _VIEW_PATH . 'redirect.html';
} else if (false) {
    // 권한 없을경우..
    $message = '권한이 없습니다. 관리자에게 문의하세요.';
    $back = true;
    include_once _VIEW_PATH . 'redirect.html';
}
//로그인 후, 로그 클릭시 이동페이지


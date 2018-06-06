<?php
/**
 * Created by PhpStorm.
 * User: mia
 * Date: 2018-06-05
 * Time: 오후 11:37
 */

class Account
{
    private $data = array(
        'id'                => 0,
        'account_id'        => '',
        'account_passwd'    => '',
        'account_name'      => '',
        'account_email'     => '',
        'account_auth_id'   => 0,
        'account_regdate'   => '0000-00-00 00:00:00',
        'account_ip'        => '',
        'account_lastdate'  => '0000-00-00 00:00:00',
        'sort'              => 0,
    );
    private  $table = 'account';

    // 로그인
    public function doLogin($request) {
        if (strlen($request['account_id']) == 0 || strlen($request['account_passwd']) == 0) {
            return false;
        }

        global $db_con;
        $account_passwd = hash('sha256', $request['account_passwd']);

        $query =<<<SQL
            SELECT * FROM `account` WHERE account_id = '{$request['account_id']}' AND account_passwd = '{$account_passwd}'
SQL;
        $account = $db_con->getRow($query);

        if (sizeof($account) == 0 ) {
            return false;
        }

        // 최근 로그인 일자 업뎃
        $query =<<<SQL
            UPDATE `account` SET `account_lastdate` = now() WHERE id = '{$account['id']}'
SQL;
        $result = $db_con->excute_query($query);
        if ($db_con->checkResult($result) == false) {
            return false;
        }

        $_SESSION = $account;
        return true;
    }

    public static function isLogin() {
        if ((int) $_SESSION['id'] == 0) {
            return false;
        }
        return true;
    }

    // 로그아웃
    public function doLogout() {
        $_SERVER = array();
        session_destroy();
        return true;
    }

    // 등록
    public function doRegister($request) {
        global $db_con;
        unset($request['id']);
        $params = array_intersect_key($request, $this->data);

        $set_field = array();
        foreach ($params as $k => $v) {
            $set_field[] = $k ."='" . $v ."'";
        }
        $field = join(',', $set_field);

        $query =<<<SQL
            INSERT INTO `{$this->table}` set {$field}
SQL;
        $result = $db_con->excute_query($query);
        return $db_con->checkResult($result);
    }

    // 정보수정
    public function doModify($request) {
        if ((int) $request['id'] == 0) {
            return false;
        }

        global $db_con;
        $params = array_intersect_key($request, $this->data);

        $id = $params['id'];
        unset($params['id']);

        $set_field = array();
        foreach ($params as $k => $v) {
            $set_field[] = $k ."='" . $v ."'";
        }
        $field = join(',', $set_field);

        $query =<<<SQL
            UPDATE `{$this->table}` set {$field} WHERE id = '{$id}'
SQL;
        $result = $db_con->excute_query($query);
        return $db_con->checkResult($result);
    }
}
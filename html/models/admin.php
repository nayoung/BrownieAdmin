<?php
class Admin
{
    private  $table = 'admin';

    // 로그인
    public function doLogin($request) {
        if (strlen($request['id']) == 0 || strlen($request['password']) == 0) {
            return false;
        }

        global $db_con;
        $password = hash('sha256', $request['password']);

        $query =<<<SQL
            SELECT * FROM `admin` WHERE id = '{$request['id']}' AND password = '{$password}'
SQL;
        $admin = $db_con->getRow($query);

        if (sizeof($admin) == 0 ) {
            return false;
        }

        $_SESSION['admin'] = $admin;
        $_SESSION['first_page'] = _WEB_ROOT . "/admin.php";
        $_SESSION['path'] = array();

        return true;
    }

    public static function isLogin() {
        if (strlen($_SESSION['admin']['id']) == 0) {
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

    // 총 갯수
    public function getCount($params) {
        global $db_con;
        $total = 0;

        $where = array();
        foreach ($params as $k => $v) {
            if (strlen($v) == 0) {
                continue;
            }
            $where[] = $k ."='" . $v ."'";
        }
        $where = join(' AND ', $where);
        if (strlen($where) > 0) {
            $where = ' WHERE ' . $where;
        }

        $query =<<<SQL
            SELECT count(1) AS cnt FROM `{$this->table}` {$where}
SQL;

        $result = $db_con->getRow($query);
        $total = $result['cnt'];

        return $total;
    }

    public function getList($params = array(), $offset = 0, $limit = 0, $order_by= array()) {
        global $db_con;
        $list = array();

        $where = array();
        foreach ($params as $k => $v) {
            if (strlen($v) == 0) {
                continue;
            }
            $where[] = $k ."='" . $v ."'";
        }
        $where = join(' AND ', $where);
        if (strlen($where) > 0) {
            $where = ' WHERE ' . $where;
        }

        $query =<<<SQL
            SELECT * FROM `{$this->table}` {$where} 
SQL;
        if ((int) $limit > 0) {
            $query .= ' LIMIT ' . $offset . ', '.$limit;
        }

        if (sizeof($order_by) > 0) {
            $query .= ' ORDER BY ' . join(',', $order_by);
        }

        $list = $db_con->getAll($query);

        return $list;
    }

     // 등록
     public function doRegister($request) {
        global $db_con;
        $params = $request;

        $params['password'] = hash('sha256', $request['password']);

        $set_field = array();
        foreach ($params as $k => $v) {
            if (strlen($v) == 0) {
                continue;
            }
            $set_field[] = $k ."='" . $v ."'";
        }
        $field = join(',', $set_field);

        $query =<<<SQL
            INSERT INTO `{$this->table}` set {$field}
SQL;

        $result = $db_con->excute_query($query);
        if ($result) {
            return true;
        }
        return false;
    }

    // 정보수정
    public function doModify($request) {
        if (strlen($request['id']) == 0) {
            return false;
        }

        global $db_con;
        $id = $request['id'];
        unset($request['id']);

        if (strlen($request['password']) == 0) {
            unset($request['password']);
        } else {
            $request['password'] = hash('sha256', $request['password']);
        }

        $params = $request;

        $set_field = array();
        foreach ($params as $k => $v) {
            $set_field[] = $k ."='" . $v ."'";
        }
        $field = join(',', $set_field);

        $query =<<<SQL
            UPDATE `{$this->table}` set {$field} WHERE id = '{$id}'
SQL;
        $result = $db_con->excute_query($query);
        if ($result) {
            return true;
        }
        return false;
    }
}
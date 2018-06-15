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

        $admin = new Admin;
        $admin_list = $admin->getList(array('id' => $request['id'], 'password' => $password ));

        if (sizeof($admin_list) == 0 ) {
            return false;
        }

        $first_page = '';
        $path = array();
        $auth_array = array();

        $auth = new Auth;
        $auth_list = $auth->getList(array('`auth_menu`.auth' => $admin_list[0]['auth']));
        foreach ($auth_list as $a) {
            $auth_array[$a['menu']] = $a;
        }

        $menu = new Menu;
        $menu_list = $menu->getList(array(), 0, 0, array('`menu`.parent_id ASC', '`menu`.id ASC'));
        foreach ($menu_list as $idx => $m) {
            if ((int) $m['parent_id'] > 0 && sizeof($path[$m['parent_id']]) > 0) {
                $path[$m['parent_id']][] = array('menu' => $m, 'auth' => $auth_array[$m['id']]);
            } else if ((int) $m['parent_id'] == 0) {
                $path[$m['id']][] = array('menu' => $m, 'auth' => $auth_array[$m['id']]);
            }

            if ($first_page == '' && $auth_array[$m['id']]['read'] == 'Y') {
                $first_page = $m['url'];
            }
        }

        /*
        echo"<pre>";
        echo"===";
print_r($menu_list);
print_r($path);

echo "<br/>".$first_page."<br/>";
        exit;
        */

        $_SESSION['admin'] = $admin_list[0];
        $_SESSION['first_page'] = _WEB_ROOT . "/" . $first_page;
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
            SELECT *
            , (SELECT name FROM `auth` WHERE `auth`.id = `admin`.auth) AS auth_name
            FROM `{$this->table}` {$where} 
SQL;
        if (sizeof($order_by) > 0) {
            $query .= ' ORDER BY ' . join(',', $order_by);
        }

        if ((int) $limit > 0) {
            $query .= ' LIMIT ' . $offset . ', '.$limit;
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
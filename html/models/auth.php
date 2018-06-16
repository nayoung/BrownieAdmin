<?php
class Auth
{
    private  $table = 'auth';

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
        if (sizeof($order_by) > 0) {
            $query .= ' ORDER BY ' . join(',', $order_by);
        }
        if ((int) $limit > 0) {
            $query .= ' LIMIT ' . $offset . ', '.$limit;
        }

        $list = $db_con->getAll($query);

        return $list;
    }

    public function getAuthMenu($auth) {
        global $db_con;
        $query =<<<SQL
            SELECT * 
            FROM `menu` LEFT JOIN `auth_menu` ON `menu`.id = `auth_menu`.menu
            WHERE `auth_menu`.auth = '{$auth}'
            ORDER BY `menu`.parent_id ASC, `menu`.id ASC
SQL;

        $list = $db_con->getAll($query);

        return $list;

    }

    public static function getAuth($menu = 0, $type = '') {
        $auth = $_SESSION['auth'];

        if ($auth[$menu][$type] != 'Y') {
            $message = '권한이 없습니다. 관리자에게 문의하세요.';
            $close = true;
            $back = true;
            include_once _VIEW_PATH . 'redirect.html';
        }
    }

    // 등록
    public function doRegister($request) {
        global $db_con;
        $params = $request;

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

    public function apply($params)
    {
        global $db_con;
        /*
        echo "<pre>";
        print_r($params);
        exit;*/

        $menu = new Menu;
        $menu_list = $menu->getList();

        $bool = true;
        foreach ($menu_list as $m) {
            if ((int) $m['parent_id'] == 0) {
                continue;
            }
            $am = $params['menu'][$m['id']];
            if (sizeof($am) > 0) {
                $am['read'] = ($am['read'] == 'Y') ? $am['read'] : 'N';
                $am['write'] = ($am['write'] == 'Y') ? $am['write'] : 'N';
            } else {
                $am = $params['menu'][$m['id']] = array('read' => 'N', 'write' => 'N');
            }
            $query = <<<SQL
            INSERT INTO auth_menu (`auth`, `menu`, `read`, `write`) VALUES ('{$params['auth']}', '{$m['id']}', '{$am['read']}', '{$am['write']}') 
            ON DUPLICATE KEY UPDATE `read` = '{$am['read']}', `write` = '{$am['write']}';
SQL;
            $result = $db_con->excute_query($query);
            if (!$result) {
                $bool = false;
            }

        }

        return $bool;
    }
}
<?php
class Job
{
    private  $table = 'job';

    public static $status = array(
        '정상',
        '정지',
        );
    
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
        if (sizeof($order_by) > 0) {
            $query .= ' ORDER BY ' . join(',', $order_by);
        }

        if ((int) $limit > 0) {
            $query .= ' LIMIT ' . $offset . ', '.$limit;
        }

        $list = $db_con->getAll($query);

        return $list;
    }
}
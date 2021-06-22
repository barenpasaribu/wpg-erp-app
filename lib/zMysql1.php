<?php

include 'config/connection.php';
function selectQuery($dbname, $table, $column = '*', $where = '', $sort = '', $distinct = false, $rowPerPage = null, $page = null)
{
    $query = 'select ';
    if (true == $distinct) {
        $query .= 'distinct ';
    }

    if (is_array($column)) {
        for ($i = 0; $i < count($column); $i++) {
            $query .= $column[$i];
            if ($i != count($column) - 1) {
                $query .= ',';
            }
        }
        $query .= ' ';
    } else {
        $query .= $column.' ';
    }

    $query .= 'from `'.$dbname.'`.`'.$table.'`';
    if ($where != '') {
        $query .= ' where '.$where;
    }

    if ($sort != '') {
        $query .= ' order by '.$sort;
    }

    if ($rowPerPage != null) {
        if ($page != null) {
            $startFrom = ($page - 1) * $rowPerPage;
        } else {
            $startFrom = 0;
        }

        $query .= ' limit '.$startFrom.','.$rowPerPage;
    }
	#echo $query;
    return $query;
}

function insertQuery($dbname, $table, $data = [], $column = [])
{
    if ($column == []) {
        $query = 'insert into `'.$dbname.'`.`'.$table.'` values ';
    } else {
        $query = 'insert into `'.$dbname.'`.`'.$table.'` (';
        for ($i = 0; $i < count($column); $i++) {
            if (0 == $i) {
                $query .= '`'.$column[$i].'`';
            } else {
                $query .= ',`'.$column[$i].'`';
            }
        }
        $query .= ') values ';
    }

    if (is_array($data)) {
        $i = 0;
        $query .= '(';
        foreach ($data as $row) {
            if (is_array($row)) {
                $j = 0;
                foreach ($row as $val) {
                    if (is_string($val)) {
                        $query .= "'".$val."'";
                    } else {
                        $query .= $val;
                    }

                    if ($j != count($row) - 1) {
                        $query .= ',';
                    }

                    $j++;
                }
                if ($i < count($data) - 1) {
                    $query .= '),(';
                }
            } else {
                if (is_string($row)) {
                    $query .= "'".$row."'";
                } else {
                    $query .= $row;
                }

                if ($i != count($data) - 1) {
                    $query .= ',';
                }
            }

            $i++;
        }
        $query .= ')';

        return $query;
    }

    return false;
}

function updateQuery($dbname, $table, $data = [], $where = '')
{
    $query = 'update '.$dbname.'.'.$table.' set ';
    if (is_array($data)) {
        $i = 0;
        foreach ($data as $key => $row) {
            if (is_string($row)) {
                $query .= '`'.$key."`='".$row."'";
            } else {
                $query .= '`'.$key.'`='.$row;
            }

            if ($i != count($data) - 1) {
                $query .= ',';
            }

            $i++;
        }
        $query .= ' ';
        if ($where != '') {
            $query .= ' where '.$where;
        }

        return $query;
    }

    return false;
}

function deleteQuery($dbname, $table, $where = '')
{
    return 'delete from `'.$dbname.'`.`'.$table.'` where '.$where;
}

function fetchData($query = null)
{
    $result = [];
    if ($query == null) {
        echo 'Error';
    } else {
        $res = mysql_query($query);
        if (!$res) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        while ($bar = mysql_fetch_assoc($res)) {
            $result[] = $bar;
        }
    }

    return $result;
}

function getPrimary($dbname, $table)
{
    $query = 'select * from '.$dbname.'.'.$table;
    $res = mysql_query($query);
    $j = mysql_num_fields($res);
    $i = 0;
    for ($primary = []; $i < $j; $i++) {
        $meta = mysql_fetch_field($res, $i);
        if ($meta->primary_key == '1') {
            $primary[] = strtolower($meta->name);
        }
    }

    return $primary;
}

function getEnum($dbname, $table, $field)
{
    $query = ' SHOW COLUMNS FROM `'.$dbname.'`.`'.$table."` LIKE '".$field."' ";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result, MYSQL_NUM);
    $regex = "/'(.*?)'/";
    preg_match_all($regex, $row[1], $enum_array);
    $enum_fields = [];
    foreach ($enum_array[1] as $row) {
        $enum_fields[$row] = $row;
    }
    return $enum_fields;
}

function getHolding($dbname, $org, $opt = false)
{
    $tipe = null;
    $tmpOrg = $org;
    if (trim($tmpOrg) != '') {
        while ('HOLDING' != $tipe) {
            $query = selectquery($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi,tipe,induk', "kodeorganisasi='".$tmpOrg."'");
            $data = fetchdata($query);
            $tipe = $data[0]['tipe'];
            $tmpOrg = $data[0]['induk'];
        }
    }

    if ($tipe == 'HOLDING') {
        if ($opt == true) {
            $resArr = [$data[0]['kodeorganisasi'] => $data[0]['namaorganisasi']];
        } else {
            $resArr = ['kode' => $data[0]['kodeorganisasi'], 'nama' => $data[0]['namaorganisasi']];
        }

        return $resArr;
    }

    return false;
}

function getPT($dbname, $org, $opt = false)
{
    $tipe = null;
    $tmpOrg = $org;
    if (trim($tmpOrg) != '') {
        while ($tipe != 'PT' && $tmpOrg != '') {
            $query = selectquery($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi,tipe,induk', "kodeorganisasi='".$tmpOrg."'");
            $data = fetchdata($query);
            $tipe = $data[0]['tipe'];
            $tmpOrg = $data[0]['induk'];
        }
    }

    if ($tipe != 'PT') {
        if ($opt != true) {
            $resArr = [$data[0]['kodeorganisasi'] => $data[0]['namaorganisasi']];
        } else {
            $resArr = ['kode' => $data[0]['kodeorganisasi'], 'nama' => $data[0]['namaorganisasi']];
        }

        return $resArr;
    }

    return false;
}

function getOrgBelow($dbname, $org, $self = true, $mode = 'all', $empty = false)
{
    $contOrg = [];
    $data = 'x';
    $tmpOrg = [$org];
    while (!empty($tmpOrg)) {
        foreach ($tmpOrg as $key => $tOrg) {
            unset($tmpOrg[$key]);
            $cols = 'kodeorganisasi,namaorganisasi,tipe';
            $query = selectquery($dbname, 'organisasi', $cols, "induk='".$tOrg."'");
            $data = fetchdata($query);
            foreach ($data as $row) {
                $contOrg[$row['tipe']][$row['kodeorganisasi']] = $row['namaorganisasi'];
                $tmpOrg[] = $row['kodeorganisasi'];
            }
        }
    }
    if ($empty == true) {
        $resOrg = ['' => ''];
    } else {
        $resOrg = [];
    }

    if ($self == true) {
        $query = selectquery($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$org."'");
        $data = fetchdata($query);
        $resOrg[$data[0]['kodeorganisasi']] = $data[0]['namaorganisasi'];
    }

    foreach ($contOrg as $tipe => $row1) {
        foreach ($row1 as $key => $row2) {
            if ($mode == 'kebun') {
                if ($tipe == 'KEBUN' || $tipe == 'AFDELING' || $tipe == 'BLOK') {
                    $resOrg[$key] = $row2;
                }
            } else {
                if ($mode == 'kebunndivisi') {
                    if ($tipe == 'KEBUN' || $tipe == 'DIVISI') {
                        $resOrg[$key] = $row2;
                    }
                } else {
                    if ($mode == 'kebunonly') {
                        if ($tipe == 'KEBUN') {
                            $resOrg[$key] = $row2;
                        }
                    } else {
                        if ($mode == 'afdeling') {
                            if ($tipe == 'AFDELING') {
                                $resOrg[$key] = $row2;
                            }
                        } else {
                            if ($mode == 'blok') {
                                if ($tipe == 'BLOK') {
                                    $resOrg[$key] = $row2;
                                }
                            } else {
                                if ($mode == 'noblok') {
                                    if ($tipe != 'BLOK') {
                                        $resOrg[$key] = $row2;
                                    }
                                } else {
                                    $resOrg[$key] = $row2;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $resOrg;
}

function getAttr($tableName, $codeField, $attrField, $codeVal, $tipe = 'str')
{
    global $dbname;
    if ($tipe == 'str') {
        $where = $codeField."='".$codeVal."'";
    } else {
        $where = $codeField.'='.$codeVal;
    }

    $query = selectquery($dbname, $tableName, $attrField, $where);
    $sel = fetchdata($query);

    return $sel[0][$attrField];
}

function getTotalRow($dbname, $table, $where = null, $joinLeft = [])
{
    $query = 'select count(*) as total from ';
    $query .= '`'.$dbname.'`.`'.$table.'`';
    if (!empty($joinLeft)) {
        $query .= ' a';
        foreach ($joinLeft as $key => $row) {
            $query .= ' left join '.$dbname.'.`'.$row['table'].'` '.chr($key + 98);
            $query .= ' on a.'.$row['refCol'].'='.chr($key + 98).'.'.$row['targetCol'];
        }
    }

    if (null != $where) {
        $query .= ' where '.$where;
    }

    $res = fetchdata($query);

    return $res[0]['total'];
}

?>
<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tableName = $_POST['tableName'];
$IDs = $_POST['IDs'];
$id = explode('##', $IDs);
$data = $_POST;
$opt = json_decode(str_replace('##', '"', $_POST['opt']), true);
unset($data['tableName'], $data['IDs'], $data['opt']);

$where = '';
for ($i = 1; $i < count($id); ++$i) {
    $tmpId = explode(',', $id[$i]);
    $tmpStr = explode('-', $tmpId[1]);
    if (3 === count($tmpStr)) {
        $tmpId[1] = tanggalsystem($tmpId[1]);
    }

    $int = (int) $tmpId[1];
    if (1 === $i) {
        if ((string) $int === $tmpId[1] && strlen((string) $int) === strlen($tmpId[1])) {
            $where .= '`'.$tableName.'`.`'.$tmpId[0].'`='.$tmpId[1];
        } else {
            if (is_string($tmpId[1])) {
                $where .= '`'.$tableName.'`.`'.$tmpId[0]."`='".$tmpId[1]."'";
            } else {
                $where .= '`'.$tableName.'`.`'.$tmpId[0].'`='.$tmpId[1];
            }
        }
    } else {
        if ((string) $int === $tmpId[1] && strlen((string) $int) === strlen($tmpId[1])) {
            $where .= ' AND `'.$tableName.'`.`'.$tmpId[0].'`='.$tmpId[1];
        } else {
            if (is_string($tmpId[1])) {
                $where .= ' AND `'.$tableName.'`.`'.$tmpId[0]."`='".$tmpId[1]."'";
            } else {
                $where .= ' AND `'.$tableName.'`.`'.$tmpId[0].'`='.$tmpId[1];
            }
        }
    }
}
$query = 'update `'.$dbname.'`.`'.$tableName.'` set ';
$i = 0;
foreach ($data as $key => $row) {
    $tmpStr = explode('-', $row);
    if (3 === count($tmpStr)) {
        $row = tanggalsystem($row);
    }

    $int = (int) $row;
    if (0 === $i) {
        if ((string) $int === $row && strlen((string) $int) === strlen($row)) {
            $query .= '`'.$tableName.'`.`'.$key.'`='.$row;
        } else {
            if (is_string($row)) {
                $query .= '`'.$tableName.'`.`'.$key."`='".$row."'";
            } else {
                $query .= '`'.$tableName.'`.`'.$key.'`='.$row;
            }
        }
    } else {
        if ((string) $int === $row && strlen((string) $int) === strlen($row)) {
            $query .= ','.'`'.$tableName.'`.`'.$key.'`='.$row;
        } else {
            if (is_string($row)) {
                $query .= ','.'`'.$tableName.'`.`'.$key."`='".$row."'";
            } else {
                $query .= ','.'`'.$tableName.'`.`'.$key.'`='.$row;
            }
        }
    }

    ++$i;
}
$query .= ' where '.$where;

try {
    if (!mysql_query($query)) {
        echo 'DB Error : '.mysql_error($conn);
        exit();
    }

    echo "var currRow = document.getElementById('currRow').value;";
    foreach ($data as $key => $row) {
        if (isset($opt[$key])) {
            $tmpCont = $opt[$key][$row];
        } else {
            $tmpCont = $row;
        }

        echo "document.getElementById('".$key."_'+currRow).innerHTML = '".$tmpCont."';";
        echo "document.getElementById('".$key."_'+currRow).setAttribute('value','".$row."');";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

?>
<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tableName = $_POST['tableName'];
$numRow = $_POST['numRow'];
$idField = $_POST['idField'];
$idVal = $_POST['idVal'];
$data = $_POST;
$data['tgltransaksi'] = date('Ymd');
unset($data['tableName'], $data['numRow'], $data['idField'], $data['idVal']);

$query = 'insert into `'.$dbname.'`.`'.$tableName.'`(';
$i = 0;
foreach ($data as $key => $row) {
    if (0 == $i) {
        $query .= '`'.$key.'`';
    } else {
        $query .= ',`'.$key.'`';
    }

    ++$i;
}
$query .= ') values (';
$i = 0;
foreach ($data as $row) {
    $tmpStr = explode('-', $row);
    if (3 == count($tmpStr)) {
        $row = tanggalsystem($row);
    }

    $int = (int) $row;
    if (0 == $i) {
        if ((string) $int == $row && strlen((string) $int) == strlen($row)) {
            $query .= $row;
        } else {
            if (is_string($row)) {
                $query .= "'".$row."'";
            } else {
                $query .= $row;
            }
        }
    } else {
        if ((string) $int == $row && strlen((string) $int) == strlen($row)) {
            $query .= ','.$row;
        } else {
            if (is_string($row)) {
                $query .= ",'".$row."'";
            } else {
                $query .= ','.$row;
            }
        }
    }

    ++$i;
}
$query .= ');';

try {
    if (!mysql_query($query)) {
        echo 'DB Error : '.mysql_error($conn);
        exit();
    }

    echo "<tr id='tr_".$numRow."' class='rowcontent'>";
    $tmpField = '';
    $tmpVal = '';
    unset($data['tgltransaksi']);
    foreach ($data as $key => $row) {
        echo "<td id='".$key.'_'.$numRow."'>".$row.'</td>';
        $tmpField .= '##'.$key;
        $tmpVal .= '##'.$row;
    }
    echo "<td><img id='editRow".$numRow."' title='Edit' onclick=\"editRow(".$numRow.",'".$tmpField."','".$tmpVal."')\"\r\n\tclass='zImgBtn' src='images/001_45.png' /></td>";
    echo "<td><img id='delRow".$numRow."' title='Hapus' onclick=\"delRow(".$numRow.",'".$idField."','".$idVal."',null,'".$tableName."')\"\r\n\tclass='zImgBtn' src='images/delete_32.png' /></td>";
    echo '</tr>';
} catch (Exception $e) {
    echo 'ERROR Query';
    echo $e->getMessage();
}

?>
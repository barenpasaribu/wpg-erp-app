<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$empty = $_GET['empty'];
$tableName = $_POST['tableName'];
$numRow = $_POST['numRow'];
$idField = $_POST['idField'];
$idVal = $_POST['idVal'];
$data = $_POST;
$opt = json_decode(str_replace('##', '"', $_POST['opt']), true);
unset($data['tableName'], $data['numRow'], $data['opt'], $data['idField'], $data['idVal'], $data['freeze']);

if (false === $empty) {
    foreach ($data as $dt => $isi) {
        if ('' === $isi) {
            echo 'warning:Please Insert The Form';
            exit();
        }
    }
}

$sCek = 'select * from `'.$dbname.'`.`'.$tableName.'` ';
$query = 'insert into `'.$dbname.'`.`'.$tableName.'`(';
$i = 0;
foreach ($data as $key => $row) {
    if (0 === $i) {
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
    if (3 === count($tmpStr)) {
        $row = tanggalsystem($row);
    }

    $int = (int) $row;
    if (0 === $i) {
        if ((string) $int === $row && strlen((string) $int) === strlen($row)) {
            $query .= $row;
        } else {
            if (is_string($row)) {
                $query .= "'".$row."'";
            } else {
                $query .= $row;
            }
        }
    } else {
        if ((string) $int === $row && strlen((string) $int) === strlen($row)) {
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
    foreach ($data as $key => $row) {
        if (isset($opt[$key])) {
            $tmpCont = $opt[$key][$row];
        } else {
            $tmpCont = $row;
        }

        echo "<td id='".$key.'_'.$numRow."' value='".$row."'>".$tmpCont.'</td>';
        $tmpField .= '##'.$key;
        $tmpVal .= '##'.$row;
    }
    if (isset($_POST['freeze'])) {
        echo "<td><img id='editRow".$numRow."' title='Edit' onclick=\"editRow(".$numRow.",'".$tmpField."','".$tmpVal."','".$_POST['freeze']."')\"\r\n\t    class='zImgBtn' src='images/001_45.png' /></td>";
    } else {
        echo "<td><img id='editRow".$numRow."' title='Edit' onclick=\"editRow(".$numRow.",'".$tmpField."','".$tmpVal."')\"\r\n\t    class='zImgBtn' src='images/001_45.png' /></td>";
    }

    echo "<td><img id='delRow".$numRow."' title='Hapus' onclick=\"delRow(".$numRow.",'".$idField."','".$idVal."',null,'".$tableName."')\"\r\n\tclass='zImgBtn' src='images/delete_32.png' /></td>";
    echo "<td><img id='detail".$i."' title='Edit Detail' onclick=\"editRow(".$i.",'".$fieldStr."','".$tmpVal."','kodeorg##shift');".'showDetail('.$i.",'".$primaryStr."##kodeorg##shift',event)\"\r\n    class='zImgBtn' src='images/application/application_view_xp.png' /></td>";
    echo '</tr>';
} catch (Exception $e) {
    echo 'ERROR Query';
    echo $e->getMessage();
}

?>
<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'add':
        $data = $_POST;
        unset($data['rowNum']);
        $data['updateby'] = $_SESSION['standard']['userid'];
        $data['updatetime'] = date('Ymd His');
        $query = insertQuery($dbname, 'sdm_5pesangon', $data);
        if (!mysql_query($query)) {
            exit('DB Error: '.mysql_error());
        }

        break;
    case 'edit':
        $data = $param;
        unset($data['rowNum'], $data['masakerja'], $data['ref']);

        $data['updateby'] = $_SESSION['standard']['userid'];
        $data['updatetime'] = date('Ymd His');
        $where = 'masakerja='.$param['ref']['masakerja'];
        $query = updateQuery($dbname, 'sdm_5pesangon', $data, $where);
        if (!mysql_query($query)) {
            exit('DB Error: '.mysql_error());
        }

        break;
    case 'delete':
        $where = 'masakerja='.$param['masakerja'];
        $query = deleteQuery($dbname, 'sdm_5pesangon', $where);
        if (!mysql_query($query)) {
            exit('DB Error: '.mysql_error());
        }

        echo $optField[$param['noakun']];

        break;
    case 'list':
        $cols = 'masakerja,pesangon,penghargaan,pengganti,perusahaan,kesalahanbiasa,kesalahanberat,uangpisah';
        $query = selectQuery($dbname, 'sdm_5pesangon', $cols);
        $data = fetchData($query);
        foreach ($data as $key => $row) {
            echo '<tr class=rowcontent>';
            echo "<td><img src='images/".$_SESSION['theme']."/edit.png' onclick=editMode(".$key.') class=zImgBtn></td>';
            echo "<td><img src='images/".$_SESSION['theme']."/delete.png' onclick=deleteData(".$key.') class=zImgBtn></td>';
            foreach ($row as $attr => $val) {
                if ('masakerja' != $attr) {
                    $tmpVal = number_format($val, 2);
                } else {
                    $tmpVal = $val;
                }

                echo "<td id='".$attr.'_'.$key."' value='".$val."'>".$tmpVal.'</td>';
            }
            echo '</tr>';
        }

        break;
    default:
        break;
}

?>
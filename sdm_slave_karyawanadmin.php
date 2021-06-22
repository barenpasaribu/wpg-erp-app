<?php



include_once 'lib/zLib.php';
$sNIK = $_POST['nik'];
$sNama = $_POST['nama'];
$where = '';
if ('' != $sNIK) {
    $where .= "nik like '".$sNIK."'";
}

if ('' != $sNama) {
    if ('' != $where) {
        $where .= ' AND ';
    }

    $where .= "namakaryawan like '".$sNama."'";
}

$query = selectQuery($dbname, 'datakaryawan', 'nik,namakaryawan,kodeorganisasi,bagian,karyawanid', $where);
$data = fetchData($query);
$header = [];
if ($data != []) {
    foreach ($data[0] as $key => $row) {
        if ('karyawanid' != $key) {
            $header[] = $key;
        }
    }
}

$table = "<table id='mainTable' name='mainTable' class='sortable' cellspacing='1' border='0'>";
$table .= "<thead><tr class='rowheader'>";
if ($data == []) {
    $table .= '<td>Data Tidak ada</td>';
} else {
    foreach ($header as $head) {
        $table .= '<td>'.$head.'</td>';
    }
}

$table .= '</tr></thead>';
$table .= "<tbody id='mainBody'>";
foreach ($data as $key => $row) {
    $table .= "<tr id='tr_".$key."' class='rowcontent'\r\n        onclick=\"showManage('edit','".$key."',event)\" style='cursor:pointer'>";
    foreach ($row as $head => $content) {
        if ('karyawanid' != $head) {
            $table .= "<td id='".$head.'_'.$key."'>".$content.'</td>';
        }
    }
    $table .= "<input id='karyawanid_".$key."' type='hidden' value='".$row['karyawanid']."'>";
    $table .= '</tr>';
}
$table .= '</tbody>';
$table .= '<tfoot>';
$table .= '</tfoot>';
$table .= '</table>';
echo "<a onclick=\"showManage('add','0',event)\" style='cursor:pointer'>Tambah Data</a>";
echo $table;

?>
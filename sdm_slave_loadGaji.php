<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
$optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in(1,2,3,4,5)");
$optComp = makeOption($dbname, 'sdm_ho_component', 'id,name', "type='basic'");
$fieldStr = '##tahun##karyawanid##idkomponen##jumlah';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$opt = ['karyawanid' => $optKary, 'idkomponen' => $optComp];
$optJs = str_replace('"', '##', json_encode($opt));
$els['btn'] = [genFormBtn($fieldStr, 'sdm_5gajipokok', '##tahun##karyawanid##idkomponen', null, null, null, null, '##', '##', $optJs)];
if ('HOLDING' == $_SESSION['empl']['tipeinduk'] || 'HOLDING' == $_SESSION['org']['tipelokasitugas']) {
    $where = '1=1 and tahun='.$_POST['tahun'];
} else {
    $where = 'karyawanid in (';
    $i = 0;
    foreach ($optKary as $key => $row) {
        if (0 == $i) {
            $where .= $key;
        } else {
            $where .= ','.$key;
        }

        ++$i;
    }
    $where .= ')  and tahun='.$_POST['tahun'];
}

$tablex = 'sdm_5gajipokok';
echo masterTable($dbname, $tablex, '*', [], [], $where, [], 'sdm_slave_5gajipokok_pdf', 'tahun##karyawanid##idkomponen', true, null, $opt, 'Gaji Pokok');

?>
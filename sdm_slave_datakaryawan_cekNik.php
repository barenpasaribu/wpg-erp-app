<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$nik = $_POST['nik'];
$method = $_POST['method'];
$lokasitugas = $_POST['lokasitugas'];
$subbagian = $_POST['subbagian'];
switch ($method) {
    case 'cekNik':
        if ($nik == '') {
        } else {
            $iCek = 'select nik from '.$dbname.".datakaryawan where nik='".$nik."'";
            $ada = true;
            $nCek = mysql_query($iCek);
            while ($dCek = mysql_fetch_assoc($nCek)) {
                if (true == $ada) {
                    echo 'warning : Nik untuk '.$nik.' sudah ada';
                    exit();
                }
            }
        }

        break;
    case 'getSub':
        $optsubbagian = "<option value='0'></option>";
        $iCek = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$lokasitugas."'";
        $nCek = mysql_query($iCek);
        while ($dCek = mysql_fetch_assoc($nCek)) {
            if ($subbagian == $dCek['kodeorganisasi']) {
                $select = 'selected=selected';
            } else {
                $select = '';
            }

            $optsubbagian .= '<option '.$select." value='".$dCek['kodeorganisasi']."'>".$dCek['namaorganisasi'].'</option>';
        }
        echo $optsubbagian;

        break;
}
echo "    \r\n        ";

?>
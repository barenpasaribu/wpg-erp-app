<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
require_once 'lib/zLib.php';
$listGol = makeOption($dbname, 'sdm_5golongan', 'kodegolongan,namagolongan');
$getrows = 20;
if ($_POST['page']) {
    $page = $_POST['page'];
} else {
    $page = 1;
}

$maxdisplay = $page * $getrows - 20;
if (isset($_POST['txtsearch'])) {
    $txtsearch = $_POST['txtsearch'];
    $orgsearch = $_POST['orgsearch'];
    $tipesearch = $_POST['tipesearch'];
    $statussearch = $_POST['statussearch'];
    $niksch = $_POST['niksch'];
} else {
    $txtsearch = '';
    $orgsearch = '';
    $tipesearch = '';
    $statussearch = '';
    $niksch = '';
}

$where = '';
if ($niksch != '') {
    $where .= " and a.nik like '%".$niksch."%'";
}

if ($txtsearch != '') {
    $where .= " and a.namakaryawan like '%".$txtsearch."%'";
}

if ($orgsearch != '') {
    $where .= " and (a.lokasitugas='".$orgsearch."' or a.subbagian='".$orgsearch."') ";
}

if ($tipesearch != '') {
    $where .= " and a.tipekaryawan='".$tipesearch."'";
}

if ($statussearch == '*') {
    $where .= ' and (a.tanggalkeluar<'.$_SESSION['org']['period']['start'].' and tanggalkeluar IS NULL)';
} else {
    if ($statussearch == '0000-00-00') {
        $where .= ' and (a.tanggalkeluar>'.$_SESSION['org']['period']['start'].' or tanggalkeluar is NULL)';
    }
}

$listOrg = ambilLokasiTugasDanTurunannya('list', $_SESSION['empl']['lokasitugas']);
$list = str_replace('|', "','", $listOrg);
$list = "'".$list."'";
if (trim($_SESSION['empl']['tipelokasitugas']) == 'HOLDING') {
    $str = 'select a.*,b.namajabatan,d.tipe from '.$dbname.".datakaryawan a,\r\n      ".$dbname.'.sdm_5jabatan b,  '.$dbname.".sdm_5tipekaryawan d where\r\n\t  a.kodejabatan=b.kodejabatan\r\n\t  and d.id=a.tipekaryawan\r\n\t  ".$where." and a.karyawanid not in (0999999999,0888888888)\r\n\t  order by a.nik asc limit ".$maxdisplay.','.$getrows;
} else {
    if (trim($_SESSION['empl']['tipelokasitugas']) == 'KANWIL') {
        $str = 'select a.*,b.namajabatan,d.tipe from '.$dbname.".datakaryawan a,\r\n      ".$dbname.'.sdm_5jabatan b,  '.$dbname.".sdm_5tipekaryawan d where\r\n\t  a.kodejabatan=b.kodejabatan\r\n\t  and d.id=a.tipekaryawan\r\n\t  ".$where.' and lokasitugas in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n\t  order by a.nik asc limit ".$maxdisplay.','.$getrows;
    } else {
        $str = 'select a.*,b.namajabatan,d.tipe from '.$dbname.".datakaryawan a,\r\n      ".$dbname.'.sdm_5jabatan b,  '.$dbname.".sdm_5tipekaryawan d where\r\n      lokasitugas in(".$list.")\r\n\t  and a.kodejabatan=b.kodejabatan\r\n\t  and d.id=a.tipekaryawan\r\n\t  ".$where."\r\n\t  order by a.nik asc limit ".$maxdisplay.','.$getrows;
    }
}

$res = mysql_query($str);
$numrows = mysql_num_rows($res);
if ($numrows < 1) {
    echo '<tr><td>NO DATA FOUND</td></tr>';
} else {
    $no = $maxdisplay;
    while ($bar = mysql_fetch_object($res)) {
        $str1 = 'select a.kelompok from '.$dbname.".sdm_5pendidikan a\r\n\t\t       where a.levelpendidikan=".$bar->levelpendidikan;
        $res1 = mysql_query($str1);
        $pendidikan = '';
        while ($barpendidikan = mysql_fetch_object($res1)) {
            $pendidikan = $barpendidikan->kelompok;
        }
        $no++;
        echo "  <tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$bar->nik."</td>
                    <td>".$bar->namakaryawan."</td>
                    <td>".$bar->namajabatan."</td>
                    <td>".$listGol[$bar->kodegolongan]."</td>
                    <td>".$bar->lokasitugas."</td>
                    <td>".$bar->kodeorganisasi."</td>
                    <td>".$bar->noktp."</td>
                    <td>".$bar->jms."</td>
                    <td>".$bar->idmedical."</td>
                    <td>".$pendidikan."</td>
                    <td>".$bar->statuspajak."</td>
                    <td>".$bar->statusperkawinan."</td>
                    <td align=right >".$bar->jumlahanak."</td>
                    <td>".tanggalnormal($bar->tanggalmasuk)."</td>
                    <td>".$bar->tipe."</td>
                    <td>";
        if ($_SESSION['standard']['userid'] == '0000000732' || $_SESSION['standard']['userid'] == '0000000265' || $_SESSION['standard']['userid'] == '0000000636' || $_SESSION['standard']['userid'] == '0000000794') {
            echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editKaryawan2('".$bar->karyawanid."','".$bar->namakaryawan."');\">";
        }else{
            echo "<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."');\">";
        }
        
        echo "<img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">
                        <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewKaryawanPDF('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">
                    </td>
                </tr>";
    }
}

?>
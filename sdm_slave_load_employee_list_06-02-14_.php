<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
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
} else {
    $txtsearch = '';
    $orgsearch = '';
    $tipesearch = '';
    $statussearch = '';
}

$where = '';
if ('' != $txtsearch) {
    $where = " and a.namakaryawan like '%".$txtsearch."%'";
}

if ('' != $orgsearch) {
    $where .= " and (a.lokasitugas='".$orgsearch."' or a.subbagian='".$orgsearch."') ";
}

if ('' != $tipesearch) {
    $where .= " and a.tipekaryawan='".$tipesearch."'";
}

if ('*' == $statussearch) {
    $where .= ' and (a.tanggalkeluar<'.$_SESSION['org']['period']['start'].' and tanggalkeluar IS NULL)';
} else {
    if ('0000-00-00' == $statussearch) {
        $where .= ' and (a.tanggalkeluar>'.$_SESSION['org']['period']['start'].' or tanggalkeluar is NULL)';
    }
}

$listOrg = ambilLokasiTugasDanTurunannya('list', $_SESSION['empl']['lokasitugas']);
$list = str_replace('|', "','", $listOrg);
$list = "'".$list."'";
if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
    $str = 'select a.*,b.namajabatan,c.namagolongan,d.tipe from '.$dbname.".datakaryawan a, \r\n      ".$dbname.'.sdm_5jabatan b, '.$dbname.'.sdm_5golongan c,  '.$dbname.".sdm_5tipekaryawan d where \r\n\t  a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan\r\n\t  and d.id=a.tipekaryawan \r\n\t  ".$where."\r\n\t  limit ".$maxdisplay.','.$getrows;
} else {
    if ('KANWIL' == trim($_SESSION['empl']['tipelokasitugas'])) {
        $str = 'select a.*,b.namajabatan,c.namagolongan,d.tipe from '.$dbname.".datakaryawan a, \r\n      ".$dbname.'.sdm_5jabatan b, '.$dbname.'.sdm_5golongan c,  '.$dbname.".sdm_5tipekaryawan d where \r\n\t  a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan\r\n\t  and d.id=a.tipekaryawan and a.tipekaryawan!=5\r\n\t  ".$where.' and lokasitugas in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\r\n\t  limit ".$maxdisplay.','.$getrows;
    } else {
        $str = 'select a.*,b.namajabatan,c.namagolongan,d.tipe from '.$dbname.".datakaryawan a, \r\n      ".$dbname.'.sdm_5jabatan b, '.$dbname.'.sdm_5golongan c,  '.$dbname.".sdm_5tipekaryawan d where \r\n      lokasitugas in(".$list.")\r\n\t  and a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan\r\n\t  and d.id=a.tipekaryawan and a.tipekaryawan!=5\r\n\t  ".$where."\r\n\t  limit ".$maxdisplay.','.$getrows;
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
        ++$no;
        echo "<tr class=rowcontent>\r\n\t\t     <td>".$no."</td>\r\n\t\t\t <td>".$bar->nik."</td>\r\n\t\t\t <td>".$bar->namakaryawan."</td>\r\n\t\t\t <td>".$bar->namajabatan."</td>\r\n\t\t\t <td>".$bar->namagolongan."</td>\r\n\t\t\t <td>".$bar->lokasitugas."</td>\r\n\t\t\t <td>".$bar->kodeorganisasi."</td>\r\n\t\t\t <td>".$bar->noktp."</td>\r\n\t\t\t <td>".$pendidikan."</td>\r\n\t\t\t <td>".$bar->statuspajak."</td>\r\n\t\t\t <td>".$bar->statusperkawinan."</td>\r\n\t\t\t <td align=right >".$bar->jumlahanak."</td>\r\n\t\t\t <td>".tanggalnormal($bar->tanggalmasuk)."</td>\r\n\t\t\t <td>".$bar->tipe."</td>\r\n\t\t\t <td>\r\n\t\t\t\t    <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."');\"> \r\n\t\t\t\t    <img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">\r\n\t\t\t\t\t<img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewKaryawanPDF('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">\t\t \r\n\t\t\t </td>\r\n\t\t\t  </tr>";
    }
}

?>
<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$tahunplafon = $_POST['tahunplafon'];
$periode = $_POST['periode'];
$jenisbiaya = $_POST['jenisbiaya'];
$karyawanid = $_POST['karyawanid'];
$method = $_POST['method'];
$ygberobat = $_POST['ygberobat'];
$rs = $_POST['rs'];
$diagnosa = $_POST['diagnosa'];
$klaim = $_POST['klaim'];
$notransaksi = $_POST['notransaksi'];
$hariistirahat = $_POST['hariistirahat'];
$tanggal = $_POST['tanggal'];
$keterangan = $_POST['keterangan'];
$byrs = $_POST['byrs'];
$byadmin = $_POST['byadmin'];
$bydr = $_POST['bydr'];
$byobat = $_POST['byobat'];
$total = $_POST['total'];
$bylab = $_POST['bylab'];
$bebanperusahaan = $_POST['bebanperusahaan'];
$bebankaryawan = $_POST['bebankaryawan'];
$bebanjamsostek = $_POST['bebanjamsostek'];
if (!isset($_POST['tahunplafon'])) {
    $tahunplafon = date('Y');
}

if ('RWINP' == $jenisbiaya || 'RWJLN' == $jenisbiaya) {
    $optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
    $scek = 'select sum(jlhbayar) as totDibyr from '.$dbname.".sdm_pengobatanht \n              where karyawanid='".$karyawanid."' and tahunplafon='".$tahunplafon."'";
    $qcek = mysql_query($scek);
    $rcek = mysql_fetch_assoc($qcek);
    $sgapok = 'select distinct sum(jumlah) as jmlhgapok from '.$dbname.".sdm_5gajipokok where\n                karyawanid='".$karyawanid."' and tahun='".$tahunplafon."' and idkomponen in (1,2)";
    $qgapok = mysql_query($sgapok);
    $rgapok = mysql_fetch_assoc($qgapok);
    $sprsn = 'select distinct persen from '.$dbname.".sdm_pengobatanplafond \n               where kodejenisbiaya='".$jenisbiaya."'";
    $qprsn = mysql_query($sprsn);
    $rprsn = mysql_fetch_assoc($qprsn);
    $totPlafon = $rgapok['jmlhgapok'] * $rprsn['persen'] / 100;
    if ($totPlafon < $rcek['totDibyr']) {
        exit('error: Plafon untuk '.$optNmKar[$karyawanid]." sudah melewati batas!!\n\n                        Plafon= ".number_format($totPlafon, 2).', Reimbursement='.number_format($rcek['totDibyr'], 2));
    }
}

$kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if ('' == $karyawanid) {
    $karyawanid = 0;
}

if ('insert' == $method) {
    $str = 'insert into '.$dbname.".sdm_pengobatanht (\t\n\t\t  `notransaksi`, `kodeorg`, `karyawanid`,\n\t\t  `tahunplafon`, `kodebiaya`, `keterangan`,\n\t\t  `rs`, `updateby`, `jasars`,  `jasadr`,\n\t\t  `jasalab`, `byobat`, `bypendaftaran`,\n\t\t  `ygsakit`, `jlhbayar`, `tanggalbayar`,\n\t\t  `totalklaim`, `jlhhariistirahat`,\n\t\t  `klaimoleh`, `periode`, `tanggal`, `diagnosa`,\n                                          `bebanperusahaan`, `bebankaryawan`, `bebanjamsostek`)\n\t\t  values(\n\t\t  '".$notransaksi."','".$kodeorg."',".$karyawanid.",\n\t\t   ".$tahunplafon.",'".$jenisbiaya."','".$keterangan."',\n\t\t    '".$rs."',".$_SESSION['standard']['userid'].",\n\t\t\t'".$byrs."','".$bydr."','".$bylab."','".$byobat."','".$byadmin."',\n\t\t\t'".$ygberobat."',0,'0000-00-00',\n\t\t\t'".$total."','".$hariistirahat."',\n\t\t\t'".$klaim."','".$periode."','".tanggalsystem($tanggal)."',\n\t\t\t'".$diagnosa."','".$bebanperusahaan."','".$bebankaryawan."','".$bebanjamsostek."'\t\t\t\n\t\t  )";
} else {
    if ('del' == $method) {
        $str = 'delete from '.$dbname.".sdm_pengobatanht where notransaksi='".$notransaksi."'";
    } else {
        $str = 'select 1=1';
    }
}

if (mysql_query($str)) {
    $str = 'select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag from '.$dbname.".sdm_pengobatanht a left join\n\t      ".$dbname.".sdm_5rs b on a.rs=b.id \n\t\t  left join ".$dbname.".datakaryawan c\n\t\t  on a.karyawanid=c.karyawanid\n\t\t  left join ".$dbname.".sdm_5diagnosa d\n\t\t  on a.diagnosa=d.id\n\t\t  where a.periode='".$tahunplafon."'  \n\t\t  and a.kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\n\t\t  order by a.updatetime desc, a.tanggal desc";
    $stream = '';
    $res = mysql_query($str);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo "<tr class=rowcontent>\n\t\t\t   <td>";
        if (0 == $bar->posting) {
            echo "<img src=images/close.png title='delete' class=resicon onclick=deletePengobatan('".$bar->notransaksi."')>";
        }

        echo "<img src=images/zoom.png title='View' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)>";
        echo '</td><td>'.$no."</td>\n\t\t\t\t  <td>".$bar->notransaksi."</td>\n\t\t\t\t  <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\n\t\t\t\t  <td>".tanggalnormal($bar->tanggal)."</td>\n\t\t\t\t  <td>".$bar->namakaryawan."</td>\n\t\t\t\t  <td>".$bar->namars.'['.$bar->kota.']'."</td>\n\t\t\t\t  <td>".$bar->kodebiaya."</td>\n\t\t\t\t  <td align=right>".number_format($bar->totalklaim, 2, '.', ',')."</td>\n\t\t\t\t  <td align=right>".number_format($bar->jlhbayar, 2, '.', ',')."</td>\n                                                                                <td align=right>".number_format($bar->bebanperusahaan, 2, '.', ',')."</td>\n                                                                                <td align=right>".number_format($bar->bebankaryawan, 2, '.', ',')."</td>\n                                                                                <td align=right>".number_format($bar->bebanjamsostek, 2, '.', ',')."</td>                                         \n\t\t\t\t  <td>".$bar->ketdiag."</td>\n\t\t\t\t  <td>".$bar->keterangan."</td>\n\t\t\t\t</tr>";
    }
} else {
    echo ' Error: '.addslashes(mysql_error($conn));
}

?>
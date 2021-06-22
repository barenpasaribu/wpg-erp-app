<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$periode = $_POST['periode'];
$kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$karyawanid = $_POST['karyawanid'];
$pt = $_POST['pt'];
$notransaksi = $_POST['notransaksi'];
$keterangan = $_POST['keterangan'];
$bytransport = $_POST['bytransport'];
$byperawatan = $_POST['byperawatan'];
$bytoll = $_POST['bytoll'];
$bylain = $_POST['bylain'];
$total = $_POST['total'];
$method = $_POST['method'];
$userid = $_SESSION['standard']['userid'];
if ('delete' == $method) {
    $str = 'delete from '.$dbname.".sdm_penggantiantransport where notransaksi='".$notransaksi."'";
} else {
    if ('insert' == $method) {
        $str = 'insert into '.$dbname.".sdm_penggantiantransport \r\n\t      (`notransaksi`,`karyawanid`,`periode`,\r\n\t\t  `keterangan`,`toll`,`trans`,\r\n\t\t  `perawatan`,`kodeorg`,`alokasi`,\r\n\t\t  `updateby`,`bylain`,`totalklaim`)\r\n\t\t  values(\r\n\t\t   '".$notransaksi."',".$karyawanid.",'".$periode."',\r\n\t\t   '".$keterangan."',".$bytoll.','.$bytransport.",\r\n\t\t   ".$byperawatan.",'".$kodeorg."','".$pt."',\r\n\t\t   ".$userid.','.$bylain.','.$total."\r\n\t\t  )";
    } else {
        if ('update' == $method) {
            $str = 'update '.$dbname.".sdm_penggantiantransport\r\n\t      set \r\n\t\t  `karyawanid`=".$karyawanid.",\r\n\t\t  `periode`='".$periode."',\r\n\t\t  `keterangan`='".$keterangan."',\r\n\t\t  `toll`=".$bytoll.",\r\n\t\t  `trans`=".$bytransport.",\r\n\t\t  `perawatan`=".$byperawatan.",\r\n\t\t  `kodeorg`='".$kodeorg."',\r\n\t\t  `alokasi`='".$pt."',\r\n\t\t  `updateby`=".$userid.",\r\n\t\t  `bylain`=".$bylain.",\r\n\t\t  `totalklaim`=".$total."\r\n\t\t  where notransaksi='".$notransaksi."'";
        } else {
            $str = 'select 1=1';
        }
    }
}

if (mysql_query($str)) {
    if ('' == $periode) {
        $periode = date('Y-m');
    }

    $str = 'select a.*,sum(b.jlhbbm) as bbm,c.namakaryawan from '.$dbname.".sdm_penggantiantransport a\r\n\t      left join ".$dbname.".sdm_penggantiantransportdt b \r\n\t\t  on a.notransaksi=b.notransaksi\r\n\t\t  left join ".$dbname.".datakaryawan c\r\n\t\t  on a.karyawanid=c.karyawanid\r\n\t\t   where periode='".$periode."' and \r\n\t\t  kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t\t  group by notransaksi";
    $res = mysql_query($str);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $add = '';
        if (0 == $bar->posting) {
            $add .= " <img src='images/close.png' class=resicon onclick=deleteBBM('".$bar->notransaksi."') title='delete'>";
        }

        $add .= " <img src='images/pdf.jpg' class=resicon onclick=previewBBM('".$bar->notransaksi."',event) title='view'>";
        echo "<tr class=rowcontent>\r\n\t\t     <td>".$no."</td>\r\n\t\t\t <td>".$bar->notransaksi."</td>\r\n\t\t\t <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n\t\t\t <td>".$bar->alokasi."</td>\r\n\t\t\t <td>".$bar->namakaryawan."</td>\r\n\t\t\t <td align=right>".number_format($bar->totalklaim, 2, ',', '.')."</td>\r\n\t\t\t <td align=right>".number_format($bar->dibayar, 2, ',', '.')."</td>\r\n\t\t\t <td>".tanggalnormal($bar->tanggalbayar)."</td>\r\n\t\t\t <td align=right>".number_format($bar->bbm, 2, ',', '.')."</td>\r\n\t\t\t <td>".$bar->keterangan."</td>\t\r\n\t\t\t <td>".$add."</td>\t \r\n\t\t   </tr>";
    }
} else {
    echo ' Gagal '.addslashes(mysql_error($conn));
}

?>
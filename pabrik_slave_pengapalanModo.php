<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
$notran = $_POST['notran'];
$tgl = tanggalsystem($_POST['tgl']);
$kodeorg = $_POST['kodeorg'];
$nokontrak = $_POST['nokontrak'];
$nodo = $_POST['nodo'];
$kdCust = $_POST['kdCust'];
$kdbarang = $_POST['kdbarang'];
$kdKapal = $_POST['kdKapal'];
$transp = $_POST['transp'];
$berat = $_POST['berat'];
$perSch = $_POST['perSch'];
$notranSch = $_POST['notranSch'];
$nokontrakSch = $_POST['nokontrakSch'];
$nmCust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nmTranp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
if ('excel' === $method) {
    $perSch = $_GET['perSch'];
    $notranSch = $_GET['notranSch'];
    $nokontrakSch = $_GET['nokontrakSch'];
}

$where = "left(notransaksi,4)='H01M' and millcode='H01M'";
if ('' !== $perSch) {
    $where .= "and tanggal like '".$perSch."%'";
}

if ('' !== $notranSch) {
    $where .= "and notransaksi like '%".$notranSch."%'";
}

if ('' !== $nokontrakSch) {
    $where .= "and nokontrak like '%".$nokontrakSch."%'";
}

echo "\r\n";
switch ($method) {
    case 'excel':
        $border = "border='1'";
        $bgcolor = 'bgcolor=#CCCCCC';
        $stream = ''.$_SESSION['lang']['pengapalanmodo'].'<br />Periode : '.$perSch.'';
        $stream .= "<table cellspacing='1' class='sortable'  ".$border.'>';
        $stream .= "<thead class=rowheader>\r\n\t\t\t  <tr>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['notransaksi']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['pabrik']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['NoKontrak']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['nodo']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['nmcust']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['kodebarang']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['kodekapal']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['transporter']."</td>\r\n\t\t\t\t <td align=center ".$bgcolor.'>'.$_SESSION['lang']['beratnormal']."</td>\r\n\t\t\t  </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        $w = 'select * from '.$dbname.'.pabrik_timbangan where  '.$where.'   ';
        $i = mysql_query($w);
        while ($b = mysql_fetch_assoc($i)) {
            ++$no;
            $stream .= '<tr '.$border.'  class=rowcontent>';
            $stream .= '<td align=center>'.$no.'</td>';
            $stream .= '<td align=left>'.$b['notransaksi'].'</td>';
            $stream .= '<td align=left>'.tanggalnormal($b['tanggal']).'</td>';
            $stream .= '<td align=left>'.$b['millcode'].'</td>';
            $stream .= '<td align=left>'.$b['nokontrak'].'</td>';
            $stream .= '<td align=left>'.$b['nodo'].'</td>';
            $stream .= '<td align=left>'.$nmCust[$b['kodecustomer']].'</td>';
            $stream .= '<td align=left>'.$nmBarang[$b['kodebarang']].'</td>';
            $stream .= '<td align=left>'.$b['nokendaraan'].'</td>';
            $stream .= '<td align=left>'.$nmTranp[$b['trpcode']].'</td>';
            $stream .= '<td align=right>'.number_format($b['beratbersih']).'</td>';
            $stream .= '</tr>';
        }
        $stream .= 'Print Time : '.date('H:i:s, d/m/Y').'<br>By : '.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'Laporan_Kelengkapan_Loses'.$tglSkrg;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n\t\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t\t</script>";
            closedir($handle);
        }

        break;
    case 'getCust':
        $i = 'select koderekanan,kodebarang from '.$dbname.".pmn_kontrakjual where nokontrak='".$nokontrak."'";
        $n = mysql_query($i);
        while ($d = mysql_fetch_assoc($n)) {
            $optCust .= "<option value='".$d['koderekanan']."'>".$nmCust[$d['koderekanan']].'</option>';
        }
        $n2 = mysql_query($i);
        while ($d2 = mysql_fetch_assoc($n2)) {
            $optBarang .= "<option value='".$d2['kodebarang']."'>".$nmBarang[$d2['kodebarang']].'</option>';
        }
        echo $optCust.'###'.$optBarang;

        break;
    case 'insert':
        $str = 'insert into '.$dbname.".pabrik_timbangan (`notransaksi`,`tanggal`,`millcode`,`nokontrak`,`nodo`,`kodecustomer`\r\n\t\t\t\t,`kodebarang`,`nokendaraan`,`trpcode`,`beratbersih`,`username`)\r\n\t\t\t\tvalues ('".$notran."','".$tgl."','".$kodeorg."','".$nokontrak."','".$nodo."','".$kdCust."','".$kdbarang."','".$kdKapal."',\r\n\t\t\t\t'".$transp."','".$berat."','".$_SESSION['standard']['username']."')";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        $str = 'update '.$dbname.".pabrik_timbangan set tanggal='".$tgl."',nokontrak='".$nokontrak."',nodo='".$nodo."',kodecustomer='".$kdCust."',\r\n\t\tkodebarang='".$kdbarang."',nokendaraan='".$kdKapal."',trpcode='".$transp."',beratbersih='".$berat."'\r\n\t\twhere notransaksi='".$notran."' and millcode='".$kodeorg."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\r\n\t<div id=container>\r\n\t\t\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['notransaksi']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['pabrik']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['NoKontrak']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['nodo']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['nmcust']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['kodebarang']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['kodekapal']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['transporter']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['beratnormal']." (Kg)</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $maxdisplay = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.pabrik_timbangan where '.$where.' ';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.'.pabrik_timbangan where '.$where.'   limit '.$offset.','.$limit.'';
        $n = mysql_query($i);
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['notransaksi'].'</td>';
            echo '<td align=left>'.tanggalnormal($d['tanggal']).'</td>';
            echo '<td align=left>'.$d['millcode'].'</td>';
            echo '<td align=left>'.$d['nokontrak'].'</td>';
            echo '<td align=left>'.$d['nodo'].'</td>';
            echo '<td align=left>'.$nmCust[$d['kodecustomer']].'</td>';
            echo '<td align=left>'.$nmBarang[$d['kodebarang']].'</td>';
            echo '<td align=left>'.$d['nokendaraan'].'</td>';
            echo '<td align=left>'.$nmTranp[$d['trpcode']].'</td>';
            echo '<td align=right>'.number_format($d['beratbersih']).'</td>';
            echo "<td align=center>\r\n\t\t\t\t\r\n\t\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['notransaksi']."');\"></td>";
            echo '</tr>';
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=43 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".pabrik_timbangan where notransaksi='".$notran."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        $i = 'update '.$dbname.".pabrik_kelengkapanloses set nilai='".$inpEdit."',`updateby`='".$_SESSION['standard']['userid']."' where kodeorg='".$kodeorgEdit."' and tanggal='".$tglEdit."' and id='".$idEdit."'";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>
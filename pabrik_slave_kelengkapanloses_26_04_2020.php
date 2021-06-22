<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$kodeorg = $_POST['kodeorg'];
$produk = $_POST['produk'];
$id = $_POST['id'];
$inp = $_POST['inp'];
('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$tgl = tanggalsystem($_POST['tgl']);
$tglsch = tanggalsystem($_POST['tglsch']);
$kodeorgEdit = $_POST['kodeorgEdit'];
$tglEdit = tanggalsystem($_POST['tglEdit']);
$idEdit = $_POST['idEdit'];
$inpEdit = $_POST['inpEdit'];
$kodeorgLap = $_POST['kodeorgLap'];
$produkLap = $_POST['produkLap'];
$tglLap = tanggalsystem($_POST['tglLap']);
if ('excel' === $method) {
    $kodeorgLap = $_GET['kodeorgLap'];
    $tglLap = tanggalsystem($_GET['tglLap']);
    $produkLap = $_GET['produkLap'];
}

$arrProduk = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,produk');
$arrItem = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,namaitem');
$arrSatuan = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,satuan');
$arrStandard = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,standard');
echo "\r\n";
$stream = ''.$_SESSION['lang']['kelengkapanloses'].'<br />Tanggal : '.tanggalnormal($tglLap).'';
if ('excel' === $method) {
    $border = "border='1'";
    $bgcolor = 'bgcolor=#CCCCCC';
} else {
    $border = "border='0'";
    $bgcolor = 'bgcolor=#FFFFFF ';
}

$stream .= "<table cellspacing='1' class='sortable'  ".$border.'>';
$stream .= "<thead class=rowheader>\r\n\t  <tr>\r\n\t\t<td align=center ".$bgcolor.'>'.$_SESSION['lang']['nourut']."</td>\r\n\t\t<td align=center ".$bgcolor.'>'.$_SESSION['lang']['kodeorg']."</td>\r\n\t\t<td align=center ".$bgcolor.'>'.$_SESSION['lang']['tanggal']."</td>\r\n\t\t<td align=center ".$bgcolor.'>'.$_SESSION['lang']['produk']."</td>\r\n\t\t<td align=center ".$bgcolor.'>'.$_SESSION['lang']['namabarang']."</td>\r\n\t\t<td align=center ".$bgcolor.'>'.$_SESSION['lang']['satuan']."</td>\r\n\t\t<td align=center ".$bgcolor.'>'.$_SESSION['lang']['standard']."</td>\r\n\t\t<td align=center ".$bgcolor.'>'.$_SESSION['lang']['realisasi']."</td>\r\n\t  </tr>\r\n</thead>\r\n<tbody>";
$i = 'select * from '.$dbname.".pabrik_kelengkapanloses where kodeorg='".$kodeorgLap."' and tanggal='".$tglLap."'\r\n\t\t\tand id in (select id from ".$dbname.".pabrik_5kelengkapanloses where produk='".$produkLap."')";
$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {
    ++$no;
    $stream .= '<tr '.$border.'  class=rowcontent>';
    $stream .= '<td align=center>'.$no.'</td>';
    $stream .= '<td align=left>'.$d['kodeorg'].'</td>';
    $stream .= '<td align=left>'.tanggalnormal($d['tanggal']).'</td>';
    $stream .= '<td align=right>'.$arrProduk[$d['id']].'</td>';
    $stream .= '<td align=left>'.$arrItem[$d['id']].'</td>';
    $stream .= '<td align=left>'.$arrSatuan[$d['id']].'</td>';
    $stream .= '<td align=left>'.$arrStandard[$d['id']].'</td>';
    $stream .= '<td align=right>'.$d['nilai'].'</td>';
    $stream .= '</tr>';
}
switch ($method) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
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
    case 'getForm':
        $i = 'select distinct namaitem,id from '.$dbname.".pabrik_5kelengkapanloses where kodeorg='".$kodeorg."' and produk='".$produk."'";
        $n = mysql_query($i);
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            $a .= "\r\n\t\t\t\t<tr id=row".$no.">\r\n\t\t\t\t\t<td><input type=hidden value='".$d['id']."'  id=id".$no." disabled onkeypress=\"return angka_doang(event);\"   class=myinputtextnumber style=\"width:50px;\"></td>\r\n\t\t\t\t\t<td>".$d['namaitem']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text id=inp".$no." onkeypress=\"return angka_doang(event);\"  value=0 class=myinputtextnumber style=\"width:50px;\"> %</td>\r\n\t\t\t\t</tr>";
        }
        echo $a;
        echo "\r\n\t\t\t<tr>\r\n\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t<button class=mybutton onclick=saveAll(".$no.")>Simpan</button>\r\n\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t<td>\r\n\t\t\t</tr>";
        echo "<input type=hidden id=jml onkeypress=\"return angka_doang(event);\"  value='".$no."' class=myinputtextnumber style=\"width:50px;\">";

        break;
    case 'savedata':
        $str = 'insert into '.$dbname.".pabrik_kelengkapanloses (`kodeorg`,`tanggal`,`id`,`nilai`,`updateby`)\r\n\t\tvalues ('".$kodeorg."','".$tgl."','".$id."','".$inp."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($str)) {
        } else {
            $str = 'update '.$dbname.".pabrik_kelengkapanloses set nilai='".$inp."',`updateby`='".$_SESSION['standard']['userid']."' where kodeorg='".$kodeorg."' and tanggal='".$tgl."' and id='".$id."'";
            if (mysql_query($str)) {
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        }

        break;
    case 'loadData':
        echo "\r\n\t<div id=container>\r\n\t\t\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['produk']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['standard']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['realisasi']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        if ('' !== $tglsch) {
            $tglsch = "and tanggal='".$tglsch."'";
        }

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
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".pabrik_kelengkapanloses where kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$tglsch.' ';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.".pabrik_kelengkapanloses where kodeorg='".$_SESSION['empl']['lokasitugas']."'  ".$tglsch.' order by tanggal desc limit '.$offset.','.$limit.'';
        $n = mysql_query($i);
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['kodeorg'].'</td>';
            echo '<td align=left>'.tanggalnormal($d['tanggal']).'</td>';
            echo '<td align=right>'.$arrProduk[$d['id']].'</td>';
            echo '<td align=left>'.$arrItem[$d['id']].'</td>';
            echo '<td align=left>'.$arrSatuan[$d['id']].'</td>';
            echo '<td align=left>'.$arrStandard[$d['id']].'</td>';
            echo '<td align=right>'.$d['nilai'].'</td>';
            echo '<td align=left>'.$optNmKar[$d['updateby']].'</td>';
            echo "<td align=left>\r\n\t\t\t\t<img src=images/application/application_edit.png class=resicon  caption='Edit' \r\n\t\t\t\t\tonclick=\"edit('".$d['kodeorg']."','".tanggalnormal($d['tanggal'])."',\r\n\t\t\t\t\t'".$arrProduk[$d['id']]."','".$arrItem[$d['id']]."','".$d['nilai']."','".$d['id']."');\">\r\n\t\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kodeorg']."','".tanggalnormal($d['tanggal'])."','".$d['id']."');\"></td>";
            echo '</tr>';
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=43 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".pabrik_kelengkapanloses where kodeorg='".$kodeorg."' and tanggal='".$tgl."' and id='".$id."'";
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
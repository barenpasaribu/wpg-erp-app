<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$subunit = $_POST['subunit'];
$tipeanalisa = $_POST['tipeanalisa'];
$tglposting = $_POST['tanggalPosting'];
$id = $_POST['id'];
$inp = $_POST['inp'];
// ('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
if (empty($_GET['method'])) {
    $method = $_POST['method'];
}else{
    $method = $_GET['method'];
}
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$tgl = tanggalsystem($_POST['tgl']);
$tglsch = tanggalsystem($_POST['tglsch']);
$kodeorgEdit = $_POST['kodeorgEdit'];
$tglEdit = tanggalsystem($_POST['tglEdit']);
$idEdit = $_POST['idEdit'];
$inpEdit = $_POST['inpEdit'];
$kodeorgLap = $_POST['kodeorgLap'];
$tipeanalisasch = $_POST['tipeanalisasch'];
$tglLap = tanggalsystem($_POST['tglLap']);
$sumber = $_POST['sumber'];
$lab = $_POST['lab'];
$bulan = $_POST['bulan'];
$tahun = $_POST['tahun'];
$bulansch = $_POST['bulansch'];
$tahunsch = $_POST['tahunsch'];
$bulanlap = $_POST['bulanLap'];
$tahunlap = $_POST['tahunLap'];
$notransaksi = $_POST['notransaksi'];
$tipeanalisaR = tanggalsystem($_POST['tipeanalisaR']);
if ('excel' === $method) {
    $bulanlap = $_GET['bulanLap'];
    $tahunlap = $_GET['tahunLap'];
}

$arrProduk = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,produk');
$arrItem = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,namaitem');
$arrSatuan = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,satuan');
$arrStandard = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,standard');
echo "\r\n";
 $iht = 'select * from '.$dbname.".pabrik_analisa_externalht  where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode_bulan='".$bulanlap."' and periode_tahun='".$tahunlap."'  order by tanggal desc";
 $nht = mysql_query($iht);
while ($dht = mysql_fetch_assoc($nht)) {
$stream = 'Laporan Analisa<br />Periode : '.$bulanlap.'-'.$tahunlap.'<br/>';
$stream .= 'Sumber: '.$dht['sumber'].'<br />';
$stream .= 'Laboratorium : '.$dht['lab'];
}
if ('excel' === $method) {
    $border = "border='1'";
    $bgcolor = 'bgcolor=#CCCCCC';
} else {
    $border = "border='0'";
    $bgcolor = 'bgcolor=#FFFFFF ';
}

$stream .= "<table cellspacing='1' class='sortable'  ".$border.'>';
$stream .= "<thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t <td align=center>Parameter</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>Realisasi</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t \r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";

 $i = 'select a.*,c.subunit as subunit,d.parameter as parameter,date(a.tanggal) as tgl,b.nilai as nilai,b.id as iddt from '.$dbname.".pabrik_analisa_externalht a,pabrik_analisa_externaldt b,pabrik_subunit_analisa c,pabrik_parameter_analisa d where a.notransaksi=b.notransaksi and b.subunitid=c.id and b.parameterid=d.id and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' and a.periode_bulan='".$bulanlap."' and a.periode_tahun='".$tahunlap."'  order by tanggal desc";

$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {
    ++$no;
  
               $stream .= '<tr class=rowcontent>';
             $stream .= '<td align=center>'.$no.'</td>';
            $stream .= '<td align=left>'.$d['parameter'].'</td>';
            $stream .= '<td align=left>'.$d['nilai'].'</td>';
            $stream .= '<td align=left>'.$optNmKar[$d['updateby']].'</td>';
            $stream .= "  <td align=left>";
    $stream .= '</tr>';
}
switch ($method) {
    case 'preview':
        print_r($stream);
        die();
        break;
    case 'excel':
        $stream .= 'Print Time : '.date('H:i:s, d/m/Y').'<br>By : '.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'Laporan_analisa'.$tglSkrg;
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
        $i = 'select a.* from '.$dbname.".pabrik_parameter_analisa a,pabrik_subunit_analisa b where a.subunitid=b.id and b.subunit like '%external%'";
        $n = mysql_query($i);
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            $a .= "\r\n\t\t\t\t<tr id=row".$no.">\r\n\t\t\t\t\t<td><input type=hidden value='".$d['id']."'  id=id".$no." disabled onkeypress=\"return angka_doang(event);\"   class=myinputtextnumber style=\"width:50px;\"></td>\r\n\t\t\t\t\t<td>".$d['parameter']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text id=inp".$no."   value=0 class=myinputtextnumber style=\"width:50px;\"> ".$d['satuan']." (standar : ".$d['standar'].") </td>\r\n\t\t\t\t</tr>";
        }
        echo $a;

        echo "\r\n\t\t\t<tr>\r\n\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t<button class=mybutton onclick=SaveAnalisaExternal(".$no.")>Simpan</button>\r\n\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t<td>\r\n\t\t\t</tr>";
        echo "<input type=hidden id=jml onkeypress=\"return angka_doang(event);\"  value='".$no."' class=myinputtextnumber style=\"width:50px;\">";

        break;
    case 'savedata':
        $str = 'insert into '.$dbname.".pabrik_analisa_externaldt (`notransaksi`,`subunitid`,`parameterid`,`nilai`)\r\n\t\tvalues ('".$notransaksi."','".$subunit."','".$id."','".$inp."')";
        if (mysql_query($str)) {
        } else {
         
                echo ' Gagal,'.addslashes(mysql_error($conn));
            
        }

        break;
     case 'savedataht':
        $str = 'insert into '.$dbname.".pabrik_analisa_externalht (`periode_bulan`,`periode_tahun`,`sumber`,`lab`,`notransaksi`,`updateby`,`kodeorg`)\r\n\t\tvalues ('".$bulan."','".$tahun."','".$sumber."','".$lab."','".$notransaksi ."','".$_SESSION['standard']['userid']."','".$_SESSION['empl']['lokasitugas']."')";
        if (mysql_query($str)) {
        } else {
         
                echo ' Gagal,'.addslashes(mysql_error($conn));
            
        }

        break;
    case 'savedataedit':
            $str = 'update '.$dbname.".pabrik_kelengkapanloses set nilai='".$inp."',`updateby`='".$_SESSION['standard']['userid']."' where kodeorg='".$kodeorg."' and tanggal='".$tgl."' and id='".$id."'";
            if (mysql_query($str)) {
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        

        break;
    case 'loadData':
        echo "\r\n\t<div id=container>\r\n\t\t\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['nourut']."</td><td align=center>No. Transaksi</td><td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>Sumber</td><td align=center>Laboratorium</td>\r\n\t\t\t\t <td align=center>Parameter</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>Realisasi</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        if ('0' !== $bulansch) {
            $bulanschh = " and a.periode_bulan='".$bulansch."'";
        }else{
            $bulanschh="";
        }
        if ('0' !== $tahunsch) {
            $tahunschh = " and periode_tahun='".$tahunsch."'";
        }else{
            $tahunschh="";
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
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".pabrik_analisa_externalht a,pabrik_analisa_externaldt b,pabrik_subunit_analisa c,pabrik_parameter_analisa d where a.notransaksi=b.notransaksi and b.subunitid=c.id and b.parameterid=d.id and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$bulanschh." ".$tahunschh.' ';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select a.*,c.subunit as subunit,d.parameter as parameter,date(a.tanggal) as tgl,b.nilai as nilai,b.id as iddt from '.$dbname.".pabrik_analisa_externalht a,pabrik_analisa_externaldt b,pabrik_subunit_analisa c,pabrik_parameter_analisa d where a.notransaksi=b.notransaksi and b.subunitid=c.id and b.parameterid=d.id and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$bulanschh." ".$tahunschh.'  order by tanggal desc limit '.$offset.','.$limit.'';
       
        $n = mysql_query($i);
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            if ($d['tipe']=="1"){
                $tipeanalisa = "Analisa Air";
            }else{
                $tipeanalisa = "Analisa Limbah";
            }
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['notransaksi'].'</td>';
            echo '<td align=left>'.$d['kodeorg'].'</td>';
            echo '<td align=left>'.tanggalnormal($d['tgl']).'</td>';
            echo '<td align=left>'.$d['sumber'].'</td>';
            echo '<td align=right>'.$d['lab'].'</td>';
            echo '<td align=left>'.$d['parameter'].'</td>';
            echo '<td align=left>'.$d['nilai'].'</td>';
            echo '<td align=left>'.$optNmKar[$d['updateby']].'</td>';
            echo "  <td align=left>";
            
            if($d['posting'] == 1){
                echo "  <img src=images/skyblue/posted.png class=resicon caption='Posted'>";
            }else{
                echo "  <img src=images/application/application_edit.png class=resicon  caption='Edit' 
                    onclick=\"editAnalisaExternal('".$d['notransaksi']."','".$d['sumber']."',
                    '".$d['lab']."','".$d['parameter']."','".$d['nilai']."','".$d['iddt']."','".$d['periode_bulan']."','".$d['periode_tahun']."');\">
                    <img src=images/application/application_delete.png class=resicon 
                    caption='Delete' onclick=\"delAnalisaExternal('".$d['notransaksi']."');\">";
                echo "  <img src=images/skyblue/posting.png class=resicon caption='Posting' 
                    onclick=\"postingAnalisaExternal('".$d['notransaksi']."');\">";
            }
            
            
            echo "</td>";
            echo '</tr>';
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=43 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".pabrik_analisa_externalht where  notransaksi='".$notransaksi."'";
        $i2 = 'delete from '.$dbname.".pabrik_analisa_externaldt where  notransaksi='".$notransaksi."'";
        if (mysql_query($i)) {
            if (mysql_query($i2)) {
                echo 'berhasil';
            }else{
                echo 'gagal';
            }
            
        } else {
            echo 'gagal';
        }

        break;
    case 'posting':
        $i = 'update '.$dbname.".pabrik_analisa_externalht set posting=1 where notransaksi='".$notransaksi."'";
        echo $i;
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'quickPosting':
        $i = 'update '.$dbname.".pabrik_analisa set posting=1 where date(tanggal)='".$tglposting."' and updateby='".$_SESSION['standard']['userid']."'";

        if (mysql_query($i)) {
            echo "Berhasil";
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        $i = 'update '.$dbname.".pabrik_analisa_externaldt set nilai='".$inpEdit."' where  id='".$idEdit."'";
        echo $i;
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>
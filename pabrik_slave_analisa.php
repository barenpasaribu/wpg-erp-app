<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$subunit = $_POST['subunit'];
$tipeanalisa = $_POST['tipeanalisa'];
$tanggal =tanggaldgnbar($_POST['tanggal']);
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
$tipeanalisaR = tanggalsystem($_POST['tipeanalisaR']);
if ('excel' === $method) {
    $kodeorgLap = $_GET['kodeorgLap'];
    $tglLap = tanggalsystem($_GET['tglLap']);
    $tipeanalisaR = tanggalsystem($_GET['tipeanalisaR']);
    $produkLap = $_GET['produkLap'];
}

$arrProduk = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,produk');
$arrItem = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,namaitem');
$arrSatuan = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,satuan');
$arrStandard = makeOption($dbname, 'pabrik_5kelengkapanloses', 'id,standard');
echo "\r\n";
$stream = 'Laporan Analisa<br />Tanggal : '.tanggalnormal($tglLap).'';
if ('excel' === $method) {
    $border = "border='1'";
    $bgcolor = 'bgcolor=#CCCCCC';
} else {
    $border = "border='0'";
    $bgcolor = 'bgcolor=#FFFFFF ';
}

$stream .= "<table cellspacing='1' class='sortable'  ".$border.'>';
$stream .= "<thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t \t <td align=center>Tipe</td><td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>Sub Unit</td>\r\n\t\t\t\t <td align=center>Parameter</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>Realisasi</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n<tbody>";

 $i = 'select a.*,b.subunit as subunit,c.parameter as parameter,date(a.tanggal) as tgl from '.$dbname.".pabrik_analisa a,pabrik_subunit_analisa b,pabrik_parameter_analisa c where a.subunitid=b.id and a.parameterid=c.id and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' and date(a.tanggal)='".$tglLap."' and tipe ='".$tipeanalisaR."' and posting=1   order by tgl,subunitid desc ";

$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {
    ++$no;
     if ($d['tipe']=="1"){
                $tipeanalisa = "Analisa Air";
            }else{
                $tipeanalisa = "Analisa Limbah";
            }
         
    $stream .= '<tr '.$border.'  class=rowcontent>';
    $stream .= '<td align=center>'.$no.'</td>';
    $stream .= '<td align=left>'.$tipeanalisa.'</td>';
    $stream .= '<td align=left>'.$d['kodeorg'].'</td>';
    $stream .= '<td align=left>'.tanggalnormal($d['tgl']).'</td>';
    $stream .= '<td align=right>'.$d['subunit'].'</td>';
    $stream .= '<td align=left>'.$d['parameter'].'</td>';
    $stream .= '<td align=left>'.$d['nilai'].'</td>';
    $stream .= '<td align=left>'.$optNmKar[$d['updateby']].'</td>';
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
        $i = 'select * from '.$dbname.".pabrik_parameter_analisa where subunitid='".$subunit."'";
        $n = mysql_query($i);
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            $a .= "\r\n\t\t\t\t<tr id=row".$no.">\r\n\t\t\t\t\t<td><input type=hidden value='".$d['id']."'  id=id".$no." disabled onkeypress=\"return angka_doang(event);\"   class=myinputtextnumber style=\"width:50px;\"></td>\r\n\t\t\t\t\t<td>".$d['parameter']."</td> \r\n\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t<td><input type=text id=inp".$no."   value=0 class=myinputtextnumber style=\"width:50px;\"> ".$d['satuan']." (standar : ".$d['standar'].") </td>\r\n\t\t\t\t</tr>";
        }
        echo $a;

        echo "\r\n\t\t\t<tr>\r\n\t\t\t\t<td colspan=3>\r\n\t\t\t\t\t<button class=mybutton onclick=saveAllAnalisa(".$no.")>Simpan</button>\r\n\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t<td>\r\n\t\t\t</tr>";
        echo "<input type=hidden id=jml onkeypress=\"return angka_doang(event);\"  value='".$no."' class=myinputtextnumber style=\"width:50px;\">";

        break;
    case 'savedata':
        $str = 'insert into '.$dbname.".pabrik_analisa (`tipe`,`subunitid`,`parameterid`,`nilai`,`updateby`,`kodeorg`,`tanggal`)\r\n\t\tvalues ('".$tipeanalisa."','".$subunit."','".$id."','".$inp."','".$_SESSION['standard']['userid']."','".$_SESSION['empl']['lokasitugas']."','".$tanggal."')";
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
        echo "\r\n\t<div id=container>\r\n\t\t\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t \t <td align=center>Tipe</td><td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>Sub Unit</td>\r\n\t\t\t\t <td align=center>Parameter</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>Realisasi</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t \r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        if ('' !== $tglsch) {
            $tglsch = " and date(a.tanggal)='".$tglsch."'";
        }
        if ('0' !== $tipeanalisasch) {
            $tipeanalisaschh = " and tipe='".$tipeanalisasch."'";
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
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".pabrik_analisa a,pabrik_subunit_analisa b,pabrik_parameter_analisa c where a.subunitid=b.id and a.parameterid=c.id and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' ".$tglsch." ".$tipeanalisasch.' ';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select a.*,b.subunit as subunit,c.parameter as parameter,date(a.tanggal) as tgl from '.$dbname.".pabrik_analisa a,pabrik_subunit_analisa b,pabrik_parameter_analisa c where a.subunitid=b.id and a.parameterid=c.id and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'  ".$tglsch." ".$tipeanalisaschh.' order by tanggal desc limit '.$offset.','.$limit.'';
       
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
            echo '<td align=left>'.$tipeanalisa.'</td>';
            echo '<td align=left>'.$d['kodeorg'].'</td>';
            echo '<td align=left>'.tanggalnormal($d['tgl']).'</td>';
            echo '<td align=right>'.$d['subunit'].'</td>';
            echo '<td align=left>'.$d['parameter'].'</td>';
            echo '<td align=left>'.$d['nilai'].'</td>';
            echo '<td align=left>'.$optNmKar[$d['updateby']].'</td>';
            echo "  <td align=left>";
            
            if($d['posting'] == 1){
                echo "  <img src=images/skyblue/posted.png class=resicon caption='Posted'>";
            }else{
                echo "  <img src=images/application/application_edit.png class=resicon  caption='Edit' 
                    onclick=\"editAnalisa('".$d['kodeorg']."','".tanggalnormal($d['tgl'])."',
                    '".$d['subunitid']."','".$d['parameter']."','".$d['nilai']."','".$d['id']."','".$d['tipe']."');\">
                    <img src=images/application/application_delete.png class=resicon 
                    caption='Delete' onclick=\"delAnalisa('".$d['id']."');\">";
                echo "  <img src=images/skyblue/posting.png class=resicon caption='Posting' 
                    onclick=\"postingAnalisa('".$d['id']."');\">";
            }
            
            
            echo "</td>";
            echo '</tr>';
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=43 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".pabrik_analisa where  id='".$id."'";
        if (mysql_query($i)) {
            echo 'berhasil';
        } else {
            echo 'gagal';
        }

        break;
    case 'posting':
        $i = 'update '.$dbname.".pabrik_analisa set posting=1 where id='".$id."'";
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
        $i = 'update '.$dbname.".pabrik_analisa set nilai='".$inpEdit."',`updateby`='".$_SESSION['standard']['userid']."' where  id='".$idEdit."'";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>
<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
$mulaijam = $_POST['jm1'].':'.$_POST['mn1'].':00';
$sampaijam = $_POST['jm2'].':'.$_POST['mn2'].':00';
$tgl = tanggalsystem($_POST['tgl']);
$kdDiv = $_POST['kdDiv'];
$kdAfd = $_POST['kdAfd'];
$kdBlok = $_POST['kdBlok'];
$userId = $_POST['userId'];
$tenagakerja = $_POST['tenagakerja'];
$alat = $_POST['alat'];
$bahan1 = $_POST['bahan1'];
$bahan2 = $_POST['bahan2'];
$bahan3 = $_POST['bahan3'];
$dosis1 = $_POST['dosis1'];
$dosis2 = $_POST['dosis2'];
$dosis3 = $_POST['dosis3'];
$pokok = $_POST['pokok'];
$bensin = $_POST['bensin'];
$oli = $_POST['oli'];
$catatan = $_POST['catatan'];
$pengawas = $_POST['pengawas'];
$asisten = $_POST['asisten'];
$mengetahui = $_POST['mengetahui'];
$perSch = $_POST['perSch'];
$kdDivSch = $_POST['kdDivSch'];
$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$nmCust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nmTranp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
echo "\r\n";
switch ($method) {
    case 'getAfd':
        $optAfd = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $i = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi  where induk='".$kdDiv."' and tipe='AFDELING'";
        $n = mysql_query($i) ;
        while ($d = mysql_fetch_assoc($n)) {
            $optAfd .= "<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi'].'</option>';
        }
        echo $optAfd;

        break;
    case 'getBlok':
        $optBlok = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $i = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi  where induk='".$kdAfd."' and tipe='BLOK'";
        $n = mysql_query($i) ;
        while ($d = mysql_fetch_assoc($n)) {
            $optBlok .= "<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi'].'</option>';
        }
        echo $optBlok;

        break;
    case 'saveData':
        $i = 'INSERT INTO `'.$dbname."`.`kebun_qc_hama` (`tanggal`, `blok`, `tenagakerja`, `mulaijam`, `sampaijam`, `alat`, \r\n\t\t\t`bahan1`, `bahan2`, `bahan3`, `dosis1`, `dosis2`, `dosis3`, \r\n\t\t\t`pokok`, `bensin`, `oli`, `catatan`, `pengawas`, `asisten`, `mengetahui`, `updateby`) \r\n\t\t\t\r\n\t\tvalues ('".$tgl."','".$kdBlok."','".$tenagakerja."','".$mulaijam."','".$sampaijam."','".$alat."',\r\n\t\t\t\t'".$bahan1."','".$bahan2."','".$bahan3."','".$dosis1."','".$dosis2."','".$dosis3."',\r\n\t\t\t\t'".$pokok."','".$bensin."','".$oli."','".$catatan."','".$pengawas."','".$asisten."','".$mengetahui."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'getData':
        $i = 'select luasareaproduktif,jumlahpokok from '.$dbname.".setup_blok where kodeorg='".$kdBlok."'";
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        echo $d['luasareaproduktif'].'###'.$d['jumlahpokok'];

        break;
    case 'getKar':
        $optMandor = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $j = 'select karyawanid,namakaryawan,nik from '.$dbname.'.datakaryawan  where lokasitugas in (select kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."' and kodeunit like '%E%') and bagian='ESTATE'";
        $k = mysql_query($j) ;
        while ($l = mysql_fetch_assoc($k)) {
            $optMandor .= "<option value='".$l['karyawanid']."'>".$l['nik'].' - '.$l['namakaryawan'].'</option>';
        }
        $optAstn = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $d = 'select karyawanid,namakaryawan,nik from '.$dbname.".datakaryawan  where lokasitugas='".$kdDiv."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  alias like '%Askep Estate%')";
        $e = mysql_query($d) ;
        while ($f = mysql_fetch_assoc($e)) {
            $optAstn .= "<option value='".$f['karyawanid']."'>".$f['nik'].' - '.$f['namakaryawan'].'</option>';
        }
        $optKadiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $g = 'select karyawanid,namakaryawan,nik from '.$dbname.".datakaryawan  where lokasitugas='".$kdDiv."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  alias like '%Manager Estate%')";
        $h = mysql_query($g) ;
        while ($i = mysql_fetch_assoc($h)) {
            $optKadiv .= "<option value='".$i['karyawanid']."'>".$i['nik'].' - '.$i['namakaryawan'].'</option>';
        }
        echo $optMandor.'###'.$optAstn.'###'.$optKadiv;

        break;
    case 'loadData':
        if ('' !== $kdDivSch) {
            $kdDivLoad = "blok like '%".$kdDivSch."%'";
        } else {
            $kdDivLoad = "blok!='' ";
        }

        if ('' !== $perSch) {
            $perLoad = "and tanggal like '%".$perSch."%'";
        } else {
            $perLoad = '';
        }

        echo "\r\n\t\t\r\n\t\t\t<table class=sortable cellspacing=1 border=0>\r\n\t\t\t <thead>\r\n\t\t\t\t <tr class=rowheader>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['divisi']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['afdeling']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['blok']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['pengawasan']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t\t </tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody>";
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $maxdisplay = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_qc_hama where '.$kdDivLoad.'  '.$perLoad.'  ';
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.'.kebun_qc_hama where '.$kdDivLoad.'  '.$perLoad.'  limit '.$offset.','.$limit.'';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.tanggalnormal($d['tanggal']).'</td>';
            echo '<td align=left>'.substr($d['blok'], 0, 4).'</td>';
            echo '<td align=left>'.substr($d['blok'], 0, 6).'</td>';
            echo '<td align=left>'.$d['blok'].'</td>';
            echo '<td align=left>'.$nmKar[$d['pengawas']].'</td>';
            echo '<td align=left>'.$nmKar[$d['updateby']].'</td>';
            echo "<td align=center>\r\n\t\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".tanggalnormal($d['tanggal'])."','".$d['blok']."','".$d['updateby']."');\">\r\n\t\t\t\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_qc_hama','".$d['tanggal'].','.$d['blok']."','','kebun_qc_hamaUlat_pdf',event)\">\r\n\t\t\t\t\t\t</td>";
            echo '</tr>';
        }
        echo "\r\n\t\t\t<tr class=rowheader><td colspan=43 align=center>\r\n\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t</td>\r\n\t\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        if ($_SESSION['standard']['userid'] === $userId) {
            $i = 'delete from '.$dbname.".kebun_qc_hama where tanggal='".$tgl."' and blok='".$kdBlok."'";
            if (mysql_query($i)) {
                echo '';
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }

            break;
        }

        exit("Error:You Can't Delete");
    case 'update':
        $i = 'update '.$dbname.".pabrik_kelengkapanloses set nilai='".$inpEdit."',`updateby`='".$_SESSION['standard']['userid']."' where kodeorg='".$kodeorgEdit."' and tanggal='".$tglEdit."' and id='".$idEdit."'";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>
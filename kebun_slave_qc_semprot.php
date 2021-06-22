<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
$tgl = tanggalsystem($_POST['tgl']);
$kdDiv = $_POST['kdDiv'];
$kdAfd = $_POST['kdAfd'];
$kdBlok = $_POST['kdBlok'];
$kdKeg = $_POST['kdKeg'];
$dosis = $_POST['dosis'];
$jenisgulma = $_POST['jenisgulma'];
$kondisigulma = $_POST['kondisigulma'];
$dosismaterial1 = $_POST['dosismaterial1'];
$dosismaterial2 = $_POST['dosismaterial2'];
$dosismaterial3 = $_POST['dosismaterial3'];
$dosisjumlah1 = $_POST['dosisjumlah1'];
$dosisjumlah2 = $_POST['dosisjumlah2'];
$dosisjumlah3 = $_POST['dosisjumlah3'];
$materialdiambil1 = $_POST['materialdiambil1'];
$materialdiambil2 = $_POST['materialdiambil2'];
$materialdiambil3 = $_POST['materialdiambil3'];
$jumlahdiambil1 = $_POST['jumlahdiambil1'];
$jumlahdiambil2 = $_POST['jumlahdiambil2'];
$jumlahdiambil3 = $_POST['jumlahdiambil3'];
$materialdipakai1 = $_POST['materialdipakai1'];
$materialdipakai2 = $_POST['materialdipakai2'];
$materialdipakai3 = $_POST['materialdipakai3'];
$jumlahdipakai1 = $_POST['jumlahdipakai1'];
$jumlahdipakai2 = $_POST['jumlahdipakai2'];
$jumlahdipakai3 = $_POST['jumlahdipakai3'];
$karyawan1 = $_POST['karyawan1'];
$karyawan2 = $_POST['karyawan2'];
$karyawan3 = $_POST['karyawan3'];
$karyawan4 = $_POST['karyawan4'];
$karyawan5 = $_POST['karyawan5'];
$karyawan6 = $_POST['karyawan6'];
$karyawan7 = $_POST['karyawan7'];
$karyawan8 = $_POST['karyawan8'];
$karyawan9 = $_POST['karyawan9'];
$karyawan10 = $_POST['karyawan10'];
$karyawan11 = $_POST['karyawan11'];
$karyawan12 = $_POST['karyawan12'];
$karyawan13 = $_POST['karyawan13'];
$karyawan14 = $_POST['karyawan14'];
$karyawan15 = $_POST['karyawan15'];
$hasilkaryawan1 = $_POST['hasilkaryawan1'];
$hasilkaryawan2 = $_POST['hasilkaryawan2'];
$hasilkaryawan3 = $_POST['hasilkaryawan3'];
$hasilkaryawan4 = $_POST['hasilkaryawan4'];
$hasilkaryawan5 = $_POST['hasilkaryawan5'];
$hasilkaryawan6 = $_POST['hasilkaryawan6'];
$hasilkaryawan7 = $_POST['hasilkaryawan7'];
$hasilkaryawan8 = $_POST['hasilkaryawan8'];
$hasilkaryawan9 = $_POST['hasilkaryawan9'];
$hasilkaryawan10 = $_POST['hasilkaryawan10'];
$hasilkaryawan11 = $_POST['hasilkaryawan11'];
$hasilkaryawan12 = $_POST['hasilkaryawan12'];
$hasilkaryawan13 = $_POST['hasilkaryawan13'];
$hasilkaryawan14 = $_POST['hasilkaryawan14'];
$hasilkaryawan15 = $_POST['hasilkaryawan15'];
$keterangan = $_POST['keterangan'];
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
    case 'getForm':
        $i = 'select * from '.$dbname.".kebun_qc_semprot where tanggal='".$tgl."' and blok='".$kdBlok."'";
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        echo substr($d['blok'], 0, 6).'###'.$d['blok'].'###'.$d['kodekegiatan'].'###'.$d['karyawan1'].'###'.$d['hasilkaryawan1'];

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
        $i = 'INSERT INTO `'.$dbname."`.`kebun_qc_semprot` (`tanggal`, `blok`, `kodekegiatan`, \r\n\t\t\t`dosismaterial1`, `dosisjumlah1`, `dosismaterial2`, `dosisjumlah2`, `dosismaterial3`, `dosisjumlah3`, \r\n\t\t\t`takaran`, `jenisgulma`, `kondisigulma`, \r\n\t\t\t`jumlahdiambil1`, `jumlahdiambil2`, `jumlahdiambil3`, \r\n\t\t\t`jumlahdipakai1`,  `jumlahdipakai2`, `jumlahdipakai3`, \r\n\t\t\t`karyawan1`, `karyawan2`, `karyawan3`, `karyawan4`, `karyawan5`, \r\n\t\t\t`karyawan6`, `karyawan7`, `karyawan8`, `karyawan9`, `karyawan10`, \r\n\t\t\t`karyawan11`, `karyawan12`, `karyawan13`, `karyawan14`, `karyawan15`, \r\n\t\t\t`hasilkaryawan1`, `hasilkaryawan2`, `hasilkaryawan3`, `hasilkaryawan4`, `hasilkaryawan5`, \r\n\t\t\t`hasilkaryawan6`, `hasilkaryawan7`, `hasilkaryawan8`, `hasilkaryawan9`, `hasilkaryawan10`, \r\n\t\t\t`hasilkaryawan11`, `hasilkaryawan12`, `hasilkaryawan13`, `hasilkaryawan14`, `hasilkaryawan15`, \r\n\t\t\t`keterangan`, `pengawas`, `asisten`, `mengetahui`, `updateby`) \r\n\t\t\t\r\n\t\tvalues ('".$tgl."','".$kdBlok."','".$kdKeg."',\r\n\t\t\t\t'".$dosismaterial1."','".$dosisjumlah1."','".$dosismaterial2."','".$dosisjumlah2."','".$dosismaterial3."','".$dosisjumlah3."',\r\n\t\t\t\t'".$takaran."','".$jenisgulma."','".$kondisigulma."',\r\n\t\t\t\t'".$jumlahdiambil1."','".$jumlahdiambil2."','".$jumlahdiambil3."',\r\n\t\t\t\t'".$jumlahdipakai1."','".$jumlahdipakai2."','".$jumlahdipakai3."',\r\n\t\t\t\t'".$karyawan1."','".$karyawan2."','".$karyawan3."','".$karyawan4."','".$karyawan5."',\r\n\t\t\t\t'".$karyawan6."','".$karyawan7."','".$karyawan8."','".$karyawan9."','".$karyawan10."',\r\n\t\t\t\t'".$karyawan11."','".$karyawan12."','".$karyawan13."','".$karyawan14."','".$karyawan15."',\r\n\t\t\t\t'".$hasilkaryawan1."','".$hasilkaryawan2."','".$hasilkaryawan3."','".$hasilkaryawan4."','".$hasilkaryawan5."',\r\n\t\t\t\t'".$hasilkaryawan6."','".$hasilkaryawan7."','".$hasilkaryawan8."','".$hasilkaryawan9."','".$hasilkaryawan10."',\r\n\t\t\t\t'".$hasilkaryawan11."','".$hasilkaryawan12."','".$hasilkaryawan13."','".$hasilkaryawan14."','".$hasilkaryawan15."',\r\n\t\t\t\t'".$keterangan."','".$pengawas."','".$asisten."','".$mengetahui."','".$_SESSION['standard']['userid']."')";
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
        $optKar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $a = 'select karyawanid,namakaryawan,nik from '.$dbname.".datakaryawan  where lokasitugas='".$kdDiv."' and tipekaryawan!='5'";
        $b = mysql_query($a) ;
        while ($c = mysql_fetch_assoc($b)) {
            $optKar .= "<option value='".$c['karyawanid']."'>".$c['nik'].' - '.$c['namakaryawan'].'</option>';
        }
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
        echo $optKar.'###'.$optMandor.'###'.$optAstn.'###'.$optKadiv;

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

        echo "\r\n\t\t\r\n\t\t\t<table class=sortable cellspacing=1 border=0>\r\n\t\t\t <thead>\r\n\t\t\t\t <tr class=rowheader>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['kodeorganisasi']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['blok']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['pengawasan']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t\t </tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody>";
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
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_qc_semprot where '.$kdDivLoad.'  '.$perLoad.'  ';
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.'.kebun_qc_semprot where '.$kdDivLoad.'  '.$perLoad.'  limit '.$offset.','.$limit.'';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.tanggalnormal($d['tanggal']).'</td>';
            echo '<td align=left>'.substr($d['blok'], 0, 4).'</td>';
            echo '<td align=left>'.$d['blok'].'</td>';
            echo '<td align=left>'.$nmKeg[$d['kodekegiatan']].'</td>';
            echo '<td align=left>'.$nmKar[$d['pengawas']].'</td>';
            echo '<td align=left>'.$nmKar[$d['updateby']].'</td>';
            echo "<td align=center>\r\n\t\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".tanggalnormal($d['tanggal'])."','".$d['blok']."');\">\t\t\r\n\t\t\t\t\t\t<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_qc_semprot','".$d['tanggal'].','.$d['blok']."','','kebun_qc_semprot_pdf',event)\">\r\n\t\t\t\t\t\t\r\n\t\t\t\t\r\n\t\t\t\t</td></tr>";
        }
        echo "\r\n\t\t\t<tr class=rowheader><td colspan=43 align=center>\r\n\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t</td>\r\n\t\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".kebun_qc_semprot where tanggal='".$tgl."' and blok='".$kdBlok."'";
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
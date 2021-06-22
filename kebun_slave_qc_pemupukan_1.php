<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
('' === $_POST['tanggal'] ? ($tanggal = tanggalsystem($_GET['tanggal'])) : ($tanggal = tanggalsystem($_POST['tanggal'])));
('' === $_POST['kodeblok'] ? ($kodeblok = $_GET['kodeblok']) : ($kodeblok = $_POST['kodeblok']));
$tanggalpanen = tanggalsystem($_POST['tanggalpanen']);
$kodedivisi = $_POST['kodedivisi'];
$kodeafdeling = $_POST['kodeafdeling'];
$namapengawas = $_POST['namapengawas'];
$jumlahpekerja = $_POST['jumlahpekerja'];
$dosis = $_POST['dosis'];
$teraplikasi = $_POST['teraplikasi'];
$kondisilahan = $_POST['kondisilahan'];
$jamMulai = $_POST['jamMulai'];
$mntMulai = $_POST['mntMulai'];
$jamSelesai = $_POST['jamSelesai'];
$mntSelesai = $_POST['mntSelesai'];
$darijam = $jamMulai.':'.$mntMulai;
$sampaijam = $jamSelesai.':'.$mntSelesai;
$comment = $_POST['comment'];
$pengawas = $_POST['pengawas'];
$asisten = $_POST['asisten'];
$mengetahui = $_POST['mengetahui'];
$nojalur = $_POST['nojalur'];
$pkkdipupuk = $_POST['pkkdipupuk'];
$pkktdkdipupuk = $_POST['pkktdkdipupuk'];
$apltdkstandar = $_POST['apltdkstandar'];
$keterangan = $_POST['keterangan'];
$perSch = $_POST['perSch'];
$kdKebunSch = $_POST['kdKebunSch'];
$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$nmCust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$nmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nmTranp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
echo "\r\n";
switch ($method) {
    case 'getAfdeling':
        $optAfd = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $i = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.".organisasi WHERE induk='".$kodedivisi."' AND tipe='AFDELING'";
        $n = mysql_query($i) ;
        while ($d = mysql_fetch_assoc($n)) {
            $optAfd .= "<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi'].'</option>';
        }
        echo $optAfd;

        break;
    case 'getBlok':
        $optBlok = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $i = 'SELECT kodeorganisasi,namaorganisasi FROM '.$dbname.".organisasi WHERE induk='".$kodeafdeling."' AND tipe='BLOK'";
        $n = mysql_query($i) ;
        while ($d = mysql_fetch_assoc($n)) {
            $optBlok .= "<option value='".$d['kodeorganisasi']."'>".$d['namaorganisasi'].'</option>';
        }
        echo $optBlok;

        break;
    case 'saveHeader':
        $i = 'INSERT INTO '.$dbname.".`kebun_qc_pemupukanht`(`kodeblok`,`tanggal`,`pengawas`,`darijam`,`sampaijam`,\r\n                    `jumlahhk`,`dosis`,`teraplikasi`,`kondisilahan`,`idqc`,`divisi`,`mengetahui`,`comment`)\r\n\t\t\t\r\n\t\tvalues ('".$kodeblok."','".$tanggal."','".$namapengawas."','".$darijam."','".$sampaijam."','".$jumlahpekerja."',\r\n                        '".$dosis."','".$teraplikasi."','".$kondisilahan."','".$asisten."','".$kodedivisi."','".$mengetahui."','".$comment."')";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insertDetail':
        $i = 'INSERT INTO '.$dbname.".`kebun_qc_pemupukandt` \r\n                (`tanggal`, `kodeblok`, `nojalur`, `pkkdipupuk`, `pkktdkdipupuk`, `apltdkstandar`, `keterangan`)\r\n\t\tvalues ('".$tanggal."','".$kodeblok."','".$nojalur."','".$pkkdipupuk."','".$pkktdkdipupuk."','".$apltdkstandar."','".$keterangan."')";
        echo 'err'.$i;
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'updateDetail':
        $i = 'UPDATE '.$dbname.".`kebun_qc_pemupukandt` SET `pkkdipupuk`='".$pkkdipupuk."', `pkktdkdipupuk`='".$pkktdkdipupuk."', \r\n                `apltdkstandar`='".$apltdkstandar."', `keterangan`='".$keterangan."' WHERE\r\n\t\t`tanggal`='".$tanggal."' AND `kodeblok`='".$kodeblok."' AND `nojalur`='".$nojalur."'";
        echo 'err'.$i;
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'getKar':
        $optKar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $d = 'select karyawanid,namakaryawan,nik from '.$dbname.".datakaryawan  where lokasitugas='".$kodedivisi."'";
        $e = mysql_query($d) ;
        while ($f = mysql_fetch_assoc($e)) {
            $optKar .= "<option value='".$f['karyawanid']."'>".$f['nik'].' - '.$f['namakaryawan'].'</option>';
        }
        $optKadiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $g = 'select karyawanid,namakaryawan,nik from '.$dbname.".datakaryawan  where lokasitugas='".$kodedivisi."' and kodejabatan in (\t\t\r\n\t\t\tselect kodejabatan from ".$dbname.".sdm_5jabatan where kodejabatan in ('5'))";
        $h = mysql_query($g) ;
        while ($i = mysql_fetch_assoc($h)) {
            $optKadiv .= "<option value='".$i['karyawanid']."'>".$i['nik'].' - '.$i['namakaryawan'].'</option>';
        }
        echo $optKar.'###'.$optKadiv;

        break;
    case 'loadDetail':
        echo "<fieldset><legend>Data Tersimpan</legend>\r\n\t\t\t<table class=sortable cellspacing=1 border=0>\r\n\t\t\t <thead>\r\n\t\t\t\t <tr class=rowheader>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['nourut'].' '.$_SESSION['lang']['jalur']."</td> \r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['pokok'].' '.$_SESSION['lang']['dipupuk']."</td>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['pokok'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['dipupuk']."</td> \r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['apl'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['standar']."</td> \r\n\t\t\t\t\t\r\n\t\t\t\t\t<td align=left>".$_SESSION['lang']['keterangan']."</td>\t\t\t\t\t \r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t\t\t\r\n\t\t\t\t </tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody></fieldset>";
        $no = 0;
        $a = 'SELECT * FROM '.$dbname.".kebun_qc_pemupukandt WHERE tanggal='".$tanggal."' AND kodeblok='".$kodeblok."' ";
        $b = mysql_query($a) ;
        while ($c = mysql_fetch_assoc($b)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n                            <td align=right>".$c['nojalur']."</td>\r\n                            <td align=right>".$c['pkkdipupuk']."</td>\r\n                            <td align=right>".$c['pkktdkdipupuk']."</td>\r\n                            <td align=right>".$c['apltdkstandar']."</td>\r\n                            <td align=left>".$c['keterangan']."</td>\r\n                            <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillFieldDetail('".$c['nojalur']."','".$c['pkkdipupuk']."','".$c['pkktdkdipupuk']."','".$c['apltdkstandar']."','".$c['keterangan']."');\" >\r\n                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".tanggalnormal($c['tanggal'])."','".$c['kodeblok']."','".$c['nojalur']."');\" ></td></tr>";
        }
        echo '</table>';

        break;
    case 'loadData':
        if ('' !== $kdKebunSch) {
            $kodedivisiLoad = "kodeblok like '%".$kdKebunSch."%'";
        } else {
            $kodedivisiLoad = "kodeblok!='' ";
        }

        if ('' !== $perSch) {
            $perLoad = "AND tanggal like '%".$perSch."%'";
        } else {
            $perLoad = '';
        }

        echo "\r\n\t\t\r\n\t\t\t<table class=sortable cellspacing=1 border=0>\r\n\t\t\t <thead>\r\n\t\t\t\t <tr class=rowheader>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['divisi']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['afdeling']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['blok']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['pengawas']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t\t </tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody>";
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
        $ql2 = 'SELECT count(*) as jmlhrow FROM '.$dbname.'.kebun_qc_pemupukanht WHERE '.$kodedivisiLoad.'  '.$perLoad.'  ';
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'SELECT * FROM '.$dbname.'.kebun_qc_pemupukanht WHERE '.$kodedivisiLoad.'  '.$perLoad.'  limit '.$offset.','.$limit.'';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            $arr = '##'.$d['kodeblok'].'##'.$d['tanggal'].'';
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.tanggalnormal($d['tanggal']).'</td>';
            echo '<td align=left>'.substr($d['kodeblok'], 0, 4).'</td>';
            echo '<td align=left>'.substr($d['kodeblok'], 0, 6).'</td>';
            echo '<td align=left>'.$d['kodeblok'].'</td>';
            echo '<td align=left>'.$nmKar[$d['pengawas']].'</td>';
            echo "<td align=center>\r\n\t\t\t\t\t\t<img src=images/application/application_delete.png class=resicon caption='Delete' onclick=\"del('".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."');\">\t\t\r\n\t\t\t\t\t\t<img onclick=datakeExcel(event,'".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."') src=images/excel.jpg class=resicon title='MS.Excel'>\r\n                                                <img onclick=datakePdf(event,'".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."') src=images/pdf.jpg class=resicon title='PDF'></td>";
            echo '</tr>';
        }
        echo "\r\n\t\t\t<tr class=rowheader><td colspan=43 align=center>\r\n\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t</td>\r\n\t\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'DELETE FROM '.$dbname.".kebun_qc_pemupukanht WHERE tanggal='".$tanggal."' AND kodeblok='".$kodeblok."'";
        if (mysql_query($i)) {
            $n = 'DELETE FROM '.$dbname.".kebun_qc_pemupukandt WHERE tanggal='".$tanggal."' AND kodeblok='".$kodeblok."'";
            if (mysql_query($n)) {
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'deleteDetail':
        $i = 'DELETE FROM '.$dbname.".kebun_qc_pemupukandt WHERE tanggal='".$tanggal."' AND kodeblok='".$kodeblok."' AND nojalur='".$nojalur."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'printExcel':
        break;
    case 'printPdf':
        break;
}

?>
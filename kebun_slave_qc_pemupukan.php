<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
echo "\t\r\n\r\n";
$nmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
('' === $_POST['tanggal'] ? ($tanggal = tanggalsystem($_GET['tanggal'])) : ($tanggal = tanggalsystem($_POST['tanggal'])));
('' === $_POST['kodeblok'] ? ($kodeblok = $_GET['kodeblok']) : ($kodeblok = $_POST['kodeblok']));
$barang = $_POST['barang'];
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
        $i = 'INSERT INTO '.$dbname.".`kebun_qc_pemupukanht`(`kodeblok`,`tanggal`,`pengawas`,`darijam`,`sampaijam`,\r\n                    `jumlahhk`,`dosis`,`teraplikasi`,`kondisilahan`,`idqc`,`divisi`,`mengetahui`,`comment`,kodebarang)\r\n\t\t\t\r\n\t\tvalues ('".$kodeblok."','".$tanggal."','".$namapengawas."','".$darijam."','".$sampaijam."','".$jumlahpekerja."',\r\n                        '".$dosis."','".$teraplikasi."','".$kondisilahan."','".$asisten."','".$kodedivisi."','".$mengetahui."','".$comment."','".$barang."')";
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

        echo "\r\n\t\t\r\n\t\t\t<table class=sortable cellspacing=1 border=0>\r\n\t\t\t <thead>\r\n\t\t\t\t <tr class=rowheader>\r\n\t\t\t\t\t<td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t <td align=center>Divisi</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['afdeling']."</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['blok']."</td>\r\n\t\t\t\t\t <td align=center>Nama Pengawas</td>\r\n\t\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t\t </tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody>";
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
            echo "<td align=center>\r\n\t\t\t\t\t\t<img src=images/application/application_delete.png class=resicon title='Delete' caption='Delete' onclick=\"del('".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."');\">\t\t\r\n\t\t\t\t\t\t<img onclick=datakeExcel(event,'".tanggalnormal($d['tanggal'])."','".$d['kodeblok']."') src=images/excel.jpg class=resicon title='MS.Excel'>\r\n                                                <img onclick=\"previewQCPemupukanPDF('".$d['tanggal']."','".$d['kodeblok']."',event)\" class=\"resicon\" src=\"images/pdf.jpg\">";
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
        $i = 'select * from '.$dbname.".kebun_qc_pemupukanht where kodeblok='".$kodeblok."' and tanggal='".$tanggal."'  ";
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        $str_sql_topo = 'select*from '.$dbname.".setup_blok where kodeorg='".$kodeblok."'";
        $query_topo = mysql_query($str_sql_topo) ;
        while ($b = mysql_fetch_array($query_topo)) {
            $topo = $b['topografi'];
        }
        $str_sql = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$d['pengawas']."'";
        $coba1 = mysql_query($str_sql) ;
        $coba2 = mysql_fetch_assoc($coba1);
        $str_sql_idqc = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$d['idqc']."'";
        $query_sql_idqc = mysql_query($str_sql_idqc) ;
        $exec_query_idqc = mysql_fetch_assoc($query_sql_idqc);
        $str_sql2 = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$d['mengetahui']."'";
        $coba12 = mysql_query($str_sql) ;
        $coba22 = mysql_fetch_assoc($coba12);
        $ctkexcel = $_SESSION['org']['namaorganisasi'];
        $ctkexcel .= '<BR>QUALITY CONTROL';
        $ctkexcel .= "<table>\r\n                    <tr>\r\n                        <td colspan=9 align=center><b><u>CHECKLIST PEMUPUKAN</u></b></td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td></td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>Tanggal</td><td colspan=4>: ".tanggalnormal($d['tanggal'])."</td>\r\n                        <td colspan=2>Jam Kerja</td><td colspan=2>: ".$d['darijam'].' s.d '.$d['sampaijam']."</td>                        \r\n                    </tr>\r\n                    <tr>\r\n                        <td>Divisi</td><td colspan=4>: ".$d['divisi']."</td>\r\n                        <td colspan=2>Jumlah Pekerja</td><td colspan=2>: ".$d['jumlahhk']."</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>".$_SESSION['lang']['afdeling'].'</td><td colspan=4>: '.substr($d['kodeblok'], 0, 6)."</td>\r\n                        <td colspan=2>Pupuk & Dosis</td><td colspan=2>: ".$nmBrg[$d['kodebarang']].', Dosis : '.$d['dosis']."</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>".$_SESSION['lang']['blok'].'</td><td colspan=4>: '.$d['kodeblok']."</td>\r\n                        <td colspan=2>Total pupuk teraplikasi</td><td colspan=2>: ".$d['teraplikasi']." Sak</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>Topo</td><td colspan=4>: ".$topo."</td>\r\n                        <td colspan=2>Kondisi Lahan</td><td colspan=2>: ".$d['kondisilahan']."</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>Nama Pengawas</td><td colspan=2>: ".$coba2['namakaryawan']."</td>\r\n                    </tr>\r\n                    <tr></tr>                                \r\n                  </table>";
        $ctkexcel .= "<table class=sortable border=1 cellspacing=1>\r\n                    <thead>\r\n                        <tr>\r\n                            <td align=center valign=center rowspan=2 bgcolor=#CCCCCC>\r\n                                <p align=center style=margin-top:0; margin-bottom:0>No.Jalur\r\n                                <p align=center style=margin-top:0; margin-bottom:0>Diperiksa\r\n                            </td>\r\n\r\n                            <td align=center valign=center  rowspan=2 colspan=2 bgcolor=#CCCCCC>\r\n                                <p align=center style=margin-top:0; margin-bottom:0>Jumlah Pokok\r\n                                <p align=center style=margin-top:0; margin-bottom:0>dipupuk\r\n                            </td>\r\n                            <td align=center valign=top  rowspan=2 colspan=2 bgcolor=#CCCCCC>\r\n                             <p align=center style=margin-top:0; margin-bottom:0>Missed Out Palms\r\n                             <p align=center style=margin-top:0; margin-bottom:0>(Jumlah pokok tdk dipupuk)\r\n                            </td>\r\n\r\n                            <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>\r\n                             <p align=center style=margin-top:0; margin-bottom:0>Aplikasi\r\n                             <p align=center style=margin-top:0; margin-bottom:0>Tdk Standar\r\n                            </td>\r\n\r\n                            <td align=center valign=top  rowspan=2 colspan=3 bgcolor=#CCCCCC><p align=center>Keterangan</td>\r\n                        </tr>";
        $w = 'select * from '.$dbname.".kebun_qc_pemupukandt where kodeblok='".$kodeblok."' and tanggal='".$tanggal."' order by nojalur asc";
        $i = mysql_query($w) ;
        while ($b = mysql_fetch_assoc($i)) {
            $ctkexcel .= "<table class=sortable border=1 cellspacing=1>\r\n                        <tr class=rowcontent>\r\n                            <td align=center>".$b['nojalur']."</td>\r\n                            <td align=center colspan=2>".$b['pkkdipupuk']."</td>\r\n                            <td align=center colspan=2>".$b['pkktdkdipupuk']."</td>\r\n                            <td align=center>".$b['apltdkstandar']."</td>\r\n                            <td align=center colspan=3>".$b['keterangan']."</td>\r\n                        </tr>";
            $totjmlpkkdipupuk += $b['pkkdipupuk'];
            $totjmlpkktdkdipupuk += $b['pkktdkdipupuk'];
            $totapltdkstandar += $b['apltdkstandar'];
        }
        $totket = round($totjmlpkktdkdipupuk / ($totjmlpkkdipupuk + $totjmlpkktdkdipupuk) * 100, 0);
        $ctkexcel .= "<tr>\r\n                    <td align=center>".$_SESSION['lang']['total']." :</td>\r\n                    <td align=center colspan=2>".$totjmlpkkdipupuk."</td>\r\n                    <td align=center colspan=2>".$totjmlpkktdkdipupuk."</td>\r\n                    <td align=center>".$totapltdkstandar."</td>\r\n\r\n                    <td align=center colspan=3>".$totket."%</td>\r\n                  </tr>\r\n                  </table>";
        $ctkexcel .= "<table>\r\n                    <tr></tr>\r\n                    <tr>\r\n                        <td valign=top rowspan=5>Comment :</td><td align=justify valign=top rowspan=5 colspan=8>".$d['comment']."</td>\r\n                    </tr>\r\n                  </table>\r\n                  \r\n                  <table>\r\n                    <tr></tr>\r\n                    <tr>\r\n                        <td colspan=4>Yang melakukan pemeriksaan</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td align=left colspan=3>Quality Control</td>\r\n                        <td align=left colspan=3>Divisi</td>\r\n                        <td align=left colspan=3>Mengetahui</td>\r\n                    </tr>\r\n                    <tr></tr>\r\n                    <tr></tr>\r\n                    <tr></tr>\r\n                    <tr></tr>\r\n                        <td align=left colspan=3><u>".$exec_query_idqc['namakaryawan']."</u></td>\r\n                        <td align=left colspan=3>________________</td>\r\n                        <td align=left colspan=3><u>".$coba22['namakaryawan']."</u></td>\r\n                    </tr>                        \r\n                    <tr></tr>\r\n                    <tr>\r\n                        <td><u>Distribusi :</u></td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>1. GM Operational</td>\r\n                    </tr>\r\n                    <tr>\r\n                        <td>2. Ka.Divisi</td>\r\n                    </tr>\r\n                  </table>";
        $nop_ = 'QC_Checklist_Pemupukan_'.tanggalnormal($d['tanggal']);
        if (0 < strlen($ctkexcel)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $ctkexcel)) {
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Tidak dapat mengkonversi ke excel!');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
}

?>
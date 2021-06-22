<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
('' === $_POST['method'] ? ($method = $_GET['method']) : ($method = $_POST['method']));
('' === $_POST['tanggalcek'] ? ($tanggalcek = tanggalsystem($_GET['tanggalcek'])) : ($tanggalcek = tanggalsystem($_POST['tanggalcek'])));
('' === $_POST['kdBlok'] ? ($kdBlok = $_GET['kdBlok']) : ($kdBlok = $_POST['kdBlok']));
$tanggalpanen = tanggalsystem($_POST['tanggalpanen']);
$kdDiv = $_POST['kdDiv'];
$kdAfd = $_POST['kdAfd'];
$pusingan = $_POST['pusingan'];
$diperiksa = $_POST['diperiksa'];
$pendamping = $_POST['pendamping'];
$mengetahui = $_POST['mengetahui'];
$nopokok = $_POST['nopokok'];
$jjgpanen = $_POST['jjgpanen'];
$jjgtdkpanen = $_POST['jjgtdkpanen'];
$jjgtdkkumpul = $_POST['jjgtdkkumpul'];
$jjgmentah = $_POST['jjgmentah'];
$jjggantung = $_POST['jjggantung'];
$brdtdkdikutip = $_POST['brdtdkdikutip'];
$rumpukan = $_POST['rumpukan'];
$piringan = $_POST['piringan'];
$jalurpanen = $_POST['jalurpanen'];
$tukulan = $_POST['tukulan'];
$arrSt = ['X', 'V'];
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
    case 'saveHeader':
        $i = 'INSERT INTO '.$dbname.".`kebun_qc_panenht` (`tanggalcek`, `kodeblok`, `pusingan`, `tanggalpanen`, `diperiksa`, `pendamping`, `mengetahui`, `updateby`)\r\n\r\n                values ('".$tanggalcek."','".$kdBlok."','".$pusingan."','".$tanggalpanen."','".$diperiksa."','".$pendamping."',\r\n                                '".$mengetahui."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'saveDetail':
        $i = 'INSERT INTO '.$dbname.".`kebun_qc_panendt` (`tanggalcek`, `kodeblok`, `nopokok`, \r\n                `jjgpanen`, `jjgtdkpanen`, `jjgtdkkumpul`, `jjgmentah`, `jjggantung`, `brdtdkdikutip`,\r\n                `rumpukan`, `piringan`, `jalurpanen`, `tukulan`)\r\n\r\n                values ('".$tanggalcek."','".$kdBlok."','".$nopokok."',\r\n                '".$jjgpanen."','".$jjgtdkpanen."','".$jjgtdkkumpul."','".$jjgmentah."','".$jjggantung."','".$brdtdkdikutip."',\r\n                '".$rumpukan."','".$piringan."','".$jalurpanen."','".$tukulan."')";
        if (mysql_query($i)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'getKar':
        $optKar = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $d = 'select karyawanid,namakaryawan,nik from '.$dbname.".datakaryawan  where lokasitugas='".$kdDiv."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  alias like '%Askep Estate%')";
        $e = mysql_query($d) ;
        while ($f = mysql_fetch_assoc($e)) {
            $optKar .= "<option value='".$f['karyawanid']."'>".$f['nik'].' - '.$f['namakaryawan'].'</option>';
        }
        $optKadiv = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $g = 'select karyawanid,namakaryawan,nik from '.$dbname.".datakaryawan  where lokasitugas='".$kdDiv."' and kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where  alias like '%Manager Estate%')";
        $h = mysql_query($g) ;
        while ($i = mysql_fetch_assoc($h)) {
            $optKadiv .= "<option value='".$i['karyawanid']."'>".$i['nik'].' - '.$i['namakaryawan'].'</option>';
        }
        echo $optKar.'###'.$optKadiv;

        break;
    case 'loadDetail':
        echo "<fieldset><legend>Data Tersimpan</legend>\r\n                        <table class=sortable cellspacing=1 border=0>\r\n                         <thead>\r\n                                 <tr class=rowheader>\r\n                                        <td align=center>".$_SESSION['lang']['nourut'].' '.$_SESSION['lang']['pokok']."</td> \r\n                                        <td align=center>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['panen']."</td>\r\n                                        <td align=center>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['panen']."</td> \r\n                                        <td align=center>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['dikumpul']."</td> \r\n\r\n                                        <td align=center>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['mentah']."</td> \r\n                                        <td align=center>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['menggantung']."</td> \r\n                                        <td align=center>".$_SESSION['lang']['brondolan'].' '.$_SESSION['lang']['tdkdikutip']."</td> \r\n                                        <td align=center>".$_SESSION['lang']['rumpukan']."</td>\r\n                                        <td align=center>".$_SESSION['lang']['piringan']."</td>\r\n                                        <td align=center>".$_SESSION['lang']['jalur'].' '.$_SESSION['lang']['panen']."</td>\r\n                                        <td align=center>".$_SESSION['lang']['tukulan']."</td>\t\t\t\t\t \r\n                                        <td align=center>".$_SESSION['lang']['action']."</td>\r\n\r\n                                 </tr>\r\n                        </thead>\r\n                        <tbody></fieldset>";
        $no = 0;
        $a = 'select * from '.$dbname.".kebun_qc_panendt where tanggalcek='".$tanggalcek."' and kodeblok='".$kdBlok."' ";
        $b = mysql_query($a) ;
        while ($c = mysql_fetch_assoc($b)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n                                        <td align=right>".$c['nopokok']."</td>\r\n                                        <td align=right>".$c['jjgpanen']."</td>\r\n                                        <td align=right>".$c['jjgtdkpanen']."</td>\r\n                                        <td align=right>".$c['jjgtdkkumpul']."</td>\r\n                                        <td align=right>".$c['jjgmentah']."</td>\r\n                                        <td align=right>".$c['jjggantung']."</td>\r\n                                        <td align=right>".$c['brdtdkdikutip']."</td>\r\n                                        <td align=center>".$arrSt[$c['rumpukan']]."</td>\r\n                                        <td align=center>".$arrSt[$c['piringan']]."</td>\r\n                                        <td align=center>".$arrSt[$c['jalurpanen']]."</td>\r\n                                        <td align=center>".$arrSt[$c['tukulan']]."</td>\r\n                                        <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".tanggalnormal($c['tanggalcek'])."','".$c['kodeblok']."','".$c['nopokok']."');\" ></td></tr>";
        }
        echo '</table>';

        break;
    case 'printExcel':
        $i = 'select * from '.$dbname.".kebun_qc_panenht where kodeblok='".$kdBlok."' and tanggalcek='".$tanggalcek."'  ";
        $n = mysql_query($i) ;
        $d = mysql_fetch_assoc($n);
        $stream = $_SESSION['org']['namaorganisasi'];
        $stream .= "\r\n                        <table>\r\n                                <tr>\r\n                                        <td colspan=11 align=center><b><u>PEMERIKSAAN PANEN</u></b></td>\r\n                                </tr>\r\n                                <tr>\r\n                                        <td colspan=11  align=center>HARVESTING CHECKLIST</td>\r\n                                </tr>\r\n                                <tr>\r\n                                        <td></td>\r\n                                </tr>\r\n                                <tr>\r\n                                        <td colspan=2>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['cek']."</td>\r\n                                        <td colspan=4>: ".tanggalnormal($d['tanggalcek'])."</td>\r\n                                        <td colspan=2>".$_SESSION['lang']['blok']."</td>\r\n                                        <td colspan=4>: ".$d['kodeblok']."</td>\r\n                                </tr>\r\n                                <tr>\r\n                                        <td colspan=2>".$_SESSION['lang']['divisi']."</td>\r\n                                        <td colspan=4>: ".substr($d['kodeblok'], 0, 4)."</td>\r\n                                        <td colspan=2>".$_SESSION['lang']['pusingan'].' '.$_SESSION['lang']['panen']."</td>\r\n                                        <td colspan=4>: ".$d['pusingan']."</td>\r\n                                </tr>\r\n                                <tr>\r\n                                        <td colspan=2>".$_SESSION['lang']['afdeling']."</td>\r\n                                        <td colspan=4>: ".substr($d['kodeblok'], 0, 6)."</td>\r\n                                        <td colspan=2>".$_SESSION['lang']['tanggal'].' '.$_SESSION['lang']['panen']."</td>\r\n                                        <td colspan=4>: ".tanggalnormal($d['tanggalcek'])."</td>\r\n                                </tr>\r\n                        </table>\r\n        ";
        $stream .= "\r\n                        <table class=sortable border=1 cellspacing=1>\r\n                                 <thead>\r\n                                         <tr>\r\n                                                <td align=center valign=top rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['nourut'].' '.$_SESSION['lang']['pokok']."</td> \r\n                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['panen']."</td>\r\n                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['panen']."</td> \r\n                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['no'].' '.$_SESSION['lang']['dikumpul']."</td> \r\n\r\n                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['mentah']."</td> \r\n                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['jjg'].' '.$_SESSION['lang']['menggantung']."</td> \r\n                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['brondolan'].' '.$_SESSION['lang']['tdkdikutip']."</td> \r\n                                                <td align=center valign=top  rowspan=2 bgcolor=#CCCCCC>".$_SESSION['lang']['rumpukan']."</td>\r\n                                                <td align=center valign=top  colspan=3 bgcolor=#CCCCCC>".$_SESSION['lang']['brondolan'].' '.$_SESSION['lang']['tdkdikutip']."</td> \r\n                                        </tr>\r\n                                        <tr>\t\t\t\t\t\t\t\r\n                                                <td align=center valign=top  bgcolor=#CCCCCC>".$_SESSION['lang']['piringan']."</td>\r\n                                                <td align=center valign=top  bgcolor=#CCCCCC>".$_SESSION['lang']['jalur'].' '.$_SESSION['lang']['panen']."</td>\r\n                                                <td align=center valign=top  bgcolor=#CCCCCC>".$_SESSION['lang']['tukulan']."</td>\t\t\t\t\t \r\n                                         </tr>";
        $w = 'select * from '.$dbname.".kebun_qc_panendt where kodeblok='".$kdBlok."' and tanggalcek='".$tanggalcek."' order by nopokok asc";
        $i = mysql_query($w) ;
        while ($b = mysql_fetch_assoc($i)) {
            $stream .= "<tr class=rowcontent>\r\n                                        <td align=right>".$b['nopokok']."</td>\r\n                                        <td align=right>".$b['jjgpanen']."</td>\r\n                                        <td align=right>".$b['jjgtdkpanen']."</td>\r\n                                        <td align=right>".$b['jjgtdkkumpul']."</td>\r\n                                        <td align=right>".$b['jjgmentah']."</td>\r\n                                        <td align=right>".$b['jjggantung']."</td>\r\n                                        <td align=right>".$b['brdtdkdikutip']."</td>\r\n                                        <td align=center>".$arrSt[$b['rumpukan']]."</td>\r\n                                        <td align=center>".$arrSt[$b['piringan']]."</td>\r\n                                        <td align=center>".$arrSt[$b['jalurpanen']]."</td>\r\n                                        <td align=center>".$arrSt[$b['tukulan']].'</td>';
            $totJjgpanen += $b['jjgpanen'];
            $totTdkPanen += $b['jjgtdkpanen'];
            $totTdkKumpul += $b['jjgtdkkumpul'];
            $totJjgMentah += $b['jjgmentah'];
            $totJjgGantung += $b['jjggantung'];
            $totBrondolan += $b['brdtdkdikutip'];
            $totRumpukan += $b['rumpukan'];
            $totPiringan += $b['piringan'];
            $totJalurPanen += $b['jalurpanen'];
            $totTukulan = $b['tukulan'];
        }
        $stream .= "<tr>\r\n                                <td align=right>".$_SESSION['lang']['total']."</td>\r\n                                <td align=right>".$totJjgpanen."</td>\r\n                                <td align=right>".$totTdkPanen."</td>\r\n                                <td align=right>".$totTdkKumpul."</td>\r\n\r\n                                <td align=right>".$totJjgMentah."</td>\r\n                                <td align=right>".$totJjgGantung."</td>\r\n                                <td align=right>".$totBrondolan."</td>\r\n\r\n                                <td align=right>".$totRumpukan."</td>\r\n                                <td align=right>".$totPiringan."</td>\r\n                                <td align=right>".$totJalurPanen."</td>\r\n                                <td align=right>".$totTukulan."</td>\r\n\r\n\r\n\r\n        </tr></table>";
        $stream .= "\r\n                        <table>\r\n                                <tr>\r\n                                        <td colspan=3>Ratio Brondolan (B/TBS)</td>\r\n                                        <td>: ".$totBrondolan / $totJjgpanen."</td>\r\n                                </tr>\r\n                                <tr>\r\n                                </tr>\r\n                                <tr>\r\n                                </tr>\r\n                                <tr>\r\n                                </tr>\r\n\r\n                                <tr>\r\n                                        <td colspan=3>".$_SESSION['lang']['diperiksa']."</td>\r\n                                        <td colspan=3>".$_SESSION['lang']['pendamping']."</td>\r\n                                        <td colspan=3>".$_SESSION['lang']['mengetahui']."</td>\r\n                                        <td></td>\r\n                                </tr>\r\n                                <tr>\r\n                                </tr>\r\n                                <tr>\r\n                                </tr>\r\n                                <tr>\r\n                                </tr>\r\n\r\n\r\n                                <tr>\r\n                                </tr>\r\n                                <tr>\r\n                                </tr>\r\n\r\n                                <tr>\r\n                                        <td colspan=6><b><u>Indicator :</b></u></td>\r\n                                        <td colspan=5><b><u>Catatan lain :</b></u></td>\r\n                                </tr>\r\n\r\n                                <tr>\r\n                                        <td colspan=3>Pruning/Rumpukan Pelepah</td>\r\n                                        <td>V = ".$_SESSION['lang']['bagus']."</td>\r\n                                        <td colspan=2>X= ".$_SESSION['lang']['buruk']."</td>\r\n                                        <td colspan=5>1. Brondolan tertinggal di TPH : ...............</td>\r\n                                </tr>\r\n                                <tr>\r\n                                        <td colspan=3>Kondisi Lahan</td>\r\n                                        <td>V= bagus</td>\r\n                                        <td colspan=2>X= jelek</td>\r\n                                        <td colspan=5>2. Brondolan tertinggal di jalan : ..............</td>\r\n                                </tr>\r\n                                <tr>\r\n                                        <td colspan=3>Tukulan Sawit (VOP) </td>\r\n                                        <td>V= bagus</td>\r\n                                        <td>X= jelek</td>\r\n                                </tr>\r\n\r\n\r\n\r\n                        </table>\r\n\r\n\r\n        ";
        $nop_ = 'Laporan_QC_panen_'.tanggalnormal($d['tanggalcek']);
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
                echo "<script language=javascript1.2>\r\n                                parent.window.alert('Can't convert to excel format');\r\n                                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                                window.location='tempExcel/".$nop_.".xls';\r\n                                </script>";
            closedir($handle);
        }

        break;
    case 'loadData':
        if ('' !== $kdDivSch) {
            $kdDivLoad = "kodeblok like '%".$kdDivSch."%'";
        } else {
            $kdDivLoad = "kodeblok!='' ";
        }

        if ('' !== $perSch) {
            $perLoad = "and tanggalcek like '%".$perSch."%'";
        } else {
            $perLoad = '';
        }

        echo "\r\n\r\n                        <table class=sortable cellspacing=1 border=0>\r\n                         <thead>\r\n                                 <tr class=rowheader>\r\n                                        <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n                                         <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n                                         <td align=center>".$_SESSION['lang']['divisi']."</td>\r\n                                         <td align=center>".$_SESSION['lang']['afdeling']."</td>\r\n                                         <td align=center>".$_SESSION['lang']['blok']."</td>\r\n                                         <td align=center>".$_SESSION['lang']['diperiksa']."</td>\r\n                                         <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n                                         <td align=center>".$_SESSION['lang']['action']."</td>\r\n                                 </tr>\r\n                        </thead>\r\n                        <tbody>";
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
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_qc_panenht where '.$kdDivLoad.'  '.$perLoad.'  ';
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.'.kebun_qc_panenht where '.$kdDivLoad.'  '.$perLoad.'  limit '.$offset.','.$limit.'';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            $arr = '##'.$d['kodeblok'].'##'.$d['tanggalcek'].'';
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.tanggalnormal($d['tanggalcek']).'</td>';
            echo '<td align=left>'.substr($d['kodeblok'], 0, 4).'</td>';
            echo '<td align=left>'.substr($d['kodeblok'], 0, 6).'</td>';
            echo '<td align=left>'.$d['kodeblok'].'</td>';
            echo '<td align=left>'.$nmKar[$d['diperiksa']].'</td>';
            echo '<td align=left>'.$nmKar[$d['updateby']].'</td>';
            echo "<td align=center>\r\n                                                <img src=images/application/application_delete.png class=resicon caption='Delete' onclick=\"del('".tanggalnormal($d['tanggalcek'])."','".$d['kodeblok']."');\">\t\t\r\n                                                <img onclick=datakeExcel(event,'".tanggalnormal($d['tanggalcek'])."','".$d['kodeblok']."') src=images/excel.jpg class=resicon title='MS.Excel'></td>";
            echo '</tr>';
        }
        echo "\r\n                        <tr class=rowheader><td colspan=43 align=center>\r\n                        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                        <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                        <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                        </td>\r\n                        </tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".kebun_qc_panenht where tanggalcek='".$tanggalcek."' and kodeblok='".$kdBlok."'";
        if (mysql_query($i)) {
            $n = 'delete from '.$dbname.".kebun_qc_panendt where tanggalcek='".$tanggalcek."' and kodeblok='".$kdBlok."'";
            if (mysql_query($n)) {
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'deleteDetail':
        $i = 'delete from '.$dbname.".kebun_qc_panendt where tanggalcek='".$tanggalcek."' and kodeblok='".$kdBlok."' and nopokok='".$nopokok."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>
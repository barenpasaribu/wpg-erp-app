<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdorg = $_POST['kdorg'];
$per = $_POST['per'];
if ('excel' === $proses || 'pdf' === $proses) {
    $kdorg = $_GET['kdorg'];
    $per = $_GET['per'];
}

$arrSt = ['X', 'V'];
$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
if ('preview' === $proses || 'excel' === $proses || 'pdf' === $proses) {
    if ('' === $kdorg) {
        echo 'Error: '.$_SESSION['lang']['divisi'].' '.$_SESSION['lang']['kosong'].'';
        exit();
    }

    if ('' === $per) {
        echo 'Error: '.$_SESSION['lang']['periode'].' '.$_SESSION['lang']['kosong'].'';
        exit();
    }
}

if ('excel' === $proses) {
    $border = "border='1'";
    $bgcolor = 'bgcolor=#CCCCCC';
} else {
    $border = "border='0'";
    $bgcolor = '#FFFFFF';
}

$stream = ' '.$_SESSION['lang']['cek'].' '.$_SESSION['lang']['panen'].'<br />'.$_SESSION['lang']['periode'].' : '.$per.' ';
$stream .= "<table cellspacing='1' ".$border." class='sortable'>";
$stream .= "<thead>\r\n\t\t\t\t\t<tr class=rowheader>\r\n\t\t\t\t\t\t<td rowspan=2 align=center ".$bgcolor.'>'.$_SESSION['lang']['divisi']."</td>\r\n\t\t\t\t\t\t<td rowspan=2 align=center ".$bgcolor.'>'.$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t\t\t\t<td rowspan=2 align=center ".$bgcolor.'>'.$_SESSION['lang']['afdeling']."</td>\r\n\t\t\t\t\t\t<td rowspan=2 align=center ".$bgcolor.'>'.$_SESSION['lang']['blok']."</td>\r\n\t\t\t\t\t\t<td rowspan=2 align=center ".$bgcolor.'>'.$_SESSION['lang']['tanggal'].'<br />'.$_SESSION['lang']['panen']."</td>\r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['nourut'].' '.$_SESSION['lang']['pokok']."</td> \r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['jjg'].'<br />'.$_SESSION['lang']['panen']."</td> \r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['jjg'].'<br />'.$_SESSION['lang']['no'].' '.$_SESSION['lang']['panen']."</td> \r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['jjg'].'<br />'.$_SESSION['lang']['no'].' '.$_SESSION['lang']['dikumpul']."</td> \r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['jjg'].'<br />'.$_SESSION['lang']['menggantung']."</td> \r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['brondolan'].'<br />'.$_SESSION['lang']['tdkdikutip']."</td> \r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['rasio'].'<br />'.$_SESSION['lang']['brondolan']."</td> \r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['pusingan']."</td>\r\n\t\t\t\t\t\t<td align=center valign=center  rowspan=2 ".$bgcolor.'>'.$_SESSION['lang']['rumpukan'].'<br />'.$_SESSION['lang']['pelepah']."</td>\r\n\t\t\t\t\t\t<td align=center valign=center  colspan=3 ".$bgcolor.'>'.$_SESSION['lang']['kondisi'].' '.$_SESSION['lang']['jalan']."</td> \r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td ".$bgcolor.'>'.$_SESSION['lang']['piringan']."</td>\r\n\t\t\t\t\t\t<td ".$bgcolor.'>'.$_SESSION['lang']['jalur'].' '.$_SESSION['lang']['panen']."</td>\r\n\t\t\t\t\t\t<td ".$bgcolor.'>'.$_SESSION['lang']['tukulan']."</td>\r\n\t\t\t\t\t</tr> \t\t\t\t\t \r\n\t\t\t\t</thead>\r\n               <tbody>";
$i = 'SELECT count(*) as jumlah FROM '.$dbname.".kebun_qc_panenht WHERE tanggalcek LIKE  '%".$per."%' AND kodeblok LIKE  '%".$kdorg."%'";
$n = mysql_query($i) ;
$d = mysql_fetch_assoc($n);
$jumlah = $d['jumlah'];
$u = 'SELECT * FROM '.$dbname.".kebun_qc_panenht WHERE tanggalcek LIKE  '%".$per."%' AND kodeblok LIKE  '%".$kdorg."%'\r\n\t\t\tGROUP BY kodeblok,tanggalcek ORDER BY kodeblok ASC , tanggalcek ASC ";
$v = mysql_query($u) ;
while ($w = mysql_fetch_assoc($v)) {
    $x = "SELECT sum(nopokok) as nopokok,sum(jjgpanen) as jjgpanen,sum(jjgtdkpanen) as jjgtdkpanen,sum(jjgtdkkumpul) as jjgtdkkumpul,\r\n\t\t\t\tsum(jjgmentah) as jjgmentah,sum(jjggantung) as jjggantung,sum(brdtdkdikutip) as brdtdkdikutip,\r\n\t\t\t\tsum(rumpukan) as rumpukan,sum(piringan) as piringan,sum(jalurpanen) as jalurpanen,sum(tukulan) as tukulan\r\n\t\t\t FROM ".$dbname.".kebun_qc_panendt WHERE kodeblok='".$w['kodeblok']."' AND tanggalcek ='".$w['tanggalcek']."' ORDER BY nopokok DESC LIMIT 1";
    $y = mysql_query($x) ;
    while ($z = mysql_fetch_assoc($y)) {
        ++$no;
        if (1 === $no) {
            $stream .= "\r\n\t\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t\t\t<td align=center valign=center rowspan=".$jumlah.'>'.substr($w['kodeblok'], 0, 4)."</td>\r\n\t\t\t\t\t\t<td align=right>".tanggalnormal($w['tanggalcek'])."</td>\r\n\t\t\t\t\t\t<td align=right>".substr($w['kodeblok'], 0, 6)."</td>\r\n\t\t\t\t\t\t<td align=right>".$w['kodeblok']."</td>\r\n\t\t\t\t\t\t<td align=right>".tanggalnormal($w['tanggalpanen'])."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['nopokok']."</td>\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t<td align=right>".$z['jjgpanen']."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['jjgtdkpanen']."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['jjgtdkkumpul']."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['jjggantung']."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['brdtdkdikutip']."</td>\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t<td align=right>".number_format($z['brdtdkdikutip'] / $z['jjgpanen'], 2)."</td>\r\n\t\t\t\t\t\t<td align=right>".$w['pusingan']."</td>\r\n\t\t\t\t\t\t<td align=center>".$z['rumpukan']."</td>\r\n\t\t\t\t\t\t<td align=center>".$z['piringan']."</td>\r\n\t\t\t\t\t\t<td align=center>".$z['jalurpanen']."</td>\r\n\t\t\t\t\t\t<td align=center>".$z['tukulan']."</td>\r\n\t\t\t\t\t</tr>";
        } else {
            $stream .= "<tr class=rowcontent>\r\n\t\t\t\t\t\t<td align=right>".tanggalnormal($w['tanggalcek'])."</td>\r\n\t\t\t\t\t\t<td align=right>".substr($w['kodeblok'], 0, 6)."</td>\r\n\t\t\t\t\t\t<td align=right>".$w['kodeblok']."</td>\r\n\t\t\t\t\t\t<td align=right>".tanggalnormal($w['tanggalpanen'])."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['nopokok']."</td>\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t<td align=right>".$z['jjgpanen']."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['jjgtdkpanen']."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['jjgtdkkumpul']."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['jjggantung']."</td>\r\n\t\t\t\t\t\t<td align=right>".$z['brdtdkdikutip']."</td>\r\n\t\t\t\t\t\t<td align=right>".number_format($z['brdtdkdikutip'] / $z['jjgpanen'], 2)."</td>\r\n\t\t\t\t\t\t\r\n\t\t\t\t\t\t<td align=right>".$w['pusingan']."</td>\r\n\t\t\t\t\t\t<td align=center>".$z['rumpukan']."</td>\r\n\t\t\t\t\t\t<td align=center>".$z['piringan']."</td>\r\n\t\t\t\t\t\t<td align=center>".$z['jalurpanen']."</td>\r\n\t\t\t\t\t\t<td align=center>".$z['tukulan']."</td>\r\n\t\t\t\t\t</tr>";
        }

        $totNoPok += $z['nopokok'];
        $totJjgPenen += $z['jjgpanen'];
        $totTdkJjgPenen += $z['jjgtdkpanen'];
        $totJjgTdkKumpul += $z['jjgtdkkumpul'];
        $totJjgGantung += $z['jjggantung'];
        $totBrondolTdkKutip += $z['brdtdkdikutip'];
    }
}
$stream .= "\r\n\t\t<thead><tr class=rowheader>\r\n\t\t\t<td colspan=5 align=center>".$_SESSION['lang']['total']."</td>\r\n\t\t\t<td align=right>".$totNoPok."</td>\r\n\t\t\t<td align=right>".$totJjgPenen."</td>\r\n\t\t\t<td align=right>".$totTdkJjgPenen."</td>\r\n\t\t\t<td align=right>".$totJjgTdkKumpul."</td>\r\n\t\t\t<td align=right>".$totJjgGantung."</td>\r\n\t\t\t<td align=right>".$totBrondolTdkKutip."</td>\r\n\t\t\t<td align=right>".number_format($totBrondolTdkKutip / $totJjgPenen, 2)."</td>\r\n\t\t\t<td colspan=5></td>\r\n\t\t</thead></tr>\r\n\t\t</table>";
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $tglSkrg = date('Ymd');
        $nop_ = 'Laporan_QC_Panen_'.$per;
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
    default:
        break;
}

?>
<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
if($pt==""){
    $pt = substr($_SESSION['empl']['lokasitugas'], 0,3);
}
$gudang = $_GET['gudang'];
$supplier = $_GET['supplier'];
$tanggalpivot = $_GET['tanggalpivot'];

$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'Seluruhnya';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$stream = '';
if ('' !== $gudang) {
    $whr .= " and substr(novp,2,4) = '".$gudang."' ";
} else {
    if ('' !== $pt) {
        $whr .= " and kodeorg = '".$pt."'";
    }
}
if ('' !== $supplier) {
    $whr .= " and kodesupplier = '".$supplier."'";
}
$str = 'select * from '.$dbname.".aging_sch_vw\r\n      where tanggal > '2012-12-31' and (nilaiinvoice > dibayar or dibayar is NULL)\r\n      ".$whr.' order by namasupplier asc';
$res = mysql_query($str);
$no = 0;
if (@mysql_num_rows($res) < 1) {
    echo '<tr class=rowcontent><td colspan=13>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    $stream .= "<table border=1>\r\n                    <tr>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nourut']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['tanggal']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['noinvoice']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['namasupplier']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['jatuhtempo']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nopokontrak']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nilaipokontrak']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['nilaiinvoice']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['belumjatuhtempo']."</td>\r\n                          <td nowrap align=center colspan=4 bgcolor=#CCCCCC>".$_SESSION['lang']['sudahjatuhtempo']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['dibayar']."</td>\r\n                          <td nowrap rowspan=2 bgcolor=#CCCCCC align=center>".$_SESSION['lang']['jmlh_hari_outstanding']."</td>\r\n                        </tr>  \r\n                    <tr>\r\n                          <td bgcolor=#CCCCCC nowrap align=center>1-30 ".$_SESSION['lang']['hari']."</td>\r\n                          <td bgcolor=#CCCCCC nowrap align=center>31-60 ".$_SESSION['lang']['hari']."</td>\r\n                          <td bgcolor=#CCCCCC nowrap align=center>61-90 ".$_SESSION['lang']['hari']."</td>\r\n                          <td bgcolor=#CCCCCC nowrap align=center>over 100 ".$_SESSION['lang']['hari']."</td>\r\n                        </tr>";
    $grantotal0 = $grantotal30 = $grandtotal60 = $grantotal90 = $grantotal100 = $grantotaldibayar = 0;
    $totalinvoice = 0;
    while ($bar = mysql_fetch_assoc($res)) {
        if ($kdsupp !== $bar['kodesupplier']) {
            $kdsupp = $bar['kodesupplier'];
            $i = 'select count(kodesupplier) as rowdt from '.$dbname.".aging_sch_vw where tanggal > '2012-12-31' and (nilaiinvoice > dibayar or dibayar is NULL) and \r\n\t\t\t\t\t\t\tkodesupplier='".$kdsupp."' order by tanggal asc";
            $n = mysql_query($i);
            $d = mysql_fetch_assoc($n);
            $rowd = $d['rowdt'];
            $no = 1;
            $subTotInvoice = 0;
            $total0 = 0;
            $total30 = 0;
            $total60 = 0;
            $total90 = 0;
            $total100 = 0;
            $row = 0;
        }

        $namasupplier = $bar['namasupplier'];
        $noinvoice = $bar['noinvoice'];
        $tanggal = $bar['tanggal'];
        $jatuhtempo = $bar['jatuhtempo'];
        $nopokontrak = $bar['nopo'];
        $nilaipo = $bar['kurs'] * $bar['nilaipo'];
        $nilaikontrak = $bar['kurs'] * $bar['nilaikontrak'];
        $nilaiinvoice = $bar['kurs'] * $bar['nilaiinvoice'];
        $dibayar = $bar['kurs'] * $bar['dibayar'];
        $sisainvoice = $nilaiinvoice - $dibayar;
        $nilaipokontrak = $nilaipo;
        if (0 < $nilaikontrak) {
            $nilaipokontrak = $nilaikontrak;
        }

        $date1 = $tanggalpivot;
        $diff = strtotime($jatuhtempo) - strtotime($date1);
        $outstd = floor($diff / (60 * 60 * 24));
        $flag0 = $flag30 = $flag60 = $flag90 = $flag100 = 0;
        if (0 !== $outstd) {
            $outstd *= -1;
        }

        if ($outstd <= 0) {
            $flag0 = 1;
        }

        if (1 <= $outstd && $outstd <= 30) {
            $flag30 = 1;
        }

        if (31 <= $outstd && $outstd <= 60) {
            $flag60 = 1;
        }

        if (61 <= $outstd && $outstd <= 90) {
            $flag90 = 1;
        }

        if (90 < $outstd) {
            $flag100 = 1;
        }

        if (1 === $flag0) {
            $total0 += $sisainvoice;
        }

        if (1 === $flag30) {
            $total30 += $sisainvoice;
        }

        if (1 === $flag60) {
            $total60 += $sisainvoice;
        }

        if (1 === $flag90) {
            $total90 += $sisainvoice;
        }

        if (1 === $flag100) {
            $total100 += $sisainvoice;
        }

        if ('0000-00-00' === $jatuhtempo) {
            $outstd = '';
            $jatuhtempo = '';
        }

        $stream .= "<tr>\r\n                                  <td nowrap align=center>".$no."</td>\r\n                                  <td nowrap align=center>".$tanggal."</td>\r\n                                  <td nowrap align=left nowrap>&nbsp;".$noinvoice."</td> \r\n                                  <td nowrap align=left nowrap>".$namasupplier."</td> \r\n                                  <td nowrap align=center>".$jatuhtempo."</td>\r\n                                  <td nowrap align=center>".$nopokontrak."</td>\r\n                                  <td nowrap align=right>".number_format($nilaipokontrak, 2)."</td>\r\n                                  <td nowrap align=right>".number_format($nilaiinvoice, 2)."</td>\r\n                                  <td nowrap align=right>";
        if (1 === $flag0) {
            $stream .= number_format($sisainvoice, 2);
        }

        $stream .= "</td>\r\n                                  <td nowrap align=right>";
        if (1 === $flag30) {
            $stream .= number_format($sisainvoice, 2);
        }

        $stream .= "</td>\r\n                                  <td nowrap align=right>";
        if (1 === $flag60) {
            $stream .= number_format($sisainvoice, 2);
        }

        $stream .= "</td>\r\n                                  <td nowrap align=right>";
        if (1 === $flag90) {
            $stream .= number_format($sisainvoice, 2);
        }

        $stream .= "</td>\r\n                                  <td nowrap align=right>";
        if (1 === $flag100) {
            $stream .= number_format($sisainvoice, 2);
        }

        $stream .= "</td>\r\n                                  <td nowrap align=right>".number_format($dibayar, 2)."</td>\r\n                                  <td nowrap align=right>".$outstd."</td>\r\n                                </tr>";
        $subTotInvoice += $nilaiinvoice;
        $subtotaldibayar += $dibayar;
        $subTotInvoice += $nilaiinvoice;
        $subtotaldibayar += $dibayar;
        ++$no;
        ++$row;
        if ($row === $rowd) {
            $stream .= "<thead><tr>\r\n                                  <td colspan=6 align=center><b>".$_SESSION['lang']['subtotal'].' '.$namasupplier."</b></td>\r\n                                  <td align=right><b>".number_format($subTotInvoice, 2)."</td>\r\n                                  <td align=right><b>".number_format($total0, 2)."</td>\r\n                                  <td align=right><b>".number_format($total30, 2)."</td>\r\n                                  <td align=right><b>".number_format($total60, 2)."</td>\r\n                                  <td align=right><b>".number_format($total90, 2)."</td>\r\n                                  <td align=right><b>".number_format($total100, 2)."</td>\r\n                                  <td align=right><b>".number_format($subtotaldibayar, 2)."</td>\r\n                                  <td align=right>&nbsp;</td>\r\n                        </tr></thead>";
            $totalinvoice += $subTotInvoice;
            $grantotaldibayar += $subtotaldibayar;
            $grantotal0 += $total0;
            $grantotal30 += $total30;
            $grantotal60 += $total60;
            $grantotal90 += $total90;
            $grantotal100 += $total100;
        }
    }
    $stream .= "<tr class=rowtitle bgcolor=#0066FF>\r\n                                  <td colspan=6 align=center><b>".$_SESSION['lang']['grnd_total']."</b></td>\r\n                                  <td align=right><b>".number_format($totalinvoice, 2)."</td>\r\n                                  <td align=right><b>".number_format($grantotal0, 2)."</td>\r\n                                  <td align=right><b>".number_format($grantotal30, 2)."</td>\r\n                                  <td align=right><b>".number_format($grantotal60, 2)."</td>\r\n                                  <td align=right><b>".number_format($grantotal90, 2)."</td>\r\n                                  <td align=right><b>".number_format($grantotal100, 2)."</td>\r\n                                  <td align=right><b>".number_format($grantotaldibayar, 2)."</td>\r\n                                  <td align=right>&nbsp;</td>\r\n                        </tr>";
    $stream .= '</table>';
}

$stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
$nop_ = 'DaftarUsiaHutang';
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
        echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
    closedir($handle);
}

function tanggalbiasa($_q)
{
    $_q = str_replace('-', '', $_q);

    return substr($_q, 4, 4).'-'.substr($_q, 2, 2).'-'.substr($_q, 0, 2);
}

?>
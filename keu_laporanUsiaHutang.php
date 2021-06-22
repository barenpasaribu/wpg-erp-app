<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
if($pt==""){
    $pt = substr($_SESSION['empl']['lokasitugas'], 0,3);
}
$gudang = $_POST['gudang'];
$supplier = $_POST['supplier'];
$tanggalpivot = $_POST['tanggalpivot'];
if ('' === $tanggalpivot) {
    exit("error: Date can't empty");
}

if ('' !== $gudang) {
    $whr .= " and substr(novp,2,4) = '".$gudang."' ";
}

if ('' !== $pt) {
    $whr .= " and kodeorg = '".$pt."'";
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
        if ('' === $namasupplier) {
            $namasupplier = '&nbsp;';
        }

        $noinvoice = $bar['noinvoice'];
        $tanggal = $bar['tanggal'];
        $jatuhtempo = $bar['jatuhtempo'];
        $nopokontrak = $bar['nopo'];
        $nilaipo = $bar['kurs'] * $bar['nilaipo'];
        $nilaikontrak = $bar['kurs'] * $bar['nilaikontrak'];
        $nilaiinvoice = $bar['kurs'] * $bar['nilaiinvoice'];
        $dibayar = $bar['kurs'] * $bar['dibayar'];
        $pph = $bar['perhitunganpph'];
        $sisainvoice = $nilaiinvoice - $dibayar - $pph ;
        $nilaipokontrak = $nilaipo;
        if (0 < $nilaikontrak) {
            $nilaipokontrak = $nilaikontrak;
        }

        $date1 = tanggaldgnbar($tanggalpivot);
        if ('0000-00-00' === $jatuhtempo) {
            $jatuhtempo = $date1;
        }

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

        $subtotaldibayar += $dibayar;
        if ('0000-00-00' === $jatuhtempo) {
            $outstd = '';
            $jatuhtempo = '';
        } else {
            $jatuhtempo = tanggalnormal($jatuhtempo);
        }

        echo "<tr class=rowcontent>\r\n                                  <td rowspan=2 align=center width=20>".$no."</td>\r\n                                  <td rowspan=2 nowrap>".$tanggal."</td> \r\n                                  <td nowrap>".$noinvoice."</td> \r\n                                 ";
        if ($jatuhtempo === tanggalnormal($date1)) {
            echo '<td rowspan=2 align=center></td>';
        } else {
            echo '<td rowspan=2 align=center>'.$jatuhtempo.'</td>';
        }

        echo '<td rowspan=2 align=center>'.$nopokontrak."</td>\r\n                                  <td rowspan=2 align=right>".number_format($nilaipokontrak, 2)."</td>\r\n                                  <td rowspan=2 align=right>".number_format($nilaiinvoice, 2)."</td>\r\n                                  <td rowspan=2 align=right>";
        if (1 === $flag0) {
            echo number_format($sisainvoice, 2);
        }

        echo "</td>\r\n                                  <td rowspan=2 align=right>";
        if (1 === $flag30) {
            echo number_format($sisainvoice, 2);
        }

        echo "</td>\r\n                                  <td rowspan=2 align=right>";
        if (1 === $flag60) {
            echo number_format($sisainvoice, 2);
        }

        echo "</td>\r\n                                  <td rowspan=2 align=right>";
        if (1 === $flag90) {
            echo number_format($sisainvoice, 2);
        }

        echo "</td>\r\n                                  <td rowspan=2 align=right>";
        if (1 === $flag100) {
            echo number_format($sisainvoice, 2);
        }

        echo "</td>\r\n                                  <td rowspan=2 align=right width=100>".number_format($dibayar, 2)."</td>\r\n                                  <td rowspan=2 align=right>".$outstd."</td>\r\n                        </tr><tr class=rowcontent>\r\n                                  <td nowrap>".$namasupplier."</td> \r\n                        </tr>";
        $subTotInvoice += $nilaiinvoice;
        $subtotaldibayar += $dibayar;
        ++$no;
        ++$row;
        if ($row === $rowd) {
            echo "<thead><tr>\r\n                                  <td colspan=6 align=center width=20><b>".$_SESSION['lang']['subtotal'].' '.$namasupplier."</b></td>\r\n                                  <td align=right><b>";
            echo number_format($subTotInvoice, 2);
            echo "</td>\r\n                                  <td align=right><b>";
            echo number_format($total0, 2);
            echo "</td>\r\n                                  <td align=right><b>";
            echo number_format($total30, 2);
            echo "</td>\r\n                                  <td align=right><b>";
            echo number_format($total60, 2);
            echo "</td>\r\n                                  <td align=right><b>";
            echo number_format($total90, 2);
            echo "</td>\r\n                                  <td align=right><b>";
            echo number_format($total100, 2);
            echo "</td>\r\n                                  <td align=right width=100><b>".number_format($subtotaldibayar, 2)."</td>\r\n                                  <td align=right>&nbsp;</td>\r\n                        </tr></thead>";
            $totalinvoice += $subTotInvoice;
            $grantotaldibayar += $subtotaldibayar;
            $grantotal0 += $total0;
            $grantotal30 += $total30;
            $grantotal60 += $total60;
            $grantotal90 += $total90;
            $grantotal100 += $total100;
        }
    }
    echo "<tr class=rowtitle bgcolor=#0066FF>\r\n                                  <td colspan=6 align=center width=20><b>".$_SESSION['lang']['grnd_total']."</b></td>\r\n                                  <td align=right><b>";
    echo number_format($totalinvoice, 2);
    echo "</td>\r\n                                  <td align=right><b>";
    echo number_format($grantotal0, 2);
    echo "</td>\r\n                                  <td align=right><b>";
    echo number_format($grantotal30, 2);
    echo "</td>\r\n                                  <td align=right><b>";
    echo number_format($grantotal60, 2);
    echo "</td>\r\n                                  <td align=right><b>";
    echo number_format($grantotal90, 2);
    echo "</td>\r\n                                  <td align=right><b>";
    echo number_format($grantotal100, 2);
    echo "</td>\r\n                                  <td align=right width=100><b>".number_format($grantotaldibayar, 2)."</td>\r\n                                  <td align=right>&nbsp;</td>\r\n                        </tr>";
}

?>
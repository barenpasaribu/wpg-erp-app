<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/devLibrary.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$id = $_POST['id'];
$kodeorg = $_POST['kodeorg'];
$hasil = $_POST['hasil'];
$tahuntanam = $_POST['tahuntanam'];
$lebihbasis = $_POST['lebihbasis'];
$rupiah = $_POST['rupiah'];
$premirajin = $_POST['premirajin'];
$premihadir = $_POST['premihadir'];
$brondolanperkg = $_POST['brondolanperkg'];
$hslpanen = $_POST['hslpanen'];
$method = $_POST['method'];
$bulanawal=$_POST['bulanawal'];
$bulanakhir=$_POST['bulanakhir'];
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
echo "\r\n";
switch ($method) {
    case 'insert':
        $i = "insert into $dbname.kebun_5premipanen (kodeorg,hasilkg,lebihbasiskg,rupiah,premirajin,updateby,hasilpanen,tahuntanam,bulanawal,bulanakhir,premihadir,brondolanperkg) ".
            "values ('".$kodeorg."','".$hasil."','".$lebihbasis."','".$rupiah."','".$premirajin."','".$_SESSION['standard']['userid']."','".$hslpanen."','".$tahuntanam."','".$bulanawal."','".$bulanakhir."','".$premihadir."','".$brondolanperkg."')";
//        echoMessage(" insert ",$i,true);
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        $i = "update $dbname.kebun_5premipanen set ".
            "kodeorg='".$kodeorg."',".
            "hasilkg='".$hasil."',".
            "lebihbasiskg='".$lebihbasis."',".
            "rupiah='".$rupiah."',".
            "premirajin='".$premirajin."',".
            "updateby='".$_SESSION['standard']['userid']."',".
            "hasilpanen='".$hslpanen."',".
            "tahuntanam='".$tahuntanam."',".
            "premihadir='".$premihadir."',".
            "brondolanperkg='".$brondolanperkg."',".
            "bulanawal='".$bulanawal."',".
            "bulanakhir='".$bulanakhir."' ".
            "where id=".$id;
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "<div id=container style='overflow:scroll;'>".
            "<table class=sortable cellspacing=1 border=0>".
            "<thead>".
            "   <tr class=rowheader> ".
            "      <td align=center>".$_SESSION['lang']['nourut']."</td>".
            "      <td align=center>".$_SESSION['lang']['kodeorg']."</td>".
            "      <td align=center>".$_SESSION['lang']['tahuntanam']."</td>".
            "      <td align=center>Bulan Awal</td>".
            "      <td align=center>Bulan Akhir</td>".
            "      <td align=center>".$_SESSION['lang']['basiskg']."</td>".
            "      <td align=center>".$_SESSION['lang']['hslpanen']."</td>".
            "      <td align=center>".$_SESSION['lang']['lebihbasis2']."</td>".
            "      <td align=center>".$_SESSION['lang']['rp']."</td>".
            "      <td align=center>".$_SESSION['lang']['premirajin']."</td>".
            "      <td align=center>".$_SESSION['lang']['premihadir']."</td>".
            "      <td align=center>".$_SESSION['lang']['brondolanperkg2']."</td>".
            "      <td align=center>".$_SESSION['lang']['action']."</td>" .
            "   </tr></thead><tbody>";



        $limit = 15;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $maxdisplay = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_5premipanen';
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.'.kebun_5premipanen where left(kodeorg,3)=\''.$_SESSION['empl']['kodeorganisasi'].'\' order by kodeorg,tahuntanam,lebihbasiskg';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        $arrBln1=array(
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "July",
            "August",
            "September",
            "October",
            "November",
            "Desember"
        );
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['kodeorg'].'</td>';
            echo '<td align=right>'.$d['tahuntanam'].'</td>';
            echo '<td align=right>'.$arrBln1[$d['bulanawal']-1].'</td>';
            echo '<td align=right>'.$arrBln1[$d['bulanakhir']-1].'</td>';
            echo '<td align=right>'.$d['hasilkg'].'</td>';
            echo '<td align=right>'.number_format($d['hasilpanen']).'</td>';
            echo '<td align=right>'.$d['lebihbasiskg'].'</td>';
            echo '<td align=right>'.number_format($d['rupiah']).'</td>';
            echo '<td align=right>'.number_format($d['premirajin']).'</td>';
            echo '<td align=right>'.number_format($d['premihadir']).'</td>';
            echo '<td align=right>'.number_format($d['brondolanperkg']).'</td>';
            echo "<td align=center>\r\n\t\t\t<img src=images/application/application_edit.png class=resicon  caption='Edit' ".
            "onclick=\"fillField('".$d['id']."','".$d['kodeorg']."','".$d['hasilkg']."','".$d['lebihbasiskg']."','".$d['rupiah']."','".$d['premirajin']."','".$d['tahuntanam']."','".$d['bulanawal']."','".$d['bulanakhir']."','".$d['premihadir']."','".$d['brondolanperkg']."','".$d['hasilpanen']."');\">\r\n\t\t\t<!--<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['id']."');\">--></td>";
            echo '</tr>';
        }
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".kebun_5premipanen where id='".$id."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>
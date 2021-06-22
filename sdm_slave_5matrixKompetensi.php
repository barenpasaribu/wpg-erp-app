<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/zMysql.php';
require_once 'lib/fpdf.php';
$jabatan = $_POST['jabatan'];
$jenis = $_POST['jenis'];
$item = $_POST['item'];
$method = $_POST['method'];
$nourut = $_POST['nourut'];
$kompetensi = $_POST['kompetensi'];
$perilaku = $_POST['perilaku'];
if ('' == $method) {
    $method = $_GET['method'];
    $jabatan = $_GET['jabatan'];
    $jenis = $_GET['jenis'];
    $item = $_GET['item'];
    $item2 = $_GET['item2'];
    $nourut = $_GET['nourut'];
    $kompetensi = $_GET['kompetensi'];
    $perilaku = $_GET['perilaku'];
}

$optJabat = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan order by kodejabatan asc';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    if ($rJabat['kodejabatan'] == $jabatan2) {
        $pilih = ' selected';
    } else {
        $pilih = '';
    }

    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
    $optJabat .= "<option value='".$rJabat['kodejabatan']."'".$pilih.'>'.$rJabat['namajabatan'].'</option>';
}
$arrItem = getEnum($dbname, 'sdm_5matrikkompetensi', 'item');
$optItem = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
foreach ($arrItem as $kei => $fal) {
    $optItem .= "<option value='".$kei."'>".$fal.'</option>';
}
switch ($method) {
    case 'updatedetail':
        $str = 'update '.$dbname.".sdm_5matrikkompetensi set nourut='".$nourut."', tingkatkompetensi='".$kompetensi."', prilaku='".$perilaku."'\r\n        where kodejabatan='".$jabatan."' and jenis='".$jenis."' and item='".$item."' and nourut='".$nourut."'";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    case 'insertdetail':
        $str = 'insert into '.$dbname.".sdm_5matrikkompetensi (kodejabatan,jenis,item,nourut,tingkatkompetensi,prilaku)\r\n        values('".$jabatan."','".$jenis."','".$item."','".$nourut."','".$kompetensi."','".$perilaku."')";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    case 'delete':
        $str = 'delete from '.$dbname.".sdm_5matrikkompetensi\r\n    where kodejabatan='".$jabatan."' and jenis='".$jenis."' and item='".$item."' and nourut='".$nourut."'";
        if (mysql_query($str)) {
            break;
        }

        echo ' Gagal, '.addslashes(mysql_error($conn));
        exit();
    case 'pdf':

class PDF extends FPDF
{
    public function Header()
    {
        global $jabatan;
        global $jenis;
        global $item;
        global $kamusJabat;
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(190, 6, strtoupper('Value '.$jenis.': '.$kamusJabat[$jabatan]), 0, 1, 'L');
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(190, 6, strtoupper($item), 0, 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        $this->Cell(10, 6, $_SESSION['lang']['nourut'], 1, 0, 'C');
        $this->Cell(60, 6, $_SESSION['lang']['kompetensi'], 1, 0, 'C');
        $this->Cell(120, 6, $_SESSION['lang']['perilaku'], 1, 0, 'C');
        $this->Ln();
    }
}

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $str1 = 'select * from '.$dbname.".sdm_5matrikkompetensi where kodejabatan like '%".$jabatan."%' and jenis like '%".$jenis."%' and item like '%".$item."%' order by kodejabatan, jenis, item, nourut";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $pdf->Cell(10, 6, $bar1->nourut, T, 0, 'R');
            $pdf->Cell(60, 6, $bar1->tingkatkompetensi, T, 0, 'L');
            $pdf->MultiCell(120, 6, $bar1->prilaku, T, 'L', false);
        }
        $pdf->Output();
        exit();
    default:
        break;
}
echo "<table><tr>\r\n        <td>Item</td>\r\n        <td><select id=item2 onchange=pilihitem()>".$optItem."</select> <img class=\"resicon\" src=\"images/pdf.jpg\" title=\"PDF\" onclick=\"lihatpdf(event,'sdm_slave_5matrixKompetensi.php')\"></td>\r\n    </tr></table>";
$limit = 20;
$page = 0;
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$ql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_5matrikkompetensi where kodejabatan like '%".$jabatan."%' and jenis like '%".$jenis."%' and item like '%".$item."%' order by kodejabatan, jenis, item, nourut";
$query2 = mysql_query($ql2);
while ($jsl = mysql_fetch_object($query2)) {
    $jlhbrs = $jsl->jmlhrow;
}
$str1 = 'select * from '.$dbname.".sdm_5matrikkompetensi where kodejabatan like '%".$jabatan."%' and jenis like '%".$jenis."%' and item like '%".$item."%' order by kodejabatan, jenis, item, nourut limit ".$offset.','.$limit.'';
$res1 = mysql_query($str1);
echo "<table class=sortable cellspacing=1 border=0 style='width:500px;'>\r\n     <thead>\r\n     <tr class=rowheader>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['jenis']."</td>\r\n        <td>Item</td>\r\n        <td>".$_SESSION['lang']['urutan']."</td>\r\n        <td>".$_SESSION['lang']['kompetensi']."</td>\r\n        <td width=100>".$_SESSION['lang']['action']."</td>\r\n     </tr></thead>\r\n     <tbody>";
$no = 0;
while ($bar1 = mysql_fetch_object($res1)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n        <td>".$kamusJabat[$bar1->kodejabatan]."</td>\r\n        <td>".$bar1->jenis."</td>\r\n        <td>".$bar1->item."</td>\r\n        <td align=right>".$bar1->nourut."</td>\r\n        <td>".$bar1->tingkatkompetensi."</td>\r\n        <td align=center>\r\n            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->kodejabatan."','".$bar1->jenis."','".$bar1->item."','".$bar1->nourut."','".$bar1->tingkatkompetensi."','".str_replace("\n", '\\n', $bar1->prilaku)."');\">\r\n            <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"hapus('".$bar1->kodejabatan."','".$bar1->jenis."','".$bar1->item."','".$bar1->nourut."');\">\r\n        </td>\r\n    </tr>";
}
echo "<tr class=rowheader><td colspan=11 align=center>\r\n".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n<br />\r\n<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n</td>\r\n</tr>";
echo "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";

?>
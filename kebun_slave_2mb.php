<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$kdorg = $_POST['kdorg'];
$kdkeg = $_POST['kdkeg'];
$per = $_POST['per'];
$kdbarang = $_POST['kdbarang'];
if ('excel' === $proses) {
    $kdorg = $_GET['kdorg'];
    $kdkeg = $_GET['kdkeg'];
    $per = $_GET['per'];
    $kdbarang = $_GET['kdbarang'];
}

$nmbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$satuanbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
if ('excel' === $proses) {
    $border = 'border=1';
    $bgcol = 'bgcolor=#CCCCCC ';
}

if ('' === $kdorg) {
    $kdorg = "and kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'";
} else {
    $kdorg = "and kodeorg like '%".$kdorg."%'";
}

if ('' === $kdbarang) {
    $kdbarang = '';
} else {
    $kdbarang = "and a.kodebarang='".$kdbarang."'";
}

if ('' === $kdkeg) {
    $kdkeg = '';
} else {
    $kdkeg = " and kodekegiatan='".$kdkeg."'";
}

$stream = '<table class=sortable cellspacing=1 '.$border.' cellpadding=0>';
$stream .= "<thead class=rowheader>\r\n                 <tr class=rowheader>\r\n\t\t\t\t \t<td align=center>No</td>\r\n\t\t\t\t\t<td align=center>Blok</td>\r\n\t\t\t\t\t<td align=center>Luas (HA)</td>\r\n\t\t\t\t\t<td align=center>Kode Barang</td>\r\n\t\t\t\t\t<td align=center>Nama Barang</td>\r\n\t\t\t\t\t<td align=center>Jumlah Barang</td>\r\n  \t\t\t\t</tr></thead>";
$sql = "SELECT sum(kwantitas) as kwantitas,sum(kwantitasha) as kwantitasha,a.kodebarang,namabarang,kodeorg,kodekegiatan\r\nFROM ".$dbname.'.kebun_pakai_material_vw a left join '.$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang\r\nwhere tanggal like '%".$per."%' ".$kdorg.' '.$kdkeg.' '.$kdbarang.'   group by kodeorg,a.kodebarang';
$qry = mysql_query($sql) || exit('SQL ERR : '.mysql_error());
while ($bar = mysql_fetch_assoc($qry)) {
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n\t\t<td>".$no."</td>\r\n\t\t<td>".$bar['kodeorg']."</td>\r\n\t\t<td>".$bar['kwantitasha']."</td>\r\n\t\t<td>".$bar['kodebarang']."</td>\r\n\t\t<td>".$nmbarang[$bar['kodebarang']]."</td>\r\n\t\t<td>".$bar['kwantitas']."</td>\r\n\t\t</tr>";
}
$stream .= '<tbody></table>';
switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $tglSkrg = date('Ymd');
        $nop_ = 'laporan_material_perblok'.$tglSkrg;
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
}
echo "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";

?>
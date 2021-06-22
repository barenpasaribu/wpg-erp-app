<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
('' === $_POST['kodeorg1'] ? ($kodeorg1 = $_GET['kodeorg1']) : ($kodeorg1 = $_POST['kodeorg1']));
('' === $_POST['tanggal1'] ? ($tanggal1 = $_GET['tanggal1']) : ($tanggal1 = $_POST['tanggal1']));
if ('' === $kodeorg1) {
    echo 'warning: Pabrik Tidak Boleh Kosong';
    exit();
}

if ('' === $tanggal1) {
    echo 'warning: Tanggal Tidak Boleh Kosong';
    exit();
}

$tanggal = explode('-', $tanggal1);
$tanggal1x = $tanggal[2].'-'.$tanggal[1].'-'.$tanggal[0];
switch ($proses) {
    case 'preview':
        echo "<table class=sortable cellspacing=1 border=0>\r\n\t\t<thead><tr class=rowheader>\r\n\t\t<td>".$_SESSION['lang']['produk']."</td>\r\n\t\t<td>".$_SESSION['lang']['saldoawal']."</td>\r\n\t\t<td>".$_SESSION['lang']['produksi']."</td>\r\n\t\t<td>".$_SESSION['lang']['pengiriman']."</td><td>".'Return'."</td>\r\n\t\t<td>".$_SESSION['lang']['sisa']."</td>\r\n\t\t</tr></thead><tbody>";
        $sql = 'select sum(kuantitas) as kuantitas from '.$dbname.".pabrik_masukkeluartangki where kodeorg = '".$kodeorg1."' and tanggal like '".$tanggal1x."%'";
        $query = mysql_query($sql);
        while ($res = mysql_fetch_assoc($query)) {
            $z = $res['kuantitas'];
        }
        $sql = 'select cpo_produksi,cpo_opening_stock, cpo_closing_stock,
						pengiriman_return_cpo, pengiriman_despatch_cpo,
						kernel_produksi, kernel_closing_stock, kernel_opening_stock,
						pengiriman_return_pk, pengiriman_despatch_pk 
					from '.$dbname.".pabrik_produksi where kodeorg LIKE '".$kodeorg1."%' and tanggal = '".$tanggal1x."'";
		//echo $sql;
        $query = mysql_query($sql);
        while ($res = mysql_fetch_assoc($query)) {
            $cpo_produksi = $res['cpo_produksi'];
			$cpo_opening_stock = $res['cpo_opening_stock'];
			$cpo_closing_stock = $res['cpo_closing_stock'];
			$pengiriman_return_cpo = $res['pengiriman_return_cpo'];
			$pengiriman_despatch_cpo = $res['pengiriman_despatch_cpo'];
			$kernel_produksi = $res['kernel_produksi'];
			$kernel_closing_stock = $res['kernel_closing_stock'];
			$kernel_opening_stock = $res['kernel_opening_stock'];
			$pengiriman_return_pk = $res['pengiriman_return_pk'];
			$pengiriman_despatch_pk = $res['pengiriman_despatch_pk'];
        }
        echo '<tr class=rowcontent><td>CPO</td>';
        echo '<td align=right>'.number_format($cpo_opening_stock, 0).'</td>';
        echo '<td align=right>'.number_format($cpo_produksi, 0).'</td>';
        echo "<td align=right style=cursor:pointer onclick=viewDetail('".$kodeorg1."','".$tanggal1x."','40000001',event) title='".$_SESSION['lang']['detailPengiriman']."'>".number_format($pengiriman_despatch_cpo, 0).'</td>';
        echo '<td align=right>'.number_format($pengiriman_return_cp, 0).'</td>';
        echo '<td align=right>'.number_format($cpo_closing_stock, 0).'</td>';
		
        echo '</tr><tr class=rowcontent><td>Kernel</td>';
        echo '<td align=right>'.number_format($kernel_opening_stock, 0).'</td>';
        echo '<td align=right>'.number_format($kernel_produksi, 0).'</td>';
        echo "<td align=right style=cursor:pointer onclick=viewDetail('".$kodeorg1."','".$tanggal1x."','40000002',event) title='".$_SESSION['lang']['detailPengiriman']."'>".number_format($pengiriman_despatch_pk, 0).'</td>';
        echo '<td align=right>'.number_format($pengiriman_return_pk, 0).'</td>';
        echo '<td align=right>'.number_format($kernel_closing_stock, 0).'</td>';
        echo '</tr></tbody></table>';

        break;
    case 'getTangki':
        $sGet = 'select kodetangki,keterangan from '.$dbname.".pabrik_5tangki where kodeorg='".$kdPbrik."'";
        $qGet = mysql_query($sGet);
        while ($rGet = mysql_fetch_assoc($qGet)) {
            $optTangki .= '<option value='.$rGet['kodetangki'].'>'.$rGet['keterangan'].'</option>';
        }
        echo $optTangki;

        break;
    default:
        break;
}

?>
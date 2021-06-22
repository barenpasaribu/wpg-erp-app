<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
include_once 'lib/rTable.php';

$param = $_POST;
$kodeorg=$param['kodeorg'];
$pt=substr($param['kodeorg'], 0,3);
//$param['periode']='2020-08';
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select tanggalmulai,tanggalsampai,periode from '.$dbname.".setup_periodeakuntansi 
		where kodeorg ='".$kodeorg."' and periode='".$param['periode']."'";

$res = mysql_query($str);

while ($bar = mysql_fetch_array($res)) {
    $tgsampai = $bar['tanggalsampai'];
    $tgmulai = $bar['tanggalmulai'];
    $periode = $bar['periode'];

    $tgl_saldoawal=date('Y-m-d', strtotime('-3 days', strtotime($tgmulai)));
}

if ($tgmulai == '' || $tgsampai == '') {
    exit('Error: Accounting period is not registered');
}

$str = 'select * from ' . $dbname . ".flag_alokasi where kodeorg='" .$param['kodeorg']. "' and periode='" . $param['periode'] . "' AND tipe='HPP' ";
$res = mysql_query($str);

if (mysql_num_rows($res) < 1) {
echo "<button  onclick=prosesJurnalHPP() id=btnproses>Proses</button>";
} else {
echo "<i>Proses jurnal sudah dilakukan</i>";	
}

echo "<table class=sortable cellspacing=1 border=0>
<thead><tr class=rowheader>
<td> PERIODE </td><td> KETERANGAN </td><td> HARGA </td><td> KGS </td><td> @Rp/KGS </td>
</tr></thead><tbody>";

$str = 'select tbs_sisa_kemarin from '. $dbname .".pabrik_produksi where kodeorg like '".$pt."%' and tanggal>='".$tgmulai. "' and tanggal<='".$tgsampai. "' order by tanggal ASC limit 1";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$saldo_awaltbs=$has['tbs_sisa_kemarin'];


$str = 'select round(avg(harga_akhir),2) as hargasaldoawal from '. $dbname .".log_supplier_harga_history a inner join log_5klsupplier b on left(a.kode_supplier,4)=b.kode where kelompok like '%".$pt."%' and tanggal_akhir>='".$tgl_saldoawal."' and tanggal_akhir<'".$tgmulai."' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_saldoawaltbs=$has['hargasaldoawal'];

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Saldo Awal TBS</td>
			<td align=right>".number_format($harga_saldoawaltbs*$saldo_awaltbs,2)."</td>
			<td align=right>".number_format($saldo_awaltbs)."</td>
			<td align=right>".number_format($harga_saldoawaltbs,2)."</td></tr>";


$str = 'select sum(tbs_masuk_netto) as tbs_masuk_netto from '. $dbname .".pabrik_produksi where kodeorg like '".$pt."%' and tanggal>='".$tgmulai. "' and tanggal<='".$tgsampai. "' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$jmlhpembelian_tbs=$has['tbs_masuk_netto'];

$str = 'select SUM(beratnormal*harga_akhir) as nilaihargapembelian from '. $dbname .".pabrik_timbangan a inner join log_supplier_harga_history b on a.kodecustomer=b.kode_supplier and left(a.tanggal,10)=b.tanggal_akhir where  kodebarang='40000003' AND millcode like '".$pt."%' and left(a.tanggal,10)>='".$tgmulai."' and left(a.tanggal,10)<='".$tgsampai."' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$nilaiharga_pembeliantbs=$has['nilaihargapembelian'];

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Pembelian TBS</td>
			<td align=right>".number_format($nilaiharga_pembeliantbs,2)."</td>
			<td align=right>".number_format($jmlhpembelian_tbs)."</td>
			<td align=right>".number_format($nilaiharga_pembeliantbs/$jmlhpembelian_tbs,2)."</td></tr>";
$hargatbstersedia=round(($harga_saldoawaltbs*$saldo_awaltbs)+($nilaiharga_pembeliantbs),2);
echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>TBS Tersedia Diolah</td>
			<td align=right>".number_format($hargatbstersedia,2)."</td>
			<td align=right>".number_format($saldo_awaltbs+$jmlhpembelian_tbs)."</td>
			<td align=right>".number_format((($harga_saldoawaltbs*$saldo_awaltbs)+($nilaiharga_pembeliantbs))/($saldo_awaltbs+$jmlhpembelian_tbs),2)."</td></tr>";

$str = 'select tbs_sisa from '. $dbname .".pabrik_produksi where kodeorg like '".$pt."%' and tanggal>='".$tgmulai. "' and tanggal<='".$tgsampai. "' order by tanggal DESC limit 1";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$saldo_akhirtbs=$has['tbs_sisa'];

$str = 'select round(avg(harga_akhir),2) as hargasaldoakhir from '. $dbname .".log_supplier_harga_history a inner join log_5klsupplier b on left(a.kode_supplier,4)=b.kode where kelompok like '%".$pt."%' and tanggal_akhir='".$tgsampai."' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_saldoakhirtbs=$has['hargasaldoakhir'];
$hargasaldoakhir=round($harga_saldoakhirtbs*$saldo_akhirtbs,2);
echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Saldo Akhir TBS</td>
			<td align=right>".number_format($hargasaldoakhir,2)."</td>
			<td align=right>".number_format($saldo_akhirtbs)."</td>
			<td align=right>".number_format($harga_saldoakhirtbs,2)."</td></tr>";

$str = 'select sum(tbs_after_grading) as tbs_after_grading from '. $dbname .".pabrik_produksi where kodeorg like '".$pt."%' and tanggal>='".$tgmulai. "' and tanggal<='".$tgsampai. "' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$jmlhpemakaian_tbs=$has['tbs_after_grading'];
/*
$str = 'select round(avg(harga_akhir),2) as hargarata from '. $dbname .".log_supplier_harga_history a inner join log_5klsupplier b on left(a.kode_supplier,4)=b.kode where kelompok like '%".$pt."%' and tanggal_akhir>='".$tgmulai."' and tanggal_akhir<'".$tgsampai."' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_ratatbs=$has['hargarata'];
*/
$harga_jmlpemakaian=$hargatbstersedia-$hargasaldoakhir;
echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Jumlah Pemakaian TBS</td>
			<td align=right>".number_format($harga_jmlpemakaian,2)." <input type=hidden id='hargapemakaiantbs' value='".$harga_jmlpemakaian."'></td>
			<td align=right>".number_format($jmlhpemakaian_tbs)."</td>
			<td align=right>".number_format($harga_jmlpemakaian/$jmlhpemakaian_tbs,2)."</td></tr>";

$str = 'select * from '. $dbname .".keu_5parameterjurnal where jurnalid='HPP1' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$noakun1=$has['noakundebet'];
$noakunsampai1=$has['sampaidebet'];
$noakun2=$has['noakunkredit'];
$noakunsampai2=$has['sampaikredit'];


$str = 'select round(sum(jumlah),2) as jumlah from '. $dbname .".keu_jurnaldt where tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' and kodeorg like '".$pt."%' and ((noakun between '".$noakun1."' and '".$noakunsampai1."')
OR (noakun between '".$noakun2."' and '".$noakunsampai2."'))";
saveLog($str);
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$biaya_pengolahan=$has['jumlah'];

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Jumlah Biaya Pengolahan</td>
			<td align=right>".number_format($biaya_pengolahan,2)."</td>
			<td align=right></td>
			<td align=right></td></tr>";

$hpp=$harga_jmlpemakaian+$biaya_pengolahan;
echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>TOTAL HARGA POKOK PRODUKSI</td>
			<td align=right>".number_format($hpp,2)."</td>
			<td align=right></td>
			<td align=right></td></tr>";

echo "<thead><tr class=rowheader>
			<td> PERIODE </td><td> KETERANGAN </td><td> CPO </td><td> PK </td></tr>
			</thead><tbody>";

$str = 'select sum(cpo_produksi) as cpo, sum(kernel_produksi) as kernel  from '. $dbname .".pabrik_produksi where kodeorg like '".$pt."%' and tanggal>='".$tgmulai. "' and tanggal<='".$tgsampai. "' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$qty_cpo=$has['cpo'];
$qty_kernel=$has['kernel'];

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Quantity Produksi Periode Berjalan (Kgs)</td>
			<td align=right>".number_format($qty_cpo)."</td>
			<td align=right>".number_format($qty_kernel)."</td>
			<td align=right></td></tr>";

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Rendemen</td>
			<td align=right>".number_format($qty_cpo/$jmlhpemakaian_tbs*100,2)." %</td>
			<td align=right>".number_format($qty_kernel/$jmlhpemakaian_tbs*100,2)." %</td>
			<td align=right></td></tr>";

$str = 'select round(avg(hargasatuan),2) as hargasatuan from '. $dbname .".pmn_kontrakjual  where kodeorg like '%".$pt."%' and tanggalkontrak>='".$tgmulai."' and tanggalkontrak<'".$tgsampai."' and kodebarang='40000001'";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_cpo=$has['hargasatuan'];

$str = 'select round(avg(hargasatuan),2) as hargasatuan from '. $dbname .".pmn_kontrakjual  where kodeorg like '%".$pt."%' and tanggalkontrak>='".$tgmulai."' and tanggalkontrak<'".$tgsampai."' and kodebarang='40000002'";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_kernel=$has['hargasatuan'];

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Harga Pasar</td>
			<td align=right>".number_format($harga_cpo,2)."</td>
			<td align=right>".number_format($harga_kernel,2)."</td>
			<td align=right></td></tr>";

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Penilaian Produksi Berdasarkan Harga Pasar</td>
			<td align=right>".number_format($qty_cpo*$harga_cpo,2)."</td>
			<td align=right>".number_format($qty_kernel*$harga_kernel,2)."</td>
			<td align=right></td></tr>";
$persen_cpo=round(($qty_cpo*$harga_cpo)/(($qty_cpo*$harga_cpo)+($qty_kernel*$harga_kernel))*100,2);
$persen_kernel=round(($qty_kernel*$harga_kernel)/(($qty_cpo*$harga_cpo)+($qty_kernel*$harga_kernel))*100,2);
echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>Persentase Alokasi Biaya Produksi</td>
			<td align=right>".number_format($persen_cpo,2)." %</td>
			<td align=right>".number_format($persen_kernel,2)." %</td>
			<td align=right></td></tr>";

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td> Porsi Biaya Gabungan (Aktual Cost)</td>
			<td align=right>".number_format($persen_cpo*$hpp/100,2)." </td>
			<td align=right>".number_format($persen_kernel*$hpp/100,2)." </td>
			<td align=right></td></tr>";
$hppCPO=$persen_cpo*$hpp/$qty_cpo/100;
$hppPK=$persen_kernel*$hpp/$qty_kernel/100;
echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td> Harga Pokok Produksi Sekarang / Kgs</td>
			<td align=right>".number_format($hppCPO,2)." </td>
			<td align=right>".number_format($hppPK,2)." </td>
			<td align=right></td></tr>";

echo "</table></td>";

echo "<br><br>
<table class=sortable cellspacing=1 border=0>
<thead><tr class=rowheader>
<td> PERIODE </td><td> PRODUK </td><td> KGS </td><td> HARGA </td><td> JUMLAH </td>
</tr></thead><tbody>";
echo "<tr class=rowcontent>
			<td colspan=5>Persediaan Awal</td></tr>";

$str = 'select cpo_opening_stock, kernel_opening_stock from '. $dbname .".pabrik_produksi where kodeorg like '".$pt."%' and tanggal='".$tgmulai. "' order by tanggal ASC limit 1";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$saldo_awalCPO=$has['cpo_opening_stock'];
$saldo_awalPK=$has['kernel_opening_stock'];

$str = 'select round(avg(hargasatuan),2) as hargasaldoawal from '. $dbname .".pmn_kontrakjual where kodept='".$pt."' and tanggalkontrak>='".$tgl_saldoawal."' and tanggalkontrak<'".$tgmulai."'  AND kodebarang='40000001' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_saldoawalCPO=$has['hargasaldoawal'];

$str = 'select round(avg(hargasatuan),2) as hargasaldoawal from '. $dbname .".pmn_kontrakjual where kodept='".$pt."' and tanggalkontrak>='".$tgl_saldoawal."' and tanggalkontrak<'".$tgmulai."'  AND kodebarang='40000002' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_saldoawalPK=$has['hargasaldoawal'];

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>CPO</td>
			<td align=right>".number_format($saldo_awalCPO,2)."</td>
			<td align=right>".number_format($harga_saldoawalCPO,2)."</td>
			<td align=right>".number_format($saldo_awalCPO*$harga_saldoawalCPO,2)."</td></tr>";

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>PK</td>
			<td align=right>".number_format($saldo_awalPK,2)."</td>
			<td align=right>".number_format($harga_saldoawalPK,2)."</td>
			<td align=right>".number_format($saldo_awalPK*$harga_saldoawalPK,2)."</td></tr>";

echo "<tr class=rowcontent>
			<td colspan=5></td></tr>";
echo "<tr class=rowcontent>
			<td colspan=5> PRODUKSI </td></tr>";
$str = 'select SUM(cpo_produksi) as cpo_produksi, SUM(kernel_produksi) as kernel_produksi from '. $dbname .".pabrik_produksi where kodeorg like '".$pt."%' and tanggal>='".$tgmulai."' AND tanggal<='".$tgsampai."' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$produksiCPO=$has['cpo_produksi'];
$produksiPK=$has['kernel_produksi'];

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>CPO</td>
			<td align=right>".number_format($produksiCPO,2)." </td>
			<td align=right>".number_format($hppCPO,2)."</td>
			<td align=right>".number_format($produksiCPO*$hppCPO,2)." <input type='hidden' id=produksicpo value='".round($produksiCPO*$hppCPO,2)."' ></td></tr>";

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>PK</td>
			<td align=right>".number_format($produksiPK,2)."</td>
			<td align=right>".number_format($hppPK,2)."</td>
			<td align=right>".number_format($produksiPK*$hppPK,2)." <input type='hidden' id=produksipk value='".round($produksiPK*$hppPK,2)."'></td></tr>";

echo "<tr class=rowcontent>
			<td colspan=5></td></tr>";
echo "<tr class=rowcontent>
			<td colspan=5> PENJUALAN </td></tr>";
$str = 'select SUM(pengiriman_despatch_cpo) as pengiriman_despatch_cpo, SUM(pengiriman_despatch_pk) as pengiriman_despatch_pk  from '. $dbname .".pabrik_produksi where kodeorg like '".$pt."%' and tanggal>='".$tgmulai."' AND tanggal<='".$tgsampai."' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$penjualanCPO=$has['pengiriman_despatch_cpo'];
$penjualanPK=$has['pengiriman_despatch_pk'];
/*
$str = 'select round(avg(hargasatuan),2) as hargacpo from '. $dbname .".pmn_kontrakjual where kodept='".$pt."' and tanggalkontrak>='".$tgmulai."' and tanggalkontrak<='".$tgsampai."'  AND kodebarang='40000001' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_CPO=$has['hargacpo'];

$str = 'select round(avg(hargasatuan),2) as hargapk from '. $dbname .".pmn_kontrakjual where kodept='".$pt."' and tanggalkontrak>='".$tgmulai."' and tanggalkontrak<='".$tgsampai."'  AND kodebarang='40000002' ";
$res = mysql_query($str);
$has=mysql_fetch_assoc($res);
$harga_PK=$has['hargapk'];
*/

$harga_CPO=round((($saldo_awalCPO*$harga_saldoawalCPO)+($produksiCPO*$hppCPO))/($saldo_awalCPO+$produksiCPO),2);
$harga_PK=round((($saldo_awalPK*$harga_saldoawalPK)+($produksiPK*$hppPK))/($saldo_awalPK+$produksiPK),2);
echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>CPO</td>
			<td align=right>".number_format($penjualanCPO,2)."</td>
			<td align=right>".number_format($harga_CPO,2)."</td>
			<td align=right>".number_format($penjualanCPO*$harga_CPO,2)." <input type='hidden' id=penjualancpo value='".round($penjualanCPO*$harga_CPO,2)."'></td></tr>";

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>PK</td>
			<td align=right>".number_format($penjualanPK,2)."</td>
			<td align=right>".number_format($harga_PK,2)."</td>
			<td align=right>".number_format($penjualanPK*$harga_PK,2)." <input type='hidden' id=penjualanpk value='".round($penjualanPK*$harga_PK,2)."'></td></tr>";

echo "<tr class=rowcontent>
			<td colspan=5></td></tr>";
echo "<tr class=rowcontent>
			<td colspan=5> PERSEDIAAN AKHIR </td></tr>";
$saldo_akhirCPO=$saldo_awalCPO+$produksiCPO-$penjualanCPO;
$saldo_akhirPK=$saldo_awalPK+$produksiPK-$penjualanPK;
$nilaisaldo_akhirCPO=round(($saldo_awalCPO*$harga_saldoawalCPO),2)+round(($produksiCPO*$hppCPO),2)-round($penjualanCPO*$harga_CPO,2);
$nilaisaldo_akhirPK=round(($saldo_awalPK*$harga_saldoawalPK),2)+round(($produksiPK*$hppPK),2)-round($penjualanPK*$harga_PK,2);
echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>CPO</td>
			<td align=right>".number_format($saldo_akhirCPO,2)."</td>
			<td align=right>".number_format($nilaisaldo_akhirCPO/$saldo_akhirCPO,2)."</td>
			<td align=right>".number_format($nilaisaldo_akhirCPO,2)."</td></tr>";

echo "<tr class=rowcontent>
			<td>".$periode."</td>
			<td>PK</td>
			<td align=right>".number_format($saldo_akhirPK,2)."</td>
			<td align=right>".number_format($nilaisaldo_akhirPK/$saldo_akhirPK,2)."</td>
			<td align=right>".number_format($nilaisaldo_akhirPK,2)."</td></tr>";

echo "</table></td>";

$a="select * from pabrik_timbangan a left join log_5supplier b on a.kodecustomer=b.supplierid left join pmn_4customer c ON a.kodecustomer=c.kodecustomer where IsPosting='0' and tanggal between '".$tgmulai."' and '".$tgsampai."' and millcode like '".$pt."%'";
$b = mysql_query($a);
$d=mysql_num_rows($b);
if($d>0){
echo "<br>";
echo "<table class=sortable cellspacing=1 border=0>
<thead><tr class=rowheader><td>No Tiket</td><td>Tanggal</td><td>No.SPB/No.SIPB</td><td>Nama Buyer/ Nama Supplier</td></tr></thead><tbody>";

while ($c = mysql_fetch_array($b)) {
   echo "<tr class=rowcontent>
			<td>".$c['notransaksi']."</td>
			<td>".$c['tanggal']."</td>
			<td>".$c['nospb']." ".$c['nosipb']."</td>
			<td>".$c['namacustomer']." ".$c['namasupplier']."</td></tr>";
}
}
?>
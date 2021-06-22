<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
$pt = $_POST['pt'];
$gudang = $_POST['gudang'];
$periode = $_POST['periode'];

if ($periode == '') {
	echo 'Error: Please choose Periode.';
	exit();
}

$arrBarang = array();
$arrAwal = array();
$kamussatuan = array();
$kamusnamabarang = array();

$str = "select l.kodebarang,l.namabarang,l.satuan, sum(saldoawalqty) as saldoawalqty , sum(nilaisaldoawal) as nilaisaldoawal, ".
"sum(qtymasuk) as qtymasuk, sum(qtykeluar) as qtykeluar, sum(qtymasukxharga) as qtymasukxharga,".
"sum(qtykeluarxharga) as qtykeluarxharga ".
"FROM  $dbname.log_5saldobulanan  s ".
"inner join $dbname.log_5masterbarang l on l.kodebarang=s.kodebarang ";
if ($gudang == '') {
	$str .=
		"where s.kodeorg='". $pt ."' ".//(select induk from organisasi where kodeorganisasi='". $pt ."') ".
		"and s.periode =  '" . $periode . "' group by l.kodebarang order by l.kodebarang";
}
else {
	$str .=
		"where s.kodeorg='". $pt ."' ".//(select induk from organisasi where kodeorganisasi='". $pt ."') ".
		"and s.kodegudang = '" . $gudang . "' " .
		"and s.periode =  '" . $periode . "' group by l.kodebarang order by l.kodebarang";
}
echo '<table>';
$definedVar=array(
	"periode"=>$periode,
);
function onScrollDB($row,$funcVar,&$definedVar){
	$hargamasuk = 0;
	$hargakeluar = 0;
	@$hargamasuk = $row['qtymasukxharga'] / $row['qtymasuk'];
	@$hargakeluar = $row['qtykeluarxharga'] / $row['qtykeluar'];
	@$salakqty = ($row['saldoawalqty'] + $row['qtymasuk']) - $row['qtykeluar'];
	@$salakrp = ($row['nilaisaldoawal'] + $row['qtymasukxharga']) - $row['qtykeluarxharga'];
	@$salakhar = $salakrp / $salakqty;
	echo "<tr class=rowcontent>" .
		"<td>" . $funcVar['linenumber'] . "</td>" .
		"<td>" . $definedVar['periode'] . "</td>".
		"<td>" . $row['kode'] . "</td>".
		"<td>" . $row['namabarang'] . "</td>" .
		"<td>" . $row['satuan'] . "</td>" .
		"<td align=right class=firsttd>" . number_format($row["saldoawalqty"], 2) . "</td>" .
		"<td align=right>" . number_format($row["hargaratasaldoawal"], 2) . "</td>" .
		"<td align=right>" . number_format($row["nilaisaldoawal"], 2) . "</td>" .
		"<td align=right class=firsttd>" . number_format($row["qtymasuk"], 2) . "</td>" .
		"<td align=right>" . number_format($hargamasuk, 2) . "</td>" .
		"<td align=right>" . number_format($row["qtymasukxharga"], 2) . "</td>" .
		"<td align=right class=firsttd>" . number_format($row["qtykeluar"], 2) . "</td>" .
		"<td align=right>" . number_format($hargakeluar, 2) . "</td>" .
		"<td align=right>" . number_format($row["qtykeluarxharga"], 2) . "</td>" .
		"<td align=right class=firsttd>" . number_format($salakqty, 2) . "</td>" .
		"<td align=right>" . number_format($salakhar, 2) . "</td>" .
		"<td align=right>" . number_format($salakrp, 2) . "</td>" .
		"</tr>";
}

eventOnScrollDB($str,$definedVar,'onScrollDB');

if (empty($arrBarang)) {
	echo '<tr class=rowcontent>' . "\r\n" . '        <td colspan=17>no data.</td>' . "\r\n" . '    </tr>';
}

echo '</table>';

?>

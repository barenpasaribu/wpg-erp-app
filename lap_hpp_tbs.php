<?php

$param=$_POST;
if (sizeof($param)==0)  $param=$_GET;
$proses = $_GET['proses'];
$caption = $param['caption'];
function createHeaderTable($param,$periode,$border=0){
	$table =
		"<table cellspacing=1 cellpadding=1 border=".$border." class=sortable>
				<thead class=rowheader>
					<tr  >
						<td colspan='3' >Nama Akun</td>
						<td >$periode</td>
						<td> YTD</td> ";

	$table .= " </tr>
 </thead>";
	return $table;
}

switch ( $proses) {
	case "detailunit":
		include_once 'lib/zLib.php';
		include_once 'lib/devLibrary.php';
		$target = ($param['target'] == '' ? 'unit' : $param['target']);
		$unit = ($param['id'] == '' ? '' : "where kodeorg like '%" . $param['id'] . "%'");
		$sqlAfd = '';
		$sqlTT = '';
		$sqlB = '';
		$sqlP = '';
		if ($target == 'unit') {
			$sqlAfd = "select kodeorganisasi,namaorganisasi from $dbname.organisasi " .
				($param['id'] == '' ? '' : " where kodeorganisasi like '%" . $param['id'] . "%'") . " and length(kodeorganisasi)=6 and tipe in ('AFDELING','BIBITAN') " .
				"order by namaorganisasi ";
		} else if ($target == 'afdeling') {
			$sqlB = "select distinct k.kodeorg,o.namaorganisasi from kebun_prestasi k " .
				"inner join organisasi o on o.kodeorganisasi = k.kodeorg " .
				"$unit order by k.kodeorg";
		} else {
			$sqlTT = "select distinct tahuntanam from kebun_prestasi $unit order by tahuntanam";
			$sqlP = "select distinct SUBSTRING(notransaksi,1,6) periode from kebun_prestasi $unit order by SUBSTRING(notransaksi,1,6)";
		}
		$result = array();
		$afdeling = makeOption2( $sqlAfd,
			array("valueinit" => '', "captioninit" => "Seluruhnya"),
			array("valuefield" => 'kodeorganisasi', "captionfield" => 'namaorganisasi')
		);
		$tahuntanam = makeOption2( $sqlTT,
			array("valueinit" => '', "captioninit" => "Seluruhnya"),
			array("valuefield" => 'tahuntanam', "captionfield" => 'tahuntanam')
		);
		$periode = makeOption2($sqlP,
			array("valueinit" => '', "captioninit" => "Seluruhnya"),
			array("valuefield" => 'periode', "captionfield" => 'periode')
		);
		$blok = makeOption2( $sqlB,
			array("valueinit" => '', "captioninit" => "Seluruhnya"),
			array("valuefield" => 'kodeorg', "captionfield" => 'kodeorg')
		);
		echo json_encode(
			array("target"=>$target,"tahuntanam" => $tahuntanam, "periode" => $periode, "blok" => $blok, "afdeling"=>$afdeling,
				"sqlTT" => $sqlTT, "sqlP" => $sqlP, "sqlB" => $sqlB, "sqlAfd" => $sqlAfd
			)
		);
		break;
	case "init":
		require_once 'master_validation.php';
		include 'lib/eagrolib.php';
		include_once 'lib/zLib.php';
		include_once 'lib/devLibrary.php';
		echo open_body();
		include 'master_mainMenu.php';
		OPEN_BOX();
		echo "\r\n";

		$optPT = makeOption2(getQuery("pt"),
			array( ),
			array("valuefield" => 'kodeorganisasi', "captionfield" => 'namaorganisasi')
		);
		$optUnit = makeOption2(getQuery("lokasitugas"),
			array("valueinit" => '', "captioninit" => $_SESSION['lang']['all']),
			array("valuefield" => 'kodeorganisasi', "captionfield" => 'namaorganisasi')
		);
		$optPeriode = makeOption2("select distinct periode from  setup_periodeakuntansi order by periode desc",
			array( ),
			array("valuefield" => 'periode', "captionfield" => 'periode')
		);

		$arr = '##pt##unit##periode##caption';
		echo "<script language=javascript src='js/zTools.js?v=".mt_rand()."'></script> 
			<script language=javascript src='js/zReport.js?v=".mt_rand()."'></script>   
			<link rel=stylesheet type='text/css' href='style/zTable.css'>";
		$title[0] = "Laporan ".$caption;
		$frm[0] .=
			"<div> " .
			"	<fieldset style='float: left;'> " .
			"		<legend><b>" . $_SESSION['lang']['form'] . "</b></legend> " .
			"		<table cellspacing='1' border='0' > " .
			"			<tr> " .
			"				<td><label>Perusahaan</label></td> " .
			"				<td><select id='pt' name='pt' style='width:150px' >" . $optPT . "</select></td> " .
			"			</tr> " .
			"			<tr> " .
			"				<td><label>Unit</label></td> " .
			"				<td><select id='unit' name='unit' style='width:150px' >" . $optUnit . "</select></td> " .
			"			</tr> " .
			"			<tr> " .
			"				<td><label>Periode</label></td> " .
			"				<td><select id='periode' name='periode' style='width:150px' >" . $optPeriode . "</select></td> " .
			"			</tr> " .
			"			<tr height='20'><td colspan='2'>&nbsp;</td></tr> " .
			"			<tr> " .
			"				<td colspan='2'> " .
			" 					<input type='hidden' id='caption' name='caption' value='".$caption."'> ".
			"					<button onclick=\"zPreview('lap_hpp_tbs','" . $arr . "','printContainer')\" class='mybutton' name='preview' id='preview'>Preview</button> " .
//			"					<button onclick=\"zExcel(event,'lap_hpp_tbs.php','" . $arr . "')\" class='mybutton' name='preview' id='preview'>Excel</button> " .
//			"					<button onclick=\"zPdf('lap_hpp_tbs','$arr','printContainer')\" class='mybutton' name='preview' id='preview'>PDF</button> " .
			"				</td> " .
			"			</tr> " .
			"		</table> " .
			"	</fieldset> " .
			"</div> " .
			"<div style='margin-bottom: 30px;'></div> " .
			"<fieldset style='clear:both'><br/><br/> " .
			"	<legend><b>Print Area</b></legend> " .
			"	<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'></div> " .
			"</fieldset>";
		$hfrm[0] = $title[0];
		drawTab('FRM', $hfrm, $frm, 200, 1220);
		echo "<br/>";
		CLOSE_BOX();
		echo close_body();
		break;
	case "preview":
	case 'excel':
	case 'pdf':
		include 'lib/eagrolib.php';
		include_once 'lib/zLib.php';
		include_once 'lib/devLibrary.php';
		require_once 'lib/fpdf.php';
		$captionCUR = date('M-Y', $t);
		$periodesaldo = str_replace('-', '', $param['periode']);
		$bulansaldo= substr($periodesaldo, 4, 2);
		$tahunini = substr($periodesaldo, 0, 4);
		$t = mktime(0, 0, 0, substr($periodesaldo, 4, 2) + 0, 15, substr($periodesaldo, 0, 4));
		$captionCUR = date('M-Y', $t);

		$arrayFilters = [];
		if ($param['unit'] != '') {
			$arrayFilters[] = " k.kodeorg ='" . $param['unit'] . "' ";
		} else {
			$arrayFilters[] = " k.kodeorg IN (select kodeorganisasi from organisasi where induk='".$param['pt']."') ";
		}
		if ($param['periode'] != '') {
			$arrayFilters[] = " k.periode ='" .$periodesaldo . "' ";
		}
		$arrayFilters[]=" k.noakun BETWEEN m.noakundari and m.noakunsampai ";
		$filter = generateFilter($arrayFilters);

		$sql = "SELECT m.tipe,m.noakundari,m.noakunsampai,m.keterangandisplay,
					case 
					  when m.tipe<>'Header' then 
						(SELECT round(SUM(debet".$bulansaldo."-kredit".$bulansaldo."),2) FROM keu_saldobulanan k ". $filter. ")
					  ELSE ''
					END AS saldo,
					case 
					  when m.tipe<>'Header' then 
						(SELECT round(SUM(awal".$bulansaldo."+debet".$bulansaldo."-kredit".$bulansaldo."),2) FROM keu_saldobulanan k ". $filter. ")
					  ELSE ''
					END AS akumulasi 
				FROM keu_5mesinlaporandt m
				WHERE namalaporan ='".$caption."';";
//		echoMessage(" sql ",$sql);
		/*
		 * Table Preview
		 */
		if ($proses == 'preview' || $proses == 'excel') {
			$table = createHeaderTable($param,$captionCUR);
			($rows = mysql_query($sql)) || true;

			$tablerow = "<tbody>";
			while ($row = mysql_fetch_assoc($rows)) {
				$tablerow .= " <tr class=rowcontent > ";
				if ($row['tipe']=='Header') {
					$tablerow .= "	<td colspan='5'><strong>" . $row['keterangandisplay'] . "</strong></td>";
				} else {
					if ($row['tipe'] == 'Detail') {
						$tablerow .= "<td>&nbsp;</td><td>&nbsp;</td><td>" . $row['keterangandisplay'] . "</td>";
					} else
						if ($row['tipe'] == 'Total') {
							$tablerow .= "<td>&nbsp;</td><td colspan='2'><strong>" . $row['keterangandisplay'] . "</strong></td>";
						}
					$style = " ";
					$multiplier = 1;
					if ($row['saldo'] < 0) {
						$style = "style='color:red;'";
						$multiplier = -1;
					}
					$tablerow .= "<td align=right><strong " . $style . ">" . number_format($row['saldo'] * $multiplier) . "</strong></td>";
					$style = " ";
					if ($row['akumulasi'] < 0) {
						$style = "style='color:red;'";
						$multiplier = -1;
					}
					$tablerow .= "<td align=right><strong " . $style . ">" . number_format($row['akumulasi'] * $multiplier) . "</td>";
				}
				$tablerow .= "</tr>";
			}
			$tablerow .= "</tbody></table>";
			$table .= $tablerow;
		}
		if ($proses == 'preview') echo $table;
		if ($proses == 'excel') {
			$nop_ = 'Laporan_'.$caption . '__' . date('His');
			if (0 < strlen($table)) {
				$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
				gzwrite($gztralala, $table);
				gzclose($gztralala);
				echo "<script language=javascript1.2>window.location='tempExcel/" . $nop_ . ".xls.gz';</script>";
			}
		}
		if ($proses == 'pdf') {
			class PDF extends FPDF
			{
				public $periode = '';

				public function Header()
				{
					global $periode;
					global $periode;
					global $dbname;
					global $wkiri;
					global $wlain;
					$width = $this->w - $this->lMargin - $this->rMargin;
					$this->SetFont('Arial', 'B', 7);
					$tinggiAkr = $this->GetY();
					$ksamping = $this->GetX();
					$this->SetY($tinggiAkr + 20);
					$this->SetX($ksamping);
					$this->Cell(790, $height, ' ', 0, 1, 'R');
					$height = 15;
					$this->SetFillColor(220, 220, 220);
					$this->SetFont('Arial', 'B', 8);
					$tinggiAkr = $this->GetY();
					$ksamping = $this->GetX();
					$this->SetY($tinggiAkr + 20);
					$this->SetX($ksamping);
					$this->Cell(330, $height, 'Nama Akun', TLR, 0, 'C', 1);
					$this->Cell(100, $height, $this->periode, TLR, 0, 'C', 1);
					$this->Cell(100, $height, 'YTD', TLR, 0, 'C', 1);

					$this->Ln();
				}

				public function Footer()
				{
					$this->SetY(-15);
					$this->SetFont('Arial', 'I', 11);
					$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
				}
			}


			$cols = 247.5;
			$wkiri = 50;
			$wlain = 11;
			$pdf = new PDF('P', 'pt', 'A4');
			$pdf->periode = $captionCUR;
			$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
			$height = 10;
			$pdf->AddPage();
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetFont('Arial', '', 7);
			$i = 0;
			$pdf->Ln();
			($rows = mysql_query($sql)) || true;
			while ($row = mysql_fetch_assoc($rows)) {

//				if ($row['tipe']=='Header') {
//					$tablerow .= "	<td colspan='5'><strong>" . $row['keterangandisplay'] . "</strong></td>";
//					$pdf->Cell(40, $height, $row['kodeorg'], 1, 0, 'R', 1);
//				} else {
//					if ($row['tipe'] == 'Detail') {
//						$tablerow .= "<td>&nbsp;</td><td>&nbsp;</td><td>" . $row['keterangandisplay'] . "</td>";
//					} else
//						if ($row['tipe'] == 'Total') {
//							$tablerow .= "<td>&nbsp;</td><td colspan='2'><strong>" . $row['keterangandisplay'] . "</strong></td>";
//						}
//					$style = " ";
//					$multiplier = 1;
//					if ($row['saldo'] < 0) {
//						$style = "style='color:red;'";
//						$multiplier = -1;
//					}
//					$tablerow .= "<td align=right><strong " . $style . ">" . number_format($row['saldo'] * $multiplier) . "</strong></td>";
//					$style = " ";
//					if ($row['akumulasi'] < 0) {
//						$style = "style='color:red;'";
//						$multiplier = -1;
//					}
//					$tablerow .= "<td align=right><strong " . $style . ">" . number_format($row['akumulasi'] * $multiplier) . "</td>";
//				}

//				++$i;
//				$pdf->Cell(40, $height, $row['kodeorg'], 1, 0, 'R', 1);
//				$pdf->Cell(40, $height, $row['tahuntanam'], 1, 0, 'R', 1);
//				$pdf->Cell(40, $height, $row['LuasPanen'], 1, 0, 'R', 1);
//				$pdf->Cell(40, $height, $row['JumlahPokok'], 1, 0, 'R', 1);
//				for ($i = 1; $i <= 4; $i++) {
//					if ($pdf->mingguke == $i || $pdf->mingguke == '') {
//						$pdf->Cell(40, $height, $row['LP' . $i], 1, 0, 'R', 1);
//						$pdf->Cell(40, $height, $row['KG' . $i], 1, 0, 'R', 1);
//						$pdf->Cell(40, $height, $row['JJG' . $i], 1, 0, 'R', 1);
//						$pdf->Cell(40, $height, $row['BJR' . $i], 1, 0, 'R', 1);
//						$pdf->Cell(40, $height, $row['HK' . $i], 1, 0, 'R', 1);
//					}
//				}
				$pdf->Ln();
			}

			$pdf->Output();
		}
		break;
}
?>

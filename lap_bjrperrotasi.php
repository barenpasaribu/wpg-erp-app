<?php

$param=$_POST;
if (sizeof($param)==0)  $param=$_GET;
$proses = $_GET['proses'];
function createHeaderTable($param){
	$table =
		"<table cellspacing=0 cellpadding=1 border=1 class=sortable>
				<thead class=rowheader>
					<tr  >
						<td rowspan='2'>Blok</td>
						<td rowspan='2'>TT</td>
						<td rowspan='2'>Luasan Panen</td>
						<td rowspan='2'>Jumlah Pokok</td>";
	if ($param['mingguke'] == '1' || $param['mingguke'] == '') {
		$table .= "<td colspan='5'>Minggu 1</td> ";
	}
	if ($param['mingguke'] == '2' || $param['mingguke'] == '') {
		$table .= "<td colspan='5'>Minggu 2</td> ";
	}
	if ($param['mingguke'] == '3' || $param['mingguke'] == '') {
		$table .= "<td colspan='5'>Minggu 3</td> ";
	}
	if ($param['mingguke'] == '4' || $param['mingguke'] == '') {
		$table .= "<td colspan='5'>Minggu 4</td> ";
	}
	$table .= "</tr>";
	$table .= "<tr>";
	for ($i = 1; $i <= 4; $i++) {
		if ($param['mingguke'] == $i || $param['mingguke'] == '') {
			$table .= " 		<td>Luasan<br/>Terpanen</td>
								<td>KG</td>
								<td>JJG</td>
								<td>BJR</td>
								<td>Jumlah<br/>HK</td>";
		}
	}
	$table .= " </tr> </thead><tbody>";
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

		$optUnit = makeOption2(getQuery("lokasitugas"),
			array("valueinit" => '', "captioninit" => $_SESSION['lang']['all']),
			array("valuefield" => 'kodeorganisasi', "captionfield" => 'namaorganisasi')
		);
		$unit = $_SESSION['empl']['lokasitugas'];
		$sql = "SELECT DISTINCT tahuntanam FROM kebun_prestasi where tahuntanam<>0 and LENGTH(tahuntanam)=4 and notransaksi like '%" . $unit . "%' order by tahuntanam";
		$optTahunTanam = makeOption2('',
			array("valueinit" => '', "captioninit" => $_SESSION['lang']['all']),
			array("valuefield" => 'tahuntanam', "captionfield" => 'tahuntanam')
		);
		$sql = "SELECT DISTINCT SUBSTRING(notransaksi,1,6) periode FROM kebun_prestasi where notransaksi like '%" . $unit . "%'  order by SUBSTRING(notransaksi,1,6)";
		$optPeriode = makeOption2('',
			array("valueinit" => '', "captioninit" => $_SESSION['lang']['all']),
			array("valuefield" => 'periode', "captionfield" => 'periode')
		);
		$sql = "SELECT DISTINCT kodeorg FROM kebun_prestasi where notransaksi like '%" . $unit . "%'  order by kodeorg";
		$optBlok = makeOption2('',
			array("valueinit" => '', "captioninit" => $_SESSION['lang']['all']),
			array("valuefield" => 'kodeorg', "captionfield" => 'kodeorg')
		);
		$optBlok = makeOption2('',
			array("valueinit" => '', "captioninit" => $_SESSION['lang']['all']),
			array("valuefield" => 'kodeorg', "captionfield" => 'kodeorg')
		);

		$optMingguKe = "<option value=''>" . $_SESSION['lang']['all'] . "</option>";
		$optMingguKe .= "<option value='1'>Satu</option>";
		$optMingguKe .= "<option value='2'>Dua</option>";
		$optMingguKe .= "<option value='3'>Tiga</option>";
		$optMingguKe .= "<option value='4'>Empat</option>";

		$arr = '##unit##tahuntanam##periode##mingguke##afdeling##blok';
		echo "<script language=javascript src='js/zTools.js?v=".mt_rand()."'></script> 
			<script language=javascript src='js/zReport.js?v=".mt_rand()."'></script>  
			<script>
				function selectChange(fileTarget,target) {
//					var tmp = document.getElementById('unit');
					var param ='target='+target+'&id='+getValue(target);
					function respon() {
						if (con.readyState == 4) {
							if (con.status == 200) {
								busy_off();
								//console.log(con.responseText);
				
								if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,' + con.responseText);
								} else {
									var result = JSON.parse(con.responseText);
									if (result['target']=='unit') { 
									document.getElementById('afdeling').innerHTML = result['afdeling'];
									document.getElementById('tahuntanam').innerHTML = result['tahuntanam'];
									document.getElementById('periode').innerHTML = result['periode'];
									document.getElementById('blok').innerHTML = result['blok'];
									} else if (result['target']=='afdeling') { 
									document.getElementById('tahuntanam').innerHTML = result['tahuntanam'];
									document.getElementById('periode').innerHTML = result['periode'];
									document.getElementById('blok').innerHTML = result['blok'];
									    } else {
									document.getElementById('tahuntanam').innerHTML = result['tahuntanam'];
									document.getElementById('periode').innerHTML = result['periode'];
									    }
									
								}
							} else {
								busy_off();
								error_catch(con.status);
							}
						}
					}
					post_response_text(fileTarget+'.php?proses=detailunit', param, respon);
				}
			</script>
			<link rel=stylesheet type='text/css' href='style/zTable.css'>";
		$title[0] = "Laporan BJR Per Rotasi";
		$frm[0] .=
			"<div> " .
			"	<fieldset style='float: left;'> " .
			"		<legend><b>" . $_SESSION['lang']['form'] . "</b></legend> " .
			"		<table cellspacing='1' border='0' > " .
			"			<tr> " .
			"				<td><label>" . $_SESSION['lang']['unit'] . "</label></td> " .
			"				<td><select id='unit' name='unit' style='width:150px' onchange=\"selectChange('lap_bjrperrotasi','unit')\">" . $optUnit . "</select></td> " .
			"			</tr> " .
			"			<tr> " .
			"				<td><label>Afdeling</label></td> " .
			"				<td><select id='afdeling' name='afdeling' style='width:150px' onchange=\"selectChange('lap_bjrperrotasi','afdeling')\">" . $optBlok . "</select></td> " .
			"			</tr> " .
			"			<tr> " .
			"				<td><label>Blok</label></td> " .
			"				<td><select id='blok' name='blok' style='width:150px' onchange=\"selectChange('lap_bjrperrotasi','blok')\">" . $optBlok . "</select></td> " .
			"			</tr> " .
			"			<tr> " .
			"				<td><label>Tahun Tanam</label></td> " .
			"				<td><select id='tahuntanam' name='tahuntanam' style='width:100px'>" . $optTahunTanam . "</select></td> " .
			"			</tr> " .
			"			<tr> " .
			"			<tr> " .
			"				<td><label>" . $_SESSION['lang']['periode'] . "</label></td> " .
			"				<td><select id='periode' name='periode' style='width:100px'>" . $optPeriode . "</select></td> " .
			"			</tr> " .
			"			<tr> " .
			"				<td><label>Minggu Ke </label></td> " .
			"				<td><select id='mingguke' name='mingguke' style='width:150px'>" . $optMingguKe . "</select></td> " .
			"			</tr> " .
			"			<tr height='20'><td colspan='2'>&nbsp;</td></tr> " .
			"			<tr> " .
			"				<td colspan='2'> " .
			"					<button onclick=\"zPreview('lap_bjrperrotasi','" . $arr . "','printContainer')\" class='mybutton' name='preview' id='preview'>Preview</button> " .
			"					<button onclick=\"zExcel(event,'lap_bjrperrotasi.php','" . $arr . "')\" class='mybutton' name='preview' id='preview'>Excel</button> " .
			"					<button onclick=\"zPdf('lap_bjrperrotasi','$arr','printContainer')\" class='mybutton' name='preview' id='preview'>PDF</button> " .
			"				</td> " .
			"			</tr> " .
			"		</table> " .
			"	</fieldset> " .
			"</div> " .
			"<div style='margin-bottom: 30px;'></div> " .
			"<fieldset style='clear:both'> " .
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
		$arrayFilters = [];
		if ($param['unit'] != '') {
			$arrayFilters[] = " unit ='" . $param['unit'] . "' ";
		}
		if ($param['tahuntanam'] != '') {
			$arrayFilters[] = " tahuntanam =" . $param['tahuntanam'] . " ";
		}
		if ($param['periode'] != '') {
			$arrayFilters[] = " periode ='" . $param['periode'] . "' ";
		}
		if ($param['mingguke'] != '') {
			$arrayFilters[] = " mingguke =" . $param['mingguke'] . " ";
		}
		if ($param['blok'] != '') {
			$arrayFilters[] = " kodeorg ='" . $param['blok'] . "' ";
		}
		$filter = generateFilter($arrayFilters);
		$sql = "SELECT  Z.unit,Z.tahuntanam,Z.periode, Z.kodeorg, 
 (SELECT round(SUM(luasareaproduktif),2) FROM setup_blok s WHERE s.kodeorg=Z.kodeorg AND s.tahuntanam=Z.tahuntanam) AS LuasPanen,
 (SELECT round(SUM(jumlahpokok),2) FROM setup_blok s WHERE s.kodeorg=Z.kodeorg AND s.tahuntanam=Z.tahuntanam) AS JumlahPokok,";
		if ($param['mingguke'] == '1' || $param['mingguke'] == '') {
			$sql .= " 
 				round(AVG(case when mingguke=1 then hasilkerja END ),2) AS HK1,
				round(AVG(case when mingguke=1 then hasilkerjakg END ),2) AS KG1,
 				round(AVG(case when mingguke=1 then luaspanen END ),2) AS LP1,
 				round(AVG(case when mingguke=1 then jjg END ),2) AS JJG1,
 				round(AVG(case when mingguke=1 then bjr END ),2) AS BJR1";
			if ($param['mingguke'] == '') $sql .= ", ";
		}
		if ($param['mingguke'] == '2' || $param['mingguke'] == '') {
			$sql .= " 
				 round(AVG(case when mingguke=2 then hasilkerja END ),2) AS HK2,
				 round(AVG(case when mingguke=2 then hasilkerjakg END ),2) AS KG2,
				 round(AVG(case when mingguke=2 then luaspanen END ),2) AS LP2,
 				round(AVG(case when mingguke=1 then jjg END ),2) AS JJG2,
				 round(AVG(case when mingguke=2 then bjr END ),2) AS BJR2";
			if ($param['mingguke'] == '') $sql .= ", ";
		}
		if ($param['mingguke'] == '3' || $param['mingguke'] == '') {
			$sql .= " 
				round(AVG(case when mingguke=3 then hasilkerja END ),2) AS HK3,
		 		round(AVG(case when mingguke=3 then hasilkerjakg END ),2) AS KG3,
		 		round(AVG(case when mingguke=3 then luaspanen END ),2) AS LP3,
 				round(AVG(case when mingguke=1 then jjg END ),2) AS JJG3,
		 		round(AVG(case when mingguke=3 then bjr END ),2) AS BJR3";
			if ($param['mingguke'] == '') $sql .= ", ";
		}
		if ($param['mingguke'] == '4' || $param['mingguke'] == '') {
			$sql .= " 
				round(AVG(case when (mingguke=4 OR mingguke=5) then hasilkerja END ),2) AS HK4,
				round(AVG(case when (mingguke=4 OR mingguke=5) then hasilkerjakg END ),2) AS KG4,
				round(AVG(case when (mingguke=4 OR mingguke=5) then luaspanen END ),2) AS LP4,
 				round(AVG(case when (mingguke=4 OR mingguke=5) then jjg END ),2) AS JJG4,
				round(AVG(case when (mingguke=4 OR mingguke=5) then bjr END ),2) AS BJR4";
		}
		$sql .= " 
			FROM  (
				SELECT tahuntanam, mingguke, periode,
			kodeorg,
				round(AVG(hasilkerja),2) AS hasilkerja,
				round(AVG(hasilkerjakg),2) AS hasilkerjakg,
				round(AVG(luaspanen),2) AS luaspanen,
				round(AVG(jjg),2) AS jjg,
				round(AVG(bjraktual),2) AS bjr, unit FROM (
					SELECT * FROM (
						SELECT tahuntanam,(FLOOR((DAYOFMONTH(cast(SUBSTRING(notransaksi,1,8) AS DATE)) - 1) / 7) + 1)  as mingguke,  
						hasilkerja,
						hasilkerjakg,
						luaspanen,
						jjg,
						bjraktual,
			kodeorg,
						SUBSTRING(notransaksi,10,4) AS unit,
						SUBSTRING(notransaksi,1,6) AS periode 
						FROM kebun_prestasi
					) X 
				) Y  
			" . $filter . " 
				GROUP BY unit,tahuntanam,kodeorg,periode,mingguke  
			) Z 
			GROUP BY unit,tahuntanam,kodeorg,periode 
			 ORDER BY unit,tahuntanam,kodeorg,periode,mingguke  ";

//		echoMessage( " query ",$sql,true);
		/*
		 * Table Preview
		 */

		if ($proses == 'preview' || $proses == 'excel') {
			$table = createHeaderTable($param);
			($rows = mysql_query($sql)) || true;

			$tablerow = "";
			while ($row = mysql_fetch_assoc($rows)) {
				$tablerow .= " <tr class=rowcontent >
						<td>" . $row['kodeorg'] . "</td>
						<td>" . $row['tahuntanam'] . "-" . $row['periode'] . "</td>
						<td align='right'>" . $row['LuasPanen'] . "</td>
						<td align='right'>" . $row['JumlahPokok'] . "</td>";
				for ($i = 1; $i <= 4; $i++) {
					if ($param['mingguke'] == $i || $param['mingguke'] == '') {
						$tablerow .= " 		<td align='right'>" . $row['LP' . $i] . "</td>
						<td align='right'>" . $row['KG' . $i] . "</td>
						<td align='right'>" . $row['JJG' . $i] . "</td>
						<td align='right'>" . $row['BJR' . $i] . "</td>
						<td align='right'>" . $row['HK' . $i] . "</td>";
					}
				}
				$tablerow .= "</tr>";
			}
			$tablerow .= "</tbody></table>";
			$table .= $tablerow;
		}
		if ($proses == 'preview') echo $table;
		if ($proses == 'excel') {
			$nop_ = 'Laporan_BJR_Per_Rotasi' . ($param['mingguke'] == '' ? '' : '_Minggu ke-' . $param['mingguke']) . '__' . date('His');
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
				public $mingguke = '';

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
					$this->Cell(40, $height, 'Blok', TLR, 0, 'C', 1);
					$this->Cell(40, $height, 'TT', TLR, 0, 'C', 1);
					$this->Cell(40, $height, 'Luas', TLR, 0, 'C', 1);
					$this->Cell(40, $height, 'Jumlah', TLR, 0, 'C', 1);
					if ($this->mingguke == '1' || $this->mingguke == '') {
						$this->Cell(200, $height, 'Minggu 1', TLR, 0, 'C', 1);
					}
					if ($this->mingguke == '2' || $this->mingguke == '') {
						$this->Cell(200, $height, 'Minggu 2', TLR, 0, 'C', 1);
					}
					if ($this->mingguke == '3' || $this->mingguke == '') {
						$this->Cell(200, $height, 'Minggu 3', TLR, 0, 'C', 1);
					}
					if ($this->mingguke == '4' || $this->mingguke == '') {
						$this->Cell(200, $height, 'Minggu 4', TLR, 0, 'C', 1);
					}
					$this->Ln();
					$this->Cell(40, $height, ' ', BLR, 0, 'C', 1);
					$this->Cell(40, $height, ' ', BLR, 0, 'C', 1);
					$this->Cell(40, $height, 'Panen', BLR, 0, 'C', 1);
					$this->Cell(40, $height, 'Pokok', BLR, 0, 'C', 1);
					if ($this->mingguke == '1' || $this->mingguke == '') {
						$this->Cell(40, $height, 'LT', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'KG', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'JJG', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'BJR', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'HK', TBLR, 0, 'C', 1);
					}
					if ($this->mingguke == '2' || $this->mingguke == '') {
						$this->Cell(40, $height, 'LT', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'KG', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'JJG', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'BJR', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'HK', TBLR, 0, 'C', 1);
					}
					if ($this->mingguke == '3' || $this->mingguke == '') {
						$this->Cell(40, $height, 'LT', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'KG', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'JJG', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'BJR', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'HK', TBLR, 0, 'C', 1);
					}
					if ($this->mingguke == '4' || $this->mingguke == '') {
						$this->Cell(40, $height, 'LT', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'KG', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'JJG', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'BJR', TBLR, 0, 'C', 1);
						$this->Cell(40, $height, 'HK', TBLR, 0, 'C', 1);
					}
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
			$pdf = new PDF('L', 'pt', 'Legal');
			$pdf->mingguke = $param['mingguke'];
			$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
			$height = 10;
			$pdf->AddPage();
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetFont('Arial', '', 7);
			$i = 0;
			$pdf->Ln();
			($rows = mysql_query($sql)) || true;
			while ($row = mysql_fetch_assoc($rows)) {
				++$i;
				$pdf->Cell(40, $height, $row['kodeorg'], 1, 0, 'R', 1);
				$pdf->Cell(40, $height, $row['tahuntanam'], 1, 0, 'R', 1);
				$pdf->Cell(40, $height, $row['LuasPanen'], 1, 0, 'R', 1);
				$pdf->Cell(40, $height, $row['JumlahPokok'], 1, 0, 'R', 1);
				for ($i = 1; $i <= 4; $i++) {
					if ($pdf->mingguke == $i || $pdf->mingguke == '') {
						$pdf->Cell(40, $height, $row['LP' . $i], 1, 0, 'R', 1);
						$pdf->Cell(40, $height, $row['KG' . $i], 1, 0, 'R', 1);
						$pdf->Cell(40, $height, $row['JJG' . $i], 1, 0, 'R', 1);
						$pdf->Cell(40, $height, $row['BJR' . $i], 1, 0, 'R', 1);
						$pdf->Cell(40, $height, $row['HK' . $i], 1, 0, 'R', 1);
					}
				}
				$pdf->Ln();
			}

			$pdf->Output();
		}
		break;
}
?>

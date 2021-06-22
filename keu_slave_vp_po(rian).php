<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo '<link rel="stylesheet" type="text/css" href="style/generic.css">' . "\r\n" . '<script language=javascript src=\'js/generic.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/keu_vp.js\'></script>' . "\r\n";
$proses = $_GET['proses'];
$param = $_POST;

switch ($proses) {
	case 'po':
		$tipeTugas = $_SESSION['empl']['tipelokasitugas'];
		$ptTugas = $_SESSION['empl']['kodeorganisasi'];
		$lokasiTugas = $_SESSION['empl']['lokasitugas'];

		if ($tipeTugas == 'HOLDING') {
			$filterPo = 'and kodeorg=\'' . $ptTugas . '\'';
		} else {
			$filterPo = 'and nopo like \'%' . $lokasiTugas . '%\'';
			$filterLain = 'and lokalpusat=1';
		}

		switch ($param['tipe']) {
			case 'po':
				$title = $_SESSION['lang']['nopo'];
				$query = selectQuery($dbname, 'keu_tagihanht', "'PO',nopo,kodeorg", "nopo like '%" . $param['po'] . "%' and posting=1 and tipeinvoice='p'  and kodeorg='".$ptTugas."' ");
				break;
			case 'k':
				$title = $_SESSION['lang']['kontrak'];
				$query = selectQuery($dbname, 'keu_tagihanht', "'Kontrak',nopo,kodeorg", "nopo like '%" . $param['po'] . "%' and posting=1 and tipeinvoice='k' and kodeorg='".$ptTugas."'");
				break;
			case 'sj':
				$title = $_SESSION['lang']['nosj'];
				$query = selectQuery($dbname, 'keu_tagihanht', "'Surat Jalan',nopo,kodeorg", "nopo like '%" . $param['po'] . "%' and posting=1  and tipeinvoice='s' and kodeorg='".$ptTugas."' ");
				break;
			case 'ns':
				$title = $_SESSION['lang']['nokonosemen'];
				$query = selectQuery($dbname, 'keu_tagihanht', "'Konosemen',nopo,kodeorg", "nopo like '%" . $param['po'] . "%' and posting=1  and tipeinvoice='n' and kodeorg='".$ptTugas."' ");
				break;
			case 'ot':
				$title = $_SESSION['lang']['lain'];
				$query = selectQuery($dbname, 'keu_tagihanht', "'Other',nopo,kodeorg", "nopo like '%" . $param['po'] . "%' and posting=1  and tipeinvoice='o' and kodeorg='".$ptTugas."' ");
				break;
			case 'b':
				$title = 'Batch';
				$query = "select 'Batch',nobatch,kodeorg from keu_batchht where nobatch like '" . $_SESSION['org']['kodeorganisasi'] . "%' and status=0";
				break;
		}

		$data = fetchData($query);
		$page = '';
		$page .= '<div style=width:750px;height:320px;overflow:auto;><table class=sortable cellspacing=1  border=0>';
		$page .= '<thead><tr class=rowheader>';
		$page .= '<td>' . $_SESSION['lang']['tipe'] . '</td>';
		$page .= '<td>' . $title . '</td><td>' . $_SESSION['lang']['kodeorganisasi'] . '</td></tr></thead>';
		$page .= '<tbody>';

		if ($param['tipe'] == 'b') {
			foreach ($data as $key => $row) {
				$page .= "<tr class='rowcontent' style='cursor:pointer' onclick=\"findDetailBatch('" . $row['nobatch'] . "')\">";
				foreach ($row as $attr => $val) {
					$page .= "<td>$val</td>";
				}
				$page .= "</tr>";
			}
		} else {
			foreach ($data as $key => $row) {
				$page .= '<tr id=\'t_po_' . $key . '\' class=rowcontent style=\'cursor:pointer\'' . "\r\n" . '                nopo = \'' . $row['nopo'] . '\'' . "\r\n\t\t\t\t" . 'tipe = \'' . $param['tipe'] . '\'' . "\r\n\t\t\t\t" . 'kodeorg = \'' . $row['kodeorg'] . '\'' . "\r\n" . '                onclick=\'findInvoice(this)\'>';

				foreach ($row as $attr => $val) {
					$page .= '<td id=\'t_po_' . $key . '_' . $attr . '\'>' . $val . '</td>';
				}

				$page .= '</tr>';
			}
		}
		$page .= '</tbody>';
		$page .= '</table></div>';
		break;

	case 'invoice':
	case 'batch':
		$page='';
		$btnsave='';
		if ($proses=='batch') {
			$sql = "select
					b.*,t.noinvoicesupplier,t.jatuhtempo, perhitunganpph from keu_batchdt b 
					inner join keu_tagihanht t on t.noinvoice=b.noinvoice
					where b.nobatch='".$param['kode']."'  ";
			$data = fetchData($sql);
			$page = '<div>' . $title . ' : <span id=t_inv_batch>' . $param['kode'] . '</span></div>';
			$btnsave=makeElement('t_inv_saveBtn', 'btn', $_SESSION['lang']['save'], array('onclick' => 'setPoInv(\''.$param['kode'].'\')'));
		} else {
			$query = selectQuery($dbname, 'keu_tagihanht', '*', 'nopo=\'' . $param['po'] . '\'');
			$data = fetchData($query);
			$page = '<div>' . $title . ' : <span id=t_inv_nopo>' . $param['po'] . '</span></div>';
			$btnsave=makeElement('t_inv_saveBtn', 'btn', $_SESSION['lang']['save'], array('onclick' => 'setPoInv(\'\')'));
		}
		$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
		switch ($param['tipe']) {
			case 'po':
				$title = $_SESSION['lang']['nopo'];
				break;

			case 'k':
				$title = $_SESSION['lang']['kontrak'];
				break;

			case 'sj':
				$title = $_SESSION['lang']['nosj'];
				break;

			case 'ns':
				$title = $_SESSION['lang']['nokonosemen'];
				break;

			case 'ot':
				$title = $_SESSION['lang']['lainnya'];
				break;
		}


//	$btnsave=makeElement('t_inv_saveBtn', 'btn', $_SESSION['lang']['save'], array('onclick' => 'setPoInv()'));
		$page .= '<table class=sortable cellspacing=1  border=0>';
		$page .= '<thead><tr class=rowheader>';
		$page .= '<td><button class=mybutton onclick=\'selAll()\'>Select All</button></td>';
		$page .= '<td>' . $_SESSION['lang']['noinvoice'] . '</td>';
//		if ($proses=='batch') {
			$page .= '<td>No PO</td>';
//		}
		$page .= '<td>' . $_SESSION['lang']['noinvoice'] . ' ' . $_SESSION['lang']['supplier'] . '</td>';
		$page .= '<td> ID Supplier </td>';
		$page .= '<td>' . $_SESSION['lang']['supplier'] . '</td>';
		$page .= '<td>' . $_SESSION['lang']['nilaiinvoice'] . '</td>';
		$page .= '<td>' . $_SESSION['lang']['ppn'] . '</td>';
		$page .= '<td>Pph</td>';
		$page .= '</tr></thead>';
		$page .= '<tbody id=\'t_inv_body\'>';

		foreach ($data as $key => $row) {
			$tgl_jt=date_create($row['jatuhtempo']);
			$tgljt=date_format($tgl_jt,'d-m-Y');

			$page .= "<tr id='t_inv_$key' class=rowcontent style='cursor:pointer'>";
//			if ($proses=='batch') {
			$page .= "<td id='t_check_$key'>
							<input type='hidden' id='hidden_tgljt_el_inv_$key' value='" . $tgljt . "'/>
							<input type='hidden' id='hidden_inv_el_inv_$key' value='" . $row['noinvoice'] . "'/>
							<input type='hidden' id='hidden_nopo_el_inv_$key' value='" . $row['nopo'] . "'/>
							<input type='hidden' id='hidden_nilai_el_inv_$key' value='" . $row['nilaiinvoice'] . "'/>
							<input type='hidden' id='hidden_ppn__el_inv_$key' value='" . $row['nilaippn'] . "'/>
							<input type='hidden' id='hidden_pph__el_inv_$key' value='" . $row['perhitunganpph'] . "'/>";
			if ($proses == 'batch') {
				$page .= "<input id = 'el_inv_$key' name = 'el_inv_$key' type = 'checkbox' " . ($row['status'] == 0 ? "" : "disabled='disabled'") . ">";
			} else {
				$page .= "<input id = 'el_inv_$key' name = 'el_inv_$key' type = 'checkbox' >";
			}
			$page .= "</td>";
			$page .= "<td id='t_noinvoice_$key'>" . $row['noinvoice'] . "</td>";
			$page .= "<td id='t_nopo_$key'>" . $row['nopo'] . "</td>";
			$page .= "<td id='t_noinvoicesupplier_$key'>" . $row['noinvoicesupplier'] . "</td>";
			$page .= "<td id='t_kodesupplier_$key'>" . $row['kodesupplier'] . "</td>";
			$page .= "<td id='t_kodesupplier_$key'>" . $optSupp[$row['kodesupplier']] . "</td>";
			$page .= "<td id='t_nilaiinvoice_$key' align=right value='" . $row['nilaiinvoice'] . "'>" . number_format($row['nilaiinvoice'], 2) . "</td>";
			$page .= "<td id='t_nilaippn_$key'  align=right value='" . $row['nilaippn'] . "'>" . number_format($row['nilaippn'], 2) . "</td>";
			$page .= "<td id='t_nilaipph_$key'  align=right value='" . $row['perhitunganpph'] . "'>" . number_format($row['perhitunganpph'], 2) . "</td>";

			$page .= "</tr>";
		}

		$page .= '</tbody>';
		$page .= '</table>';
		$page .= $btnsave;
		break;
//	case "batch":
//
//
//		$page = '<div>' . $title . ' : <span id=t_inv_nopo>' . $param['po'] . '</span></div>';
//		$page .= '<table class=sortable cellspacing=1  border=0>';
//		$page .= '<thead><tr class=rowheader>';
//		$page .= '<td><button class=mybutton onclick=\'selAll()\'>Select All</button></td>';
//		$page .= '<td>' . $_SESSION['lang']['noinvoice'] . '</td>';
//		$page .= '<td>' . $_SESSION['lang']['noinvoice'] . ' ' . $_SESSION['lang']['supplier'] . '</td>';
//		$page .= '<td>' . $_SESSION['lang']['supplier'] . '</td>';
//		$page .= '<td>' . $_SESSION['lang']['nilaiinvoice'] . '</td>';
//		$page .= '<td>' . $_SESSION['lang']['ppn'] . '</td>';
//		$page .= '</tr></thead>';
//		$page .= '<tbody id=\'t_inv_body\'>';
//
//		foreach ($data as $key => $row) {
//			$page .= '<tr id=\'t_inv_' . $key . '\' class=rowcontent style=\'cursor:pointer\'>';
//			$page .= '<td id=\'t_check_' . $key . '\'>' . makeElement('el_inv_' . $key, 'checkbox') . '</td>';
//			$page .= '<td id=\'t_noinvoice_' . $key . '\'>' . $row['noinvoice'] . '</td>';
//			$page .= '<td id=\'t_noinvoicesupplier_' . $key . '\'>' . $row['noinvoicesupplier'] . '</td>';
//			$page .= '<td id=\'t_kodesupplier_' . $key . '\'>' . $optSupp[$row['kodesupplier']] . '</td>';
//			$page .= '<td id=\'t_nilaiinvoice_' . $key . '\' align=right value=\'' . $row['nilaiinvoice'] . '\'>' . number_format($row['nilaiinvoice'], 2) . '</td>';
//			$page .= '<td id=\'t_nilaippn_' . $key . '\' value=\'' . $row['nilaippn'] . '\'>' . number_format($row['nilaippn'], 2) . '</td>';
//			$page .= '</tr>';
//		}
//
//		$page .= '</tbody>';
//		$page .= '</table>';
//		$page .= makeElement('t_inv_saveBtn', 'btn', $_SESSION['lang']['save'], array('onclick' => 'setPoInv()'));
//		break;
}
echo $page;
?>

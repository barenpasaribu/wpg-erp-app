<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;

switch ($proses) {
case 'showDetail':
	if ((substr($param['divisi'], 0, 2) == 'AK') || (substr($param['divisi'], 0, 2) == 'PB')) {
		$optBlok = makeOption($dbname, 'project', 'kode,nama', 'kode=\'' . $param['divisi'] . '\' and posting=0');
		$str = 'select kegiatan,namakegiatan from ' . $dbname . '.project_dt where kodeproject=\'' . $param['divisi'] . '\'';
		$res = mysql_query($str);
		
		while ($bar = mysql_fetch_object($res)) {
			$optAct[$bar->kegiatan] = $bar->namakegiatan;
		}
	}
	else {
		$optBlok = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'induk=\'' . $param['divisi'] . '\' or kodeorganisasi like \'' . substr($param['divisi'], 0, 4) . '%\'');
		$optAct = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
	}

	$where = 'notransaksi=\'' . $param['notransaksi'] . '\' and kodeblok like \'' . substr($param['divisi'], 0, 4) . '%\'';
	$cols = 'kodeblok,kodekegiatan,hk,hasilkerjajumlah,satuan,jumlahrp';
	$query = selectQuery($dbname, 'log_spkdt', $cols, $where);
	$data = fetchData($query);
	$dataShow = array();

	foreach ($data as $key => $row) {
		$dataShow[$key]['kodeblok'] = $optBlok[$row['kodeblok']];
		$dataShow[$key]['kodekegiatan'] = $optAct[$row['kodekegiatan']];
		$dataShow[$key]['hk'] = $row['hk'];
		$dataShow[$key]['hasilkerjajumlah'] = $row['hasilkerjajumlah'];
		$dataShow[$key]['satuan'] = $row['satuan'];
		$dataShow[$key]['jumlahrp'] = $row['jumlahrp'];
	}

	$headName = array($_SESSION['lang']['subunit'], $_SESSION['lang']['kodekegiatan'], $_SESSION['lang']['hk'], $_SESSION['lang']['hasilkerjajumlah'], $_SESSION['lang']['satuan'], $_SESSION['lang']['jumlahrp']);
	$grid = '<table class=\'sortable\'><thead><tr class=\'rowheader\'>';

	foreach ($headName as $head) {
		$grid .= '<td>' . $head . '</td>';
	}

	$grid .= '</tr></thead>';
	$grid .= '<tbody>';

	if (empty($data)) {
		$grid .= '<tr class=\'rowcontent\'><td colspan=\'9\'>Data Empty</td></tr>';
	}
	else {
		foreach ($dataShow as $key => $row) {
			$grid .= '<tr class=\'rowcontent\' onclick="manageDetail(' . $key . ')" style=\'cursor:pointer\'>';

			foreach ($row as $head => $cont) {
				$grid .= '<td id=\'' . $head . '_' . $key . '\' ';

				if (isset($data[$key][$head])) {
					$grid .= 'value=\'' . $data[$key][$head] . '\' ';
				}
				else {
					$grid .= 'value=\'\' ';
				}

				if (($head == 'kodeblok') || ($head == 'kodekegiatan')) {
					$grid .= 'align=\'left\'';
				}
				else {
					$grid .= 'align=\'right\'';
				}

				if ($head == 'jumlahrp') {
					$grid .= '>' . number_format($cont) . '</td>';
				}
				else {
					$grid .= '>' . $cont . '</td>';
				}
			}

			$grid .= '</tr>';
			$grid .= '<tr><td colspan=\'6\'><div id=\'detail_' . $key . '\'></div></td></tr>';
		}
	}

	$grid .= '</tbody>';
	$grid .= '</table>';
	echo '<fieldset><legend><b>Detail</b></legend>';
	echo $grid;
	echo '</fieldset>';
	break;

case 'manageDetail':
	$cols = 'kodeblok,tanggal,hasilkerjarealisasi,hkrealisasi,jumlahrealisasi,statusjurnal,jjgkontanan,nodo,notiket';
	$where = 'notransaksi=\'' . $param['notransaksi'] . '\' and kodekegiatan=\'' . $param['kodekegiatan'] . '\' and blokspkdt=\'' . $param['kodeblok'] . '\'';
	$notransaksi = $param['notransaksi'];
	$query = selectQuery($dbname, 'log_baspk', $cols, $where);
	$resDetail = fetchData($query);
	$norekanan = $param['koderekanan'];
	$queryDO = "select notiket from log_baspk WHERE notiket!='' and notransaksi='".$notransaksi."' ";
	$queryActDo = mysql_query($queryDO);
	$queryAct = mysql_query($queryTiket);
	 while($data = mysql_fetch_array($queryActDo)){
	 		$dataT = explode(",",$data['notiket']);
	 		foreach($dataT as $T) {
    		$T = trim($T);
   			 $list .= "'" . $T . "',";
			}
			 $listTiketed .= $list;
	 }

	$queryTiket = "SELECT a.nosipb as nosipb FROM pabrik_timbangan a,log_5supplier b WHERE a.trpcode=b.supplierid and a.trpcode='".$norekanan	."' AND a.notransaksi NOT IN (".$listTiketed."'--') and a.nokontrak = '".$notransaksi	."' and  a.IsPosting='1' group by nosipb";
	
	$queryAct = mysql_query($queryTiket);
	 while($data = mysql_fetch_array($queryAct)){
			 $optTiket[$data['nosipb']] .= $data['nosipb'];
	 }
	
	$optTiket[0] = " ";
	 while($data = mysql_fetch_array($queryAct)){
			 $optTiket[$data['nosipb']] .= $data['nosipb'];
	 }	
	
	foreach ($resDetail as $key => $row) {
		$resDetail[$key]['jumlahrealisasi'] = number_format($row['jumlahrealisasi']);
	}

	if ($_SESSION['empl']['tipelokasitugas'] != 'KEBUN') {
		$optBlok = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'induk=\'' . $param['divisi'] . '\' or kodeorganisasi like \'' . $param['divisi'] . '%\'');
		if ((substr($param['divisi'], 0, 2) == 'AK') || (substr($param['divisi'], 0, 2) == 'PB')) {
			$optBlok = makeOption($dbname, 'project', 'kode,nama', 'kode=\'' . $param['divisi'] . '\' and posting=0');
		}
	}
	else {
		$optBlok = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'induk=\'' . $param['divisi'] . '\' or kodeorganisasi like \'' . $param['divisi'] . '%\'');
		if ((substr($param['divisi'], 0, 2) == 'AK') || (substr($param['divisi'], 0, 2) == 'PB')) {
			$optBlok = makeOption($dbname, 'project', 'kode,nama', 'kode=\'' . $param['divisi'] . '\' and posting=0');
		}
	}

	$header = array($_SESSION['lang']['subunit'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['hkrealisasi'], $_SESSION['lang']['hasilkerjarealisasi'], $_SESSION['lang']['jumlahrealisasi'], $_SESSION['lang']['jjgkontanan'],'NO DO','No Tiket', $_SESSION['lang']['action']);
	$table = '';
	$table .= '<table class=\'sortable\' style=\'margin-bottom:15px\'>';
	$table .= '<thead><tr class=\'rowheader\'>';

	foreach ($header as $head) {
		$table .= '<td>' . $head . '</td>';
	}

	$table .= '</tr></thead>';
	$table .= '<tbody id=\'detailBody_' . $param['numRow'] . '\'>';
	$i = 0;

	foreach ($resDetail as $row) {
		$optTiket[$row['nodo']] .= $row['nodo'];
		$tanggal = tanggalnormal($row['tanggal']);
		$table .= '<tr id=\'tr_' . $param['numRow'] . '_' . $i . '\' class=\'rowcontent\'>';
		$table .= '<td>' . makeElement('blokalokasi_' . $param['numRow'] . '_' . $i, 'select', $row['kodeblok'], array('disabled' => 'disabled'), $optBlok) . '</td>';
		$table .= '<td>' . makeElement('tanggal_' . $param['numRow'] . '_' . $i, 'text', $tanggal, array('disabled' => 'disabled')) . '</td>';

		if ($row['statusjurnal'] == 0) {
			$table .= '<td>' . makeElement('hkrealisasi_' . $param['numRow'] . '_' . $i, 'textnum', $row['hkrealisasi']) . '</td>';
			$table .= '<td>' . makeElement('hasilkerjarealisasi_' . $param['numRow'] . '_' . $i, 'textnum', $row['hasilkerjarealisasi'], array('onkeyup' => 'calJumlah(' . $param['numRow'] . ',' . $i . ')')) . '</td>';
			$table .= '<td>' . makeElement('jumlahrealisasi_' . $param['numRow'] . '_' . $i, 'textnum', $row['jumlahrealisasi'], array('onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')) . '</td>';
			$table .= '<td>' . makeElement('jjgkontanan_' . $param['numRow'] . '_' . $i, 'textnum', $row['jjgkontanan'], array('onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')) . '</td>';
				$klSup = substr($norekanan, 0,4);
				$klSupK = substr($norekanan, 0,1);
			if($klSup !='S006' ){
			 $table .= '<td>' . makeElement('notiket_' . $param['numRow'] . '_' . $i, 'hidden', '0') . '</td>';
			   $table .= '<td>' . makeElement('nodo_' . $param['numRow'] . '_' . $i, 'hidden', '0') . '</td>';
			}else{
			$table .= '<td>' . makeElement('nodo_' . $param['numRow'] . '_' . $i, 'select', $row['nodo'], array('onchange' => 'getTiket(this.value,hasilkerjarealisasi_' . $param['numRow'] . '_' . $i.','.'notiket_' . $param['numRow'] . '_' . $i.','.$param['numRow'] . ',' . $i.')'), $optTiket) . '</td>';
			$table .= '<td>' . makeElement('notiket_' . $param['numRow'] . '_' . $i, 'text', $row['notiket'], array('onclick' => 'lihatDataTimbangan(this.value,' . $param['numRow'] . ',' . $i . ')')) . '</td>';
			}
			$table .= '<td><img id=\'btn_' . $param['numRow'] . '_' . $i . '\' class=\'zImgBtn\' ';
			$table .= 'src=\'images/' . $_SESSION['theme'] . '/save.png\' ';
			$table .= 'onclick=\'saveData(' . $param['numRow'] . ',' . $i . ')\'>&nbsp;';
			$table .= '<img id=\'btnDel_' . $param['numRow'] . '_' . $i . '\' class=\'zImgBtn\' ';
			$table .= 'src=\'images/' . $_SESSION['theme'] . '/delete.png\' ';
			$table .= 'onclick=\'deleteData(' . $param['numRow'] . ',' . $i . ')\'>&nbsp;';
			$table .= '<img id=\'btnPost_' . $param['numRow'] . '_' . $i . '\' class=\'zImgBtn\' ';
			$table .= 'src=\'images/' . $_SESSION['theme'] . '/posting.png\' ';
			$table .= 'onclick="postingData(' . $param['numRow'] . ',' . $i . ',\'' . $_SESSION['theme'] . '\')">';
		}
		else {

			$table .= '<td>' . makeElement('hkrealisasi_' . $param['numRow'] . '_' . $i, 'textnum', $row['hkrealisasi'], array('disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('hasilkerjarealisasi_' . $param['numRow'] . '_' . $i, 'textnum', $row['hasilkerjarealisasi'], array('disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('jumlahrealisasi_' . $param['numRow'] . '_' . $i, 'textnum', $row['jumlahrealisasi'], array('disabled' => 'disabled', 'onchange' => 'this.value = _formatted(this)')) . '</td>';
			$table .= '<td>' . makeElement('jjgkontanan_' . $param['numRow'] . '_' . $i, 'textnum', $row['jjgkontanan'], array('disabled' => 'disabled', 'onchange' => 'this.value = _formatted(this)')) . '</td>';
				$klSup = substr($norekanan, 0,4);
				$klSupK = substr($norekanan, 0,1);
			if($klSup !='S006' ){
			 $table .= '<td>' . makeElement('notiket_' . $param['numRow'] . '_' . $i, 'hidden', '0') . '</td>';
			   $table .= '<td>' . makeElement('nodo_' . $param['numRow'] . '_' . $i, 'hidden', '0') . '</td>';
			}else{
			$table .= '<td>' . makeElement('nodo_' . $param['numRow'] . '_' . $i, 'select', $row['nodo'], array('onchange' => 'getTiket(this.value,hasilkerjarealisasi_' . $param['numRow'] . '_' . $i.','.'notiket_' . $param['numRow'] . '_' . $i.','.$param['numRow'] . ',' . $i.')'), $optTiket) . '</td>';
			$table .= '<td>' . makeElement('notiket_' . $param['numRow'] . '_' . $i, 'text', $row['notiket'], array('onclick' => 'lihatDataTimbangan(this.value,' . $param['numRow'] . ',' . $i . ')')) . '</td>';
			}
			$table .= '<td>&nbsp;&nbsp;<img id=\'btnPost_' . $param['numRow'] . '_' . $i . '\' class=\'zImgBtn\' ';
			$table .= 'src=\'images/' . $_SESSION['theme'] . '/posted.png\'>';
		}

		$table .= '</td>';
		$table .= '</tr>';
		++$i;

	}

	$table .= '<tr id=\'tr_' . $param['numRow'] . '_' . $i . '\' class=\'rowcontent\'>';
	$table .= '<td>' . makeElement('blokalokasi_' . $param['numRow'] . '_' . $i, 'select', '', array(), $optBlok) . '</td>';
	$table .= '<td>' . makeElement('tanggal_' . $param['numRow'] . '_' . $i, 'date') . '</td>';
	$table .= '<td>' . makeElement('hkrealisasi_' . $param['numRow'] . '_' . $i, 'textnum', 0) . '</td>';
	$table .= '<td>' . makeElement('hasilkerjarealisasi_' . $param['numRow'] . '_' . $i, 'textnum', 0, array('onkeyup' => 'calJumlah(' . $param['numRow'] . ',' . $i . ')')) . '</td>';
	$table .= '<td>' . makeElement('jumlahrealisasi_' . $param['numRow'] . '_' . $i, 'textnum', 0, array('onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')) . '</td>';
	$table .= '<td>' . makeElement('jjgkontanan_' . $param['numRow'] . '_' . $i, 'textnum', 0, array('onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')) . '</td>';
			$klSup = substr($norekanan, 0,4);
				$klSupK = substr($norekanan, 0,1);
			if($klSup !='S006' ){
			 $table .= '<td>' . makeElement('notiket_' . $param['numRow'] . '_' . $i, 'hidden', '0') . '</td>';
			  $table .= '<td>' . makeElement('nodo_' . $param['numRow'] . '_' . $i, 'hidden', '0') . '</td>';
			}else{
			$table .= '<td>' . makeElement('nodo_' . $param['numRow'] . '_' . $i, 'select', $row['notiket'], array('onchange' => 'getTiket(this.value,hasilkerjarealisasi_' . $param['numRow'] . '_' . $i.','.'notiket_' . $param['numRow'] . '_' . $i.','.$param['numRow'] . ',' . $i.')'), $optTiket) . '</td>';
			$table .= '<td>' . makeElement('notiket_' . $param['numRow'] . '_' . $i, 'text', '', array('onclick' => 'lihatDataTimbangan(this.value,' . $param['numRow'] . ',' . $i . ')')) . '</td>';
			}
	$table .= '<td><img id=\'btn_' . $param['numRow'] . '_' . $i . '\' class=\'zImgBtn\' ';
	$table .= 'src=\'images/' . $_SESSION['theme'] . '/plus.png\' ';
	$table .= 'onclick="addData(' . $param['numRow'] . ',' . $i . ',\'' . $_SESSION['theme'] . '\')">&nbsp;';
	$table .= '<img id=\'btnDel_' . $param['numRow'] . '_' . $i . '\' class=\'zImgBtn\' ';
	$table .= 'src=\'images/' . $_SESSION['theme'] . '/delete.png\' style=\'display:none\'';
	$table .= 'onclick=\'deleteData(' . $param['numRow'] . ',' . $i . ')\'>&nbsp;';
	$table .= '<img id=\'btnPost_' . $param['numRow'] . '_' . $i . '\' class=\'zImgBtn\' ';
	$table .= 'src=\'images/' . $_SESSION['theme'] . '/posting.png\' ';
	$table .= 'onclick="postingData(' . $param['numRow'] . ',' . $i . ',\'' . $_SESSION['theme'] . '\')" style=\'display:none\'>';
	$table .= '</td>';
	$table .= '</tr>';
	++$i;
	$table .= '</tbody>';
	$table .= '</table>';

	echo $table;
	
	break;

case 'add':
	$data = $param;
	unset($data['numRow1']);
	unset($data['divisi']);
	unset($data['blokalokasi']);
	unset($data['numRow2']);
	unset($data['tanggalSpk']);
	$data['kodeblok'] = $param['blokalokasi'];
	$data['posting'] = '0';
	$data['statusjurnal'] = '0';
	$data['blokspkdt'] = $param['kodeblok'];

	if (tanggalsystem($param['tanggal']) < tanggalsystem($param['tanggalSpk'])) {
		exit('Warning: Tanggal Realisasi tidak boleh sebelum tanggal SPK');
	}

	$data['jumlahrealisasi'] = str_replace(',', '', $data['jumlahrealisasi']);
	$data['jjgkontanan'] = str_replace(',', '', $data['jjgkontanan']);
	$dtCol = array('notransaksi', 'kodeblok', 'kodekegiatan', 'tanggal', 'hasilkerjarealisasi', 'hkrealisasi','notiket','nodo', 'jumlahrealisasi', 'jjgkontanan', 'posting', 'statusjurnal', 'blokspkdt');
	$optBlok = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', 'induk=\'' . $param['divisi'] . '\' or kodeorganisasi like \'' . $param['divisi'] . '%\'');
	if ((substr($param['divisi'], 0, 2) == 'AK') || (substr($param['divisi'], 0, 2) == 'PB')) {
		$optBlok = makeOption($dbname, 'project', 'kode,nama', 'kode=\'' . $param['divisi'] . '\' and posting=0');
	}

	foreach ($data as $cont) {
		if ($cont == '') {
			echo 'Warning : Data tidak boleh ada yang kosong'.$cont['notiket'];
			exit();
		}
	}

	$data['tanggal'] = tanggalsystemw($data['tanggal']);
	$query = insertQuery($dbname, 'log_baspk', $data, $dtCol);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}
	else {
			$queryR = "SELECT trpcode FROM pabrik_timbangan  WHERE nokontrak='".$param['notransaksi']."' limit 1";
	
	$queryRek = mysql_query($queryR);
	$dt = mysql_fetch_array($queryRek);
	$norekanan = $dt['trpcode'];

	$notransaksi = $param['notransaksi'];
	$queryDO = "select notiket from log_baspk WHERE notiket!='' and notransaksi='".$notransaksi."' ";
	$queryActDo = mysql_query($queryDO);
	$queryAct = mysql_query($queryTiket);
	 while($dataTik = mysql_fetch_array($queryActDo)){
	 		$dataT = explode(",",$dataTik['notiket']);
	 		foreach($dataT as $T) {
    		$T = trim($T);
   			 $list .= "'" . $T . "',";
			}
			 $listTiketed .= $list;
	 }

	$queryTiket = "SELECT a.nosipb as nosipb FROM pabrik_timbangan a,log_5supplier b WHERE a.trpcode=b.supplierid and a.trpcode='".$norekanan	."' AND a.notransaksi NOT IN (".$listTiketed."'--') and a.nokontrak = '".$notransaksi	."' and  a.IsPosting='1' group by nosipb";
	
	$queryAct = mysql_query($queryTiket);
	
	
	$optTiket[0] = " ";
	 while($data2 = mysql_fetch_array($queryAct)){
			 $optTiket[$data2['nosipb']] .= $data2['nosipb'];
	 }
		$i = $param['numRow2'] + 1;
		$row = '<td>' . makeElement('blokalokasi_' . $param['numRow1'] . '_' . $i, 'select', '', array(), $optBlok) . '</td>';
		$row .= '<td>' . makeElement('tanggal_' . $param['numRow1'] . '_' . $i, 'date') . '</td>';
		$row .= '<td>' . makeElement('hkrealisasi_' . $param['numRow1'] . '_' . $i, 'textnum', 0) . '</td>';
		$row .= '<td>' . makeElement('hasilkerjarealisasi_' . $param['numRow1'] . '_' . $i, 'textnum', 0, array('onkeyup' => 'calJumlah(' . $param['numRow1'] . ',' . $i . ')')) . '</td>';
		$row .= '<td>' . makeElement('jumlahrealisasi_' . $param['numRow1'] . '_' . $i, 'textnum', 0, array('onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')) . '</td>';
		$row .= '<td>' . makeElement('jjgkontanan_' . $param['numRow1'] . '_' . $i, 'textnum', 0, array('onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)')) . '</td>';
		$klSup = substr($norekanan, 0,4);
				$klSupK = substr($norekanan, 0,1);
	
			if($klSup !='S006' ){
			 $row .= '<td>' . makeElement('notiket_' . $param['numRow1'] . '_' . $i, 'hidden', '0') . '</td>';
			  $row .= '<td>' . makeElement('nodo_' . $param['numRow1'] . '_' . $i, 'hidden', '0') . '</td>';
			}else{
			$row .= '<td>' . makeElement('nodo_' . $param['numRow1'] . '_' . $i, 'select', $row['notiket'], array('onchange' => 'getTiket(this.value,hasilkerjarealisasi_' . $param['numRow1'] . '_' . $i.','.'notiket_' . $param['numRow1'] . '_' . $i.','.$param['numRow1'] . ',' . $i.')'), $optTiket) . '</td>';
			$row .= '<td>' . makeElement('notiket_' . $param['numRow1'] . '_' . $i, 'text', '', array('onclick' => 'lihatDataTimbangan(this.value,' . $param['numRow'] . ',' . $i . ')')) . '</td>';
			}
		
		$row .= '<td><img id=\'btn_' . $param['numRow1'] . '_' . $i . '\' class=\'zImgBtn\' ';
		$row .= 'src=\'images/' . $_SESSION['theme'] . '/plus.png\' ';
		$row .= 'onclick="addData(' . $param['numRow1'] . ',' . $i . ',\'' . $_SESSION['theme'] . '\')">&nbsp;';
		$row .= '<img id=\'btnDel_' . $param['numRow1'] . '_' . $i . '\' class=\'zImgBtn\' ';
		$row .= 'src=\'images/' . $_SESSION['theme'] . '/delete.png\' style=\'display:none\'';
		$row .= 'onclick=\'deleteData(' . $param['numRow1'] . ',' . $i . ')\'>&nbsp;';
		$row .= '<img id=\'btnPost_' . $param['numRow1'] . '_' . $i . '\' class=\'zImgBtn\' ';
		$row .= 'src=\'images/' . $_SESSION['theme'] . '/posting.png\' ';
		$row .= 'onclick="postingData(' . $param['numRow1'] . ',' . $i . ',\'' . $_SESSION['theme'] . '\')" style=\'display:none\'>';
		$row .= '</td>';
		echo $row;
	}

	break;

case 'edit':
	$data = $param;
	unset($data['notransaksi']);
	unset($data['kodeblok']);
	unset($data['blokalokasi']);
	unset($data['kodekegiatan']);
	unset($data['tanggal']);
	unset($data['tanggalSpk']);
	unset($data['numRow1']);
	unset($data['numRow2']);
	$data['jumlahrealisasi'] = str_replace(',', '', $data['jumlahrealisasi']);
	$data['jjgkontanan'] = str_replace(',', '', $data['jjgkontanan']);

	if (tanggalsystem($param['tanggal']) < tanggalsystem($param['tanggalSpk'])) {
		exit('Warning: Tanggal Realisasi tidak boleh sebelum tanggal SPK');
	}

	foreach ($data as $cont) {
		if ($cont == '') {
			echo 'Warning : Data tidak boleh ada yang kosong';
			exit();
		}
	}

	$param['tanggal'] = tanggalsystem($param['tanggal']);
	$where = 'notransaksi=\'' . $param['notransaksi'] . '\' and kodeblok=\'' . $param['blokalokasi'] . '\' and kodekegiatan=\'' . $param['kodekegiatan'] . '\' and tanggal=\'' . $param['tanggal'] . '\' and blokspkdt=\'' . $param['kodeblok'] . '\'';
	$query = updateQuery($dbname, 'log_baspk', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	break;

case 'delete':
	$param['tanggal'] = tanggalsystem($param['tanggal']);
	$where = 'notransaksi=\'' . $param['notransaksi'] . '\' and kodeblok=\'' . $param['blokalokasi'] . '\' and kodekegiatan=\'' . $param['kodekegiatan'] . '\' and tanggal=\'' . $param['tanggal'] . '\' and blokspkdt=\'' . $param['kodeblok'] . '\'';
	$query = 'delete from `' . $dbname . '`.`log_baspk` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
		exit();
	}

	break;
	case 'gettiket':
	$nodo = $param['notiket'];
	$tgl = tanggaldgnbar($param['tanggal']);
	$queryDO = "select notiket from log_baspk WHERE notiket!='' and nodo='".$nodo."' ";
	
	$queryActDo = mysql_query($queryDO);
	$queryAct = mysql_query($queryTiket);
	 while($data = mysql_fetch_array($queryActDo)){
	 		$dataT = explode(",",$data['notiket']);
	 		foreach($dataT as $T) {
    		$T = trim($T);
   			  $list .= "'" . $T . "',";
			}
			 $listTiketed .= $list;
	 }

	$queryTiket = "SELECT a.notransaksi as notiket,a.beratbersih as beratbersih  FROM pabrik_timbangan a,log_5supplier b WHERE a.trpcode=b.supplierid  AND a.notransaksi NOT IN (".$listTiketed."'--') and a.nosipb = '".$nodo	."' and date(a.tanggal)= '".$tgl."' and a.IsPosting='1' ";

	$queryAct = mysql_query($queryTiket);
	$tiketdetail="";
	$beratbersih=0;
	 while($data = mysql_fetch_array($queryAct)){
			 $tiketdetail .= $data['notiket'].",";
			 $beratbersih += $data['beratbersih'];
	 }
	// $query = "select * from ". $dbname .".pabrik_timbangan where nodo='".$nodo."' ";

	//   $qdata = mysql_query($query);
 //      $rdata = mysql_fetch_assoc($qdata);
 //      $tonase = $rdata['beratbersih'];
      echo $beratbersih."#".$tiketdetail;
	

	break;
}

?>

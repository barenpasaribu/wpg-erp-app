<?php


function formHeader($mode, $data)
{
	global $dbname;

	if (empty($data)) {
		$data['kodeorg'] = '';
		$data['notransaksi'] = '0';
		$data['tanggal'] = '';
		$data['divisi'] = '';
		$data['koderekanan'] = '';
		$data['nilaikontrak'] = '0';
		$data['keterangan'] = '';
		$data['dari'] = '';
		$data['sampai'] = '';
	}
	else {
		$data['nilaikontrak'] = number_format($data['nilaikontrak']);
	}

	if ($mode == 'edit') {
		$disabled = 'disabled';
	}
	else {
		$disabled = '';
	}

	if (($_SESSION['empl']['tipelokasitugas'] == 'TRAKSI') || ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') || ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL')) {
		$whereOrg = 'length(kodeorganisasi)=4';
	}
	else {
		$whereOrg = 'kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
	}

	$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg);

	if ($data['divisi'] == '') {
		if ($_SESSION['empl']['tipelokasitugas'] == 'TRAKSI') {
			$whereDiv = 'induk=\'' . getFirstKey($optOrg) . '\' or kodeorganisasi=\'' . getFirstKey($optOrg) . '\'';
		}
		else {
			$whereDiv = 'induk=\'' . $_SESSION['empl']['lokasitugas'] . '\' or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
		}
	}
	else {
		$whereDiv = 'induk=\'' . $data['kodeorg'] . '\' or kodeorganisasi=\'' . $data['kodeorg'] . '\'';
	}

	$optDiv = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereDiv);

	if (!empty($data['kodeorg'])) {
		$tmpDiv = $data['kodeorg'];
	}
	else {
		$tmpDiv = $_SESSION['empl']['lokasitugas'];
	}

	$str = 'select kode,nama from ' . $dbname . '.project where kodeorg=\'' . $tmpDiv . '\' and posting=0';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$optDiv[$bar->kode] = '[Project]-' . $bar->nama;
	}

	$optSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'left(kodekelompok,1)=\'K\' or kodekelompok like \'S0%\'');
	$els = array();
	if (($_SESSION['empl']['tipelokasitugas'] == 'TRAKSI') || ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') || ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL')) {
		$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], array('style' => 'width:200px', $disabled => $disabled, 'onchange' => 'updSub()'), $optOrg));
	}
	else {
		$els[] = array(makeElement('kodeorg', 'label', $_SESSION['lang']['kebun']), makeElement('kodeorg', 'select', $data['kodeorg'], array('style' => 'width:200px', $disabled => $disabled), $optOrg));
	}

	$els[] = array(makeElement('notransaksi', 'label', $_SESSION['lang']['notransaksi']), makeElement('notransaksi', 'text', $data['notransaksi'], array('style' => 'width:200px', 'maxlength' => '50', $disabled => $disabled)));
	$els[] = array(makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('divisi', 'label', $_SESSION['lang']['subunit']), makeElement('divisi', 'select', $data['divisi'], array('style' => 'width:200px', $disabled => $disabled), $optDiv));
	$els[] = array(makeElement('koderekanan', 'label', $_SESSION['lang']['koderekanan']), makeElement('koderekanan', 'selectsearch', $data['koderekanan'], array('style' => 'width:300px'), $optSup));
	$els[] = array(makeElement('nilaikontrak', 'label', $_SESSION['lang']['nilaikontrak']), makeElement('nilaikontrak', 'textnum', $data['nilaikontrak'], array('style' => 'width:200px', 'maxlength' => '15', 'this.value=remove_comma(this);onchange' => 'this.value = _formatted(this)')));
	$els[] = array(makeElement('keterangan', 'label', $_SESSION['lang']['project']), makeElement('keterangan', 'text', $data['keterangan'], array('style' => 'width:200px', 'maxlength' => '50')));
	$els[] = array(makeElement('dari', 'label', $_SESSION['lang']['dari']), makeElement('dari', 'text', $data['dari'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));
	$els[] = array(makeElement('sampai', 'label', $_SESSION['lang']['sampai']), makeElement('sampai', 'text', $data['sampai'], array('style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)')));

	if ($mode == 'add') {
		$els['btn'] = array(makeElement('addHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'addDataTable()')));
	}
	else if ($mode == 'edit') {
		$els['btn'] = array(makeElement('editHead', 'btn', $_SESSION['lang']['save'], array('onclick' => 'editDataTable()')));
	}

	if ($mode == 'add') {
		return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
	}

	if ($mode == 'edit') {
		return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
	}
}

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$proses = $_GET['proses'];
$param = $_POST;

switch ($proses) {
case 'showHeadList':
	if (($_SESSION['empl']['tipelokasitugas'] == 'TRAKSI') || ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') || ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL')) {
		// $where = 'length(kodeorg)=4';
		$where = 'kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
	}
	else {
		$where = 'kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
	}

	if (isset($param['where'])) {
		$arrWhere = json_decode(str_replace('\\', '', $param['where']), true);

		if (!empty($arrWhere)) {
			foreach ($arrWhere as $key => $r1) {
				$where .= ' and ' . $r1[0] . ' like \'%' . $r1[1] . '%\'';
			}
		}
	}

	$header = array($_SESSION['lang']['kodeorg'], $_SESSION['lang']['notransaksi'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['subunit'], $_SESSION['lang']['koderekanan'], $_SESSION['lang']['nilaikontrak'], $_SESSION['lang']['dari'], $_SESSION['lang']['sampai'], $_SESSION['lang']['jumlahrealisasi'], $_SESSION['lang']['status']);
	$cols = 'kodeorg,notransaksi,tanggal,divisi,koderekanan,nilaikontrak,dari,sampai';	
	$query = selectQuery($dbname, 'log_spkht', $cols, $where . ' order by tanggal desc', '', false, $param['shows'], $param['page']);
	$data = fetchData($query);
	$totalRow = getTotalRow($dbname, 'log_spkht', $where);

	foreach ($data as $key => $row) {
		$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
		$data[$key]['dari'] = tanggalnormal($row['dari']);
		$data[$key]['sampai'] = tanggalnormal($row['sampai']);
		$data[$key]['realisasi'] = 0;
		$strx = 'select sum(jumlahrealisasi) from ' . $dbname . '.log_baspk' . "\r\n\t\t\t\t\t" . '  where notransaksi=\'' . $data[$key]['notransaksi'] . '\'';
		// $strx = 'select sum(jumlahrealisasi) from ' . $dbname . '.log_baspk where notransaksi=\'' . $data[$key]['notransaksi'] . '\' and blokspkdt = \'' . $data[$key]['divisi'] . '\'';
		$resx = mysql_query($strx);

		while ($barx = mysql_fetch_array($resx)) {
			$data[$key]['realisasi'] = number_format($barx[0]);
		}

		$data[$key]['status'] = '';
		$strx = 'select statusjurnal from ' . $dbname . '.log_baspk' . "\r\n\t\t\t\t\t" . '  where notransaksi=\'' . $data[$key]['notransaksi'] . '\' and statusjurnal=0';
		$resx = mysql_query($strx);

		if (0 < mysql_num_rows($resx)) {
			$data[$key]['status'] = '?';
		}
		else if (($data[$key]['realisasi'] == 0) && ($data[$key]['status'] == '')) {
			$data[$key]['status'] = '?';
		}
		else {
			$data[$key]['status'] = 'Ready to Post';
		}

		$stru = 'select posting,useridapprove from ' . $dbname . '.log_spkht where notransaksi=\'' . $data[$key]['notransaksi'] . '\'';
		$resu = mysql_query($stru);
		$post = 0;
		$useridapprove = 0;
		//echo $stru;
		while ($baru = mysql_fetch_array($resu)) {
			$post = $baru[0];
			$useridapprove = (int)$baru[1];
		}

		// if ($post == 1) {
		// 	$data[$key]['status'] = 'Posted';
		// }elseif( $post == 0 && $useridapprove != 0){
		// 	$data[$key]['status'] = 'Approved';
		// }

		if ($post == 1) {
			$data[$key]['status'] = 'Posted';
			$data[$key]['switched'] = true;
		}
	
		unset($data[$key]['posting']);
	}

	if (!empty($data)) {
		$whereSupp = 'supplierid in (';

		foreach ($data as $key => $row) {
			if ($key == 0) {
				$whereSupp .= '\'' . $row['koderekanan'] . '\'';
			}
			else {
				$whereSupp .= ',\'' . $row['koderekanan'] . '\'';
			}
		}

		$whereSupp .= ')';
	}
	else {
		$whereSupp = NULL;
	}

	$optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', $whereSupp);
	$dataShow = $data;

	foreach ($dataShow as $key => $row) {
		$dataShow[$key]['koderekanan'] = $optSupp[$row['koderekanan']];
		$dataShow[$key]['nilaikontrak'] = number_format($row['nilaikontrak'], 0);
	}

	// $qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', 'kodeaplikasi=\'panen\'');
	// $tmpPost = fetchData($qPosting);
	// $postJabatan = $tmpPost[0]['jabatan'];
	// $tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
	// $tHeader->addAction('showEdit', 'Edit', 'images/' . $_SESSION['theme'] . '/edit.png');
	// $tHeader->addAction('deleteData', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
	// $tHeader->addAction('postingData', 'Posting', 'images/' . $_SESSION['theme'] . '/posting.png');
	// $tHeader->_actions[2]->setAltImg('images/' . $_SESSION['theme'] . '/posted.png');

	// if ($_SESSION['empl']['tipelokasitugas' != 'HOLDING']) {
	// 	if ($postJabatan != $_SESSION['empl']['kodejabatan']) {
	// 		$tHeader->_actions[2]->_name = '';
	// 	}
	// }
	$qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', 'kodeaplikasi=\'panen\'');
	$tmpPost = fetchData($qPosting);
	$postJabatan = $tmpPost[0]['jabatan'];
	$tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
	$tHeader->addAction('showEdit', 'Edit', 'images/' . $_SESSION['theme'] . '/edit.png');
	$tHeader->addAction('deleteData', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
	$tHeader->addAction('postingData', 'Posting', 'images/' . $_SESSION['theme'] . '/posting.png');
	$tHeader->_actions[2]->setAltImg('images/' . $_SESSION['theme'] . '/posted.png');

	if ($_SESSION['empl']['tipelokasitugas' != 'HOLDING']) {
		if ($postJabatan != $_SESSION['empl']['kodejabatan']) {
			$tHeader->_actions[2]->_name = '';
		}
	}

	$tHeader->addAction('detailPDF', 'Print Data Detail', 'images/' . $_SESSION['theme'] . '/pdf.jpg');
	$tHeader->_actions[3]->addAttr('event');
	$tHeader->_switchException = array('detailPDF');
	$tHeader->pageSetting($param['page'], $totalRow, $param['shows']);

	if (isset($param['where'])) {
		$tHeader->setWhere($arrWhere);
	}

	$tHeader->renderTable();
	break;

case 'showAdd':
	echo formHeader('add', array());
	echo '<div id=\'detailField\' style=\'clear:both\'></div>';
	break;

case 'showEdit':
	$where = 'notransaksi=\'' . $param['notransaksi'] . '\' and kodeorg=\'' . $param['kodeorg'] . '\'';
	$query = selectQuery($dbname, 'log_spkht', '*', $where);
	$tmpData = fetchData($query);
	$data = $tmpData[0];
	$data['tanggal'] = tanggalnormal($data['tanggal']);
	$data['dari'] = tanggalnormal($data['dari']);
	$data['sampai'] = tanggalnormal($data['sampai']);
	echo formHeader('edit', $data);
	echo '<div id=\'detailField\' style=\'clear:both\'></div>';
	break;

case 'add':
	$data = $_POST;
	$warning = '';

	if ($data['notransaksi'] == '') {
		$warning .= 'No SPK harus diisi' . "\n";
	}

	if ($data['tanggal'] == '') {
		$warning .= 'Tanggal harus diisi' . "\n";
	}

	if ($data['kodeorg'] == '') {
		$warning = 'Lokasi Tugas harus Kebun' . "\n";
	}

	if ($warning != '') {
		echo 'Warning :' . "\n" . $warning;
		exit();
	}

	$data['tanggal'] = tanggalsystemw($data['tanggal']);
	$data['dari'] = tanggalsystem($data['dari']);
	$data['sampai'] = tanggalsystem($data['sampai']);
	$data['nilaikontrak'] = str_replace(',', '', $data['nilaikontrak']);
	$data['useridcreate'] = $_SESSION['standard']['userid'];
	$data['tglcreate'] = date("Y-m-d");
	
	
	$cols = array('kodeorg', 'notransaksi', 'tanggal', 'divisi', 'koderekanan', 'nilaikontrak', 'dari', 'sampai', 'keterangan', 'useridcreate', 'tglcreate');
	$query = insertQuery($dbname, 'log_spkht', $data, $cols);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}

	break;

case 'edit':
	$data = $_POST;
	$where = 'notransaksi=\'' . $data['notransaksi'] . '\'';
	unset($data['notransaksi']);
	$data['tanggal'] = tanggalsystemw($data['tanggal']);
	$data['dari'] = tanggalsystem($data['dari']);
	$data['sampai'] = tanggalsystem($data['sampai']);
	$data['nilaikontrak'] = str_replace(',', '', $data['nilaikontrak']);
	$query = updateQuery($dbname, 'log_spkht', $data, $where);

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error();
	}

	break;

case 'delete':
	$m = 0;
	$strx = 'select sum(jumlahrealisasi) from ' . $dbname . '.log_baspk' . "\r\n" . '                  where notransaksi=\'' . $param['notransaksi'] . '\'';
	$resx = mysql_query($strx);

	while ($barx = mysql_fetch_array($resx)) {
		$m = $barx[0];
	}

	$n = '';
	$strx = 'select statusjurnal from ' . $dbname . '.log_baspk' . "\r\n" . '                  where notransaksi=\'' . $param['notransaksi'] . '\' and statusjurnal=0';
	$resx = mysql_query($strx);

	if (0 < mysql_num_rows($resx)) {
		$n = '?';
	}

	if (($n == '') && ($m == 0)) {
		$where = 'notransaksi=\'' . $param['notransaksi'] . '\'';
		$query = 'delete from `' . $dbname . '`.`log_spkht` where ' . $where;

		if (!mysql_query($query)) {
			echo 'DB Error : ' . mysql_error();
			exit();
		}
	}
	else {
		exit('Error:Realisasi sudah terisi');
	}

	break;

case 'updSub':
	$whereDiv = 'induk=\'' . $param['kodeorg'] . '\' or kodeorganisasi=\'' . $param['kodeorg'] . '\'';
	$optDiv = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereDiv);
	$str = 'select kode,nama from ' . $dbname . '.project where kodeorg=\'' . $param['kodeorg'] . '\' and posting=0';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$optDiv[$bar->kode] = '[Project]-' . $bar->nama;
	}

	echo json_encode($optDiv);
	break;
}

?>

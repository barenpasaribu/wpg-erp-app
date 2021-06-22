<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$proses = $_GET['proses'];
$param = $_POST;
unset($param['par']);
switch ($proses) {
    case 'showHeadList':
        $where = '';
        if (isset($param['where'])) {
            $tmpW = str_replace('\\', '', $param['where']);
            $arrWhere = json_decode($tmpW, true);
            if (!empty($arrWhere)) {
                foreach ($arrWhere as $key => $r1) {
                    if ('' != $where) {
                        $where .= ' and ';
                    }
                    $where .= $r1[0]." like '%".$r1[1]."%'";
                }
            }
        }

        if (!empty($where)) {
            $where = 'where '.$where;
        }

        $header = [$_SESSION['lang']['nodok'], $_SESSION['lang']['namakaryawan'], $_SESSION['lang']['periodegaji'], $_SESSION['lang']['masakerja'], $_SESSION['lang']['total']];
        $limit = ($param['page'] - 1) * $param['shows'];
        $query = 'select a.nodok,a.karyawanid,b.namakaryawan,a.periodegaji,a.masakerja,a.total 
			from '.$dbname.'.sdm_pesangonht a 
			join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid 
			'.$where.' AND b.kodeorganisasi LIKE "'.substr($_SESSION['empl']['kodeorganisasi'], 0, 3).'%"
			order by a.tanggal desc limit '.$limit.','.$param['shows'];
		//echo $query;
        $data = fetchData($query);
        if (empty($where)) {
            $where = null;
        } else {
            $where = str_replace('where', '', $where);
        }
        $joinLeft = [['table' => 'datakaryawan', 'refCol' => 'karyawanid', 'targetCol' => 'karyawanid']];
        $totalRow = getTotalRow($dbname, 'sdm_pesangonht', $where, $joinLeft);
        $dataShow = $data;
        foreach ($data as $key => $row) {
            $dataShow[$key]['karyawanid'] = $row['namakaryawan'];
            $dataShow[$key]['total'] = number_format($row['total'], 2);
            unset($data[$key]['namakaryawan'], $dataShow[$key]['namakaryawan']);
        }
        $tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
        $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
        $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
        $tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');
        $tHeader->_actions[2]->addAttr('event');
        $tHeader->_switchException = ['detailPDF'];
        $tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
        if (isset($param['where'])) {
            $tHeader->setWhere($arrWhere);
        }

        $tHeader->renderTable();

        break;
    case 'showAdd':
        echo formHeader('add', []);
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'showEdit':
        $query = selectQuery($dbname, 'sdm_pesangonht', '*', "karyawanid='".$param['karyawanid']."'");
		//echo $query;
        $tmpData = mysql_query($query);
		$runData = mysql_fetch_array($tmpData);
        $data = $runData[0];
		//echo $runData['karyawanid'];
        echo formHeader('edit', $runData);
		//echo $param['karyawanid'];
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'add':
        $data = ['tanggal' => $param['tanggal'], 'periodegaji' => $param['periodegaji'], 'masakerja' => $param['masakerja'], 'lembur' => $param['lembur'], 'karyawanid' => $param['karyawanid'], 'alasankeluar' => $param['alasankeluar'], 'tanggalkeluar' => $param['tanggalkeluar'], 'nodok' => $param['nodok'], 'pesangon' => 0, 'penghargaan' => 0, 'pengganti' => 0, 'perusahaan' => 0, 'kesalahanbiasa' => 0, 'kesalahanbesar' => 0, 'uangpisah' => 0, 'total' => 0, 'pph' => 0];
        $tglArr = explode('-', $param['tanggal']);
        // $qGaji = selectQuery($dbname, 'sdm_5gajipokok', 'jumlah', "karyawanid='".$param['karyawanid']."' and tahun=".$tglArr[2].' and idkomponen=1');
        // $resGaji = fetchData($qGaji);
        // if (!empty($resGaji)) {
        //     $gaji = $resGaji[0]['jumlah'];
        // } else {
        //     $gaji = 0;
        // }

        $qPes = selectQuery($dbname, 'sdm_5pesangon', 'pesangon,penghargaan,pengganti,perusahaan,kesalahanbiasa,kesalahanberat,uangpisah', 'masakerja='.$param['masakerja']);
        $resPes = fetchData($qPes);
        if (!empty($resPes)) {
            $pes = $resPes[0];
        } else {
            $pes = ['pesangon' => 0, 'penghargaan' => 0, 'pengganti' => 0, 'perusahaan' => 0, 'kesalahanbiasa' => 0, 'kesalahanbesar' => 0, 'uangpisah' => 0];
        }

        $data['pesangon'] = str_replace(',', '', $pes['pesangon']);
        $data['penghargaan'] = str_replace(',', '', $pes['penghargaan']);
        $data['pengganti'] = str_replace(',', '', $pes['pengganti']);
        $data['perusahaan'] = str_replace(',', '', $pes['perusahaan']);
        $data['kesalahanbiasa'] = str_replace(',', '', $pes['kesalahanbiasa']);
        $data['kesalahanbesar'] = str_replace(',', '', $pes['kesalahanbesar']);
        $data['uangpisah'] = str_replace(',', '', $pes['uangpisah']);

        $subTotal = $data['pesangon'] + $data['penghargaan'] + $data['pengganti'] + $data['perusahaan'] + $data['kesalahanbiasa'] + $data['kesalahanbesar'] + $data['uangpisah'] ;
        $pph = 0;
        $data['total'] = $subTotal - $pph;
        $data['pph'] = $pph;

        $warning = '';
        if ('' == $data['tanggal']) {
            $warning .= "Date is obligatory\n";
        }

        if ('' == $data['tanggalkeluar']) {
            $warning .= "Resignation Date is obligatory\n";
        }

        if ('' != $warning) {
            echo "Warning :\n".$warning;
            exit();
        }

        $data['tanggalkeluar'] = tanggalsystemw($data['tanggalkeluar']);
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $cols = [];
        foreach ($data as $key => $row) {
            $cols[] = $key;
        }
        $query = insertQuery($dbname, 'sdm_pesangonht', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }
        break;
    case 'edit':
        $data = $_POST;
		$tgl = tanggalsystemw($data['tanggal']);
		$klrtgl = tanggalsystemw($data['tanggalkeluar']);
		$tanggal = substr($tgl, 0,4) . '-'. substr($tgl, 4, 2) . '-' . substr($tgl, 6,2);
		$ktgl = substr($klrtgl, 0,4) . '-'. substr($klrtgl, 4, 2) . '-' . substr($klrtgl, 6,2);
        $where = "karyawanid='".$data['karyawanid']."'";
		$sql = "UPDATE sdm_pesangonht SET 
		tanggal= '". $tanggal . "',
		periodegaji='".$data['periodegaji']. "',
		nodok='".$data['nodok']. "',
		tanggalkeluar= '". $ktgl ."',
		pesangon= '". str_replace(',', '', $data['pesangon']) . "',
        penghargaan= '". str_replace(',', '', $data['penghargaan']) . "', 
        pengganti= '". str_replace(',', '', $data['pengganti']) . "', 
        perusahaan= '". str_replace(',', '', $data['perusahaan']) . "', 
        kesalahanbiasa= '". str_replace(',', '', $data['kesalahanbiasa']) . "',
        kesalahanbesar= '". str_replace(',', '', $data['kesalahanbesar']) . "',
        total= '". str_replace(',', '', $data['detailDiterima']) . "',
        uangpisah= '". str_replace(',', '', $data['uangpisah']) . "',
        pph= '". str_replace(',', '', $data['pph']) . "' 
		WHERE karyawanid = '". $data['karyawanid'] ."'";
        $query = $sql;
		//updateQuery($dbname, 'sdm_pesangonht', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }
        break;
    case 'delete':
        $where = "karyawanid='".$param['karyawanid']."'";
        $query = 'delete from `'.$dbname.'`.`sdm_pesangonht` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }
        break;
    case 'changeKary':
		// cek dahulu, sdh pernah diinput pesangonnya atau belum - FA 20200831
		$sql = "select * from sdm_pesangonht where karyawanid =".$param['karyawanid']; 
		$qstr = mysql_query($sql);
		if (mysql_num_rows($qstr)>0){
			echo "Warning: Pesangon atas karyawan ini sudah pernah diinput, cek kembali di Susunan Data";
			exit();
		}	

        $qKaryInfo = 'select lokasitugas,tanggalmasuk,tanggalkeluar from '.$dbname.'.datakaryawan where karyawanid='.$param['karyawanid'];
        $resKaryInfo = fetchData($qKaryInfo);
        $tglMasukArr = explode('-', $resKaryInfo[0]['tanggalmasuk']);
        $optPeriod = makeOption($dbname, 'sdm_5periodegaji', 'periode,periode', "kodeorg='".$resKaryInfo[0]['lokasitugas']."'");
        $masaKerja = date('Y') - $tglMasukArr[0];
        $res = ['period' => $optPeriod, 'masakerja' => $masaKerja, 'tanggalkeluar' => tanggalnormal($resKaryInfo[0]['tanggalkeluar'])];
        echo json_encode($res);

        break;
    default:
        break;
}

function formHeader($mode, $data)
{
    global $dbname;
	
    if (empty($data)) {
        $data['karyawanid'] = 0;
        $data['periodegaji'] = date('Y-m');
        $data['tanggal'] = date('d-m-Y');
        $data['masakerja'] = 0;
        $data['lembur'] = 0;
        $data['alasankeluar'] = 'perusahaan';
        $data['tanggalkeluar'] = '';
        $data['nodok'] = '';
        $data['pesangon'] = 0;
        $data['penghargaan'] = 0;
        $data['pengganti'] = 0;
        $data['perusahaan'] = 0;
        $data['kesalahanbiasa'] = 0;
        $data['kesalahanbesar'] = 0;
        $data['uangpisah'] = 0;
        $data['total'] = 0;
    } else {
        $data['tanggal'] = tanggalnormal($data['tanggal']);
        $data['tanggalkeluar'] = tanggalnormal($data['tanggalkeluar']);
    }
    if ('edit' == $mode) {
        $disabled = 'disabled';
    } else {
        $disabled = '';
    }

/*
    $qKary = 'select a.karyawanid,a.namakaryawan from '.$dbname.'.datakaryawan a join '.$dbname.".bgt_regional_assignment b on 
    a.lokasitugas=b.kodeunit where a.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and b.regional='".$_SESSION['empl']['regional']."' and a.tanggalkeluar IS NULL and a.isduplicate = 0 ORDER BY namakaryawan ASC";
*/
	$tglmulai= substr($_SESSION['org']['period']['start'],0,4).'-'.substr($_SESSION['org']['period']['start'],4,2).'-'.substr($_SESSION['org']['period']['start'],6,2);
	$tglakhir= substr($_SESSION['org']['period']['end'],0,4).'-'.substr($_SESSION['org']['period']['end'],4,2).'-'.substr($_SESSION['org']['period']['end'],6,2);

    //$qKary = "select karyawanid,namakaryawan from datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tanggalkeluar >= '".$tglmulai."' and tanggalkeluar <= '".$tglakhir."' and isduplicate = 0 ORDER BY namakaryawan ASC";
	if ('add' == $mode) {
        $qKary = "select karyawanid,namakaryawan from datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tanggalkeluar >= '".$tglmulai."' and tanggalkeluar <= '".$tglakhir."' and isduplicate = 0 ORDER BY namakaryawan ASC";
    } else {
        if ('edit' == $mode) {
            $qKary ="select karyawanid,namakaryawan from datakaryawan where karyawanid='".$data['karyawanid']."' ORDER BY namakaryawan ASC";
        }
    }
	//echo $qKary;
	$qCekKary = mysql_query($qKary);
	if (mysql_num_rows($qCekKary)==0) {
		echo "Warning: Tidak ada karyawan yang keluar di periode ".substr($tglmulai,0,7)." / Dari: ".$tglmulai." sd. ".$tglakhir;
		exit();
    }

    $resKary = fetchData($qKary);
    $optKary = [];
    foreach ($resKary as $row) {
        $optKary[$row['karyawanid']] = $row['namakaryawan'];
    }
    if (empty($data['karyawanid'])) {
        $data['karyawanid'] = key($optKary);
	}
			
    $qKaryInfo = 'select lokasitugas,tanggalmasuk,tanggalkeluar from datakaryawan where karyawanid="'.$data['karyawanid']. '"';
    $resKaryInfo = fetchData($qKaryInfo);
    $tglMasukArr = explode('-', $resKaryInfo[0]['tanggalmasuk']);
    if ('add' == $mode) {
    $optPeriod = makeOption($dbname, 'sdm_5periodegaji', 'periode,periode', "kodeorg='".$resKaryInfo[0]['lokasitugas']."' and periode='".substr($resKaryInfo[0]['tanggalkeluar'],0,7)."'");
	}else{
        if ('edit' == $mode) {
    $optPeriod = makeOption($dbname, 'sdm_5periodegaji', 'periode,periode', "kodeorg='".$resKaryInfo[0]['lokasitugas']."' and periode='".substr($resKaryInfo[0]['tanggalkeluar'],0,7)."'");
		}
	}
    $optMasa = [];
    for ($i = 0; $i < 41; ++$i) {
        $optMasa[$i] = $i;
    }
    $optMasa[0] = '<1';
    $optAlasan = ['perusahaan' => 'Keinginan Perusahaan', 'salahkecil' => 'Kesalahan Kecil', 'salahbesar' => 'Kesalahan Besar / Mengundurkan Diri'];
    $masaKerja = date('Y') - $tglMasukArr[0];
    $els = [];
    $els[] = [makeElement('karyawanid', 'label', $_SESSION['lang']['namakaryawan']), makeElement('karyawanid', 'select', $data['karyawanid'], ['style' => 'width:200px', 'onchange' => 'changeKary()', $disabled => $disabled], $optKary)];

    $els[] = [makeElement('periodegaji', 'label', $_SESSION['lang']['periodegaji']), makeElement('periodegaji', 'select', $data['periodegaji'], ['style' => 'width:200px', 'disabled' => 'disabled'], $optPeriod)];

    $els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];
    $els[] = [makeElement('alasankeluar', 'label', 'Alasan Keluar'), makeElement('alasankeluar', 'select', $data['alasankeluar'], ['style' => 'width:200px', $disabled => $disabled], $optAlasan)];
    $els[] = [makeElement('tanggalkeluar', 'label', $_SESSION['lang']['tanggalkeluar']), makeElement('tanggalkeluar', 'text', tanggalnormal($resKaryInfo[0]['tanggalkeluar']), ['style' => 'width:200px', 'disabled' => 'disabled'])];
    $els[] = [makeElement('masakerja', 'label', $_SESSION['lang']['masakerja']), makeElement('masakerja', 'select', $masaKerja, ['style' => 'width:200px', 'disabled' => 'disabled'], $optMasa)];
    $els[] = [makeElement('lembur', 'label', ""), makeElement('lembur', 'checkbox', $data['lembur'], ['style' => 'display:none'])];
    $els[] = [makeElement('nodok', 'label', $_SESSION['lang']['nodok']), makeElement('nodok', 'text', $data['nodok'], ['style' => 'width:200px', 'maxlength' => 30])];

    if ('add' == $mode) {
        $els['btn'] = [makeElement('addHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'addDataTable()'])];
    } else {
        if ('edit' == $mode) {
            $els['btn'] = [makeElement('editHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'editDataTable()'])];
        }
    }

    if ('add' == $mode) {
        return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);		
    }

    if ('edit' == $mode) {
        return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
    }
}

?>
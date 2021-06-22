<?php



require_once 'master_validation.php';

include_once 'lib/eagrolib.php';

include_once 'lib/zLib.php';

//include_once 'lib/rTable2.php';

include_once 'lib/devLibrary.php';

include_once 'lib/kasbank_helper.php';

echo "\r\n";

$proses = $_GET['proses'];

if (!empty($_POST)) {

    $param = $_POST;

} else {

    $param = $_GET;

}



empty($param['page']) ? $page=1 : $page=$param['page'];

empty($param['shows']) ? $shows=SHOW_ROW_COUNT : $shows=$param['shows'];

$tipe =$param['tipe'];

$where = $param['where'];

$kodeorg = $param['kodeorg'];

$offset=($page-1)*$shows;



switch ($proses) {

    case 'rows':

        $sql = prepareQuery($param);

        $totalRow = getRowCount($sql);

        $sql .= "  limit $offset,$shows";

        $res = mysql_query($sql);

        $row = array('datas' => array(), 'totalrow' => 0);

        while ($bar = mysql_fetch_assoc($res)) {

            $row['datas'][] = $bar;

        }

        $row['totalrow'] = $totalRow;

        $row['tablehead'] = generateTable('', 1, $totalRow);

        echo json_encode($row);

        break;

    case 'showHeadList':

//        $sql = prepareQuery($param);

//        $totalRow = getRowCount($sql);

//        $sql .= "  limit $offset,$shows";

//        echo generateTable('', 1, $totalRow);

        break;

//        $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' ";

//        if (isset($param['where'])) {

//            $arrWhere = json_decode(str_replace('\\', '', $param['where']), true);

//            if (!empty($arrWhere)) {

//                foreach ($arrWhere as $key => $r1) {

//                    if ('4' == $key) {

//                        if ('' != $r1[1]) {

//                            $where .= ' and k.notransaksi in (select notransaksi from '.$dbname.".keu_kasbankdt where kodesupplier in \r\n\t\t\t\t\t\t\t(select supplierid from ".$dbname.".log_5supplier where namasupplier like '%".$r1[1]."%'))";

//                        }

//                    } else {

//                        $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";

//                    }

//                }

//            }

//        }

//

//        $header = [$_SESSION['lang']['notransaksi'], $_SESSION['lang']['unit'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['noakun'], $_SESSION['lang']['tipe'], $_SESSION['lang']['jumlah'], 'Balance', $_SESSION['lang']['remark'], $_SESSION['lang']['nobayar']];

//        $align = explode(',', 'C,L,C,L,C,R,C');

//        $cols = "notransaksi,kodeorg,tanggal,noakun,tipetransaksi,jumlah,'balan',keterangan,nobayar,posting,approval";

//        $query = selectQuery($dbname, 'keu_kasbankht', $cols, $where, 'tanggal desc, notransaksi desc', false, $param['shows'], $param['page']);

//		#echo $query;

//        $data = fetchData($query);

//        $totalRow = getTotalRow($dbname, 'keu_kasbankht', $where);

//        $whereAkun = '';

//        $whereOrg = '';

//        $i = 0;

//        foreach ($data as $key => $row) {

//            if (1 == $row['posting']) {

//                $data[$key]['switched'] = true;

//            }

//

//            if ('' !== $row['approval'] && 0 == $row['posting']) {

//                $data[$key]['switchedApproval'] = true;

//            }

//

//            $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);

//            unset($data[$key]['posting']);

//            if (0 == $i) {

//                $whereAkun .= "noakun='".$row['noakun']."'";

//                $whereOrg .= "kodeorganisasi='".$row['kodeorg']."'";

//            } else {

//                $whereAkun .= " or noakun='".$row['noakun']."'";

//                $whereOrg .= " or kodeorganisasi='".$row['kodeorg']."'";

//            }

//

//            $i++;

//        }

//        $qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', "kodeaplikasi='keuangan'");

//        $tmpPost = fetchData($qPosting);

//        $postJabatan = $tmpPost[0]['jabatan'];

//        if ('EN' == $_SESSION['language']) {

//            $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun1', $whereAkun);

//        } else {

//            $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereAkun);

//        }

//

//        $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg);

//        $dataShow = $data;

//        foreach ($dataShow as $key => $row) {

//            $dataShow[$key]['jumlah'] = number_format($row['jumlah'], 2);

//            $dataShow[$key]['noakun'] = $optAkun[$row['noakun']];

//            $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];

//            $str = 'select sum(jumlah) as jumlah from '.$dbname.".keu_kasbankdt \r\n                  where notransaksi='"

//				.$data[$key]['notransaksi']."' \r\n                  and kodeorg='"

//				.$data[$key]['kodeorg']."' \r\n                  and tipetransaksi='"

//				.$data[$key]['tipetransaksi']."'\r\n                  and noakun2a='"

//				.$data[$key]['noakun']."'";

//            $res = mysql_query($str);

//            $bar = mysql_fetch_object($res);

//            $balan = 0;

//            $balan = $bar->jumlah;

//            $balan = $balan - $row['jumlah'];

//            $dataShow[$key]['balan'] = number_format($balan, 2);

//        }

//        $tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);

//        $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');

//        $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');

//        $tHeader->addAction('approvalData', 'Approval', 'images/'.$_SESSION['theme'].'/10.png');

//        $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/10.png');

//

//        $tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');

//        $tHeader->_actions[3]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');

//        $tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');

//        if ($postJabatan != $_SESSION['empl']['kodejabatan'] && 'HOLDING' != $_SESSION['empl']['tipelokasitugas']) {

//            $tHeader->_actions[3]->_name = '';

//        }

//        $tHeader->_actions[4]->addAttr('event');

//        $tHeader->pageSetting($param['page'], $totalRow, $param['shows']);

//        $tHeader->addAction('tampilDetail', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/zoom.png');

//        $tHeader->_actions[5]->addAttr('event');

//        $tHeader->_switchException = ['detailPDF', 'tampilDetail'];

//        if (isset($param['where'])) {

//            $tHeader->setWhere($arrWhere);

//        }

////        echoMessage("header ", $dataShow);

//        $tHeader->setAlign($align);

//        $tHeader->renderTable();

//

//        break;

    case 'showAdd':

       

        echo formHeader('add', []);

        echo "<div id='detailField' style='clear:both'></div>";



        break;

    case 'showEdit':

//        $query = selectQuery($dbname, 'keu_kasbankht', '*', "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'");

        $query = selectQuery($dbname, 'keu_kasbankht', '*', " notransaksi='" . $param['notransaksi'] . "' ");

        $tmpData = fetchData($query);

        $data = $tmpData[0];

        $data['tanggal'] = tanggalnormal($data['tanggal']);

        echo formHeader('edit', $data);

        echo "<div id='detailField' style='clear:both'></div>";



        break;

    case 'add':

        $data = $_POST;

        if (empty($data['keterangan'])) {

            exit('Warning: Keterangan harus diisi');

        }



        if (1 == $data['hutangunit'] && ('' == $data['pemilikhutang'] || '' == $data['noakunhutang'])) {

            exit('Error: Please complete the form.');

        }



        if ('' == $data['hutangunit']) {

            $data['hutangunit'] = 0;

        }



        $warning = '';

        if ('' == $data['tanggal']) {

            $warning .= "Tanggal harus diisi\n";

        }



        if ('' != $warning) {

            echo "Warning :\n" . $warning;

            exit();

        }



        $sekarang = tanggalsystemw($data['tanggal']);

        if ($sekarang < $_SESSION['org']['period']['start']) {

            echo 'Validation Error : Date out or range';



            break;

        }



        $kode_transaksi='[' . $_SESSION['empl']['lokasitugas'] . ']' . date('Ym');

        $sQl="select max(RIGHT(notransaksi,8)) from keu_kasbankht WHERE LEFT(notransaksi,12)='".$kode_transaksi."'" ;

        $res=mysql_query($sQl); 

        $hasil=mysql_fetch_array($res);

        

        $v_id_last = $hasil[0];

        $v_id = $v_id_last + 1;

        $jumlah=strlen($v_id);

        if($jumlah<6) $nol=str_repeat("0", 6-$jumlah);

        $data['notransaksi'] = $kode_transaksi.$nol.$v_id;



//      $data['notransaksi'] = '[' . $_SESSION['empl']['lokasitugas'] . ']' . date('YmdHis'); //perbaikan

        $data['tanggal'] = tanggalsystemw($data['tanggal']);

        $data['jumlah'] = str_replace(',', '', $data['jumlah']);

        $data['userid'] = $_SESSION['standard']['userid'];

        $cols = ['notransaksi', 'noakun', 'tanggal', 'matauang', 'kurs', 'tipetransaksi', 'jumlah', 'cgttu', 'keterangan', 'yn', 'kodeorg', 'nogiro', 'hutangunit', 'pemilikhutang', 'noakunhutang', 'diperiksa', 'disetujui', 'diterima', 'userid'];

        $query = insertQuery($dbname, 'keu_kasbankht', $data, $cols);

        if (!mysql_query($query)) {

            echo 'DB Error : ' . mysql_error();

        } else {

            echo $data['notransaksi'];

        }



        break;

    case 'edit':

        $data = $_POST;

        if (empty($data['keterangan'])) {

            exit('Warning: Keterangan harus diisi');

        }



        if (1 == $data['hutangunit'] && ('' == $data['pemilikhutang'] || '' == $data['noakunhutang'])) {

            exit('Error: Silakan melengkapi data hutang.');

        }



        $where = "notransaksi='" . $data['notransaksi'] . "' and kodeorg='" . $data['kodeorg'] . "' and noakun='" . $data['oldNoakun'] . "' and tipetransaksi='" . $data['tipetransaksi'] . "'";

        $wheredt = "notransaksi='" . $data['notransaksi'] . "' and kodeorg='" . $data['kodeorg'] . "'";

        $datadt['noakun2a'] = $param['noakun'];

        unset($data['notransaksi'], $data['kodeorg'], $data['oldNoakun'], $data['tipetransaksi']);



        $data['tanggal'] = tanggalsystemw($data['tanggal']);

        $data['jumlah'] = str_replace(',', '', $data['jumlah']);

        $query = updateQuery($dbname, 'keu_kasbankht', $data, $where);

        $querydt = updateQuery($dbname, 'keu_kasbankdt', $datadt, $wheredt);

        if (!mysql_query($query)) {

            echo 'DB Error ht : ' . mysql_error();

        } else {

            if (!mysql_query($querydt)) {

                echo 'DB Error dt : ' . mysql_error();

            } else {

                echo 'Done.';

            }

        }



        break;

    case 'delete':

//        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";

        $where = " notransaksi='" . $param['notransaksi'] . "' ";

        $query = "delete from $dbname.keu_kasbankdt where $where ;";

        if (!mysql_query($query)) {

            echo 'DB Error : ' . mysql_error();

            exit();

        }



        $query = "delete from $dbname.keu_kasbankht where $where ;";



        if (!mysql_query($query)) {

            echo 'DB Error : ' . mysql_error();

            exit();

        }



        break;

    case 'checkApproval':

        $sql = "select k.notransaksi," .

				"d1.karyawanid as id_disetujuioleh,

			d2.karyawanid as id_approvaloleh,

			nullif(d1.namakaryawan,'') as disetujuioleh,

			nullif(d2.namakaryawan,'') as approvaloleh, " .

				"(select namakaryawan from datakaryawan where karyawanid='" . $_SESSION['standard']['userid'] . "') as userlogin " .

				"from keu_kasbankht k 

			left outer join datakaryawan d1 on d1.karyawanid=k.disetujui

			left outer join datakaryawan d2 on d2.karyawanid=k.approval

			where notransaksi='" . $param['notransaksi'] . "' ";

        $res = mysql_query($sql);

        $row = [];

        $row['action'] = $param['action'];

        $row['sql'] = $sql;

        while ($bar = mysql_fetch_assoc($res)) {

            $row['userlogin'] = $bar['userlogin'] == null ? '' : $bar['userlogin'];

            $row['disetujuioleh'] = $bar['disetujuioleh'] == null ? '' : $bar['disetujuioleh'];

            $row['approvaloleh'] = $bar['approvaloleh'] == null ? '' : $bar['approvaloleh'];

            $row['id_disetujuioleh'] = $bar['id_disetujuioleh'] == null ? '' : $bar['id_disetujuioleh'];

            $row['id_approvaloleh'] = $bar['id_approvaloleh'] == null ? '' : $bar['id_approvaloleh'];

        }

        echo json_encode($row);

        break;

    case 'setApproval':

        $sql = "update keu_kasbankht set approval='" . $param['aprroval'] . "' where notransaksi='" . $param['notransaksi'] . "'";

        if (mysql_query($sql)) {

        } else {

            echo $sql."\r\n".

                " Gagal,".addslashes(mysql_error($conn));

        }

        break;

    default:

        break;

}

function formHeader($mode, $data)

{

    global $dbname;

    $noakunxx= '';

	$disetujuixx = '';

	$diperiksaxx = '';

	$diterimaxx = '';

	$noakunhutangxx = '';

	

	if (empty($data)) {

        $data['notransaksi'] = '';

        $data['kodeorg'] = $_SESSION['empl']['lokasitugas'];

        $data['noakun'] = '';

        $data['tanggal'] = '';

        $data['tipetransaksi'] = '';

        $data['jumlah'] = '0';

        $data['matauang'] = 'IDR';

        $data['kurs'] = '1';

        $data['cgttu'] = '';

        $data['keterangan'] = '';

        $data['yn'] = '0';

        $data['oldNoakun'] = '';

        $data['hutangunit'] = 0;

        $data['pemilikhutang'] = '';

        $data['noakunhutang'] = '';

        $data['nogiro'] = '';

        $data['disetujui'] = '';

        $data['diperiksa'] = '';

        $data['diterima'] = '';

    } else {

        $data['jumlah'] = number_format($data['jumlah'], 2);

		$noakunxx = $data['noakun'];

		$disetujuixx = $data['disetujui'];

		$diperiksaxx = $data['diperiksa'];

		$diterimaxx = $data['diterima'];

		$noakunhutangxx = $data['noakunhutang'];



	}

	

    if ('edit' == $mode) {

        $disabled = 'disabled';

    } else {

        $disabled = '';

    }



    $whereJam = " kasbank=1 and detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']

				."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']

				."' or pemilik='".$_SESSION['empl']['induklokasitugas']."')";

	if ($disabled == 'disabled') {

		$whereJam = " noakun = '".$noakunxx."' and ".$whereJam;

	}

	$optMataUang = makeOption($dbname, 'setup_matauang', 'kode,matauang');

    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");



    if ('EN' == $_SESSION['language']) {

        $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun1', $whereJam);

    } else {

        $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereJam);

    }



    $optTipe = ['M' => $_SESSION['lang']['masuk'], 'K' => $_SESSION['lang']['keluar']];

    #$optCgt = getEnum($dbname, 'keu_kasbankht', 'cgttu');

    $optCgt = array('Cash'=>'Cash','Transfer'=>'Transfer','Giro'=>'Giro','Cheque'=>'Cheque');

    $optYn = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];

    $wheredz = " kodeorganisasi != '".$_SESSION['empl']['lokasitugas']."' and length(kodeorganisasi)=4";

    $optPemilikHutang = makeOption($dbname, 'organisasi', 'kodeorganisasi,kodeorganisasi', $wheredz);

	$optPemilikHutang[''] = '';

	ksort($optPemilikHutang);

    

/*

	if ($disabled == 'disabled') {

		$wheredx = " noakun = ';".$noakunhutangxx."'";

		$optNoakunHutang = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $wheredx);

	} else {

*/

		$wheredx = " noakun like '211%' and length(noakun)=7";

		$optNoakunHutang = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $wheredx);

		$optNoakunHutang[''] = '';

		ksort($optNoakunHutang);

//	}

	

    $wherettd = " bagian='HO_ACTX' or bagian='HO_FICO' or bagian='MILL_ADMACC'"; // ini masih dipakai? FA-20191009

    $optttd = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $wherettd);

    $where=[];

    $opt=[];

    $opt[]='';

	if ($disabled == 'disabled') {

		$where1 = " karyawanid = '".$disetujuixx."'";//KBAPP3

		$where2 = " karyawanid = '".$diperiksaxx."'";//KBAPP2

		$where3 = " karyawanid = '".$diterimaxx."'";//KBAPP1

	} else {

//		$where1 = " namakaryawan like '%sudiarman saragih%' or namakaryawan like '%cahyo nugroho%' or namakaryawan like '%Jimmi Ferinando Karo%' or namakaryawan like '%Sondang Kurnia%' or namakaryawan like '%Hawalid Siregar%'";

//		$where2 = " namakaryawan like '%gilang pratomo%' or namakaryawan like '%ahmad zarkasih%' or namakaryawan like '%siswadi%' or namakaryawan like '%Yeni Herawati%' or namakaryawan like '%Hari Siswanto Roedyantoro%'";

//		$where3 = " namakaryawan like '%narwanto%' or namakaryawan like '%fredi%' or nik like '%1503034%' or namakaryawan like '%ANDIKA RAHMADANI%' or namakaryawan like '%miranda lubis%' or namakaryawan like '%Yeni Herawati%'";

        $where[] = '';

        $where[] = "s.applikasi ='KBAPP3' and s.kodeunit='" . $_SESSION['empl']['lokasitugas'] . "' ";

        $where[] = "s.applikasi ='KBAPP2' and s.kodeunit='" . $_SESSION['empl']['lokasitugas'] . "' ";

        $where[] = "s.applikasi ='KBAPP1' and s.kodeunit='" . $_SESSION['empl']['lokasitugas'] . "' ";

        // 1503034 = SAMI,

    }

	$sql = "select d.karyawanid,d.namakaryawan ".

        "from $dbname.datakaryawan d ".

        "inner join $dbname.setup_approval s on s.karyawanid=d.karyawanid where ";



	for($i=1;$i<=3;$i++) {

        $str = $sql . $where[$i];

        $result = mysql_query($str);

        if (!$result){

            break;

        } else {

            $data = [];

            while ($row = mysql_fetch_assoc($result)) {

                $data = array($row['karyawanid'] => $row['namakaryawan']);

                $opt[] = $data;

            }

        }

        mysql_free_result($result);

    }

    if(empty($data['tanggal'])){

            $data['tanggal'] = date("d-m-Y");

    }

    $sql1 = "select d.karyawanid,d.namakaryawan ".
        "from $dbname.datakaryawan d ".
        "inner join $dbname.setup_approval s on s.karyawanid=d.karyawanid where s.applikasi ='KBAPP1' and s.kodeunit='" . $_SESSION['empl']['lokasitugas'] . "'";

    $result1 = mysql_query($sql1);
            while ($row1 = mysql_fetch_array($result1)) {
                $optx[$row1['karyawanid']]=$row1['namakaryawan'];
            }

    $sql2 = "select d.karyawanid,d.namakaryawan ".
        "from $dbname.datakaryawan d ".
        "inner join $dbname.setup_approval s on s.karyawanid=d.karyawanid where s.applikasi ='KBAPP2' and s.kodeunit='" . $_SESSION['empl']['lokasitugas'] . "'";

    $result2 = mysql_query($sql2);
            while ($row2 = mysql_fetch_array($result2)) {
                $opty[$row2['karyawanid']]=$row2['namakaryawan'];
            }

    $sql3 = "select d.karyawanid,d.namakaryawan ".
        "from $dbname.datakaryawan d ".
        "inner join $dbname.setup_approval s on s.karyawanid=d.karyawanid where s.applikasi ='KBAPP3' and s.kodeunit='" . $_SESSION['empl']['lokasitugas'] . "'";

    $result3 = mysql_query($sql3);
            while ($row3 = mysql_fetch_array($result3)) {
                $optz[$row3['karyawanid']]=$row3['namakaryawan'];
            }

    $els = [];

    $els[] = [makeElement('notransaksi', 'label', $_SESSION['lang']['notransaksi']), makeElement('notransaksi', 'text', $data['notransaksi'], ['style' => 'width:200px', 'maxlength' => '25', 'disabled' => 'disabled'])];

    $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:200px', $disabled => $disabled], $optOrg)];

    $els[] = [makeElement('noakun2a', 'label', $_SESSION['lang']['noakun']), makeElement('noakun2a', 'select', $data['noakun'], ['style' => 'width:300px', $disabled => $disabled], $optAkun)];

    $els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];

    $els[] = [makeElement('matauang', 'label', $_SESSION['lang']['matauang']), makeElement('matauang', 'select', $data['matauang'], ['style' => 'width:300px'], $optMataUang)];

    $els[] = [makeElement('kurs', 'label', $_SESSION['lang']['kurs']), makeElement('kurs', 'textnum', $data['kurs'], ['style' => 'width:300px'])];

    $els[] = [makeElement('tipetransaksi', 'label', $_SESSION['lang']['tipetransaksi']), makeElement('tipetransaksi', 'select', $data['tipetransaksi'], ['style' => 'width:200px', $disabled => $disabled], $optTipe)];

    $els[] = [makeElement('nogiro', 'label', $_SESSION['lang']['nogiro']), makeElement('nogiro', 'text', $data['nogiro'], ['style' => 'width:200px', 'maxlength' => '25'])];

    $els[] = [makeElement('jumlah', 'label', $_SESSION['lang']['jumlah']), makeElement('jumlah', 'textnum', $data['jumlah'], ['style' => 'width:200px', 'onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)'])];



    $els[] = [makeElement('oldNoakun', 'hid', $data['noakun'])]; // di-hidden

    

	$els[] = [makeElement('cgttu', 'label', $_SESSION['lang']['cgttu']), makeElement('cgttu', 'select', $data['cgttu'], ['style' => 'width:300px'], $optCgt)];

    $els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', $data['keterangan'], ['style' => 'width:200px', 'maxlength' => '255'])];

    $els[] = [makeElement('yn', 'label', $_SESSION['lang']['yn']), makeElement('yn', 'select', $data['yn'], ['style' => 'width:200px', 'disabled' => 'disabled'], $optYn)];

    

    if ($data['hutangunit'] == 0) {

        $dis = 'disabled';

    } else {

        $dis = '';

    }



    $els[] = [makeElement('hutangunit', 'label', $_SESSION['lang']['hutangunit']), makeElement('hutangunit', 'checkbox', $data['hutangunit'], ['onclick' => 'pilihhutang()', $disabled => $disabled])];

    $els[] = [makeElement('pemilikhutang', 'label', $_SESSION['lang']['pemilikhutang']), makeElement('pemilikhutang', 'select', $data['pemilikhutang'], ['style' => 'width:200px', $dis => $dis], $optPemilikHutang)];

    $els[] = [makeElement('noakunhutang', 'label', $_SESSION['lang']['noakunhutang']), makeElement('noakunhutang', 'select', $data['noakunhutang'], ['style' => 'width:200px', $dis => $dis], $optNoakunHutang)];

    $els[] = [makeElement('disetujui', 'label', 'Disetujui Oleh'), makeElement('disetujui', 'select',  $data['disetujui'], ['style' => 'width:200px'], $optz)];

    $els[] = [makeElement('diperiksa', 'label', 'Diperiksa Oleh'), makeElement('diperiksa', 'select',  $data['diperiksa'], ['style' => 'width:200px'], $opty)];

    $els[] = [makeElement('diterima', 'label', 'Diterima Oleh'), makeElement('diterima', 'select',  $data['diterima'], ['style' => 'width:200px'], $optx)];



   if ($mode == 'add') {

        $els['btn'] = [makeElement('addHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'addDataTable()'])];

//        return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);

    }

	

	if ($mode == 'edit') {

        $els['btn'] = [makeElement('editHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'editDataTable()'])];

//        return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);

    }



    if ($mode == 'add') {

        return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);

    }



    if ($mode == 'edit') {

        return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);

    }



}



?>
<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo "\r\n";
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'showHeadList':
        $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
        if (isset($param['where'])) {
            $arrWhere = json_decode(str_replace('\\', '', $param['where']), true);
            if (!empty($arrWhere)) {
                foreach ($arrWhere as $key => $r1) {
                    if ('4' === $key) {
                        if ('' !== $r1[1]) {
                            $where .= ' and notransaksi in (select notransaksi from '.$dbname.".keu_kasbankdt where kodesupplier in \r\n\t\t\t\t\t\t\t(select supplierid from ".$dbname.".log_5supplier where namasupplier like '%".$r1[1]."%'))";
                        }
                    } else {
                        $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
                    }
                }
            }
        }

        $header = [$_SESSION['lang']['notransaksi'], $_SESSION['lang']['unit'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['noakun'], $_SESSION['lang']['tipe'], $_SESSION['lang']['jumlah'], 'Balance', $_SESSION['lang']['remark'], $_SESSION['lang']['nobayar']];
        $align = explode(',', 'C,L,C,L,C,R,C');
        $cols = "notransaksi,kodeorg,tanggal,noakun,tipetransaksi,jumlah,'balan',keterangan,nobayar,posting";
        $query = selectQuery($dbname, 'keu_kasbankht', $cols, $where, 'tanggal desc, notransaksi desc', false, $param['shows'], $param['page']);
		#echo $query;
        $data = fetchData($query);
        $totalRow = getTotalRow($dbname, 'keu_kasbankht', $where);
        $whereAkun = '';
        $whereOrg = '';
        $i = 0;
        foreach ($data as $key => $row) {
            if (1 == $row['posting']) {
                $data[$key]['switched'] = true;
            }

            $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            unset($data[$key]['posting']);
            if (0 == $i) {
                $whereAkun .= "noakun='".$row['noakun']."'";
                $whereOrg .= "kodeorganisasi='".$row['kodeorg']."'";
            } else {
                $whereAkun .= " or noakun='".$row['noakun']."'";
                $whereOrg .= " or kodeorganisasi='".$row['kodeorg']."'";
            }

            ++$i;
        }
        $qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', "kodeaplikasi='keuangan'");
        $tmpPost = fetchData($qPosting);
        $postJabatan = $tmpPost[0]['jabatan'];
        if ('EN' === $_SESSION['language']) {
            $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun1', $whereAkun);
        } else {
            $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereAkun);
        }

        $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg);
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['jumlah'] = number_format($row['jumlah'], 2);
            $dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
            $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
            $str = 'select sum(jumlah) as jumlah from '.$dbname.".keu_kasbankdt \r\n                  where notransaksi='".$data[$key]['notransaksi']."' \r\n                  and kodeorg='".$data[$key]['kodeorg']."' \r\n                  and tipetransaksi='".$data[$key]['tipetransaksi']."'\r\n                  and noakun2a='".$data[$key]['noakun']."'";
            $res = mysql_query($str);
            $bar = mysql_fetch_object($res);
            $balan = 0;
            $balan = $bar->jumlah;
            $balan = $balan - $row['jumlah'];
            $dataShow[$key]['balan'] = number_format($balan, 2);
        }
        $tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
        $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
        $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
		
        $tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');
        $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');
        $tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');
        if ($postJabatan !== $_SESSION['empl']['kodejabatan'] && 'HOLDING' !== $_SESSION['empl']['tipelokasitugas']) {
            $tHeader->_actions[2]->_name = '';
        }

        $tHeader->_actions[3]->addAttr('event');
        $tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
        $tHeader->addAction('tampilDetail', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/zoom.png');
        $tHeader->_actions[4]->addAttr('event');
        $tHeader->_switchException = ['detailPDF', 'tampilDetail'];
        if (isset($param['where'])) {
            $tHeader->setWhere($arrWhere);
        }

        $tHeader->setAlign($align);
        $tHeader->renderTable();

        break;
    case 'showAdd':
        echo formHeader('add', []);
        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'showEdit':
        $query = selectQuery($dbname, 'keu_kasbankht', '*', "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'");
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

        if (1 === $data['hutangunit'] && ('' === $data['pemilikhutang'] || '' === $data['noakunhutang'])) {
            exit('Error: Please complete the form.');
        }

        if ('' === $data['hutangunit']) {
            $data['hutangunit'] = 0;
        }

        $warning = '';
        if ('' === $data['tanggal']) {
            $warning .= "Date is obligatory\n";
        }

        if ('' !== $warning) {
            echo "Warning :\n".$warning;
            exit();
        }

        $sekarang = tanggalsystemw($data['tanggal']);
        if ($sekarang < $_SESSION['org']['period']['start']) {
            echo 'Validation Error : Date out or range';

            break;
        }

        $data['notransaksi'] = '['.$_SESSION['empl']['lokasitugas'].']'.date('YmdHis');
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $data['jumlah'] = str_replace(',', '', $data['jumlah']);
        $data['userid'] = $_SESSION['standard']['userid'];
        $cols = ['notransaksi', 'noakun', 'tanggal', 'matauang', 'kurs', 'tipetransaksi', 'jumlah', 'cgttu', 'keterangan', 'yn', 'kodeorg', 'nogiro', 'hutangunit', 'pemilikhutang', 'noakunhutang', 'userid', 'disetujui', 'diperiksa', 'diterima'];
        $query = insertQuery($dbname, 'keu_kasbankht', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        } else {
            echo $data['notransaksi'];
        }

        break;
    case 'edit':
        $data = $_POST;
        if (empty($data['keterangan'])) {
            exit('Warning: Keterangan harus diisi');
        }

        if (1 === $data['hutangunit'] && ('' === $data['pemilikhutang'] || '' === $data['noakunhutang'])) {
            exit('Error: Silakan melengkapi data hutang.');
        }

        $where = "notransaksi='".$data['notransaksi']."' and kodeorg='".$data['kodeorg']."' and noakun='".$data['oldNoakun']."' and tipetransaksi='".$data['tipetransaksi']."'";
        $wheredt = "notransaksi='".$data['notransaksi']."' and kodeorg='".$data['kodeorg']."'";
        $datadt['noakun2a'] = $param['noakun'];
        unset($data['notransaksi'], $data['kodeorg'], $data['oldNoakun'], $data['tipetransaksi']);

        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $data['jumlah'] = str_replace(',', '', $data['jumlah']);
        $query = updateQuery($dbname, 'keu_kasbankht', $data, $where);
        $querydt = updateQuery($dbname, 'keu_kasbankdt', $datadt, $wheredt);
        if (!mysql_query($query)) {
            echo 'DB Error ht : '.mysql_error();
        } else {
            if (!mysql_query($querydt)) {
                echo 'DB Error dt : '.mysql_error();
            } else {
                echo 'Done.';
            }
        }

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $query = 'delete from `'.$dbname.'`.`keu_kasbankht` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    default:
        break;
}
function formHeader($mode, $data)
{
    global $dbname;
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
    }

    if ('edit' === $mode) {
        $disabled = 'disabled';
    } else {
        $disabled = '';
    }

    $whereJam = " kasbank=1 and detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."' or pemilik='".$_SESSION['empl']['induklokasitugas']."')";
    $optMataUang = makeOption($dbname, 'setup_matauang', 'kode,matauang');
    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
    if ('EN' === $_SESSION['language']) {
        $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun1', $whereJam);
    } else {
        $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereJam);
    }

    $optTipe = ['M' => $_SESSION['lang']['masuk'], 'K' => $_SESSION['lang']['keluar']];
    #$optCgt = getEnum($dbname, 'keu_kasbankht', 'cgttu');
    $optCgt = array('Cash'=>'Cash','Transfer'=>'Transfer','Giro'=>'Giro','Cheque'=>'Cheque');
    $optYn = [$_SESSION['lang']['belumposting'], $_SESSION['lang']['posting']];
    $wheredz = " kodeorganisasi != '".$_SESSION['empl']['lokasitugas']."' and length(kodeorganisasi)=4";
    $wheredx = " noakun like '211%' and length(noakun)=7";
    $optPemilikHutang = makeOption($dbname, 'organisasi', 'kodeorganisasi,kodeorganisasi', $wheredz);
    $optNoakunHutang = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $wheredx);
    $optPemilikHutang[''] = '';
    ksort($optPemilikHutang);
    $optNoakunHutang[''] = '';
    ksort($optNoakunHutang);

    $wherettd = " bagian='HO_ACTX' or bagian='HO_FICO'";
    $optttd = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $wherettd);
	
    $where1 = " karyawanid=0000000455 or karyawanid=1000000163";
    $opt1 = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $where1);	

    $where2 = " karyawanid=0000000020 or karyawanid=0000000047";
    $opt2 = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $where2);
	
    $where3 = " karyawanid=0000000316 or namakaryawan like '%fredi%'";
    $opt3 = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $where3);
	
    $els = [];
    $els[] = [makeElement('notransaksi', 'label', $_SESSION['lang']['notransaksi']), makeElement('notransaksi', 'text', $data['notransaksi'], ['style' => 'width:200px', 'maxlength' => '25', 'disabled' => 'disabled'])];
    $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:200px', $disabled => $disabled], $optOrg)];
    $els[] = [makeElement('noakun2a', 'label', $_SESSION['lang']['noakun']), makeElement('noakun2a', 'select', $data['noakun'], ['style' => 'width:300px'], $optAkun)];
    $els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];
    $els[] = [makeElement('matauang', 'label', $_SESSION['lang']['matauang']), makeElement('matauang', 'select', $data['matauang'], ['style' => 'width:300px'], $optMataUang)];
    $els[] = [makeElement('kurs', 'label', $_SESSION['lang']['kurs']), makeElement('kurs', 'textnum', $data['kurs'], ['style' => 'width:300px'])];
    $els[] = [makeElement('tipetransaksi', 'label', $_SESSION['lang']['tipetransaksi']), makeElement('tipetransaksi', 'select', $data['tipetransaksi'], ['style' => 'width:200px', $disabled => $disabled], $optTipe)];
    $els[] = [makeElement('nogiro', 'label', $_SESSION['lang']['nogiro']), makeElement('nogiro', 'text', $data['nogiro'], ['style' => 'width:200px', 'maxlength' => '25'])];
    $els[] = [makeElement('oldNoakun', 'hid', $data['noakun'])];
    $els[] = [makeElement('jumlah', 'label', $_SESSION['lang']['jumlah']), makeElement('jumlah', 'textnum', $data['jumlah'], ['style' => 'width:200px', 'onchange' => 'this.value=remove_comma(this);this.value = _formatted(this)'])];
    $els[] = [makeElement('cgttu', 'label', $_SESSION['lang']['cgttu']), makeElement('cgttu', 'select', $data['cgttu'], ['style' => 'width:300px'], $optCgt)];
    $els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', $data['keterangan'], ['style' => 'width:200px', 'maxlength' => '255'])];
    $els[] = [makeElement('yn', 'label', $_SESSION['lang']['yn']), makeElement('yn', 'select', $data['yn'], ['style' => 'width:200px', 'disabled' => 'disabled'], $optYn)];
    
    if (0 === $data['hutangunit']) {
        $dis = 'disabled';
    } else {
        $dis = '';
    }

    $els[] = [makeElement('hutangunit', 'label', $_SESSION['lang']['hutangunit']), makeElement('hutangunit', 'checkbox', $data['hutangunit'], ['onclick' => 'pilihhutang()', $disabled => $disabled])];
    $els[] = [makeElement('pemilikhutang', 'label', $_SESSION['lang']['pemilikhutang']), makeElement('pemilikhutang', 'select', $data['pemilikhutang'], ['style' => 'width:200px', $dis => $dis], $optPemilikHutang)];
    $els[] = [makeElement('noakunhutang', 'label', $_SESSION['lang']['noakunhutang']), makeElement('noakunhutang', 'select', $data['noakunhutang'], ['style' => 'width:200px', $dis => $dis], $optNoakunHutang)];
    //$els[] = [makeElement('disetujui', 'label', 'Disetujui Oleh'), makeElement('disetujui', 'select',  $data['disetujui'], ['style' => 'width:200px'], $optttd)];
    //$els[] = [makeElement('diperiksa', 'label', 'Diperiksa Oleh'), makeElement('diperiksa', 'select',  $data['diperiksa'], ['style' => 'width:200px'], $optttd)];
    //$els[] = [makeElement('diterima', 'label', 'Diterima Oleh'), makeElement('diterima', 'select',  $data['diterima'], ['style' => 'width:200px'], $optttd)];
    $els[] = [makeElement('disetujui', 'label', 'Disetujui Oleh'), makeElement('disetujui', 'select',  $data['disetujui'], ['style' => 'width:200px'], $opt1)];
    $els[] = [makeElement('diperiksa', 'label', 'Diperiksa Oleh'), makeElement('diperiksa', 'select',  $data['diperiksa'], ['style' => 'width:200px'], $opt2)];
    $els[] = [makeElement('diterima', 'label', 'Diterima Oleh'), makeElement('diterima', 'select',  $data['diterima'], ['style' => 'width:200px'], $opt3)];

    if ('add' === $mode) {
        $els['btn'] = [makeElement('addHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'addDataTable()'])];
    } else {
        if ('edit' === $mode) {
            $els['btn'] = [makeElement('editHead', 'btn', $_SESSION['lang']['save'], ['onclick' => 'editDataTable()'])];
        }
    }

    if ('add' === $mode) {
        return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
    }

    if ('edit' === $mode) {
        return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
    }
}

?>
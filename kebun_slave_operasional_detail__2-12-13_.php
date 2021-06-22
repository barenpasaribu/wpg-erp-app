<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
$proses = $_GET['proses'];
$param = $_POST;
switch ($proses) {
    case 'cekSisip':
        $kegiatan = $param['kodekegiatan'];
        $where = "nilai = '".$kegiatan."'";
        $cols = 'kodeaplikasi';
        $query = selectQuery($dbname, 'setup_parameterappl', $cols, $where);
        $res = mysql_query($query);
        while ($bar = mysql_fetch_object($res)) {
            $kodeaplikasi = $bar->kodeaplikasi;
        }
        echo $kodeaplikasi;

        break;
    case 'saveSisip':
        $notrans = $param['notrans'];
        $kodeorg = $param['kodeorg'];
        $jumlah = $param['jumlah'];
        $penyebab = $param['penyebab'];
        $where = "notransaksi = '".$notrans."'";
        $cols = 'tanggal';
        $query = selectQuery($dbname, 'kebun_aktifitas', $cols, $where);
        $res = mysql_query($query);
        while ($bar = mysql_fetch_object($res)) {
            $tanggal = $bar->tanggal;
        }
        $qwe = 'INSERT INTO `'.$dbname."`.`kebun_sisip` (`notransaksi` ,`tanggal` ,`kodeorg` ,`jumlah` ,`penyebab`)\n        VALUES ('".$notrans."', '".$tanggal."', '".$kodeorg."', '".$jumlah."', '".$penyebab." ')";
        if (mysql_query($qwe)) {
        } else {
            echo 'Error:'.addslashes(mysql_error($conn).$str);
        }

        break;
    case 'showDetail':
        $headFrame = [$_SESSION['lang']['prestasi'], $_SESSION['lang']['absensi'], $_SESSION['lang']['material']];
        $contentFrame = [];
        $blokStatus = $_SESSION['tmp']['actStat'];
        $whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in('2','3','4','6')";
        $whereKary .= " and (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."')";
        $whereKeg = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ";
        switch ($blokStatus) {
            case 'lc':
                $whereKeg = "(kelompok='TB')";

                break;
            case 'bibit':
                $whereKeg = "(kelompok='BBT' or kelompok='PN' or kelompok='MN')";

                break;
            case 'tbm':
                $whereKeg = "(kelompok='TBM')";

                break;
            case 'tm':
                $whereKeg = "kelompok='TM'";

                break;
            default:
                break;
        }
    // no break
    case 'updateUMR':
        $firstKary = $param['nik'];
        $jhk = $param['jhk'];
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', 'karyawanid='.$firstKary.' and tahun='.$param['tahun'].' and idkomponen in (1,31)');
        $Umr = fetchData($qUMR);
        $zUmr = ($jhk * $Umr[0]['nilai']) / 25;
        echo $zUmr;

        break;
    case 'gatKarywanAFD':
        if ('afdeling' === $param['tipe']) {
            $subbagian = substr($param['kodeorg'], 0, 6);
            $str = 'select karyawanid,namakaryawan,subbagian from '.$dbname.".datakaryawan where subbagian='".$subbagian."'  and (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."')\n                and tipekaryawan in('2','3','4','6') order by namakaryawan";
        } else {
            $subbagian = substr($param['kodeorg'], 0, 4);
            $str = 'select karyawanid,namakaryawan,subbagian from '.$dbname.".datakaryawan where lokasitugas='".$subbagian."'  and (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."')\n                and tipekaryawan in('2','3','4','6') order by namakaryawan";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            echo "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' - '.$bar->subbagian.'</option>';
        }

        break;
    default:
        break;
}
if ('bibit' === $blokStatus) {
    $whereOrg = " tipe='BIBITAN' and length(kodeorganisasi)>6 and left(kodeorganisasi,4)='".$param['afdeling']."'";
} else {
    $whereOrg = ' kodeorganisasi in (select distinct kodeorg from '.$dbname.".setup_blok where left(kodeorg,4)='".$param['afdeling']."' and luasareaproduktif>0)\n                          and tipe='BLOK' and left(kodeorganisasi,4)='".$param['afdeling']."'";
}

$optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan,subbagian', $whereKary, '5');
if ('EN' === $_SESSION['language']) {
    $optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1,satuan,noakun', $whereKeg, '6');
} else {
    $optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,satuan,noakun', $whereKeg, '6');
}

$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg);
$optAbs = makeOption($dbname, 'sdm_5absensi', 'kodeabsen,keterangan', 'kodeabsen="H"');
$optBin = [1 => $_SESSION['lang']['yes'], 0 => $_SESSION['lang']['no']];
$where = "notransaksi='".$param['notransaksi']."'";
$cols = 'kodekegiatan,kodeorg,hasilkerja,jumlahhk,upahkerja,umr,upahpremi';
$query = selectQuery($dbname, 'kebun_prestasi', $cols, $where);
$data = fetchData($query);
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
    $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
}
$theForm2 = new uForm('prestasiForm', $_SESSION['lang']['form'].' '.$_SESSION['lang']['prestasi'], 2);
$theForm2->addEls('kodekegiatan', $_SESSION['lang']['kodekegiatan'], '', 'select', 'L', 25, $optKeg);
$theForm2->addEls('kodeorg', $_SESSION['lang']['kodeorg'], '', 'select', 'L', 25, $optOrg);
$theForm2->_elements[1]->_attr['onchange'] = 'changeOrg()';
$theForm2->_elements[1]->_attr['title'] = 'Please choose block';
$theForm2->addEls('hasilkerja', $_SESSION['lang']['hasilkerjajumlah'], '0', 'textnum', 'R', 10);
$theForm2->addEls('jumlahhk', $_SESSION['lang']['jumlahhk'], '0', 'textnum', 'R', 10);
$theForm2->_elements[3]->_attr['onfocus'] = "document.getElementById('tmpValHk').value = this.value";
$theForm2->_elements[3]->_attr['onkeyup'] = "totalVal();cekVal(this,'Pres','Hk')";
$theForm2->addEls('upahkerja', $_SESSION['lang']['upahkerja'], '0', 'textnum', 'R', 10);
$theForm2->_elements[4]->_attr['disabled'] = 'disabled';
$theForm2->addEls('umr', $_SESSION['lang']['umr'], '0', 'textnum', 'R', 10);
$theForm2->_elements[5]->_attr['disabled'] = 'disabled';
$theForm2->_elements[5]->_attr['onfocus'] = "document.getElementById('tmpValUmr').value = this.value";
$theForm2->_elements[5]->_attr['onkeyup'] = "totalVal();cekVal(this,'Pres','Umr')";
$theForm2->addEls('upahpremi', $_SESSION['lang']['upahpremi'], '0', 'textnum', 'R', 10);
$theForm2->_elements[6]->_attr['disabled'] = 'disabled';
$theForm2->_elements[6]->_attr['onfocus'] = "document.getElementById('tmpValIns').value = this.value";
$theForm2->_elements[6]->_attr['onkeyup'] = "totalVal();cekVal(this,'Pres','Ins')";
$theTable2 = new uTable('prestasiTable', $_SESSION['lang']['tabel'].' '.$_SESSION['lang']['prestasi'], $cols, $data, $dataShow);
$formTab2 = new uFormTable('ftPrestasi', $theForm2, $theTable2, null, ['notransaksi']);
$formTab2->_target = 'kebun_slave_operasional_prestasi';
$formTab2->_onedata = true;
if (!empty($data)) {
    $formTab2->_noaction = true;
    $theBlok = $data[0]['kodeorg'];
} else {
    $theBlok = '';
}

$contentFrame[0] = $formTab2->prep();
$where = "notransaksi='".$param['notransaksi']."'";
$cols = 'nourut,nik,absensi,hasilkerja,jhk,umr,insentif';
$query = selectQuery($dbname, 'kebun_kehadiran', $cols, $where);
$data = fetchData($query);
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['nik'] = $optKary[$row['nik']];
    $dataShow[$key]['absensi'] = $optAbs[$row['absensi']];
    $dataShow[$key]['umr'] = number_format($row['umr'], 0);
}
$firstKary = getFirstKey($optKary);
$qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', 'karyawanid='.$firstKary.' and tahun='.date('Y').' and idkomponen in (1,31)');
$Umr = fetchData($qUMR);
$theForm1 = new uForm('absensiForm', $_SESSION['lang']['form'].' '.$_SESSION['lang']['absensi'], 2);
$theForm1->addEls('nourut', $_SESSION['lang']['nourut'], '0', 'textnum', 'R', 3);
$theForm1->_elements[0]->_attr['disabled'] = 'disabled';
$theForm1->addEls('nik', $_SESSION['lang']['nik'], '', 'select', 'L', 25, $optKary);
$theForm1->_elements[1]->_attr['onchange'] = 'updateUMR(this)';
$theForm1->addEls('absensi', $_SESSION['lang']['absensi'], 'H', 'select', 'L', 25, $optAbs);
$theForm1->addEls('hasilkerja', $_SESSION['lang']['hasilkerjad'], '0', 'textnum', 'R', 10);
$theForm1->addEls('jhk', $_SESSION['lang']['jhk'], '0', 'textnum', 'R', 10);
$theForm1->_elements[4]->_attr['onkeyup'] = "totalVal();cekVal(this,'Abs','Hk');updateUMR2()";
$theForm1->addEls('umr', $_SESSION['lang']['umrhari'], $Umr[0]['nilai'] / 25, 'textnum', 'R', 10);
$theForm1->_elements[5]->_attr['onkeyup'] = 'totalVal();';
$theForm1->addEls('insentif', $_SESSION['lang']['insentif'], '0', 'textnum', 'R', 10);
$theForm1->_elements[6]->_attr['onkeyup'] = 'totalVal();';
$theTable1 = new uTable('absensiTable', $_SESSION['lang']['tabel'].' '.$_SESSION['lang']['absensi'], $cols, $data, $dataShow);
$formTab1 = new uFormTable('ftAbsensi', $theForm1, $theTable1, null, ['notransaksi']);
$formTab1->_target = 'kebun_slave_operasional_absensi';
$formTab1->_noEnable = '##nourut';
$formTab1->_defValue = '##umr='.$Umr[0]['nilai'] / 25;
$contentFrame[1] = "<input type=checkbox id=filternik onclick=filterKaryawan('nik',this) title=Filter Employee>Filter Employee</checkbox>";
$contentFrame[1] .= $formTab1->prep();
$where = "notransaksi='".$param['notransaksi']."'";
$cols = 'kodeorg,kwantitasha,kodegudang,kodebarang,kwantitas';
$query = selectQuery($dbname, 'kebun_pakaimaterial', $cols, $where);
$data = fetchData($query);
if (!empty($data)) {
    $whereBarang = '';
    $i = 0;
    foreach ($data as $row) {
        if (0 === $i) {
            $whereBarang .= "kodebarang='".$row['kodebarang']."'";
        } else {
            $whereBarang .= " or kodebarang='".$row['kodebarang']."'";
        }

        ++$i;
    }
    $optBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang', $whereBarang);
} else {
    $optBarang = [];
}

$optGudang = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', " kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' and tipe='GUDANGTEMP'");
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
    $dataShow[$key]['kwantitasha'] = number_format($row['kwantitasha'], 2);
    $dataShow[$key]['kodegudang'] = $optGudang[$row['kodegudang']];
    $dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
    $dataShow[$key]['kwantitas'] = number_format($row['kwantitas'], 2);
}
$theForm3 = new uForm('materialForm', $_SESSION['lang']['form'].' '.$_SESSION['lang']['pakaimaterial'], 2);
$theForm3->addEls('kodeorg', $_SESSION['lang']['kodeorg'], $theBlok, 'select', 'L', 25, $optOrg);
$theForm3->_elements[0]->_attr['disabled'] = 'disabled';
$theForm3->addEls('kwantitasha', $_SESSION['lang']['kwantitasha'], '0', 'textnum', 'R', 10);
$theForm3->addEls('kodegudang', $_SESSION['lang']['pilihgudang'], '', 'select', 'L', 25, $optGudang);
$theForm3->addEls('kodebarang', $_SESSION['lang']['kodebarang'], '', 'searchBarang', 'L', 20);
$theForm3->addEls('kwantitas', $_SESSION['lang']['kwantitas'], '0', 'textnum', 'R', 10);
$theTable3 = new uTable('materialTable', $_SESSION['lang']['tabel'].' '.$_SESSION['lang']['pakaimaterial'], $cols, $data, $dataShow);
$formTab3 = new uFormTable('ftMaterial', $theForm3, $theTable3, null, ['notransaksi']);
$formTab3->_target = 'kebun_slave_operasional_material';
$formTab3->_noClearField = '##kodebarang';
$formTab3->_noEnable = '##kodebarang##kodeorg';
$contentFrame[2] = $formTab3->prep();
echo '<fieldset><legend><b>Detail</b></legend>';
drawTab('FRM', $headFrame, $contentFrame, 150, '100%');
echo '</fieldset>';


?>
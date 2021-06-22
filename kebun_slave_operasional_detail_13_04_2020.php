<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
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
        $qwe = 'INSERT INTO `'.$dbname."`.`kebun_sisip` (`notransaksi` ,`tanggal` ,`kodeorg` ,`jumlah` ,`penyebab`)\r\n        VALUES ('".$notrans."', '".$tanggal."', '".$kodeorg."', '".$jumlah."', '".$penyebab." ')";
        if (mysql_query($qwe)) {
        } else {
            echo 'Error:'.addslashes(mysql_error($conn).$str);
        }

        break;
    case 'showDetail':
        $notransaksi = $param['notransaksi'];
        $i = 'select * from '.$dbname.".kebun_prestasi where notransaksi='".$notransaksi."'";
        $n = mysql_query($i);
        $d = mysql_fetch_assoc($n);
        $kodekegiatan = $d['kodekegiatan'];
        $w = 'select konversi,kodekegiatan from '.$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
        $i = mysql_query($w) ;
        $b = mysql_fetch_assoc($i);
        $konversi = $b['konversi'];
        $kdKeg = $b['kodekegiatan'];
        $notransaksi = $param['notransaksi'];
        $tglTran = substr($notransaksi, 0, 8);
        $x = 'select tanggal from '.$dbname.".sdm_5harilibur where tanggal='".$tglTran."' and regional='".$_SESSION['empl']['regional']."'";
        $y = mysql_query($x) ;
        $z = mysql_fetch_assoc($y);
        $tglCek = $z['tanggal'];
        $headFrame = [$_SESSION['lang']['prestasi'], $_SESSION['lang']['absensi'], $_SESSION['lang']['material']];
        $contentFrame = [];
        $blokStatus = $_SESSION['tmp']['actStat'];
 
        $whereKeg = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ";
        switch ($blokStatus) {
            case 'lc':
                $whereKeg = "(kelompok='TB' or kelompok='TBM')";

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
        break;
    case 'updateUMR':
        $firstKary = $param['nik'];
        $jhk = $param['jhk'];
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', 'karyawanid='.$firstKary.' and tahun='.$param['tahun'].' and idkomponen in (1,31)');
        if (!executeQuery($qUMR)) {
            exit();
        }
//		echo 'warning:'.$qUMR;
//		exit();
		
        $Umr = fetchData($qUMR);
        $zUmr = ($jhk * $Umr[0]['nilai']) / 30; // / 25
        echo $zUmr;

        break;
    case 'gatKarywanAFD':
        if ('afdeling' == $param['tipe']) {
            $subbagian = substr($param['kodeorg'], 0, 6);
            $str = 'select karyawanid,namakaryawan,subbagian from '.$dbname.".datakaryawan where subbagian='".$subbagian."'  and (tanggalkeluar is NULL or tanggalkeluar= '0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."')\r\n                and tipekaryawan in('2','3','4','6') order by namakaryawan";
        } else {
            $subbagian = substr($param['kodeorg'], 0, 4);
            $str = 'select karyawanid,namakaryawan,subbagian from '.$dbname.".datakaryawan where lokasitugas='".$subbagian."'  and (tanggalkeluar is NULL or tanggalkeluar= '0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."')\r\n                and tipekaryawan in('2','3','4','6') order by namakaryawan";
        }

        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            echo "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.' - '.$bar->subbagian.'</option>';
        }

        break;
    default:
        break;
}
if ('bibit' == $blokStatus) {
    $whereOrg = " statusblok='BBT' and left(kodeorg,4)='".$param['afdeling']."'";
} if ($blokStatus == 'tbm'){
    $whereOrg = " statusblok='TBM' and left(kodeorg,4)='".$param['afdeling']."'";
} 
else {
    $whereOrg = " luasareaproduktif>0 and statusblok!='BBT' and left(kodeorg,4)='".$param['afdeling']."'";
}

$whereAbsen = "kodeabsen in ('H','L','MG')";
if ('EN' == $_SESSION['language']) {
    $optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1,satuan,noakun', $whereKeg, '2', true);
} else {
    $optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,satuan', $whereKeg, '2', true);
}

$optOrg = makeOption($dbname, 'setup_blok', 'kodeorg,bloklama,kodeorg', $whereOrg, '8', true);
$optAbs = makeOption($dbname, 'sdm_5absensi', 'kodeabsen,keterangan', $whereAbsen);
$optBin = [1 => $_SESSION['lang']['yes'], 0 => $_SESSION['lang']['no']];
$where = "notransaksi='".$param['notransaksi']."'";
$cols = 'kodekegiatan,kodeorg,jjg,hasilkerja,jumlahhk,upahkerja,umr,upahpremi';
$query = selectQuery($dbname, 'kebun_prestasi', $cols, $where);
$data = fetchData($query);
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $kodekegiatan = $row['kodekegiatan'];
    $dataShow[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
    $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
}
if (!empty($data)) {
    $qKonv = 'select konversi from '.$dbname.".kebun_5psatuan where kodekegiatan='".$kodekegiatan."' and regional='".$_SESSION['empl']['regional']."' ";
    $resKonv = fetchData($qKonv);
    if (1 == $resKonv[0]['konversi']) {
        $disabled = '';
    } else {
        $disabled = 'disabled';
    }

    $data = $data[0];
    $dataShow = $dataShow[0];
    $cont = "<table class=\"sortable\" cellspacing=\"1\" border=\"0\" id=\"prestasiTable\">\r\n\t\t\t<thead id=\"thead_ftPrestasi\">\r\n\t\t\t\t<tr class=\"rowheader\">\r\n\t\t\t\t\t<td id=\"head_kodekegiatan\" align=\"center\" style=\"width:250px\">".$_SESSION['lang']['kodekegiatan']."</td>\r\n\t\t\t\t\t<td id=\"head_kodeorg\" align=\"center\" style=\"width:250px\">".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t\t\t<td id=\"head_jjg\" align=\"center\" style=\"width:100px\">".$_SESSION['lang']['jjg']."</td>\r\n\t\t\t\t\t<td id=\"head_hasilkerja\" align=\"center\" style=\"width:100px\">".$_SESSION['lang']['hasilkerjajumlah']."</td>\r\n\t\t\t\t\t<td id=\"head_jumlahhk\" align=\"center\" style=\"width:100px\">".$_SESSION['lang']['jumlahhk']."</td>\r\n\t\t\t\t\t<td>*</td>\r\n\t\t\t\t</tr>\r\n\t\t\t</thead>\r\n\t\t\t<tbody id=\"tbody_ftPrestasi\">\r\n\t\t\t\t<tr id=\"tr_ftPrestasi_0\" class=\"rowcontent\">\r\n\t\t\t\t\t<td id=\"ftPrestasi_kodekegiatan_0\" align=\"left\" style=\"width:25px\" value=\"".$data['kodekegiatan']."\">\r\n\t\t\t\t\t\t".$dataShow['kodekegiatan']."\r\n\t\t\t\t\t</td>\r\n\t\t\t\t\t<td id=\"ftPrestasi_kodeorg_0\" align=\"left\" style=\"width:25px\" value=\"".$data['kodeorg'].'">'.$dataShow['kodeorg']."</td>\r\n\t\t\t\t\t<td id=\"ftPrestasi_jjg_0\" align=\"right\" style=\"width:10px\" value=\"".$data['jjg'].'">'.makeElement('jjg', 'textnum', $dataShow['jjg'], [$disabled => $disabled, 'onchange' => "getHasilKerja(true);getById('tr_ftPrestasi_0').style.background='#FC8848'"])."</td>\r\n\t\t\t\t\t<td id=\"ftPrestasi_hasilkerja_0\" align=\"right\" style=\"width:10px\" value=\"".$data['hasilkerja'].'">'.makeElement('hasilkerja', 'textnum', $dataShow['hasilkerja'], ['onchange' => "getById('tr_ftPrestasi_0').style.background='#FC8848'"])."</td>\r\n\t\t\t\t\t<td id=\"ftPrestasi_jumlahhk_0\" align=\"right\" style=\"width:10px\" value=\"".$data['jumlahhk'].'">'.makeElement('jumlahhk', 'textnum', $dataShow['jumlahhk'], ['onchange' => "getById('tr_ftPrestasi_0').style.background='#FC8848'", 'onkeyup' => 'totalVal()'])."</td>\r\n\t\t\t\t\t<td>\r\n\t\t\t\t\t\t<span id=ftPrestasi_umr_0 value=0 style='display:none'>0</span>\r\n\t\t\t\t\t\t<span id=ftPrestasi_upahkerja_0 value=0 style='display:none'>0</span>\r\n\t\t\t\t\t\t<span id=ftPrestasi_upahpremi_0 value=0 style='display:none'>0</span>\r\n\t\t\t\t\t\t<img src=images/save.png class=zImgBtn onclick=\"savePrestasi()\">\r\n\t\t\t\t\t</td>\r\n\t\t\t\t</tr>\r\n\t\t\t</tbody>\r\n\t\t</table>";
    $contentFrame[0] = $cont;
    $theBlok = $data['kodeorg'];
} else {
    $theForm2 = new uForm('prestasiForm', $_SESSION['lang']['form'].' '.$_SESSION['lang']['prestasi'], 2);
    $theForm2->addEls('kodekegiatan', $_SESSION['lang']['kodekegiatan'], '', 'selectsearch', 'L', 25, $optKeg);
    $theForm2->_elements[0]->_attr['onchange'] = 'cekKonversi()';
    $theForm2->addEls('kodeorg', $_SESSION['lang']['kodeorg'], '', 'selectsearch', 'L', 25, $optOrg, null, null, null, 'ftPrestasi_kodeorg');
    $theForm2->_elements[1]->_attr['onchange'] = 'changeOrg();getHasilKerja()';
    $theForm2->_elements[1]->_attr['title'] = 'Please choose block';
    $theForm2->addEls('jjg', $_SESSION['lang']['jjg'], '0', 'textnum', 'R', 10);
    $theForm2->_elements[2]->_attr['onblur'] = 'getHasilKerja();getKg()';
    $theForm2->addEls('hasilkerja', $_SESSION['lang']['hasilkerjajumlah'], '0', 'textnum', 'R', 10);
    $theForm2->addEls('jumlahhk', $_SESSION['lang']['jumlahhk'], '0', 'textnum', 'R', 10);
    $theForm2->_elements[4]->_attr['onfocus'] = "document.getElementById('tmpValHk').value = this.value";
    $theForm2->_elements[4]->_attr['onkeyup'] = 'totalVal();';
    $theForm2->addEls('upahkerja', $_SESSION['lang']['upahkerja'], '0', 'textnum', 'R', 10);
    $theForm2->_elements[5]->_attr['disabled'] = 'disabled';
    $theForm2->addEls('umr', $_SESSION['lang']['umr'], '0', 'textnum', 'R', 10);
    $theForm2->_elements[6]->_attr['disabled'] = 'disabled';
    $theForm2->_elements[6]->_attr['onfocus'] = "document.getElementById('tmpValUmr').value = this.value";
    $theForm2->_elements[6]->_attr['onkeyup'] = 'totalVal();';
    $theForm2->addEls('upahpremi', $_SESSION['lang']['upahpremi'], '0', 'textnum', 'R', 10);
    $theForm2->_elements[7]->_attr['disabled'] = 'disabled';
    $theForm2->_elements[7]->_attr['onfocus'] = "document.getElementById('tmpValIns').value = this.value";
    $theForm2->_elements[7]->_attr['onkeyup'] = 'totalVal();';
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
}

$where = "notransaksi='".$param['notransaksi']."'";
$cols = 'nourut,nik,absensi,jjg,hasilkerja,jhk,umr,insentif,hasilkerja*insentif as totalpremi';
$query = selectQuery($dbname, 'kebun_kehadiran', $cols, $where);
$data = fetchData($query);
$nikList = '';
foreach ($data as $row) {
    if ('' != $nikList) {
        $nikList .= ',';
    }

    $nikList .= $row['nik'];
}
//$whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in('2','3','4','6')";
$whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and sistemgaji = 'Harian'";
$whereKary .= " and (tanggalkeluar is NULL or tanggalkeluar= '0000-00-00' or tanggalkeluar > '".$_SESSION['org']['period']['start']."'))";
if (!empty($nikList)) {
    $whereKary .= ' or karyawanid in ('.$nikList.')';
}

$optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,nik,namakaryawan', $whereKary, '5', true);
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['nik'] = $optKary[$row['nik']];
    $dataShow[$key]['absensi'] = $optAbs[$row['absensi']];
    $dataShow[$key]['umr'] = number_format($row['umr'], 0);
    $dataShow[$key]['total_insentif'] = number_format($row['total_insentif'], 0);
}
$theForm1 = new uForm('absensiForm', $_SESSION['lang']['form'].' '.$_SESSION['lang']['absensi'], 2);
$theForm1->addEls('nourut', $_SESSION['lang']['nourut'], '0', 'textnum', 'R', 3);
$theForm1->_elements[0]->_attr['disabled'] = 'disabled';
$theForm1->addEls('nik', $_SESSION['lang']['nik'], '', 'selectsearch', 'L', 25, $optKary, null, null, null, 'ftAbsensi_nik');
$theForm1->_elements[1]->_attr['onchange'] = 'cekAbsensiAll()';
if ('' != $tglCek) {
    $cekMG = date('D', strtotime($tglCek));
    if ('Sun' == $cekMG) {
        $theForm1->addEls('absensi', $_SESSION['lang']['absensi'], 'MG', 'select', 'L', 25, $optAbs);
        $theForm1->_elements[2]->_attr['disabled'] = 'disabled';
    } else {
        $theForm1->addEls('absensi', $_SESSION['lang']['absensi'], 'L', 'select', 'L', 25, $optAbs);
        $theForm1->_elements[2]->_attr['disabled'] = 'disabled';
    }
} else {
    if ('' == $tglCek) {
        $theForm1->addEls('absensi', $_SESSION['lang']['absensi'], 'H', 'select', 'L', 25, $optAbs);
        $theForm1->_elements[2]->_attr['disabled'] = 'disabled';
    }
}

if ('' == $konversi || '0' == $konversi) {
    $theForm1->addEls('jjg', $_SESSION['lang']['jjg'], '0', 'textnum', 'R', 10);
    $theForm1->_elements[3]->_attr['onchange'] = 'cekAbsensiAll()';
    $theForm1->_elements[3]->_attr['disabled'] = 'disabled';
} else {
    $theForm1->addEls('jjg', $_SESSION['lang']['jjg'], '0', 'textnum', 'R', 10);
    $theForm1->_elements[3]->_attr['onchange'] = 'cekAbsensiAll()';
}

$theForm1->addEls('hasilkerja', $_SESSION['lang']['hasilkerjajumlah'], '0', 'textnum', 'R', 10);
$theForm1->_elements[4]->_attr['onchange'] = 'cekAbsensiAll()';
$theForm1->addEls('jhk', $_SESSION['lang']['jhk'], '0', 'textnum', 'R', 10);
$theForm1->_elements[5]->_attr['onkeyup'] = 'totalVal();';
$theForm1->_elements[5]->_attr['onchange'] = 'getUMR1()';
$theForm1->addEls('umr', $_SESSION['lang']['umrhari'], '', 'textnum', 'R', 10);
$theForm1->_elements[6]->_attr['onkeyup'] = 'totalVal();';
$theForm1->addEls('insentif', $_SESSION['lang']['insentif'], '0', 'textnum', 'R', 10);
$theForm1->_elements[7]->_attr['onkeyup'] = 'totalVal();';
$theForm1->addEls('totalpremi', $_SESSION['lang']['totalpremi'], '0', 'textnum', 'R', 10);
$theForm1->_elements[8]->_attr['onkeyup'] = 'totalVal();';
$theTable1 = new uTable('absensiTable', $_SESSION['lang']['tabel'].' '.$_SESSION['lang']['absensi'], $cols, $data, $dataShow);
$formTab1 = new uFormTable('ftAbsensi', $theForm1, $theTable1, null, ['notransaksi']);
$formTab1->_target = 'kebun_slave_operasional_absensi';
$formTab1->_noEnable = '##nourut##jjg##absensi';
$formTab1->_defValue = '##umr='.$Umr[0]['nilai'] / 30; // / 25
$formTab1->_afterCrud = 'totalVal';
$contentFrame[1] = "<input id=bjrKeg type=hidden value='0'>";
$contentFrame[1] .= "<input type=checkbox id=filternik onclick=filterKaryawan('nik',this) title=Filter Employee>Filter Employee</checkbox>";
$contentFrame[1] .= "<input type=checkbox id=allptnik onclick=allPtKaryawan('nik',this) title=Show All Employee in Company>All Employee in Company</checkbox>";
$contentFrame[1] .= $formTab1->prep();
$where = "notransaksi='".$param['notransaksi']."'";
$cols = 'kodeorg,kwantitasha,kodegudang,kodebarang,kwantitas';
$query = selectQuery($dbname, 'kebun_pakaimaterial', $cols, $where);
$data = fetchData($query);
if (!empty($data)) {
    $whereBarang = '';
    $i = 0;
    foreach ($data as $row) {
        if (0 == $i) {
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

$optGudang = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', " kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' and tipe in ('GUDANG','GUDANGTEMP')");
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
$formTab3->_noClearField = '##kodebarang##kodeorg';
$formTab3->_noEnable = '##kodebarang##kodeorg';
$contentFrame[2] = $formTab3->prep();
echo '<fieldset><legend><b>Detail</b></legend>';
drawTab('FRM', $headFrame, $contentFrame, 150, '100%');
echo '</fieldset>';

//break;

?>
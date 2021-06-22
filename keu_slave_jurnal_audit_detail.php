<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/zGrid.php';
require_once 'lib/formTable.php';
$proses = $_GET['proses'];
$data = $_POST;
$tmpNoJ = explode('/', $data['nojurnal']);
$org = $tmpNoJ[1];
switch ($proses) {
    case 'show':
        $ids = $_POST;
        $whereAsset = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and posting=0";
        $whereKary = "lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
        $whereJam = " detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
        $optCashFlow = makeOption($dbname, 'keu_5mesinlaporandt', 'nourut,keterangandisplay', "tipe='Detail' and namalaporan='CASH FLOW DIRECT'", '2', true);
        $optMatauang = makeOption($dbname, 'setup_matauang', 'kode,matauang');
        $optAsset = makeOption($dbname, 'project', 'kode,nama', $whereAsset, '2', true);
        $optSupplier = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', null, '0', true);
        $optCustomer = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer', null, '0', true);
        $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKary, '0', true);
        if ('EN' === $_SESSION['language']) {
            $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun1', $whereJam, '2', true);
        } else {
            $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereJam, '2', true);
        }

        $optVhc = makeOption($dbname, 'vhc_5master', 'kodevhc,kodeorg', '', '2', true);
        if ('KEBUN' === $_SESSION['empl']['tipelokasitugas']) {
            $optBlok = makeOption($dbname, 'setup_blok', 'kodeorg,kodeorg', "kodeorg like '".$blok."%'", '', true);
        } else {
            $optBlok = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "length(kodeorganisasi)>6 and tipe = 'BLOK'", '0', true);
        }

        for ($i = 1; $i <= 99; ++$i) {
            $optRev[$i] = $i;
        }
        if ('EN' === $_SESSION['language']) {
            $optKlpKeg = makeOption($dbname, 'setup_klpkegiatan', 'kodeklp,namakelompok1', null, '0', true);
            $qKegiatan = selectQuery($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1 as namakegiatan,kelompok').' order by namakegiatan1';
        } else {
            $optKlpKeg = makeOption($dbname, 'setup_klpkegiatan', 'kodeklp,namakelompok', null, '0', true);
            $qKegiatan = selectQuery($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,kelompok').' order by namakegiatan';
        }

        $tmpKeg = fetchData($qKegiatan);
        $optKegiatan = ['' => ''];
        foreach ($tmpKeg as $row) {
            $optKegiatan[$row['kodekegiatan']] = $row['kodekegiatan'].'-'.$row['namakegiatan'].' ('.$optKlpKeg[$row['kelompok']].')';
        }
        $tmpKlp = makeOption($dbname, 'setup_klpkegiatan', 'noakun,namakelompok');
        $cols = ['nourut', 'noakun', 'keterangan', 'jumlah', 'matauang', 'kurs', 'noaruskas', 'kodekegiatan', 'kodeasset', 'kodebarang', 'nik', 'kodecustomer', 'kodesupplier', 'kodevhc', 'nodok', 'kodeblok', 'revisi'];
        $where = "nojurnal='".$ids['nojurnal']."'";
        $query = selectQuery($dbname, 'keu_jurnaldt', $cols, $where);
        $data = fetchData($query);
        if ($data !== []) {
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

        $dataShow = $data;
        $totalJumlah = 0;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['nik'] = $optKary[$row['nik']];
            $dataShow[$key]['noaruskas'] = $optCashFlow[$row['noaruskas']];
            $dataShow[$key]['kodekegiatan'] = $optKegiatan[$row['kodekegiatan']];
            $dataShow[$key]['kodecustomer'] = $optCustomer[$row['kodecustomer']];
            $dataShow[$key]['kodesupplier'] = $optSupplier[$row['kodesupplier']];
            $dataShow[$key]['kodevhc'] = $optVhc[$row['kodevhc']];
            $dataShow[$key]['matauang'] = $optMatauang[$row['matauang']];
            $dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
            if ('' !== $row['kodebarang'] && '0' !== $row['kodebarang']) {
                $dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
            }

            $dataShow[$key]['kodeblok'] = $optBlok[$row['kodeblok']];
            $dataShow[$key]['revisi'] = $optRev[$row['revisi']];
            $totalJumlah += $row['jumlah'];
        }
        $theForm = new uForm('jurnalForm', 'Form Jurnal Detail', 2);
        $theForm->addEls('nourut', $_SESSION['lang']['nourut'], '0', 'textnum', 'R', 3);
        $theForm->_elements[0]->_attr['disabled'] = 'disabled';
        $theForm->addEls('noakun', $_SESSION['lang']['noakun'], '', 'select', 'L', 25, $optAkun);
        $theForm->addEls('keterangan', $_SESSION['lang']['keterangan'], '', 'text', 'L', 25);
        $theForm->addEls('jumlah', $_SESSION['lang']['jumlah'], $totalJumlah * -1, 'dk', 'R', 15);
        $theForm->_elements[3]->_attr['onkeyup'] = 'this.value=remove_comma(this);this.value = _formatted(this,null,0)';
        $theForm->addEls('matauang', $_SESSION['lang']['matauang'], 'IDR', 'select', 'L', 25, $optMatauang);
        $theForm->addEls('kurs', $_SESSION['lang']['kurs'], '1', 'textnum', 'R', 10);
        $theForm->addEls('noaruskas', $_SESSION['lang']['noaruskas'], '', 'select', 'L', 25, $optCashFlow);
        $theForm->addEls('kodekegiatan', $_SESSION['lang']['kodekegiatan'], '', 'select', 'L', 25, $optKegiatan);
        $theForm->addEls('kodeasset', $_SESSION['lang']['aktivadalam'], '', 'select', 'L', 35, $optAsset);
        $theForm->addEls('kodebarang', $_SESSION['lang']['kodebarang'], '', 'searchBarang', 'L', 10);
        $theForm->addEls('nik', $_SESSION['lang']['nik'], '', 'select', 'L', 35, $optKary);
        $theForm->addEls('kodecustomer', $_SESSION['lang']['kodecustomer'], '', 'select', 'L', 35, $optCustomer);
        $theForm->addEls('kodesupplier', $_SESSION['lang']['kodesupplier'], '', 'select', 'L', 35, $optSupplier);
        $theForm->addEls('kodevhc', $_SESSION['lang']['kodevhc'], '', 'select', 'L', 35, $optVhc);
        $theForm->addEls('nodok', $_SESSION['lang']['nodok'], '', 'text', 'L', 30);
        $theForm->addEls('kodeblok', $_SESSION['lang']['kodeblok'], '', 'select', 'L', 30, $optBlok);
        $theForm->addEls('revisi', $_SESSION['lang']['revisi'], $ids['revisi'], 'select', 'L', 25, $optRev);
        $theForm->_elements[16]->_attr['disabled'] = 'disabled';
        $theTable = new uTable('jurnalTable', 'Tabel Jurnal Detail', '', $data, $dataShow);
        $formTab = new uFormTable('ftJurnalDt', $theForm, $theTable, null, ['nojurnal', 'kodejurnal', 'tanggal', 'matauang', 'revisi']);
        $formTab->_target = 'keu_slave_jurnal_audit_manage_detail';
        $formTab->_defValue = '##matauang=IDR##kurs=1';
        $formTab->_noClearField = '##keterangan';
        $formTab->_numberFormat = '##jumlah';
        $formTab->render();

        break;
    default:
        break;
}

?>
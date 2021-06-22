<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
require_once 'lib/tanaman.php';
$proses = $_GET['proses'];
$param = $_POST;

//FA 20190203
$tipetrans = 'K';
$tipetrans = $param['tipetransaksi'];

switch ($proses) {
    case 'showDetail':
        $whereAKB = "kodeaplikasi='GL' and aktif=1 and jurnalid!= 'M'";
        $queryAKB = selectQuery($dbname, 'keu_5parameterjurnal', 'jurnalid,noakundebet,sampaidebet,noakunkredit,sampaikredit', $whereAKB);
        $optAKB = fetchData($queryAKB);
        $tipe = '';
		$tipetrans = $param['tipetransaksi'];
		
        foreach ($optAKB as $row) {
            if ('K' == $param['tipetransaksi']) {
                if ($row['noakunkredit'] <= $param['noakun'] && $param['noakun'] <= $row['sampaikredit']) {
                    $tipe = $row['jurnalid'];
                }
            } else {
                if ($row['noakundebet'] <= $param['noakun'] && $param['noakun'] <= $row['sampaidebet']) {
                    $tipe = $row['jurnalid'];
                }
            }
        }
        $whereKel = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$tipe."'";
        $optKel = makeOption($dbname, 'keu_5kelompokjurnal', 'kodekelompok,keterangan', $whereKel);
        if (empty($optKel)) {
            echo "Warning : Journal Group  (".$_SESSION['org']['kodeorganisasi']." / ".$tipe.") not assign for your unit/Company\n";
            echo 'Please contact  IT Dept.';
            exit();
        }

        $whereJam = " detail=1 and noakun <> '".$param['noakun']."' and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."')";
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."' and (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."') and tipekaryawan in ('5')";
        } else {
            $whereKary = "kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."' and (tanggalkeluar is NULL or tanggalkeluar > '".$_SESSION['org']['period']['start']."')";
        }

        $whereAsset = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and posting=0";
        $optAsset = makeOption($dbname, 'project', 'kode,nama', $whereAsset, '2', true);
        $optMataUang = makeOption($dbname, 'setup_matauang', 'kode,matauang');
        $optSupplier = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', '1', '0', true);
        $optCustomer = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer', null, '0', true);
        if ('EN' == $_SESSION['language']) {
            $optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1,satuan,noakun', null, '6', true);
        } else {
            $optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,satuan,noakun', null, '6', true);
        }

        $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,nik,namakaryawan', $whereKary, '5', true);
        if ('EN' == $_SESSION['language']) {
            $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun1', $whereJam, '2', true);
        } else {
            $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereJam, '2', true);
        }

        $optVhc = makeOption($dbname, 'vhc_5master', 'kodevhc,kodeorg', '', '2', true);
        if ('KEBUN' == $_SESSION['empl']['tipelokasitugas']) {
            $optOrgAl = makeOption($dbname, 'setup_blok', 'kodeorg,kodeorg', "\r\n\t\t\t\t\t\tkodeorg like '".$_SESSION['empl']['lokasitugas']."%' and luasareaproduktif!=0", '', true);
        } else {
            $optOrgAl = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "length(kodeorganisasi)>6 and induk like '".$_SESSION['empl']['lokasitugas']."%'", '0', true);
        }

        $optCashFlow = makeOption($dbname, 'keu_5mesinlaporandt', 'nourut,keterangandisplay', "tipe='Detail' and namalaporan='CASH FLOW DIRECT'", '2', true);
        $optHutangUnit = [$_SESSION['lang']['no'], $_SESSION['lang']['yes']];

        if ('K' == $param['tipetransaksi']) {
            $invTab = 'keu_tagihanht';
        } else {
            $invTab = 'keu_penagihanht';
        }
        $optInvoice = makeOption($dbname, $invTab, 'noinvoice,noinvoice', "kodeorg='".$_SESSION['org']['kodeorganisasi']."'", '0', true);

        $optField = makeOption($dbname, 'keu_5akun','noakun,fieldaktif', "noakun='".end(array_reverse(array_keys($optAkun)))."'");
        $fieldAktif = '0000000';
        if (isset($optField[end(array_reverse(array_keys($optAkun)))])) {
            $fieldAktif = $optField[end(array_reverse(array_keys($optAkun)))];
        }

        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and tipetransaksi='".$param['tipetransaksi']
				."' and noakun2a='".$param['noakun']."'";
        $cols = 'kode,keterangan1,noakun,noaruskas,matauang,kurs,keterangan2,jumlah,'.'kodekegiatan,kodeasset,kodebarang,
				nik,kodecustomer,kodesupplier,kodevhc,orgalokasi,nodok,hutangunit1';
        $query = selectQuery($dbname, 'keu_kasbankdt', $cols, $where);
        $data = fetchData($query);
        $dataShow = $data;
        $totalJumlah = 0;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
            $dataShow[$key]['kode'] = $optKel[$row['kode']];
            $dataShow[$key]['nik'] = $optKary[$row['nik']];
            $dataShow[$key]['noaruskas'] = $optCashFlow[$row['noaruskas']];
            $dataShow[$key]['kodekegiatan'] = $optKegiatan[$row['kodekegiatan']];
            $dataShow[$key]['kodecustomer'] = $optCustomer[$row['kodecustomer']];
            $dataShow[$key]['kodesupplier'] = $optSupplier[$row['kodesupplier']];
            $dataShow[$key]['kodevhc'] = $optVhc[$row['kodevhc']];
            $dataShow[$key]['matauang'] = $optMataUang[$row['matauang']];
            $dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
            $dataShow[$key]['orgalokasi'] = $optOrgAl[$row['orgalokasi']];
            $dataShow[$key]['hutangunit1'] = $optHutangUnit[$row['hutangunit1']];
            $totalJumlah += $row['jumlah'];
        }
        $theForm2 = new uForm('kasbankForm', 'Form Kas Bank', 2);
        $theForm2->addEls('kode', $_SESSION['lang']['kode'], '', 'select', 'L', 25, $optKel);
        $theForm2->addEls('keterangan1', $_SESSION['lang']['noinvoice'], '', 'text', 'L', 25);
        //$theForm2->addEls('keterangan1', $_SESSION['lang']['noinvoice'], '', 'text', 'L', 25);
        $theForm2->_elements[1]->_attr['onclick'] = "searchNopo('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['noinvoice']
			."','<div id=formPencariandata></div>',event,'".$param['tipetransaksi']."')";
        $theForm2->_elements[1]->_attr['readonly'] = true;
        $theForm2->_elements[1]->_attr['placeholder'] = 'Click to search invoice';
        $theForm2->addEls('noakun', $_SESSION['lang']['noakun'], '', 'selectsearch', 'L', 25, $optAkun);
        $theForm2->_elements[2]->_attr['onchange'] = 'updFieldAktif()';
        $theForm2->addEls('noaruskas', $_SESSION['lang']['noaruskas'], '', 'selectsearch', 'L', 25, $optCashFlow);
        $theForm2->addEls('matauang', $_SESSION['lang']['matauang'], 'IDR', 'select', 'L', 25, $optMataUang);
        $theForm2->addEls('kurs', $_SESSION['lang']['kurs'], '1', 'textnum', 'L', 10);
        $theForm2->addEls('keterangan2', $_SESSION['lang']['keterangan2'], '', 'text', 'L', 40);
        $theForm2->addEls('jumlah', $_SESSION['lang']['jumlah'], $param['jumlahHeader'] - $totalJumlah, 'textnum', 'R', 40);
        $theForm2->_elements[7]->_attr['onchange'] = 'this.value=remove_comma(this);this.value = _formatted(this)';
        $theForm2->addEls('kodekegiatan', $_SESSION['lang']['kodekegiatan'], '', 'selectsearch', 'L', 35, $optKegiatan);
        if ('0' == $fieldAktif[0]) {
            $theForm2->_elements[8]->_attr['disabled'] = 'disabled';
        }

        $theForm2->addEls('kodeasset', $_SESSION['lang']['aktivadalam'], '', 'select', 'L', 35, $optAsset);
        if ('0' == $fieldAktif[1]) {
            $theForm2->_elements[9]->_attr['disabled'] = 'disabled';
        }

        $theForm2->addEls('kodebarang', $_SESSION['lang']['kodebarang'], '', 'searchBarang', 'L', 10);
        if ('0' == $fieldAktif[2]) {
            $theForm2->_elements[10]->_attr['disabled'] = 'disabled';
        }

        $theForm2->addEls('nik', $_SESSION['lang']['nik'], '', 'selectsearch', 'L', 35, $optKary);
        if ('0' == $fieldAktif[3]) {
            $theForm2->_elements[11]->_attr['disabled'] = 'disabled';
        }

        $theForm2->addEls('kodecustomer', $_SESSION['lang']['kodecustomer'], '', 'select', 'L', 35, $optCustomer);
        if ('0' == $fieldAktif[4]) {
            $theForm2->_elements[12]->_attr['disabled'] = 'disabled';
        }

        $theForm2->addEls('kodesupplier', $_SESSION['lang']['kodesupplier'], '', 'selectsearch', 'L', 35, $optSupplier);
        if ('0' == $fieldAktif[5]) {
            $theForm2->_elements[13]->_attr['disabled'] = 'disabled';
        }

        $theForm2->addEls('kodevhc', $_SESSION['lang']['kodevhc'], '', 'selectsearch', 'L', 35, $optVhc);
        if ('0' == $fieldAktif[6]) {
            $theForm2->_elements[14]->_attr['disabled'] = 'disabled';
        }

        $theForm2->addEls('orgalokasi', $_SESSION['lang']['kodeorg'], '', 'select', 'L', 35, $optOrgAl);
        $theForm2->addEls('nodok', $_SESSION['lang']['nodok'], '', 'text', 'L', 35);
        $theForm2->addEls('hutangunit1', $_SESSION['lang']['hutangunit'], '', 'select', 'L', 25, $optHutangUnit);
        $theTable2 = new uTable('kasbankTable', 'Tabel Kas Bank', $cols, $data, $dataShow);
        $formTab2 = new uFormTable('ftPrestasi', $theForm2, $theTable2, null, ['notransaksi', 'kodeorg', 'noakun2a', 'tipetransaksi', 'hutangunit']);
        $formTab2->_target = 'keu_slave_kasbank_detail';
        $formTab2->_noClearField = '##keterangan1##nodok##jumlah';
        $formTab2->_defValue = '##matauang=IDR##kurs=1';
        $formTab2->_numberFormat = '##jumlah';
        $formTab2->_afterCrud = 'afterCrud';
        echo '<fieldset><legend><b>Detail</b></legend>';
        $formTab2->render();
        echo '</fieldset>';

        break;
    case 'add':
        $data = $param;
        $supp = ceksupp($dbname, $param['notransaksi']);
        if (empty($data['noakun'])) {
            exit('Warning: Nomor Akun harus dipilih');
        }

        if (cekuangmuka($dbname, $param['noakun']) && empty($data['nodok'])) {
            exit('Warning: Nomor Dokumen harus diisi untuk akun Uang Muka');
        }

        if (!empty($supp) && !isset($supp[$param['kodesupplier']])) {
            exit('Warning: Supplier harus sama dengan supplier yang sudah ada di notransaksi ini');
        }
/*
        if ((1 == $param['hutangunit'] || '211' == substr($param['noakun'], 0, 3)) && empty($param['keterangan1']) && '2111103' != $param['noakun'] && '2111104' != $param['noakun']) {
            exit('Warning: No Invoice harus dipilih');
        }

        if ('2111103' == $param['noakun'] && empty($param['nodok'])) {
            exit('Warning: No Documen harus dipilih (diisikan No. PO');
        }
*/
        $cols = ['kode', 'keterangan1', 'noakun', 'noaruskas', 'matauang', 'kurs', 'keterangan2', 'jumlah', 'kodekegiatan', 'kodeasset', 'kodebarang', 'nik', 'kodecustomer', 'kodesupplier', 'kodevhc', 'orgalokasi', 'nodok', 'hutangunit1', 'notransaksi', 'kodeorg', 'noakun2a', 'tipetransaksi'];
        unset($data['numRow'], $data['hutangunit']);

        $blk = str_replace(' ', '', $data['orgalokasi']);
        $nik = str_replace(' ', '', $data['nik']);
        $sup = str_replace(' ', '', $data['kodesupplier']);
        $vhc = str_replace(' ', '', $data['kodevhc']);
        if (cekAkun($data['noakun']) && '' == $blk) {
            exit('[ Error ]: Plant Account must comply with Block Code.');
        }

        if (cekAkun($data['noakun']) && '' == $data['kodekegiatan']) {
            exit('[ Error ]: Activity is obligatory.');
        }
/*
        if (cekAkunPiutang($data['noakun']) && '' == $nik) {
            exit('[ Error ]: Employee ID is Obligatory to this Account.');
        }

        if (cekAkunHutang($data['noakun']) && '' == $sup) {
            exit('[ Error ]: Supplier Code is obligatory to this Account.');
        }
*/
        if (cekAkunTrans($data['noakun']) && '' == $vhc) {
            exit('[ Error ]: Vehicle Code is obligatory to this accout.');
        }

        $data['jumlah'] = str_replace(',', '', $data['jumlah']);
        $query = insertQuery($dbname, 'keu_kasbankdt', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['notransaksi'], $data['kodeorg'], $data['noakun2a'], $data['tipetransaksi']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

        break;
    case 'edit':
        $data = $param;
        if (empty($data['noakun'])) {
            exit('Warning: Nomor Akun harus dipilih');
        }

        if (cekuangmuka($dbname, $param['noakun']) && empty($data['nodok'])) {
            exit('Warning: Nomor Dokumen harus diisi untuk akun Uang Muka');
        }
/*
        if ((1 == $param['hutangunit'] || '211' == substr($param['noakun'], 0, 3) && '2111104' != $param['noakun']) && empty($param['keterangan1'])) {
            exit('Warning: No Invoice harus dipilih');
        }
*/
        unset($data['notransaksi'], $data['hutangunit']);

        foreach ($data as $key => $cont) {
            if ('cond_' == substr($key, 0, 5)) {
                unset($data[$key]);
            }
        }
        $data['jumlah'] = str_replace(',', '', $data['jumlah']);
        $where = "notransaksi='".$param['notransaksi']."' and noakun='".$param['cond_noakun']."' and tipetransaksi='".$param['tipetransaksi']."' and noakun2a='".$param['noakun2a']."' and keterangan1='".$param['cond_keterangan1']."' and keterangan2='".$param['cond_keterangan2']."' and kodeorg='".$param['kodeorg']."'";
        $query = updateQuery($dbname, 'keu_kasbankdt', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and noakun2a='".$param['noakun2a']."' and tipetransaksi='".$param['tipetransaksi']."' and keterangan1='".$param['keterangan1']."'\r\n\t\t\t\t and keterangan2='".$param['keterangan2']."'";
        $query = 'delete from `'.$dbname.'`.`keu_kasbankdt` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    case 'updField':
        $optField = makeOption($dbname, 'keu_5akun', 'noakun,fieldaktif', "noakun='".$param['noakun']."'");
        echo $optField[$param['noakun']];

        break;
    case 'getForminvoice':
        $form = "<fieldset style=float: left;>
			<legend>".$_SESSION['lang']['find'].' '.$_SESSION['lang']['noinvoice']."</legend>"
			.$_SESSION['lang']['find'].'<input type=text class=myinputtext id=no_brg value='.date('Y').">&nbsp;
			Suppl/Cust <input id=supplierIdcr style=width:150px>&nbsp;"
			.$_SESSION['lang']['nopo']."<input id=nopocr style=width:150px>&nbsp;
			<button class=mybutton onclick=findNoinvoice('".$_GET['tipetransaksi']."')>Find</button>
			</fieldset><div id=container2><fieldset><legend>".$_SESSION['lang']['result'].'</legend></fieldset></div>';
        echo $form;

        break;
    case 'getInvoice':
        $arrTipe = ['p' => $_SESSION['lang']['pesananpembelian'], 'k' => $_SESSION['lang']['kontrak']];
        $dat = '<fieldset><legend>'.$_SESSION['lang']['result'].'</legend>';
        $dat .= '<div style=overflow:auto;width:100%;height:500px;>';
        $dat .= "<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat .= "<tr class='rowheader'><td>No.</td>";
        $dat .= '<td>'.$_SESSION['lang']['noinvoice'].'</td>';
        $dat .= '<td>'.$_SESSION['lang']['nopo'].'</td>';
        $dat .= '<td>'.$_SESSION['lang']['namasupplier'].'</td>';
        $dat .= '<td>'.$_SESSION['lang']['tipeinvoice'].'</td>';
        $dat .= '<td>'.$_SESSION['lang']['nilaiinvoice'].'</td>';
        $dat .= '<td>'.$_SESSION['lang']['nilaippn'].'</td>';
        $dat .= '<td>'.$_SESSION['lang']['noakun'].'</td>';
        $dat .= '</tr></thead><tbody>';
        if ($tipetrans == 'K') {
			$str = 'select distinct noinvoice from '.$dbname.
				".aging_sch_vw where (((dibayar<nilaipo)or(dibayar<nilaikontrak)or(dibayar<nilaiinvoice))or(dibayar is null or dibayar=0)) 
				and noinvoice like '".$param['txtfind']."%'";
		} else {
			$str = 'select distinct noinvoice from '.$dbname.
				".penagihan_ht where (terbayar<nilaiinvoice or (terbayar is null) or terbayar=0) and noinvoice like '".$param['txtfind']."%'";
		}
        $qstr = mysql_query($str);
        while ($rstr = mysql_fetch_assoc($qstr)) {
            $belumlunas[$rstr['noinvoice']] = $rstr['noinvoice'];
        }
        
		// ---------
		if (!isset($param['idSupplier']) || '' == $param['idSupplier']) {
            $kdsup = ' ';
        } else {
            $kdsup = " and c.namasupplier like '%".$param['idSupplier']."%'  ";
        }
        if ('' != $param['nopocr']) {
            $nopocr = "and a.nopo like '%".$param['nopocr']."%' ";
        }
        // ---------

//       if ($param['nopocr'] == 'K') {
        if ($tipetrans == 'K') {
            $sPo = "select distinct kodesupplier,noinvoice,nopo,tipeinvoice,nilaiinvoice,nilaippn,a.noakun,keterangan,posting,
				b.namakaryawan,c.namasupplier from ".$dbname.".keu_tagihanht a left join "
				.$dbname.".datakaryawan b on a.postingby=b.karyawanid left join "
				.$dbname.".log_5supplier c on a.kodesupplier=c.supplierid 
				where noinvoice like '".$param['txtfind']."%' ".$kdsup.' '.$nopocr.' and a.nilaiinvoice > a.terbayar order by tanggal asc';
        } else {
            $sPo = "select distinct a.kodecustomer,a.noinvoice,a.noorder,a.tipeinvoice,a.nilaiinvoice,a.nilaippn,a.bayarke,a.keterangan,a.posting,
				a.debet,c.kodecustomer,c.namacustomer from ".$dbname.".keu_penagihanht a left join "
				.$dbname.".pmn_4customer c on a.kodecustomer=c.kodecustomer
				where noinvoice like '".$param['txtfind']."%' and a.nilaiinvoice > a.terbayar order by tanggal asc";
        }
        
        $qPo = mysql_query($sPo);
        if (mysql_num_rows($qpo) == 0) {
            $qPo = mysql_query($sPo);
        }
        $no = 0;
        while ($rPo = mysql_fetch_assoc($qPo)) {
//            if ($param['nopocr'] == 'K') {
            if ($tipetrans == 'K') {
                if ($rPo['noinvoice'] == $belumlunas[$rPo['noinvoice']]) {
                    $qVp = 'select a.noinv,b.posting,b.novp,c.noakun,c.kurs,c.matauang,c.jumlah from '.$dbname.'.keu_vp_inv a left join '
						.$dbname.'.keu_vpht b on a.novp=b.novp left join '.$dbname.".keu_vpdt c on a.novp=c.novp where a.noinv='".$rPo['noinvoice']
						."' and b.posting=1";
                    $cekVp = fetchData($qVp);
                    $kursPpn = 1;
                    $kursInv = 1;
                    foreach ($cekVp as $row) {
                        if ('116' == substr($row['noakun'], 0, 3)) {
                            $kursPpn = $row['jumlah'];
                        } else {
                            if ('211' == substr($row['noakun'], 0, 3)) {
                                $kursInv = $row['jumlah'];
                            }
                        }
                    }
                    $sJmlh = 'select distinct sum(jumlah*kurs) as jmlhKas from '.$dbname.".keu_kasbankdt where keterangan1='".$rPo['noinvoice']."'";
                    $qJmlh = mysql_query($sJmlh);
                    $rJmlh = mysql_fetch_assoc($qJmlh);

                    ++$no;

                    $sCek = 'select distinct nilaiinvoice,nilaippn from '.$dbname.".keu_tagihanht where  noinvoice='".$rPo['noinvoice']."'";
                    $qCek = mysql_query($sCek);
                    $rCek = mysql_fetch_assoc($qCek);
                    $totalInvoice = $rCek['nilaiinvoice'] * $kursInv;
                    $totalInvoice += $rCek['nilaippn'] * $kursPpn;
                    if (0 == $rPo['posting'] || empty($cekVp)) {
                        if (empty($cekVp)) {
                            $dat .= "<tr class='rowcontent' title='Document not complete:VP NOT EXIST'><td>".$no.'</td>';
                        } else {
                            $dat .= "<tr class='rowcontent' title='Document not complete:".$rPo['namakaryawan']."'><td>".$no.'</td>';
                        }

                        $dat .= "<td style='background-color:red;'>".$rPo['noinvoice'].'</td>';
                    } else {
                        if ($totalInvoice <= $rJmlh['jmlhKas']) {
                            $dat .= "<tr class='rowcontent' title='Already exist'><td>".$no.'</td>';
                            $dat .= "<td>".$rPo['noinvoice'].'</td>';
                        } else {
                            $sakun = 'select noakun from '.$dbname.'.keu_vpdt a left join '.$dbname.'.keu_vp_inv b on a.novp=b.novp '
								."where b.noinv='".$rPo['noinvoice']."' and left(noakun,3)='211'";
                            $qakun = mysql_query($sakun);
                            $rakun = mysql_fetch_assoc($qakun);
                            $dat .= "<tr class='rowcontent' onclick=\"setPo('".$rPo['noinvoice']."','".$rCek['nilaiinvoice']."','"
								.$rakun['noakun']."','".$rPo['keterangan']."','".$rPo['kodesupplier']."','"
								.$rPo['nopo']."')\" style='pointer:cursor;'><td>".$no.'</td>';
                            $dat .= '<td>'.$rPo['noinvoice'].'</td>';
                        }
                    }

                    $dat .= '<td>'.$rPo['nopo'].'</td>';
                    $dat .= '<td>'.$rPo['namasupplier'].'</td>';
                    $dat .= '<td>'.$arrTipe[$rPo['tipeinvoice']].'</td>';
                    $dat .= '<td>'.number_format($rPo['nilaiinvoice'], 2).'</td>';
                    $dat .= '<td>'.$rPo['nilaippn'].'</td>';
                    $dat .= '<td>'.$rPo['noakun'].'</td></tr>';
                }
            } else if ($tipetrans == 'M') {
                if ($rPo['nilaiinvoice']>$rPo['terbayar']) {
                    $kursPpn = 1;
                    $kursInv = 1;

                    $sJmlh = 'select distinct sum(jumlah*kurs) as jmlhKas from '.$dbname.".keu_kasbankdt where keterangan1='".$rPo['noinvoice']."'";
                    $qJmlh = mysql_query($sJmlh);
                    $rJmlh = mysql_fetch_assoc($qJmlh);

/*
                    $sCek = 'select distinct nilaiinvoice,nilaippn from '.$dbname.".keu_penagihanht where  noinvoice='".$rPo['noinvoice']."'";
                    $qCek = mysql_query($sCek);
                    $rCek = mysql_fetch_assoc($qCek);
*/
                    $no++;

//                    $totalInvoice = $rCek['nilaiinvoice'] * $kursInv;
//                    $totalInvoice += $rCek['nilaippn'] * $kursPpn;
                    $totalInvoice = $rPo['nilaiinvoice'] + $rPo['nilaippn'];

//                    if (0 == $rPo['posting'] || empty($cekVp)) {
                    if (0 == $rPo['posting']) {
//                        if (empty($cekVp)) {
                            $dat .= "<tr class='rowcontent' title=''><td>".$no.'</td>';
/*
                        } else {
                            $dat .= "<tr class='rowcontent' title=''><td>".$no.'</td>';
                        }
*/
                        $dat .= "<td style='background-color:red;'>".$rPo['noinvoice'].'</td>';
                    } else {
                        if ($totalInvoice <= $rJmlh['jmlhKas']) {
                            $dat .= "<tr class='rowcontent' title='Already exist'><td>".$no.'</td>';
                            $dat .= "<td>".$rPo['noinvoice'].'</td>';
                        } else {
//                            $dat .= "<tr class='rowcontent' onclick=\"setPo('".$rPo['noinvoice']."','".$rCek['jmlhinvoice']."','','".$rPo['keterangan']."','".$rPo['kodesupplier']."','".$rPo['nopo']."')\" style='pointer:cursor;'><td>".$no.'</td>';
                            $dat .= "<tr class='rowcontent' onclick=\"setPo('".$rPo['noinvoice']."','".$totalInvoice."','".$rPo['debet']."','".$rPo['keterangan']."','".$rPo['kodecustomer']."','".$rPo['noorder']."')\" style='pointer:cursor;'><td>".$no.'</td>';
                            $dat .= '<td>'.$rPo['noinvoice'].'</td>';
                        }
                    }

                    $dat .= '<td>'.$rPo['noorder'].'</td>';
                    $dat .= '<td>'.$rPo['namacustomer'].'</td>';
                    $dat .= '<td>'.$arrTipe[$rPo['tipeinvoice']].'</td>';
                    $dat .= '<td>'.number_format($rPo['nilaiinvoice'], 2).'</td>';
                    $dat .= '<td>'.$rPo['nilaippn'].'</td>';
                    $dat .= '<td>'.$rPo['debet'].'</td></tr>';
                }
            }
        }
        $dat .= '</tbody></table></div>#Status S atau K, refer To S=Supplier,K=Contractor</fieldset>';
        echo $dat;

        break;
    default:
        break;
}
function cekSupp($dbname, $noTrans)
{
    $query = selectQuery($dbname, 'keu_kasbankdt', 'kodesupplier', "notransaksi='".$noTrans."' and kodesupplier!=''");
    $res = fetchData($query);
    $optSupp = [];
    foreach ($res as $row) {
        $optSupp[$row['kodesupplier']] = $row['kodesupplier'];
    }

    return $optSupp;
}

function cekUangMuka($dbname, $noakun)
{
    $optParam = makeOption($dbname, 'setup_parameterappl', 'kodeparameter,nilai', "kodeaplikasi='UM'");
    $stat = false;
    foreach ($optParam as $nilai) {
        if ($nilai == $noakun) {
            $stat = true;
        }
    }

    return $stat;
}

?>
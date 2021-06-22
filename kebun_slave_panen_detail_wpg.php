<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
include_once 'lib/formTable.php';
include_once 'lib/devLibrary.php';
echo "\r\n";
$proses = $_GET['proses'];
$param = $_POST;

//if (isset($_POST)) {
//    $param = $_POST;
//}
//else {
////    $proses = $_GET['proses'];
//    $param = $_GET;
//}
//$param['kodeorg']= 'BJUE';//$_SESSION['empl']['lokasitugas'];

$month = tanggalsystemm($param['tanggal']);
$thntnm=0;
$query = selectQuery($dbname, 'setup_blok', 'kodeorg,tahuntanam', "kodeorg='".$param['kodeorg']."'");
$res = fetchData($query);
if (!empty($res)) {
    $thntnm = $res[0]['tahuntanam'];
	
}// else {
//    $thntnm = 0;
//}

switch ($proses) {
    case 'showDetail':
        $headFrame = [$_SESSION['lang']['prestasi'], $_SESSION['lang']['absensi'], $_SESSION['lang']['material']];
        $contentFrame = [];
        $whereKeg = "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and ";
        $whereKeg .= "kelompok='PNN'";
        $optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan', $whereKeg);
        $whereOrg = "kodeorg like '%".$param['afdeling']."%' and kodeorg not in 
			(select kodeorganisasi from organisasi where kodeorganisasi like '%".$param['afdeling']."%' and detail=0)";
        // $optOrg = makeOption($dbname, 'setup_blok', 'kodeorg,bloklama', $whereOrg, 2, true);
        $optOrg = makeOption2("select * from setup_blok where $whereOrg",
            array("valueinit"=>'',"captioninit"=> ''),
            array("valuefield"=>'kodeorg',"captionfield"=> 'bloklama' ),null,true);
        $firstOrg = end(array_reverse(array_keys($optOrg)));
        $optThTanam = makeOption($dbname, 'setup_blok', 'kodeorg,tahuntanam', "kodeorg='".end(array_reverse(array_keys($optOrg)))."'");
        $optBin = [1 => 'Ya', 0 => 'Tidak'];
        $thTanam = $optThTanam[end(array_reverse(array_keys($optOrg)))];
        $tgld = explode('-', $param['tanggal']);
        $sBjr = "SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal \r\n\t\t   FROM ".$dbname.'.`kebun_spbdt` a left join '.$dbname.".kebun_spbht b on \r\n\t\t   a.nospb=b.nospb where blok like '".substr($firstOrg, 0, 6)."%'\r\n\t\t   and tanggal <= '".tanggalsystem($param['tanggal'])."' group by tanggal order by tanggal desc limit 1";
        $qBjr = mysql_query($sBjr) ;
        $rBjr = mysql_fetch_assoc($qBjr);
        $rBjrCek = mysql_num_rows($qBjr);
        if ((int) ($rBjr['bjr'])==0 || $rBjrCek==0) {
            $query = selectQuery($dbname, 'kebun_5bjr', 'kodeorg,bjr', "kodeorg='".$firstOrg."' and tahunproduksi = '".$tgld[2]."'");
            $res = fetchData($query);
            if (!empty($res)) {
                $rBjr['bjr'] = $res[0]['bjr'];
            } else {
                $rBjr['bjr'] = 0;
            }
        }

        $where = "notransaksi='".$param['notransaksi']."'";
        $cols = 'nik,kodeorg,bjraktual,tahuntanam,tarif,turunhujan,norma,hasilkerjakg,brondolan,hasilkerja,jumlahlbhbasis,'.'umr,premilebihbasis,premihadir,premirajin,premibrondol,upahpremi,upahkerja,penalti1,penalti2,penalti3,penalti4,penalti5,penalti6,penalti7,rupiahpenalty,luaspanen';
        $query = selectQuery($dbname, 'kebun_prestasi', $cols, $where);
        $data = fetchData($query);
        $nikList = '';
        foreach ($data as $row) {
            if ($nikList !='') {
                $nikList .= ',';
            }

            $nikList .= $row['nik'];
        }
        $whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and "."alias like '%Pemanen%')";
		//kodejabatan='48')";
        //$whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and "."alias like '%Pemanen%')";
	//	//kodejabatan='48')";
        if (!empty($nikList)) {
            $whereKary .= ' or karyawanid in ('.$nikList.')';
        }

        $qKary = sdmJabatanQuery($whereKary); //selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan,nik,subbagian', $whereKary);
        $resKary = fetchData($qKary);
        $optKary = [];
        $optKary[] = '';
        foreach ($resKary as $kary) {
            $optKary[$kary['karyawanid']] = $kary['nik'].'-'.$kary['namakaryawan'].'('.$kary['subbagian'].')';
        }
        $firstKary = getFirstKey($optKary);
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', 'karyawanid='.$firstKary.' and tahun='.date('Y').' and idkomponen in (1)');
        $Umr = fetchData($qUMR);
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            $dataShow[$key]['nik'] = $optKary[$row['nik']];
            $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
        }
        $arrData = [];
        // $arrHujan = ['Tidak' => 'Tidak', 'Ya' => 'Ya'];
        $arrHujan = "<option value=''></option><option value='Tidak'>Tidak</option><option value='Ya'>Ya</option>";
        // echoMessage('karyawan ',"select * from datakaryawan where $whereKary order by namakaryawan");
        $optKary2 = makeOption2($qKary,
            array("valueinit"=>'',"captioninit"=>''),
            array("valuefield"=>'karyawanid',"captionfield"=> 'namakaryawan' ),null,true);
        $denda=[];
        $data=getRows("select * from kebun_5denda where kode='BM'");
        if (count($data)==0) {$denda['BM']=0;} else {$denda['BM']=number_format($data['jumlah'],2);}
        $data=getRows("select * from kebun_5denda where kode='BT'");
        if (count($data)==0) {$denda['BT']=0;} else {$denda['BT']=number_format($data['jumlah'],2);}
        $data=getRows("select * from kebun_5denda where kode='JT'");
        if (count($data)==0) {$denda['JT']=0;} else {$denda['JT']=number_format($data['jumlah'],2);}
        $data=getRows("select * from kebun_5denda where kode='PT'");
        if (count($data)==0) {$denda['PT']=0;} else {$denda['PT']=number_format($data['jumlah'],2);}
        $data=getRows("select * from kebun_5denda where kode='TD'");
        if (count($data)==0) {$denda['TD']=0;} else {$denda['TD']=number_format($data['jumlah'],2);}
        $data=getRows("select * from kebun_5denda where kode='TM'");
        if (count($data)==0) {$denda['TM']=0;} else {$denda['TM']=number_format($data['jumlah'],2);}
        $data=getRows("select * from kebun_5denda where kode='TP'");
        if (count($data)==0) {$denda['TP']=0;} else {$denda['TP']=number_format($data['jumlah'],2);}
         
        $html = "
    <fieldset>
        <legend><b>Detail</b></legend>
        <input type='checkbox' id='allptnik' onclick='allPtKaryawan('nik',this)' title='Show All Employee in Company'>All Employee in Company
        <div id='ftPrestasi'>
            <div id='form_ftPrestasi'>
                <fieldset>
                    <legend id='form_ftPrestasi_title'><b>Form Prestasi : <span id='form_ftPrestasi_mode'>Mode Tambah</span></b></legend>
                    <table>
                        <tbody>
                            <tr>
                                <td width='10%'>
                                    <label for='nik'>NIK</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_nik'>
                                    <select id='nik' name='nik' style='width:195px' onchange='updUpah()'>".$optKary2."
                                    </select><img id='nik_find' onclick='z.elSearch(&quot;nik&quot;,event)' class='zImgBtn' src='images/onebit_02.png' style='position:relative;top:5px'></td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='kodeorg'>Kode Organisasi</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_kodeorg'>
                                    <select id='kodeorgafd' name='kodeorgafd' style='width:195px' onchange='updTahunTanam();'> ".$optOrg."
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='bjraktual'>BJR Aktual</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_bjraktual'>
                                    <input id='bjraktual' name='bjraktual' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='' style='width:39px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='tahuntanam'>Tahun Tanam</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_tahuntanam'>
                                    <input id='tahuntanam' name='tahuntanam' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='-' style='width:39px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='tarif'>Tarif</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_tarif'>
                                    <select id='tarif' name='tarif' style='width:65px' disabled='disabled'></select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='turunhujan'>Premi Turun Hujan</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_turunhujan'>
                                    <select id='turunhujan' name='turunhujan' style='width:65px' onchange='updTahunTanam();' disabled='disabled'>
                                        <option value='Tidak'>Tidak</option>
                                        <option value='Ya'>Ya</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='norma'>Basis (KG)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_norma'>
                                    <input id='norma' name='norma' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='hasilkerja'>Hasil Kerja (JJG)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_hasilkerja'>
                                    <input id='hasilkerja' name='hasilkerja' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onblur='updUpah();'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='brondolan'>Brondolan (Kg)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_brondolan'>
                                    <input id='brondolan' name='brondolan' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onblur='updUpah();'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='hasilkerjakg'>Hasil Kerja (KG)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_hasilkerjakg'>
                                    <input id='hasilkerjakg' name='hasilkerjakg' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled' title='Hasil Kerja (JJG) * BJR [Kebun - Setup - Tabel BJR]'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='jumlahlbhbasis'>Jumlah Panen Lebih Basis (Kg)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_jumlahlbhbasis'>
                                    <input id='jumlahlbhbasis' name='jumlahlbhbasis' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='umr'>Upah Harian (Rp)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_umr'>
                                    <input id='umr' name='umr' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='premilebihbasis'>Premi Lebih Basis (Rp)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_premilebihbasis'>
                                    <input id='premilebihbasis' name='premilebihbasis' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='premihadir'>Premi Hadir (Rp)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_premihadir'>
                                    <input id='premihadir' name='premihadir' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='premirajin'>Premi Rajin (Rp)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_premirajin'>
                                    <input id='premirajin' name='premirajin' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='premibrondol'>Premi Brondolan (Rp)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_premibrondol'>
                                    <input id='premibrondol' name='premibrondol' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='upahpremi'>Total Premi (Rp)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_upahpremi'>
                                    <input id='upahpremi' name='upahpremi' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='upahkerja'>Total Upah Harian Dan Premi (Rp)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_upahkerja'>
                                    <input id='upahkerja' name='upahkerja' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:78px' disabled='disabled'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='penalti1'>Janjang Buah Mentah</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_penalti1'>
                                    <input id='penalti1' name='penalti1' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onchange='hitungDenda(this);' dendacode='001'>
                                    &nbsp;<label for='dendaPenalti1'>Denda</label>&nbsp;<input type='text' id='dendaBM' name='dendaPenalti1' class='myinputtextnumber' style='width:75px' value='".$denda['BM']."' disabled='disabled'> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='penalti2'>Janjang Buah Tangkai Panjang</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_penalti2'>
                                    <input id='penalti2' name='penalti2' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onchange='hitungDenda(this);'>
                                    &nbsp;<label for='dendaPenalti2'>Denda</label>&nbsp;<input type='text' id='dendaBT' name='dendaPenalti2' class='myinputtextnumber' style='width:75px' value='".$denda['BT']."' disabled='disabled'> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='penalti3'>Janjang matang tidak di panen</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_penalti3'>
                                    <input id='penalti3' name='penalti3' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onchange='hitungDenda(this);'>
                                    &nbsp;<label for='dendaPenalti3'>Denda</label>&nbsp;<input type='text' id='dendaJT' name='dendaPenalti3' class='myinputtextnumber' style='width:75px' value='".$denda['JT']."' disabled='disabled'> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='penalti4'>Janjang tinggal di lapangan</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_penalti4'>
                                    <input id='penalti4' name='penalti4' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onchange='hitungDenda(this);'>
                                    &nbsp;<label for='dendaPenalti4'>Denda</label>&nbsp;<input type='text' id='dendaPT' name='dendaPenalti4' class='myinputtextnumber' style='width:75px' value='".$denda['PT']."' disabled='disabled'> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='penalti5'>Brondolan tidak dikutip</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_penalti5'>
                                    <input id='penalti5' name='penalti5' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onchange='hitungDenda(this);'>
                                    &nbsp;<label for='dendaPenalti5'>Denda</label>&nbsp;<input type='text' id='dendaTD' name='dendaPenalti5' class='myinputtextnumber' style='width:75px' value='".$denda['TD']."' disabled='disabled'> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='penalti6'>Pelepah Tidak Disusun</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_penalti6'>
                                    <input id='penalti6' name='penalti6' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onchange='hitungDenda(this);'>
                                    &nbsp;<label for='dendaPenalti6'>Denda</label>&nbsp;<input type='text' id='dendaTM' name='dendaPenalti6' class='myinputtextnumber' style='width:75px' value='".$denda['TM']."' disabled='disabled'> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='penalti7'>Pelepah Menggantung</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_penalti7'>
                                    <input id='penalti7' name='penalti7' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px' onchange='hitungDenda(this);'>
                                    &nbsp;<label for='dendaPenalti7'>Denda</label>&nbsp;<input type='text' id='dendaTP' name='dendaPenalti7' class='myinputtextnumber' style='width:75px' value='".$denda['TP']."' disabled='disabled'> 
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='rupiahpenalty'>Denda (Rp)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_rupiahpenalty'>
                                    <input id='rupiahpenalty' name='rupiahpenalty' disabled='disabled'class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px'>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for='luaspanen'>Luas Panen (Ha)</label>
                                </td>
                                <td>:</td>
                                <td id='ftPrestasi_luaspanen'>
                                    <input id='luaspanen' name='luaspanen' class='myinputtextnumber' onkeypress='return angka_doang(event)' type='text' value='0' style='width:65px'>
                                </td>
                            </tr>
                            <tr>
                                <td colspan='9'>
                                    <input id='ftPrestasi_numRow' name='ftPrestasi_numRow' type='hidden' value='0'>
                                    <button id='addFTBtn_ftPrestasi' name='addFTBtn_ftPrestasi' class='mybutton' onclick='simpanData();'>Simpan</button>
                                    <button id='clearFTBtn_ftPrestasi' name='clearFTBtn_ftPrestasi' class='mybutton' onclick='theFT.clearFT('ftPrestasi','##nik##kodeorg##bjraktual##tahuntanam##tarif##turunhujan##norma##hasilkerja##brondolan##hasilkerjakg##jumlahlbhbasis##umr##premilebihbasis##premihadir##premirajin##premibrondol##upahpremi##upahkerja##penalti1##penalti2##penalti3##penalti4##penalti5##penalti6##penalti7##rupiahpenalty##luaspanen','##notransaksi','Mode Tambah','','##tahuntanam##bjraktual##upahkerja##upahpremi##rupiahpenalty##hasilkerjakg##norma##tarif##umr##jumlahlbhbasis##premirajin##premihadir##premibrondol##premilebihbasis##hasilkerja##turunhujan','##upahkerja=0')'>Batal</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
            <div id='table_ftPrestasi'>
                <fieldset>
                    <legend id='table_ftPrestasi_title'><b>Tabel Prestasi</b></legend>
                    <div style='max-height:200px;overflow:auto'>
                        <table class='sortable' cellspacing='1' border='0' id='prestasiTable'>
                            <thead id='thead_ftPrestasi'>
                                <tr class='rowheader'>
                                <td colspan='2'>Aksi</td>
                                <td id='head_nik' align='center' style='width:300px'>NIK</td>
                                    <td id='head_kodeorg' align='center' style='width:300px'>Kode Organisasi</td>
                                    <td id='head_bjraktual' align='center' style='width:60px'>BJR Aktual</td>
                                    <td id='head_tahuntanam' align='center' style='width:60px'>Tahun Tanam</td>
                                    <td id='head_tarif' align='center' style='width:100px'>Tarif</td>
                                    <td id='head_turunhujan' align='center' style='width:100px'>Premi Turun Hujan</td>
                                    <td id='head_norma' align='center' style='width:100px'>Basis (KG)</td>
                                    <td id='head_hasilkerja' align='center' style='width:100px'>Hasil Kerja (JJG)</td>
                                    <td id='head_brondolan' align='center' style='width:100px'>Brondolan (Kg)</td>
                                    <td id='head_hasilkerjakg' align='center' style='width:120px'>Hasil Kerja (KG)</td>
                                    <td id='head_jumlahlbhbasis' align='center' style='width:120px'>Jumlah Panen Lebih Basis (Kg)</td>
                                    <td id='head_umr' align='center' style='width:120px'>Upah Harian (Rp)</td>
                                    <td id='head_premilebihbasis' align='center' style='width:120px'>Premi Lebih Basis (Rp)</td>
                                    <td id='head_premihadir' align='center' style='width:120px'>Premi Hadir (Rp)</td>
                                    <td id='head_premirajin' align='center' style='width:120px'>Premi Rajin (Rp)</td>
                                    <td id='head_premibrondol' align='center' style='width:120px'>Premi Brondolan (Rp)</td>
                                    <td id='head_upahpremi' align='center' style='width:120px'>Total Premi (Rp)</td>
                                    <td id='head_upahkerja' align='center' style='width:120px'>Total Upah Harian Dan Premi (Rp)</td>
                                    <td id='head_penalti1' align='center' style='width:100px'>Janjang Buah Mentah</td>
                                    <td id='head_penalti2' align='center' style='width:100px'>Janjang Buah Tangkai Panjang</td>
                                    <td id='head_penalti3' align='center' style='width:100px'>Janjang matang tidak di panen</td>
                                    <td id='head_penalti4' align='center' style='width:100px'>Janjang tinggal di lapangan</td>
                                    <td id='head_penalti5' align='center' style='width:100px'>Brondolan tidak dikutip</td>
                                    <td id='head_penalti6' align='center' style='width:100px'>Pelepah Tidak Disusun</td>
                                    <td id='head_penalti7' align='center' style='width:100px'>Pelepah Menggantung</td>
                                    <td id='head_rupiahpenalty' align='center' style='width:100px'>Denda (Rp)</td>
                                    <td id='head_luaspanen' align='center' style='width:100px'>Luas Panen (Ha)</td>
                                </tr>
                            </thead>
                            <tbody id='tbody_ftPrestasi'>########BODY_ROWS########</tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </fieldset>
            </div>
        </div>
    </fieldset> 
        ";
        $prestasi=[];
        $sql = "select p.*,d.namakaryawan from kebun_prestasi p 
        inner join datakaryawan d ON d.karyawanid=p.nik 
        where notransaksi='".$param['notransaksi']."'";
        // $count=getRowCount($sql);
        // if ($count==1) {$prestasi=[getRows($sql)];} else {$prestasi= getRows($sql);}
        $tr="";
        $index=1; 
        $qstr = mysql_query($sql);
        while ($row = mysql_fetch_assoc($qstr)) {
        // foreach($prestasi as $row) { 
            $rowdata=json_encode($row);
            $rowdata=str_replace('"','\'',$rowdata);
		//<td><img src='images/skyblue/edit.png' class='zImgBtn' onclick=\"var data = ".$rowdata."; showDetailData(data);\" title='Edit'></td>
            $tr.= "<tr class='rowcontent'>
			<td><img src='images/icons/grid_dot.png' class='zImgBtn'></td>
            <td><img src='images/skyblue/delete.png' class='zImgBtn' onclick=\"deleteDetailData('".$row['notransaksi']."','".$row['nik']."','".$row['kodeorg']."',".$index.");\" title='Delete'></td>
            <td id='head_nik' align='center' style='width:300px'>".$row['nik']." - ".$row['namakaryawan']."</td>
            <td id='head_kodeorg' align='center' style='width:300px'>".$row['kodeorg']."</td>
            <td id='head_bjraktual' align='center' style='width:60px'>".$row['bjraktual']."</td>
            <td id='head_tahuntanam' align='center' style='width:60px'>".$row['tahuntanam']."</td>
            <td id='head_tarif' align='center' style='width:100px'>".$row['tarif']."</td>
            <td id='head_turunhujan' align='center' style='width:100px'>".$row['turunhujan']."</td>
            <td id='head_norma' align='center' style='width:100px'>".number_format($row['norma'],0)."</td>
            <td id='head_hasilkerja' align='center' style='width:100px'>".number_format($row['hasilkerja'],0)."</td>
            <td id='head_brondolan' align='center' style='width:100px'>".number_format($row['brondolan'],0)."</td>
            <td id='head_hasilkerjakg' align='center' style='width:120px'>".number_format($row['hasilkerjakg'],0)."</td>
            <td id='head_jumlahlbhbasis' align='center' style='width:120px'>".number_format($row['jumlahlbhbasis'],0)."</td>
            <td id='head_umr' align='center' style='width:120px'>".number_format($row['umr'],0)."</td>
            <td id='head_premilebihbasis' align='center' style='width:120px'>".number_format($row['premilebihbasis'],0)."</td>
            <td id='head_premihadir' align='center' style='width:120px'>".number_format($row['premihadir'],0)."</td>
            <td id='head_premirajin' align='center' style='width:120px'>".number_format($row['premirajin'],0)."</td>
            <td id='head_premibrondol' align='center' style='width:120px'>".number_format($row['premibrondol'],0)."</td>
            <td id='head_upahpremi' align='center' style='width:120px'>".number_format($row['upahpremi'],0)."</td>
            <td id='head_upahkerja' align='center' style='width:120px'>".number_format($row['upahkerja'],0)."</td>
            <td id='head_penalti1' align='center' style='width:100px'>".number_format($row['penalti1'],0)."</td>
            <td id='head_penalti2' align='center' style='width:100px'>".number_format($row['penalti2'],0)."</td>
            <td id='head_penalti3' align='center' style='width:100px'>".number_format($row['penalti3'],0)."</td>
            <td id='head_penalti4' align='center' style='width:100px'>".number_format($row['penalti4'],0)."</td>
            <td id='head_penalti5' align='center' style='width:100px'>".number_format($row['penalti5'],0)."</td>
            <td id='head_penalti6' align='center' style='width:100px'>".number_format($row['penalti6'],0)."</td>
            <td id='head_penalti7' align='center' style='width:100px'>".number_format($row['penalti7'],0)."</td>
            <td id='head_rupiahpenalty' align='center' style='width:100px'>".number_format($row['rupiahpenalty'],0)."</td>
            <td id='head_luaspanen' align='center' style='width:100px'>".number_format($row['luaspanen'],0)."</td>
            </tr>
            ";
            $index++;
        }
        $html=str_replace('########BODY_ROWS########',$tr,$html);
        echo $html;
        break;
    case 'add':
        $cols = [ 'notransaksi', 'nik', 'kodeorg', 'bjraktual', 'tahuntanam', 
        'tarif', 'turunhujan', 'norma', 
        'hasilkerjakg', 'brondolan', 
        'hasilkerja', 'jumlahlbhbasis', 'umr', 'premilebihbasis', 
        'premihadir', 'premirajin', 'premibrondol', 'upahpremi',
         'upahkerja', 'penalti1', 'penalti2', 'penalti3', 'penalti4',
          'penalti5', 'penalti6', 'penalti7', 'rupiahpenalty', 'luaspanen','kodekegiatan', 'statusblok', 'pekerjaanpremi'];
        $data = $param;
        $data['kodeorg']=$data['kodeorgafd'];
		// hilangkan tanda koma (,)
        $data['tarif']=str_replace(',','',$data['tarif']);
        $data['norma']=str_replace(',','',$data['norma']);
        $data['hasilkerjakg']=str_replace(',','',$data['hasilkerjakg']);
        $data['brondolan']=str_replace(',','',$data['brondolan']);
        $data['hasilkerja']=str_replace(',','',$data['hasilkerja']);
        $data['jumlahlbhbasis']=str_replace(',','',$data['jumlahlbhbasis']);
        $data['umr']=str_replace(',','',$data['umr']);
        $data['premilebihbasis']=str_replace(',','',$data['premilebihbasis']);
        $data['premihadir']=str_replace(',','',$data['premihadir']);
        $data['premirajin']=str_replace(',','',$data['premirajin']);
        $data['premibrondol']=str_replace(',','',$data['premibrondol']);
        $data['upahpremi']=str_replace(',','',$data['upahpremi']);
        $data['upahkerja']=str_replace(',','',$data['upahkerja']);
        $data['penalti1']=str_replace(',','',$data['penalti1']);
        $data['penalti2']=str_replace(',','',$data['penalti2']);
        $data['penalti3']=str_replace(',','',$data['penalti3']);
        $data['penalti4']=str_replace(',','',$data['penalti4']);
        $data['penalti5']=str_replace(',','',$data['penalti5']);
        $data['penalti6']=str_replace(',','',$data['penalti6']);
        $data['penalti7']=str_replace(',','',$data['penalti7']);
        $data['rupiahpenalty']=str_replace(',','',$data['rupiahpenalty']);
		// hilangkan tanda titik (.)
        $data['tarif']=str_replace('.','',$data['tarif']);
        $data['norma']=str_replace('.','',$data['norma']);
        $data['hasilkerjakg']=str_replace('.','',$data['hasilkerjakg']);
        $data['brondolan']=str_replace('.','',$data['brondolan']);
        $data['hasilkerja']=str_replace('.','',$data['hasilkerja']);
        $data['jumlahlbhbasis']=str_replace('.','',$data['jumlahlbhbasis']);
        $data['umr']=str_replace('.','',$data['umr']);
        $data['premilebihbasis']=str_replace('.','',$data['premilebihbasis']);
        $data['premihadir']=str_replace('.','',$data['premihadir']);
        $data['premirajin']=str_replace('.','',$data['premirajin']);
        $data['premibrondol']=str_replace('.','',$data['premibrondol']);
        $data['upahpremi']=str_replace('.','',$data['upahpremi']);
        $data['upahkerja']=str_replace('.','',$data['upahkerja']);
        $data['penalti1']=str_replace('.','',$data['penalti1']);
        $data['penalti2']=str_replace('.','',$data['penalti2']);
        $data['penalti3']=str_replace('.','',$data['penalti3']);
        $data['penalti4']=str_replace('.','',$data['penalti4']);
        $data['penalti5']=str_replace('.','',$data['penalti5']);
        $data['penalti6']=str_replace('.','',$data['penalti6']);
        $data['penalti7']=str_replace('.','',$data['penalti7']);
        $data['rupiahpenalty']=str_replace('.','',$data['rupiahpenalty']);
        unset($data['numRow']);
        // unset($data['kodeorgafd']);
        $data['kodekegiatan'] = '0';
        $data['statusblok'] = 0;
        $data['pekerjaanpremi'] = 0;
       
        if ($data['norma'] == 0) {
            $warning = 'Basis (KG) tidak boleh Nol.';
            echo 'error:  '.$warning.'.';
            exit();
        }

        if ($data['hasilkerjakg'] == 0) {
            $warning = 'Hasil Kerja (KG) tidak boleh Nol.';
            echo 'error:  '.$warning.'.';
            exit();
        }

        if ($data['jumlahlbhbasis'] < 0) {
            $warning = 'Jumlah Panen Lebih Basis (Kg) tidak boleh Minus.';
            echo 'error:  '.$warning.'.';
            exit();
        }

        if ($data['luaspanen'] <= 0) {
            $warning = 'Luas Panen(Ha)';
            echo 'error: Silakan mengisi '.$warning.'.';
            exit();
        }
        $dmn = "notransaksi='".$data['notransaksi']."' and nik='".$data['nik']."' and kodekegiatan='".$data['kodekegiatan']."'";
        $optCek = makeOption($dbname, 'kebun_prestasi', 'notransaksi,nik', $dmn); 
        $dml = "";
        echoMessage('tarif ',$data['tarif']);
		/*
        if (isset($optCek[$data['notransaksi']]) && $optCek[$data['notransaksi']] != '') {
            $dml = "update kebun_prestasi set ".
            "tarif=".(!empty($data['tarif'])?$data['tarif']:"''").", ".
            "norma=".str_replace(',','',$data['norma']).", ".
            "hasilkerjakg=".str_replace(',','',$data['hasilkerjakg']).", ".
            "brondolan=".str_replace(',','',$data['brondolan']).", ".
            "hasilkerja=".str_replace(',','',$data['hasilkerja']).", ".
            "jumlahlbhbasis=".str_replace(',','',$data['jumlahlbhbasis']).", ".
            "umr=".str_replace(',','',$data['umr']).", ".
            "premilebihbasis=".str_replace(',','',$data['premilebihbasis']).", ".
            "premihadir=".str_replace(',','',$data['premihadir']).", ".
            "premirajin=".str_replace(',','',$data['premirajin']).", ".
            "premibrondol=".str_replace(',','',$data['premibrondol']).", ".
            "upahpremi=".str_replace(',','',$data['upahpremi']).", ".
            "upahkerja=".str_replace(',','',$data['upahkerja']).", ".
            "penalti1=".str_replace(',','',$data['penalti1']).", ".
            "penalti2=".str_replace(',','',$data['penalti2']).", ".
            "penalti3=".str_replace(',','',$data['penalti3']).", ".
            "penalti4=".str_replace(',','',$data['penalti4']).", ".
            "penalti5=".str_replace(',','',$data['penalti5']).", ".
            "penalti6=".str_replace(',','',$data['penalti6']).", ".
            "penalti7=".str_replace(',','',$data['penalti7']).", ".
            "rupiahpenalty=".str_replace(',','',$data['rupiahpenalty'])." ".
            "where notransaksi='".$data['notransaksi']."' and ".
            "nik='".$data['nik']."' and  kodekegiatan=0 and kodeorg='".$data['kodeorg']."' ";
            
        } else {
		*/
            $dml = "insert into kebun_prestasi (";
            for ($i=0;$i<count($cols);$i++) {
                $dml.=$cols[$i]. ($i == count($cols) - 1 ? "" : ",");
            }
            $dml.=") values(";
            for ($i=0;$i<count($cols);$i++) {
                $dml.= "'".$data[$cols[$i]]."'". ($i == count($cols) - 1 ? "" : ",");
            }
            $dml.=")";
        //}
 
		// $query = insertQuery($dbname, 'kebun_prestasi', $data, $cols);
        // echo $insert;
        if (!executeQuery2($dml)) {
            exit();
        }

        unset($data['notransaksi'], $data['kodekegiatan'], $data['statusblok'], $data['pekerjaanpremi']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';

        // echo $result;

        break;
    case 'edit':
        $data = $param;
        $data['kodeorg']=$data['kodeorgafd'];
        unset($data['notransaksi']);
        foreach ($data as $key => $cont) {
            if (substr($key, 0, 5) == 'cond_') {
                unset($data[$key]);
            }
        }
        $dmn = "notransaksi='".$data['notransaksi']."' and nik='".$data['nik']."' and kodekegiatan='".$data['kodekegiatan']."'";
        $optCek = makeOption($dbname, 'kebun_prestasi', 'notransaksi,nik', $dmn);

        /*
        if ($optCek[$data['notransaksi']] != '') {
            $warning = 'Data sudah ada';
            echo 'error:  '.$warning.'.';
            exit();
        }
        */

        /*
		if ($data['upahkerja'] == 0) {
            $warning = 'Upah tidak boleh kosong';
            echo 'error:  '.$warning.'.';
            exit();
        }
		*/

        if ($data['luaspanen'] == 0) {
            $warning = 'Luas Panen(Ha)';
            echo 'error: Silakan mengisi '.$warning.'.';
            exit();
        }

        $where = "notransaksi='".$param['notransaksi']."' and nik='".$param['cond_nik']."' and kodeorg='".$param['cond_kodeorg']."'";
        $query = updateQuery($dbname, 'kebun_prestasi', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        echo json_encode($param);

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."' and nik='".$param['nik']."' and kodeorg='".$param['kodeorg']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_prestasi` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        break;
    case 'updTahunTanam':
        //        $query = selectQuery($dbname, 'setup_blok', 'kodeorg,tahuntanam', "kodeorg='".$param['kodeorg']."'");
        //        $res = fetchData($query);
        //        if (!empty($res)) {
        //            $thntnm = $res[0]['tahuntanam'];
        //        } else {
        //            $thntnm = 0;
        //        }
        
                $tgld = explode('-', $param['tanggal']);
                $sBjr = "SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal FROM $dbname.kebun_spbdt a ".
                    "left join $dbname.kebun_spbht b on a.nospb=b.nospb ".
                    "where blok like '".substr($param['kodeorg'], 0, 10)."%' ".
                    "and tanggal <= '".tanggalsystem($param['tanggal'])."' group by tanggal order by tanggal desc limit 1";
                 
                $qBjr = mysql_query($sBjr) ;
                $rBjr = mysql_fetch_assoc($qBjr);
                $rBjrCek = mysql_num_rows($qBjr);
                if ((int)($rBjr['bjr']) == 0 || $rBjrCek == 0) {
                    $query = selectQuery($dbname, 'kebun_5bjr', 'kodeorg,bjr', "kodeorg='".$param['kodeorg']."' and tahunproduksi = '".$tgld[2]."'");
                    $res = fetchData($query);
                    if (!empty($res)) {
                        $rBjr['bjr'] = $res[0]['bjr'];
                    } else {
                        exit('error: BJR is not exist' );
                    }
                }
        

                $regDataQ = $_SESSION['empl']['lokasitugas'];

                //FA 20190923 - langsung baca blok saja
                $querybs = "select distinct tahuntanam,hasilkg from $dbname.kebun_5premipanen ".
                    "where  kodeorg='".$param['kodeorg']."' and (bulanawal<=".$month." and bulanakhir>=".$month.") ";
                    
                $resbs = fetchData($querybs);
                if (!empty($resbs)) {
                    $basiskg = $resbs[0]['hasilkg'];
                    $thntnm = $resbs[0]['tahuntanam'];
                } else {
                    $basiskg = 0;
                    $thntnm = 1800;
                }
        
                $querycrhhjn = selectQuery($dbname, 'kebun_curahhujan', 'kodeorg,tanggal,pagi,sore,mulaipagi,selesaipagi,TIMESTAMPDIFF(HOUR, mulaipagi, selesaipagi) as selisihjam', "kodeorg='".substr($param['kodeorg'], 0, 6)."' and tanggal='".tanggalsystem($param['tanggal'])."' and pagi >= 20");
                $rescrhhjn = fetchData($querycrhhjn);
                if (!empty($rescrhhjn)) {
                    $pagi = $rescrhhjn[0]['pagi'];
                    $mulaipagi = $rescrhhjn[0]['mulaipagi'];
                    $selesaipagi = $rescrhhjn[0]['selesaipagi'];
                    $selisihjam = $rescrhhjn[0]['selisihjam'];
                    $turunhujan = "yatidak";
                } else {
                    $pagi = 0;
                    $selisihjam = 0;
                    $turunhujan = "tidak";
                }
        
                if ($selisihjam > 0 && $selisihjam < 5) {
                    $basiskg2 = $basiskg - $basiskg / 7 * $selisihjam;
                } else {
                    $basiskg2 = 0;
                }
        
                echo $thntnm.'####'.number_format($rBjr['bjr'], 2).'####'.$basiskg.'####'.$pagi.'####'.$selisihjam.'####'.number_format($basiskg2, 2).'####'.$turunhujan;
        
                break;
    case 'updBjr':
                $tahuntahuntahun = substr($param['notransaksi'], 0, 4);
                $hasil = $param['hasilkerja'];
                $query = selectQuery($dbname, 'kebun_5bjr', 'kodeorg,bjr', "kodeorg='".$param['kodeorg']."' and tahunproduksi = '".$tahuntahuntahun."'");
                $res = fetchData($query);
                if (!empty($res)) {
                    $hasilhasil = $hasilhasilhasil * $res[0]['bjr'];
                    echo $hasilhasil;
                } else {
                    echo '0';
                }
        
                break;
    case 'updUpah':
		$paramBlok = $param['blok'];
        $dtr = "kodeorg='".$paramBlok."'";
        $optTopo = makeOption($dbname, 'setup_blok', 'kodeorg,topografi', $dtr);
        $hasilKg = $param['bjraktual'] * $param['jmlhJjg'];
        $hasilJjg = $param['hslkrjkg'] / $param['bjraktual'];

        // FA 20180108 - jika sdh ada data yg pakai basis utk notransaksi yg sama, blok selanjutnya tanpa basis
        $subnotrans = substr($param['tanggal'],6,4).substr($param['tanggal'],3,2).substr($param['tanggal'],0,2);
        $subnotrans = $subnotrans."/".$_SESSION['empl']['lokasitugas']."/PNN"; //FA - 20190315, dipisahkan per unit utk BKM PNN (PANEN)
        $xnotrans = '';
        $xnik = '';
        $str = "select notransaksi,nik,kodeorg,hasilkerjakg from kebun_prestasi where substr(notransaksi,1,17) = '".$subnotrans."' and nik = '".$param['nik']."' limit 1";
		$qstr = mysql_query($str);
        while ($rows = mysql_fetch_assoc($qstr)) {
            $xnotrans = $rows['notransaksi'];
            $xnik = $rows['nik'];
			if ($hasilKg < $rows['hasilkerjakg']) {
				$paramBlok = $rows['kodeorg'];
			}
        }
        if ($xnotrans != '' && $xnik != '') {
            $isDobel = 1;
        } else {
            $isDobel= 0;
        }

        // FA 20180108 - ambil total lebih basis dan premi kerajinannya yg sudah didapat
        // penggabungan hasil kerja untuk dpt basis -> khusus MIG
        $xtahuntanam = $thntnm;//'2009'; //untuk contoh default aja
        $xnorma = 0;
        $xhasilkerja = 0;
        $tothasilkerja = 0;
        $tothasilkerja_ori = 0;
        $totlbhbasis = 0;
		$totpremilbhbasis= 0;
        $totpremirajin = 0;
        $totpremihadir = 0;
        /*
		$str = "select tahuntanam, norma, sum(hasilkerja-brondolan) as tothasilkerja, 
			sum(jumlahlbhbasis) as totlbhbasis, sum(premirajin) as totpremirajin from kebun_prestasi 
			where substr(notransaksi,1,17) = '".$subnotrans."' and nik = '".$param['nik']."' group by substr(notransaksi,1,17)";
        */
		//FA 20191108 - CDS/LSP
		$str = "select kodeorg, tahuntanam, norma, sum(hasilkerjakg) as tothasilkerja, sum(premilebihbasis) as totpremilbhbasis, 
			sum(jumlahlbhbasis) as totlbhbasis, sum(premirajin) as totpremirajin, sum(premihadir) as totpremihadir from kebun_prestasi 
			where substr(notransaksi,1,17) = '".$subnotrans."' and nik = '".$param['nik']."' group by substr(notransaksi,1,17),nik";
		$qstr = mysql_query($str);
        while ($rows = mysql_fetch_assoc($qstr)) {
            $xtahuntanam = $rows['tahuntanam'];
            $xnorma = $rows['norma'];
            $tothasilkerja = $rows['tothasilkerja'];
			$tothasilkerja_ori = $tothasilkerja;
            $totlbhbasis = $rows['totlbhbasis']; // Kg-nya
			$totpremilbhbasis = $rows['totpremilbhbasis']; // Rupiah-nya
            $totpremirajin = $rows['totpremirajin'];
			$totpremihadir = $rows['totpremihadir'];
        }

        $isgabunghasil = 0;
        if (($tothasilkerja > 0) && ($tothasilkerja<=$xnorma)){
            if ($tothasilkerja+$hasilKg>$xnorma) {
				$isgabunghasil = 1;
				$tothasilkerja = $tothasilkerja + $hasilKg;
			} else {
				$isgabunghasil = 2; // digabung tetep gak dpt basis
				$tothasilkerja = $hasilKg;
			}
        }
		
		$metode= 0; // untuk cek metode
		// CDS -> metode = 1, semua hasil kerja digabung menjadi 1
		$str = "select metode from kebun_5premipanen where kodeorg='".$paramBlok."' limit 1";
		$qstr = mysql_query($str);
		$rows = mysql_fetch_assoc($qstr);
		if ($rows['metode']==1 && $tothasilkerja>0) {
			$isgabunghasil = 3; // untuk blok kedua
            $isDobel = 1;
			$tothasilkerja = $hasilKg;
			$tothasilkerja_ori= $tothasilkerja_ori + $hasilKg;
			$metode= 1;
		}

        //$jumlahlbhbasisxx = $hasilKg - $param['brondolan']; // hari minggu
        $jumlahlbhbasisxx = $hasilKg; // FA 20190510 - Khusus Libo
        if ($isDobel == 0) {
            //$jumlahlbhbasis = $hasilKg - $param['norma'] - $param['brondolan']; // hari bukan minggu
            $jumlahlbhbasis = $hasilKg - $param['norma']; // FA 20190510 - Khusus Libo
//echo "warning: hasilkg=".$hasilKg."/bjraktual =".$param['bjraktual']."* jmlhJjg =".$param['jmlhJjg']." = ".$param['bjraktual'] * $param['jmlhJjg'];
//exit();
        } else {
            if ($isgabunghasil == 0) {
                $jumlahlbhbasis = $jumlahlbhbasisxx;
            } else if ($isgabunghasil == 1) {
                //$jumlahlbhbasis = $tothasilkerja - $xnorma  - $param['brondolan'];                 
				$jumlahlbhbasis = $tothasilkerja - $xnorma; // FA 20190510 - Khusus Libo
            } else if ($isgabunghasil == 3) {
				$jumlahlbhbasis = $tothasilkerja;
			} 
        }
        if ($hasilKg == 0) {
            $jumlahlbhbasis = 0;
        }

        $firstKary = $param['nik'];
        $tgl = explode('-', $param['tanggal']);
        $tnggl = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', "karyawanid='". $param['nik']."' and tahun='".$tgl[2]."'  and idkomponen in (1)");
        $Umr = fetchData($qUMR);
        if ($isDobel == 0) {
            $uphHarian = $Umr[0]['nilai'] / 30; //FA-20191005
            if ($uphHarian == 0) {
                exit("error: Belum punya dasar gaji");
            }
        } else {
            $uphHarian = 0;
        }
		
        $qwe = date('D', strtotime($tnggl));
        $dhr = "regional='".$_SESSION['empl']['regional']."' and tanggal='".$tnggl."'";
        $optHariLbr = makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal', $dhr);

        // sementara hardcoded - FA 20190314
        /*
        if ($_SESSION['empl']['regional']=='KALTENG') {
            $regData = $_SESSION['empl']['regional'];
        } else {
            */
        // $regData =$_SESSION['empl']['lokasitugas'];
        //}
        // ----------------------------------

        $regData = $paramBlok; // FA 20190923 - FA 20191110
        $dmn = "kodeorg='".$regData."' ". //and tahuntanam='".$param['tahuntanam']."' ".
            " and  (bulanawal<=".$month." and bulanakhir>=".$month.") ";
			
		// FA 20190924 - Ganti acuan dari Thn Tanam ke Blok
		/*
        $optRp = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,rupiah,hasilkg,premihadir', $dmn);
        $optBasis = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,hasilkg', $dmn);
        $optPremiHadir = makeOption($dbname, 'kebun_5premipanen', tahuntanam,premihadir', $dmn);
        $optBrondol = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,brondolanperkg', $dmn);
        $optDenda = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,dendabasis', $dmn);
		*/
        $optRp = makeOption($dbname, 'kebun_5premipanen', 'kodeorg,rupiah,hasilkg,premihadir', $dmn);
        $optBasis = makeOption($dbname, 'kebun_5premipanen', 'kodeorg,hasilkg', $dmn);
        $optPremiHadir = makeOption($dbname, 'kebun_5premipanen', 'kodeorg,premihadir', $dmn);
        $optBrondol = makeOption($dbname, 'kebun_5premipanen', 'kodeorg,brondolanperkg', $dmn);
        $optDenda = makeOption($dbname, 'kebun_5premipanen', 'kodeorg,dendabasis', $dmn);
        $lstert = 0;

        // Ini gak dipakai sepertinya - FA 20190314
		/*
        $sTarif = 'select distinct * from '.$dbname.".kebun_5premipanen where kodeorg='".$_SESSION['empl']['regional']."' ".
            " and  (bulanawal<=".$month." and bulanakhir>=".$month.") ";
        $qTarif = mysql_query($sTarif) ;
        while ($rTarif = mysql_fetch_assoc($qTarif)) {
            $rpLbh[$rTarif['bjr']] = $rTarif['rupiah'];
            $basisPanen[$rTarif['bjr']] = $rTarif['hasilkg'];
        }
		*/
        // -----------------------------------------

		// start hitung premi
		$is = 0;
		$sum = 0;
		$lebihbasisx=0;
		$rupiahx=0;
		$pilthntanam = $param['tahuntanam'];
		if ($isgabunghasil == 1) {
			$pilthntanam = $xtahuntanam;
		}

		$sPremi = "select distinct * from $dbname.kebun_5premipanen ".
			"where (kodeorg='".$_SESSION['empl']['regional']
			."' or kodeorg='".	$_SESSION['empl']['lokasitugas']
			."' or kodeorg='".	$paramBlok."') ".
			" and  (bulanawal<=".$month." and bulanakhir>=".$month.")  ".
//			"and (hasilpanen between 0 and ".round($jumlahlbhbasis, 0, PHP_ROUND_HALF_UP).") ".
			"order by hasilkg desc";

		//echo "warning: jumlbhbasis=".$jumlahlbhbasis." / ".$hasilKg." / ".$sPremi." /isgabunghasil=".$isgabunghasil." /isdobel=".$isDobel;
		//exit();
			
		$qPremi = mysql_query($sPremi) ;
		while ($rPremi = mysql_fetch_assoc($qPremi)) {
			$basisKg2[$is] = $rPremi['lebihbasiskg'];
			$lebihbasisx = $rPremi['lebihbasiskg'];
			$rupiahx = $rPremi['rupiah'];
			$basisKg[$is] = $rPremi['hasilpanen'];
			$premiRajin[$is] = $rPremi['premirajin'];
			$rupiah[$is] = $rPremi['rupiah'];
			$is++;
		}
		$JmlhRow = $is - 1;

		$basis = $optBasis[$paramBlok];
		if ($basis == 0) {
			$upah = $uphHarian;
			$insentif = 0;
			$premihadir = 0;
			$premjin = 0;
			$premibrondol = 0;
		}
		$premihadir_proses= 0;
		//echo "warning: basis=".$basis." /paramBlok=".$paramBlok;
		//exit();

		if ($basis !=0) {
			if ($optDenda[$paramBlok] == 1) {
				if ($isgabunghasil == 1 && $tothasilkerja > 0) {
					$xhasilKg = $tothasilkerja;
				} else {
					$xhasilKg = $hasilKg;
				}

				if ($xhasilKg <= $basis) {
					$premibrondol = $optBrondol[$paramBlok] * $param['brondolan'];
					$insentif = 0;
					if ($isDobel == 0) {
						$jumlahlbhbasis = 0;
						$upah = $uphHarian;
						$premihadir = $optPremiHadir[$paramBlok];
						$premjin = 0;
					} else if ($isDobel == 1) {
						$premihadir = 0;
					}
				} 
 
				//FA 20191110
				//if (($xhasilKg > $basis) || ($isDobel == 1)){
				if (($xhasilKg > $basis || $isDobel == 1) && $isgabunghasil !=2){
					if ($optRp[$paramBlok] !=0) {
						$upah = $uphHarian;
						$insentif = 0;
						// FA modified -------------------------------
						$premi = 0;
						$lbhbasis = round($jumlahlbhbasis, 0, PHP_ROUND_HALF_UP);
						//echo "warning: ".$JmlhRow." /".$sPremi;
						//exit();
						$batasbawah = 0;
						$batasatas=0;
						if ($totlbhbasis>0){
							$batasatas= $totlbhbasis;
						}
						$rupiah=0;

						for ($awl = 0; $awl <= $JmlhRow; $awl++) {
							$str = "select hasilpanen, lebihbasiskg, rupiah, metode, premihadir from kebun_5premipanen";
							if ($awl == 0 && $isDobel==1){
								$str = $str." where hasilpanen < ".$batasatas." and lebihbasiskg>".$batasatas;
							} else {
								$str = $str." where hasilpanen > ".$batasatas;
							}
							$str = $str." and bulanawal<=".$month." and bulanakhir>=".$month." and kodeorg='".$paramBlok."' limit 1";
							$qstr = mysql_query($str);
							while ($rows = mysql_fetch_assoc($qstr)) {
								$batasbawah = $rows['hasilpanen'];
								$batasatas = $rows['lebihbasiskg'];
								$rupiah = $rows['rupiah'];
								$premihadir_proses = $rows['premihadir'];
								$metode = $rows['metode'];
							}

							//echo "warning: lbhbasis=".$lbhbasis."/batasatas=".$batasatas."/totlbhbasis=".$totlbhbasis."/awl=".$awl."/row=".$JmlhRow."/isgabung=".$isgabunghasil."/-> ".$str;
							//exit();

							if ($lbhbasis > $batasatas || (($totlbhbasis + $lbhbasis) > $batasatas && $awl == 0)){
								// FA-20191030 -> Jika progresif (0) seperti MIG, jika tidak (1), langsung dikali rate tertinggi (CDS)
								if ($metode==0) {
									if ($batasatas-$totlbhbasis>0){
										$premi = $premi + (($batasatas - $totlbhbasis) * $rupiah); // FA 20200119 - utk WPG/Libo
									} else {
										$premi = $premi + ( $lbhbasis * $rupiah);
									}
									$premihadir = $premihadir_proses; // FA-20191203
								}
								//echo "warning1: premi=".$premi."/premihadir=".$premihadir."/batasatas=".$batasatas."/totlbhbasis=".$totlbhbasis;
								//exit();
							} else {
								$premi = $premi + ($lbhbasis * $rupiah);
								$premihadir = $premihadir_proses;
								//echo "warning2: premi=".$premi."/premihadir=".$premihadir."/batasatas=".$batasatas."/totlbhbasis=".$lbhbasis;
								//exit();
							}
							// FA-20191030, lanjut 20200119 ubah utk WPG
							if ($metode==0) {
								//echo "warning3: lbh=".$lbhbasis."/batasatas=".$batasatas."/totlbhbasis=".$totlbhbasis;
								//exit();
								if ($batasatas - $totlbhbasis >0){
									$lbhbasis = $lbhbasis - ($batasatas - $totlbhbasis);
								} else {
									$lbhbasis = $lbhbasis - $batasatas;
								}
							}
							if($lbhbasis <= 0) {
								#untuk menghentikan for
								$awl = $JmlhRow+1;
							}
						}
						
						$insentif = $premi;
						// ------------------------------------------------
						
						// FA 20191110 - Sebetulnya sudah ada diatas sebelum if (($xhasilKg > $basis) || ($isDobel == 1))
						if ($isDobel == 0) {
						//	$premihadir = $premihadir_proses;
						} else {
							$premihadir = 0;
						}
						$premibrondol = $optBrondol[$paramBlok] * $param['brondolan'];

						// Premi Kerajinan berdasar lebih basis (Extreme Hardcoded) - FA 20190314
						// Ditotal dari yang sudah diterima sebelumnya
						$xtotjumlbhbasis = $totlbhbasis + $jumlahlbhbasis;
						if (round($xtotjumlbhbasis, 0, PHP_ROUND_HALF_UP) < 0.001) {
							$premjin = 0;
						} else {
							if (round($xtotjumlbhbasis, 0, PHP_ROUND_HALF_UP) >= 0.001 && round($xtotjumlbhbasis, 0, PHP_ROUND_HALF_UP) <= 250.99) {
								//if ($_SESSION['empl']['regional']=='KALTENG')
								if ($_SESSION['empl']['lokasitugas']=='BMIE' || $_SESSION['empl']['lokasitugas']=='BJUE'){
									$premjin = 7500;
								} else {
									if ($_SESSION['empl']['lokasitugas']=='HSGE') {
										$premjin = 5000;
									}
									if ($_SESSION['empl']['lokasitugas']=='BNME') {
										$premjin = 3000;
									}
								}
							} else {
								if (round($xtotjumlbhbasis, 0, PHP_ROUND_HALF_UP) > 250.99 && round($xtotjumlbhbasis, 0, PHP_ROUND_HALF_UP) <= 500.99) {
									//if ($_SESSION['empl']['regional']=='KALTENG')
									if ($_SESSION['empl']['lokasitugas']=='BMIE' || $_SESSION['empl']['lokasitugas']=='BJUE'){
										$premjin = 10000;
									} else {
										if ($_SESSION['empl']['lokasitugas']=='HSGE') {
											$premjin = 5000;
										}
										if ($_SESSION['empl']['lokasitugas']=='BNME') {
											$premjin = 5000;
										}
									}
								} else {
									//if ($_SESSION['empl']['regional']=='KALTENG')
									if ($_SESSION['empl']['lokasitugas']=='BMIE' || $_SESSION['empl']['lokasitugas']=='BJUE'){
										$premjin = 15000;
									} else {
										if ($_SESSION['empl']['lokasitugas']=='HSGE') {
											$premjin = 5000;
										}
										if ($_SESSION['empl']['lokasitugas']=='BNME') {
											$premjin = 6000;
										}
									}
								}
							}
							$premjin = $premjin - $totpremirajin;
							if ($premjin < 0) {
								$premjin = 0;
							}
						}
						// --------------------------------------------------------------------------------------------
					} else {
						$upah = $uphHarian;
						$insentif = 0;
						$premjin = 0;
					}
				} else {
					$upah = $uphHarian;
					$insentif = 0;
					$premjin = 0;
				}
				//}
			} else {
				$upah = $optRp[$paramBlok] * $hasilKg;
				$insentif = 0;
				$premjin = 0;
			}
		}

		//FA 20191030 - Untuk CDS dan LSP
		if ($hasilKg < $basis) {
			$premihadir= 0;
		}

		//FA 20191209 - Untuk CDS (1) dan LSP (0)
		if ($xhasilKg > $basis && $isDobel==1) {
			if ($rows['metode']==0){
				$premihadir = $premihadir_proses;
			} else if ($rows['metode']==1){
				$premihadir = $premihadir_proses - $totpremihadir;
				if ($premihadir<0){
					$premihadir= 0;
				}
			}
		}
		if ($isgabunghasil==3 && $totpremihadir==0 && $premi>0){
			$premihadir = $premihadir_proses;
		}
		
		// FA 20191218 - Untuk CDS (1) dan LSP (0), tambah jagaan 
		if ($totpremihadir > 0 && ($premihadir+$totpremihadir > $premihadir_proses)) {
			$premihadir = $premihadir_proses - $totpremihadir;
			if ($premihadir<0){
				$premihadir= 0;
			}
		}
		// --------------------------------
		
		//echo "warning: ".$premihadir." / batasatas=".$batasbawah;
		//exit();

		$totprem = $insentif + $premihadir + $premjin + $premibrondol;
		$totuppremx = $upah + $totprem;
		echo round($upah, 0, PHP_ROUND_HALF_UP).'####'.number_format($basis, 0).'####'.round($insentif, 0, PHP_ROUND_HALF_UP).
		'####'.round($hasilKg, 0, PHP_ROUND_HALF_UP).'####'.$premihadir.'####'.round($jumlahlbhbasis, 0, PHP_ROUND_HALF_UP).
		'####'.round($premjin, 0, PHP_ROUND_HALF_UP).'####'.round($premibrondol, 0, PHP_ROUND_HALF_UP).
		'####'.round($totprem, 0, PHP_ROUND_HALF_UP).'####'.round($totuppremx, 0, PHP_ROUND_HALF_UP)
		.'####'.json_encode($Umr).'####'.$dmn.'####'.$optRp[$paramBlok];

        break;

    case 'updDenda':
        // sementara tidak digunakan karena di BKM Panen input free teks - MIG
        /*
                if ($_SESSION['empl']['regional'] == 'KALTENG') {
                    $dtbjr = 0;
                } else {
         */
        $lstert = 0;
        $sTarif = 'select distinct * from '.$dbname.".kebun_5basispanen where \r\n                         
			kodeorg='".$_SESSION['empl']['regional']."' and jenis='".$param['tarif']."' order by bjr desc";
        $qTarif = mysql_query($sTarif) ;
        while ($rTarif = mysql_fetch_assoc($qTarif)) {
            $rpLbh[$rTarif['bjr']] = $rTarif['rplebih'];
            $basisPanen[$rTarif['bjr']] = $rTarif['basisjjg'];
            $lstBjr[] = $rTarif['bjr'];
            $lstBjr2[$lstert] = $rTarif['bjr'];
            $lstert++;
        }
        $MaxRow = count($lstBjr);
        foreach ($lstBjr as $lstRow => $dtIsiBjr) {
            if ($lstRow == 0) {
                if ($dtIsiBjr < $param['bjraktual']) {
                    $dtbjr = $dtIsiBjr;

                    break;
                }
            } else {
                if ($lstRow != $MaxRow) {
                    $leapdt = $lstRow + 1;
                    if ($param['bjraktual'] == $dtIsiBjr || $lstBjr2[$leapdt] < $param['bjraktual']) {
                        $dtbjr = $dtIsiBjr;

                        break;
                    }
                } else {
                    $dmin = $dtIsiBjr - 1;
                    if ($dmin <= $param['bjraktual']) {
                        $dtbjr = $dtIsiBjr;

                        break;
                    }

                    $dtbjr = 0;
                }
            }
        }
//        }

        $regData = $_SESSION['empl']['regional'];
//        if ($_SESSION['empl']['regional']  == 'KALTENG') {
        $afd = substr($paramBlok, 0, 6);
        $dmn = "kodeorg='".$afd."'";
        $optCek = makeOption($dbname, 'kebun_5basispanen', 'kodeorg,jenis', $dmn);
        if ($optCek[$afd] !=  '') {
            $regData = $afd;
//            }
        }

        $dmn = "kodeorg='".$regData."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
//        if ($_SESSION['empl']['regional'] == 'KALTENG') {
        $dmn = "kodeorg='".$regData."' and jenis='".$param['tarif']."'";
//        }

        /*
                if ($regData == 'H12E02') {
                    $dmn = "kodeorg='".$_SESSION['empl']['regional']."' and jenis='satuan'";
                }
        */

        $optRp = makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih', $dmn);
        $optDenda = makeOption($dbname, 'kebun_5denda', 'kode,jumlah');
        for ($der = 1; $der < 8; $der++) {
            if ($der == 1) {
                $det = 'BM';
                $dend = $_POST['isiDt'][$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
            } else {
                if ($der == 3) {
                    $det = 'TD';
                    $dend = $_POST['isiDt'][$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                } else {
                    if ($der == 5) {
                        $det = 'BT';
                        $dend = $_POST['isiDt'][$der] / $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                    } else {
                        $det = 'TP';
                        $dend = $_POST['isiDt'][$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                    }
                }
            }

            $denda += $dend;
        }
        echo $denda;

        break;
}

?>

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
        $whereOrg = "kodeorg like '%".$param['afdeling']."%'";
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
        $cols = 'nik,kodeorg,bjraktual,tahuntanam,tarif,turunhujan,norma,hasilkerjakg as hasilkerja,brondolan,hasilkerja as hasilkerjakg,jumlahlbhbasis,'.'umr,premilebihbasis,premihadir,premirajin,premibrondol,upahpremi,upahkerja,penalti1,penalti2,penalti3,penalti4,penalti5,penalti6,penalti7,rupiahpenalty,luaspanen';
        $query = selectQuery($dbname, 'kebun_prestasi', $cols, $where);
        $data = fetchData($query);
        $nikList = '';
        foreach ($data as $row) {
            if ($nikList !='') {
                $nikList .= ',';
            }

            $nikList .= $row['nik'];
        }
        $whereKary = "(lokasitugas='".$_SESSION['empl']['lokasitugas']."' and "."tipekaryawan in (3,4) and kodejabatan='120')";
        if (!empty($nikList)) {
            $whereKary .= ' or karyawanid in ('.$nikList.')';
        }

        $qKary = selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan,nik,subbagian', $whereKary);
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

        $optKary2 = makeOption2("select * from datakaryawan where $whereKary order by namakaryawan",
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
        $rows = getRows("select * from kebun_prestasi where notransaksi='".$param['notransaksi']."'");
        if (count($rows)==1) {$prestasi=[$rows];} else {$prestasi=$rows;}
        $tr="";
        
        foreach($prestasi as $row) { 
            $tr.= "<tr class='rowcontent'>
            <td><img src='images/skyblue/edit.png' class='zImgBtn' onclick='showDetail();' title='Edit'></td>
            <td><img src='images/skyblue/delete.png' class='zImgBtn' onclick='deleteDetail();' title='Delete'></td>
            <td id='head_nik' align='center' style='width:300px'>".$row['nik']."</td>
            <td id='head_kodeorg' align='center' style='width:300px'>".$row['kodeorg']."</td>
            <td id='head_bjraktual' align='center' style='width:60px'>".$row['bjraktual']."</td>
            <td id='head_tahuntanam' align='center' style='width:60px'>".$row['tahuntanam']."</td>
            <td id='head_tarif' align='center' style='width:100px'>".$row['tarif']."</td>
            <td id='head_turunhujan' align='center' style='width:100px'>".$row['turunhujan']."</td>
            <td id='head_norma' align='center' style='width:100px'>".$row['norma']."</td>
            <td id='head_hasilkerja' align='center' style='width:100px'>".$row['hasilkerja']."</td>
            <td id='head_brondolan' align='center' style='width:100px'>".$row['brondolan']."</td>
            <td id='head_hasilkerjakg' align='center' style='width:120px'>".$row['hasilkerjakg']."</td>
            <td id='head_jumlahlbhbasis' align='center' style='width:120px'>".$row['jumlahlbhbasis']."</td>
            <td id='head_umr' align='center' style='width:120px'>".$row['umr']."</td>
            <td id='head_premilebihbasis' align='center' style='width:120px'>".$row['premilebihbasis']."</td>
            <td id='head_premihadir' align='center' style='width:120px'>".$row['premihadir']."</td>
            <td id='head_premirajin' align='center' style='width:120px'>".$row['premirajin']."</td>
            <td id='head_premibrondol' align='center' style='width:120px'>".$row['premibrondol']."</td>
            <td id='head_upahpremi' align='center' style='width:120px'>".$row['upahpremi']."</td>
            <td id='head_upahkerja' align='center' style='width:120px'>".$row['upahkerja']."</td>
            <td id='head_penalti1' align='center' style='width:100px'>".$row['penalti1']."</td>
            <td id='head_penalti2' align='center' style='width:100px'>".$row['penalti2']."</td>
            <td id='head_penalti3' align='center' style='width:100px'>".$row['penalti3']."</td>
            <td id='head_penalti4' align='center' style='width:100px'>".$row['penalti4']."</td>
            <td id='head_penalti5' align='center' style='width:100px'>".$row['penalti5']."</td>
            <td id='head_penalti6' align='center' style='width:100px'>".$row['penalti6']."</td>
            <td id='head_penalti7' align='center' style='width:100px'>".$row['penalti7']."</td>
            <td id='head_rupiahpenalty' align='center' style='width:100px'>".$row['rupiahpenalty']."</td>
            <td id='head_luaspanen' align='center' style='width:100px'>".$row['luaspanen']."</td>
        </tr>
            ";
        }
        $html=str_replace('########BODY_ROWS########',$tr,$html);
        echo $html;
        // $theForm2 = new uForm('prestasiForm', 'Form Prestasi', 3);
        // $theForm2->addEls('nik', $_SESSION['lang']['nik'], '', 'selectsearch', 'L', 30, $optKary);
        // $theForm2->_elements[0]->_attr['onchange'] = 'updUpah()';
        // $theForm2->addEls('kodeorg', $_SESSION['lang']['kodeorg'], '', 'select', 'L', 30, $optOrg, null, null, null, 'ftPrestasi_kodeorg');
        // $theForm2->_elements[1]->_attr['onchange'] = 'updTahunTanam();';
        // $theForm2->addEls('bjraktual', $_SESSION['lang']['bjraktual'], number_format($rBjr['bjr'], 2), 'textnum', 'R', 6);
        // $theForm2->_elements[2]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('tahuntanam', $_SESSION['lang']['tahuntanam'], $thTanam, 'textnum', 'R', 6);
        // $theForm2->_elements[3]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('tarif', $_SESSION['lang']['tarif'], '', 'select', 'L', 10, $arrData);
        // $theForm2->_elements[4]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('turunhujan', $_SESSION['lang']['premiturunhujan'], '', 'select', 'L', 10, $arrHujan);
        // $theForm2->_elements[5]->_attr['onchange'] = 'updTahunTanam();';
        // $theForm2->_elements[5]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('norma', $_SESSION['lang']['basiskg'], '0', 'textnum', 'R', 10);
        // $theForm2->_elements[6]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('hasilkerja', $_SESSION['lang']['hasilkerja'], '0', 'textnum', 'R', 10);
        // $theForm2->_elements[7]->_attr['onblur'] = 'updUpah();';
        // $theForm2->addEls('brondolan', $_SESSION['lang']['brondolan'], '0', 'textnum', 'R', 10);
        // $theForm2->_elements[8]->_attr['onblur'] = 'updUpah();';
        // $theForm2->addEls('hasilkerjakg', $_SESSION['lang']['hasilkerjakg'], '0', 'textnum', 'R', 12);
        // $theForm2->_elements[9]->_attr['disabled'] = 'disabled';
        // $theForm2->_elements[9]->_attr['title'] = 'Hasil Kerja (JJG) * BJR [Kebun - Setup - Tabel BJR]';
        // $theForm2->addEls('jumlahlbhbasis', $_SESSION['lang']['jumlahlbhbasis'], '0', 'textnum', 'R', 12);
        // $theForm2->_elements[10]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('umr', $_SESSION['lang']['upahharian'], $Umr[0]['nilai'] / 25, 'textnum', 'R', 12);
        // $theForm2->_elements[11]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('premilebihbasis', $_SESSION['lang']['premilebihbasis'], '0', 'textnum', 'R', 12);
        // $theForm2->_elements[12]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('premihadir', $_SESSION['lang']['premihadir'], '0', 'textnum', 'R', 12);
        // $theForm2->_elements[13]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('premirajin', $_SESSION['lang']['premirajin'], '0', 'textnum', 'R', 12);
        // $theForm2->_elements[14]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('premibrondol', $_SESSION['lang']['premibrondol'], '0', 'textnum', 'R', 12);
        // $theForm2->_elements[15]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('upahpremi', $_SESSION['lang']['totalpremi'], '0', 'textnum', 'R', 12);
        // $theForm2->_elements[16]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('upahkerja', $_SESSION['lang']['totupprem'], '0', 'textnum', 'R', 12);
        // $theForm2->_elements[17]->_attr['disabled'] = 'disabled';
        // $theForm2->addEls('penalti1', $_SESSION['lang']['penalti1'], '0', 'textnum', 'R', 10);
        // $theForm2->addEls('penalti2', $_SESSION['lang']['penalti2'], '0', 'textnum', 'R', 10);
        // $theForm2->addEls('penalti3', $_SESSION['lang']['penalti3'], '0', 'textnum', 'R', 10);
        // $theForm2->addEls('penalti4', $_SESSION['lang']['penalti4'], '0', 'textnum', 'R', 10);
        // $theForm2->addEls('penalti5', $_SESSION['lang']['penalti5'], '0', 'textnum', 'R', 10);
        // $theForm2->addEls('penalti6', $_SESSION['lang']['penalti6'], '0', 'textnum', 'R', 10);
        // $theForm2->addEls('penalti7', $_SESSION['lang']['penalti7'], '0', 'textnum', 'R', 10);
        // $theForm2->_elements[18]->_attr['onchange'] = 'hitungDenda(this);';
        // $theForm2->_elements[19]->_attr['onchange'] = 'hitungDenda(this);';
        // $theForm2->_elements[20]->_attr['onchange'] = 'hitungDenda(this);';
        // $theForm2->_elements[21]->_attr['onchange'] = 'hitungDenda(this);';
        // $theForm2->_elements[22]->_attr['onchange'] = 'hitungDenda(this);';
        // $theForm2->_elements[23]->_attr['onchange'] = 'hitungDenda(this);';
        // $theForm2->_elements[24]->_attr['onchange'] = 'hitungDenda(this);';
        // $theForm2->_elements[18]->_attr['dendacode'] = '001';
        // $theForm2->_elements[19]->_attr['dendacode'] = '002';
        // $theForm2->_elements[20]->_attr['dendacode'] = '003';
        // $theForm2->_elements[21]->_attr['dendacode'] = '004';
        // $theForm2->_elements[22]->_attr['dendacode'] = '005';
        // $theForm2->_elements[23]->_attr['dendacode'] = '006';
        // $theForm2->_elements[24]->_attr['dendacode'] = '007';
        // // $theForm2->_elements[18]->_attr['onblur'] = "updDenda('BM');";
        // // $theForm2->_elements[19]->_attr['onblur'] = "updDenda('TP');";
        // // $theForm2->_elements[20]->_attr['onblur'] = "updDenda('TD');";
        // // $theForm2->_elements[21]->_attr['onblur'] = "updDenda('BT');";
        // // $theForm2->_elements[22]->_attr['onblur'] = "updDenda('PT');";
        // // $theForm2->_elements[23]->_attr['onblur'] = "updDenda('TM');";
        // $theForm2->addEls('rupiahpenalty', $_SESSION['lang']['rupiahpenalty'], '0', 'textnum', 'R', 10);
        // $theForm2->addEls('luaspanen', $_SESSION['lang']['luaspanen'], '0', 'textnum', 'R', 10);
        // $theTable2 = new uTable('prestasiTable', 'Tabel Prestasi', $cols, $data, $dataShow);
        // $formTab2 = new uFormTable('ftPrestasi', $theForm2, $theTable2, null, ['notransaksi']);
        // $formTab2->_target = 'kebun_slave_panen_detail';
        // $formTab2->_noClearField = '';
        // $formTab2->_noEnable = '##tahuntanam##bjraktual##upahkerja##upahpremi##rupiahpenalty##hasilkerjakg##norma##tarif##umr##jumlahlbhbasis##premirajin##premihadir##premibrondol##premilebihbasis##hasilkerja##turunhujan';
        // $formTab2->_defValue = '##upahkerja='.$Umr[0]['nilai'] / 25;
        // echo "<fieldset><legend><b>Detail</b></legend><input type=checkbox id=allptnik onclick=allPtKaryawan('nik',this) title='Show All Employee in Company'>All Employee in Company</checkbox>";
        // $formTab2->render();
        // echo '</fieldset>';

        break;
    case 'add':
        $cols = [ 'notransaksi', 'nik', 'kodeorg', 'bjraktual', 'tahuntanam', 'tarif', 'turunhujan', 'norma', 'hasilkerjakg', 'brondolan', 'hasilkerja', 'jumlahlbhbasis', 'umr', 'premilebihbasis', 'premihadir', 'premirajin', 'premibrondol', 'upahpremi', 'upahkerja', 'penalti1', 'penalti2', 'penalti3', 'penalti4', 'penalti5', 'penalti6', 'penalti7', 'rupiahpenalty', 'luaspanen','kodekegiatan', 'statusblok', 'pekerjaanpremi'];
        $data = $param;
        $data['kodeorg']=$data['kodeorgafd'];
        $data['rupiahpenalty']=str_replace(',','',$data['rupiahpenalty']);
        unset($data['numRow']);
        // unset($data['kodeorgafd']);
        $data['kodekegiatan'] = '0';
        $data['statusblok'] = 0;
        $data['pekerjaanpremi'] = 0;
        $dmn = "notransaksi='".$data['notransaksi']."' and nik='".$data['nik']."' and kodekegiatan='".$data['kodekegiatan']."'";
        $optCek = makeOption($dbname, 'kebun_prestasi', 'notransaksi,nik', $dmn); 
        /*
        if (isset($optCek[$data['notransaksi']]) && $optCek[$data['notransaksi']] != '') {
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

        $insert = "insert into kebun_prestasi (";
        for($i=0;$i<count($cols);$i++){
            $insert.=$cols[$i]. ($i == count($cols) - 1 ? "" : ",");
        }
        $insert.=") values(";
        echoMessage('data ',$data);
        for($i=0;$i<count($cols);$i++){
            $insert.= "'".$data[$cols[$i]]."'". ($i == count($cols) - 1 ? "" : ",");
        }
        $insert.=")";
        // $query = insertQuery($dbname, 'kebun_prestasi', $data, $cols);
        echo $insert;
        if (!mysql_query($insert)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

        unset($data['notransaksi'], $data['kodekegiatan'], $data['statusblok'], $data['pekerjaanpremi']);

        $res = '';
        foreach ($data as $cont) {
            $res .= '##'.$cont;
        }
        $result = '{res:"'.$res.'",theme:"'.$_SESSION['theme'].'"}';
        echo $result;

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
                exit('error: BJR is not exist');
            }
        }

        /* if ($_SESSION['empl']['regional']=='KALTENG') {
             $regDataQ = $_SESSION['empl']['regional'];
         } else {
         */
        //$regDataQ =  substr($param['kodeorg'], 0, 4);
        $regDataQ = $_SESSION['empl']['lokasitugas'];
        //}
        /*
    //        $querybs = selectQuery($dbname, 'kebun_5premipanen', 'kodeorg,tahuntanam,hasilkg', "kodeorg='".$regDataQ."' and tahuntanam='".$thntnm."'");
        */
        $querybs = "select distinct hasilkg from $dbname.kebun_5premipanen ".
            "where  kodeorg='".$regDataQ."' and tahuntanam=".$thntnm." and (bulanawal<=".$month." and bulanakhir>=".$month.") ";
        $resbs = fetchData($querybs);
        if (!empty($resbs)) {
            $basiskg = $resbs[0]['hasilkg'];
        } else {
            $basiskg = 0;
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
        //echo "warning: ".$_SESSION['empl']['lokasitugas']." kodeorg: ".$param['kodeorg']." blok: ".$param['blok'];
        //exit();

        $dtr = "kodeorg='".$param['blok']."'";
        $optTopo = makeOption($dbname, 'setup_blok', 'kodeorg,topografi', $dtr);
        $hasilKg = $param['bjraktual'] * $param['jmlhJjg'];
        $hasilJjg = $param['hslkrjkg'] / $param['bjraktual'];

        // FA 20180108 - jika sdh ada data yg pakai basis utk notransaksi yg sama, blok selanjutnya tanpa basis
        $subnotrans = substr($param['tanggal'],6,4).substr($param['tanggal'],3,2).substr($param['tanggal'],0,2);
        $subnotrans = $subnotrans."/".$_SESSION['empl']['lokasitugas']; //FA - 20190315, dipisahkan per unit
        $xnotrans = '';
        $xnik = '';
        $xkodeorg = '';
        $str = "select notransaksi,nik,kodeorg from kebun_prestasi where substr(notransaksi,1,13) = '".$subnotrans."' and nik = '".$param['nik']."' limit 1";
        $qstr = mysql_query($str);
        while ($rows = mysql_fetch_assoc($qstr)) {
            $xnotrans = $rows['notransaksi'];
            $xnik = $rows['nik'];
            $xkodeorg = $rows['kodeorg'];
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
        $totlbhbasis = 0;
        $totpremirajin = 0;
        $str = "select tahuntanam, norma, sum(hasilkerja-brondolan) as tothasilkerja, 
			sum(jumlahlbhbasis) as totlbhbasis, sum(premirajin) as totpremirajin from kebun_prestasi 
			where substr(notransaksi,1,13) = '".$subnotrans."' and nik = '".$param['nik']."' group by substr(notransaksi,1,13)";
        $qstr = mysql_query($str);
        while ($rows = mysql_fetch_assoc($qstr)) {
            $xtahuntanam = $rows['tahuntanam'];
            $xnorma = $rows['norma'];
            $tothasilkerja = $rows['tothasilkerja'];
            $totlbhbasis = $rows['totlbhbasis'];
            $totpremirajin = $rows['totpremirajin'];
        }

        $isgabunghasil = 0;
        if (($tothasilkerja > 0) && ($tothasilkerja<=$xnorma) && ($tothasilkerja+$hasilKg>$xnorma)){
            $isgabunghasil = 1;
            $tothasilkerja = $tothasilkerja + $hasilKg;
        }
        // ----------

        $jumlahlbhbasisxx = $hasilKg - $param['brondolan']; // hari minggu
        if ($isDobel == 0) {
            $jumlahlbhbasis = $hasilKg - $param['norma'] - $param['brondolan']; // hari bukan minggu
        } else {
            if ($isgabunghasil == 0) {
                $jumlahlbhbasis = $jumlahlbhbasisxx;
            } else {
                $jumlahlbhbasis = $tothasilkerja - $xnorma  - $param['brondolan'];
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
            $uphHarian = $Umr[0]['nilai'] / 25;
            if ($uphHarian == 0) {
                exit("error: Don't have basic salary !!");
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
        $regData =$_SESSION['empl']['lokasitugas'];
        //}
        // ----------------------------------
//        $dmn = "kodeorg='".$regData."' and tahuntanam='".$param['tahuntanam']."'";
        $dmn = "kodeorg='".$regData."' and tahuntanam='".$param['tahuntanam']."' ".
            " and  (bulanawal<=".$month." and bulanakhir>=".$month.") ";
        $optRp = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,rupiah,hasilkg,premihadir', $dmn);
        $optBasis = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,hasilkg', $dmn);
        $optPremiHadir = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,premihadir', $dmn);
        $optBrondol = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,brondolanperkg', $dmn);
        $optDenda = makeOption($dbname, 'kebun_5premipanen', 'tahuntanam,dendabasis', $dmn);
        $lstert = 0;

        // Ini gak dipakai sepertinya - FA 20190314
        $sTarif = 'select distinct * from '.$dbname.".kebun_5premipanen where kodeorg='".$_SESSION['empl']['regional']."' ".
            " and  (bulanawal<=".$month." and bulanakhir>=".$month.") ";
        $qTarif = mysql_query($sTarif) ;
        while ($rTarif = mysql_fetch_assoc($qTarif)) {
            $rpLbh[$rTarif['bjr']] = $rTarif['rupiah'];
            $basisPanen[$rTarif['bjr']] = $rTarif['hasilkg'];
        }
        // -----------------------------------------



        // Jika hari Minggu , tidak ada Premi Kerajinan
        if ($qwe == 'Sun'|| isset($optHariLbr[$_SESSION['empl']['regional']]) && $optHariLbr[$_SESSION['empl']['regional']] !='') {
            $is = 1;
            $sum = 0;
            $sPremi = "select distinct * from $dbname.kebun_5premipanen ".
                "where (kodeorg='".$_SESSION['empl']['regional']."' or kodeorg='".	$_SESSION['empl']['lokasitugas']."') ".
                " and tahuntanam='".$param['tahuntanam']."' ".
                /* tambah disini */
                " and  (bulanawal<=".$month." and bulanakhir>=".$month.") ".
                /* end tambah disini */
                "order by lebihbasiskg";
            while ($rPremi = mysql_fetch_assoc($qPremi)) {
                $basisKg2[$is] = $rPremi['lebihbasiskg'];
                $basisKg[$is] = $rPremi['hasilpanen'];
                $premiRajin[$is] = $rPremi['premirajin'];
                $rupiah[$is] = $rPremi['rupiah'];
                $is++;
            }

            $JmlhRow = $is - 1;
            $basis = $optBasis[$param['tahuntanam']];
            if ($basis == 0) {
                $upah = $uphHarian;
                $insentif = 0;
                $premihadir = 0;
                $premjin = 0;
                $premibrondol = 0;
            }

            if ($basis !=0) {
                if ($optDenda[$param['tahuntanam']] == 1) {
                    if ($hasilKg <= 0) {
                        $upah = $uphHarian;
                        $insentif = 0;
                        if ($isDobel == 0) {
                            $premihadir = $optPremiHadir[$param['tahuntanam']];
                        } else {
                            $premihadir = 0;
                        }
                        $premjin = 0;
                        $premibrondol = $optBrondol[$param['tahuntanam']] * $param['brondolan'];
                    } else {
                        if ($hasilKg <= $basis || $basis <= $hasilKg) {
                            if ($optRp[$param['tahuntanam']] !=0) {
                                $upah = $uphHarian;
                                $insentif = 0;
                                //$penguranglbhbasis = $jumlahlbhbasisxx;

                                // Kodesa modified -------------------------------
                                $premi = 0;
                                $lbhbasis = round($jumlahlbhbasisxx, 0, PHP_ROUND_HALF_UP);
                                $batasatas=0;
                                $rupiah=0;
                                $batasbawah = 0;
                                for ($awl = 0; $awl <= $JmlhRow; $awl++) {
                                    $str = "select hasilpanen, lebihbasiskg, rupiah from kebun_5premipanen 
									where kebun_5premipanen.tahuntanam = ".$param['tahuntanam']
                                        ." and kebun_5premipanen.hasilpanen > ".$batasatas
                                        ." and  (bulanawal<=".$month." and bulanakhir>=".$month.") "
                                        ." and (kodeorg='".$_SESSION['empl']['regional']."' or kodeorg='".$_SESSION['empl']['lokasitugas']."') limit 1";
                                    $qstr = mysql_query($str);
                                    while ($rows = mysql_fetch_assoc($qstr)) {
                                        $batasbawah = $rows['hasilpanen'];
                                        $batasatas = $rows['lebihbasiskg'];
                                        $rupiah = $rows['rupiah'];
                                    }
                                    if ($lbhbasis > $batasatas){
                                        $premi = $premi + ($batasatas * $rupiah);
                                    } else {
                                        $premi = $premi + ($lbhbasis * $rupiah);
                                    }

                                    $lbhbasis = $lbhbasis - $batasatas;
                                    if($lbhbasis <= 0) {
                                        #untuk menghentikan for
                                        $awl = $JmlhRow+1;
                                    }
                                }

                                $insentif = $premi;
                                // ------------------------------------------------

                                if ($isDobel == 0) {
                                    $premihadir = $optPremiHadir[$param['tahuntanam']];
                                } else {
                                    $premihadir = 0;
                                }
                                $premibrondol = $optBrondol[$param['tahuntanam']] * $param['brondolan'];
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
                    }
                } else {
                    $upah = $optRp[$param['tahuntanam']] * $hasilKg;
                    $insentif = 0;
                    $premjin = 0;
                }
            }


            $totprem = $insentif + $premihadir + $premjin + $premibrondol;
            $totuppremx = $upah + $totprem;
            if ($jumlahlbhbasisxx <= 0) {
                $insentif = $hasilKg * 100;
            }
            echo round($upah, 0, PHP_ROUND_HALF_UP).'####'.
                number_format($basis, 0).'####'.
                round($insentif, 0, PHP_ROUND_HALF_UP).'####'.
                round($hasilKg, 0, PHP_ROUND_HALF_UP).'####'.
                round($premihadir, 0, PHP_ROUND_HALF_UP).'####'.
                round($jumlahlbhbasisxx, 0, PHP_ROUND_HALF_UP).'####'.
                round($premjin, 0, PHP_ROUND_HALF_UP).'####'.
                round($premibrondol, 0, PHP_ROUND_HALF_UP).'####'.
                round($totprem, 0, PHP_ROUND_HALF_UP).'####'.
                round($totuppremx, 0, PHP_ROUND_HALF_UP);

        }

        // Jika bukan hari Minggu
        else {
            $is = 0;
            $sum = 0;
            $lebihbasisx=0;
            $rupiahx=0;
            $pilthntanam = $param['tahuntanam'];
            if ($isgabunghasil == 1) {
                $pilthntanam = $xtahuntanam;
            }
            $sPremi = "select distinct * from $dbname.kebun_5premipanen ".
                "where (kodeorg='".$_SESSION['empl']['regional']."' or kodeorg='".$_SESSION['empl']['lokasitugas']."') ".
            "and tahuntanam='".$pilthntanam."' ".
                " and  (bulanawal<=".$month." and bulanakhir>=".$month.")  ".
                "and (hasilpanen between 0 and ".round($jumlahlbhbasis, 0, PHP_ROUND_HALF_UP).") ".
            "order by hasilkg desc";
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

            $basis = $optBasis[$param['tahuntanam']];
            if ($basis == 0) {
                $upah = $uphHarian;
                $insentif = 0;
                $premihadir = 0;
                $premjin = 0;
                $premibrondol = 0;
            }

            if ($basis !=0) {
                if ($optDenda[$param['tahuntanam']] == 1) {
                    if ($isgabunghasil == 1 && $tothasilkerja > 0) {
                        $xhasilKg = $tothasilkerja;
                    } else {
                        $xhasilKg = $hasilKg;
                    }
                    if ($xhasilKg <= $basis) {
                        $premibrondol = $optBrondol[$param['tahuntanam']] * $param['brondolan'];
                        $insentif = 0;
                        $jumlahlbhbasis = 0;
                        if ($isDobel == 0) {
                            $upah = $uphHarian;
                            $premihadir = $optPremiHadir[$param['tahuntanam']];
                            $premjin = 0;
                        } else if ($isDobel == 1) {
                            $premihadir = 0;
                        }
                    } else {
                        if (($xhasilKg > $basis) || ($isDobel == 1)){
                            if ($optRp[$param['tahuntanam']] !=0) {
                                $upah = $uphHarian;
                                $insentif = 0;
                                // Kodesa modified -------------------------------
                                $premi = 0;
                                $lbhbasis = round($jumlahlbhbasis, 0, PHP_ROUND_HALF_UP);
                                $batasatas=0;
                                $rupiah=0;
                                $batasbawah = 0;

                                for ($awl = 0; $awl <= $JmlhRow; $awl++) {
                                    $str = "select hasilpanen, lebihbasiskg, rupiah from kebun_5premipanen 
									where kebun_5premipanen.tahuntanam = ".$param['tahuntanam']
                                        ." and kebun_5premipanen.hasilpanen > ".$batasatas
                                        ." and  (bulanawal<=".$month." and bulanakhir>=".$month.") "
                                        ." and (kodeorg='".$_SESSION['empl']['regional']."' or ".
                                        "kodeorg='".$_SESSION['empl']['lokasitugas']."') limit 1";

                                    $qstr = mysql_query($str);
                                    while ($rows = mysql_fetch_assoc($qstr)) {
                                        $batasbawah = $rows['hasilpanen'];
                                        $batasatas = $rows['lebihbasiskg'];
                                        $rupiah = $rows['rupiah'];
                                    }
                                    if ($lbhbasis > $batasatas){
                                        $premi = $premi + ($batasatas * $rupiah);
                                    } else {
                                        $premi = $premi + ($lbhbasis * $rupiah);
                                    }

                                    $lbhbasis = $lbhbasis - $batasatas;
                                    if($lbhbasis <= 0) {
                                        #untuk menghentikan for
                                        $awl = $JmlhRow+1;
                                    }
                                }

                                $insentif = $premi;
                                // ------------------------------------------------

                                if ($isDobel == 0) {
                                    $premihadir = $optPremiHadir[$param['tahuntanam']];
                                } else {
                                    $premihadir = 0;
                                }
                                $premibrondol = $optBrondol[$param['tahuntanam']] * $param['brondolan'];

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
                    }
                } else {
                    $upah = $optRp[$param['tahuntanam']] * $hasilKg;
                    $insentif = 0;
                    $premjin = 0;
                }
            }

            $totprem = $insentif + $premihadir + $premjin + $premibrondol;
            $totuppremx = $upah + $totprem;
            echo round($upah, 0, PHP_ROUND_HALF_UP).'####'.number_format($basis, 0).'####'.round($insentif, 0, PHP_ROUND_HALF_UP).'####'.round($hasilKg, 0, PHP_ROUND_HALF_UP).'####'.$premihadir.'####'.round($jumlahlbhbasis, 0, PHP_ROUND_HALF_UP).'####'.round($premjin, 0, PHP_ROUND_HALF_UP).'####'.round($premibrondol, 0, PHP_ROUND_HALF_UP).'####'.round($totprem, 0, PHP_ROUND_HALF_UP).'####'.round($totuppremx, 0, PHP_ROUND_HALF_UP);
        }
        break;

    case 'updDenda':
        // sementara tidak digunakan karena di BKM Panen input free teks
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
        $afd = substr($param['blok'], 0, 6);
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

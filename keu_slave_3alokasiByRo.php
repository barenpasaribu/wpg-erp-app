<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$tanggal = str_replace('-', '', $param['periode']).'28';
$strx = 'select * from '.$dbname.".organisasi where induk='".$param['pt']."' and tipe='KEBUN'";
$resx = mysql_query($strx);
if (mysql_num_rows($resx) < 1) {
    exit(' Error: Tidak ada unit kebun pada PT tujuan');
}

$str = 'select * from  '.$dbname.".keu_jurnaldt_vw where noreferensi='ALK_".$param['kodeorg']."' and tanggal=".$tanggal." \r\n          and kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$param['pt']."' and tipe='KEBUN')\r\n           limit 1";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    exit(' Error: Sudah pernah dialokasikan untuk PT '.$param['pt'].' pada periode ini');
}

$pemilik['akundebet'] = [];
$pemilik['akunkredit'] = '';
$str = 'select noakunkredit from '.$dbname.".keu_5parameterjurnal where jurnalid='RODL'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $pemilik['akunkredit'] = $bar->noakunkredit;
}
if ('' === $pemilik['akunkredit']) {
    exit(' Error: Parameter jurnal untuk jurnalid RODL belum ada');
}

if ($_SESSION['empl']['kodeorganisasi'] !== $param['pt']) {
    $kode = 'inter';
} else {
    $kode = 'intra';
}

$penerima['unit'] = [];
while ($bar = mysql_fetch_object($resx)) {
    $penerima['unit'][] = $bar->kodeorganisasi;
    $str1 = 'select akunpiutang from '.$dbname.".keu_5caco where jenis='".$kode."' and kodeorg='".$bar->kodeorganisasi."'";
    $res1 = mysql_query($str1);
    while ($bar1 = mysql_fetch_object($res1)) {
        $pemilik['akundebet'][$bar->kodeorganisasi] = $bar1->akunpiutang;
    }
    foreach ($penerima['unit'] as $key => $val) {
        if ('' === $pemilik['akundebet'][$val]) {
            exit(' Error: Akun intra/interco belum ada untuk unit '.$val);
        }
    }
}
$str = 'select sum(luasareaproduktif)  as luas,left(kodeorg,4) as unit from '.$dbname.'.setup_blok group by left(kodeorg,4)';
$res = mysql_query($str);
$luas = [];
while ($bar = mysql_fetch_object($res)) {
    $luas[$bar->unit] = $bar->luas;
}
$totalLuas = 0;
foreach ($penerima['unit'] as $key => $val) {
    $luaspenerima[$val] = $luas[$val];
    $totalLuas += $luas[$val];
}
unset($luas);
if (0 === $totalLuas) {
    $jumlahunit = count($penerima['unit']);
    foreach ($penerima['unit'] as $key => $val) {
        $jumlah[$val] = $param['jumah'] / $jumlahunit;
    }
} else {
    foreach ($penerima['unit'] as $key => $val) {
        $jumlah[$val] = $luaspenerima[$val] / $totalLuas * $param['jumlah'];
    }
}

$arrNoJurnal = [];
$kodejurnal = 'M';
$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
$nojurnal = $tanggal.'/'.$param['kodeorg'].'/'.$kodejurnal.'/'.$konter;
$arrNoJurnal[] = $nojurnal;
$data['header'][] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => $param['jumlah'], 'totalkredit' => -1 * $param['jumlah'], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_'.$param['kodeorg'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
$noUrut = 1;
$dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $pemilik['akunkredit'], 'keterangan' => 'Biaya Tidak Langsung yang dialokasi', 'jumlah' => -1 * $param['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $param['kodeorg'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_'.$param['kodeorg'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
++$noUrut;
foreach ($penerima['unit'] as $key => $val) {
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $pemilik['akundebet'][$val], 'keterangan' => 'Alokasi Biaya Tidak Langsung RO/HO', 'jumlah' => $jumlah[$val], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $param['kodeorg'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_'.$param['kodeorg'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
    ++$noUrut;
}
$luastbm = [];
$jatahtm = [];
$jatahtbm = [];
foreach ($penerima['unit'] as $key => $val) {
    if (0 < $luaspenerima[$val]) {
        $str = 'select luasareaproduktif as luastbm,kodeorg from '.$dbname.".setup_blok where left(kodeorg,4)='".$val."' \r\n                       and statusblok in('LC','TB','TBM','TBM1','TBM2','TBM3','TBMPRO','BBT')";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $luastbm[$val] += $bar->luastbm;
            $blok[$val][] = $bar->kodeorg;
        }
        $jatahtbm[$val] = $luastbm[$val] / $luaspenerima[$val] * $jumlah[$val];
        $jatahtm[$val] = $jumlah[$val] - $jatahtbm[$val];
    } else {
        $jatahtm[$val] = $jumlah[$val];
        $jatahtbm[$val] = 0;
    }
}
$penerima['akunkredit'] = '';
$str1 = 'select akunhutang from '.$dbname.".keu_5caco where jenis='".$kode."' and kodeorg='".$param['kodeorg']."'";
$res1 = mysql_query($str1);
while ($bar1 = mysql_fetch_object($res1)) {
    $penerima['akunkredit'] = $bar1->akunhutang;
}
if ('' === $penerima['akunkredit']) {
    exit(' Error: Akun intra/interco belum ada untuk unit '.$param['kodeorg']);
}

$penerima['akundebet']['tm'] = '';
$penerima['akundebet']['tbm'] = '';
$str = 'select noakundebet,jurnalid from '.$dbname.".keu_5parameterjurnal where jurnalid in('ROTBM','ROTM')";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if ('ROTM' === $bar->jurnalid) {
        $penerima['akundebet']['tm'] = $bar->noakundebet;
    } else {
        $penerima['akundebet']['tbm'] = $bar->noakundebet;
    }
}
if ('' === $penerima['akundebet']['tm'] || '' === $penerima['akundebet']['tbm']) {
    exit(' Error: No.Akun debet untuk ROTBM atau ROTM belum terisi pada parameterjurnal');
}

$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$param['pt']."' and kodekelompok='".$kodejurnal."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
foreach ($penerima['unit'] as $key => $val) {
    $nojurnal = $tanggal.'/'.$val.'/'.$kodejurnal.'/'.$konter;
    $arrNoJurnal[] = $nojurnal;
    $data['header'][] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => $tanggal, 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => $jumlah[$val], 'totalkredit' => -1 * $jumlah[$val], 'amountkoreksi' => '0', 'noreferensi' => 'ALK_'.$param['kodeorg'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
    $noUrut = 1;
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $penerima['akunkredit'], 'keterangan' => 'Alokasi Biaya Tidak Langsung RO/HO', 'jumlah' => -1 * $jumlah[$val], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $val, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_'.$param['kodeorg'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
    ++$noUrut;
    if (0 < $jatahtm[$val]) {
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $penerima['akundebet']['tm'], 'keterangan' => 'Alokasi Biaya Tidak Langsung RO/HO', 'jumlah' => $jatahtm[$val], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $val, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_'.$param['kodeorg'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
    }

    if (0 < count($blok[$val])) {
        foreach ($blok[$val] as $kunci => $kodeblok) {
            $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tanggal, 'nourut' => $noUrut, 'noakun' => $penerima['akundebet']['tbm'], 'keterangan' => 'Alokasi Biaya Tidak Langsung RO/HO', 'jumlah' => $jatahtbm[$val] / count($blok[$val]), 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $val, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '0', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => 'ALK_'.$param['kodeorg'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $kodeblok, 'revisi' => '0'];
            ++$noUrut;
        }
    }

    $konter = addZero($konter + 1, 3);
}
$errorDB = '';
foreach ($data['header'] as $key => $dataDet) {
    $queryH = insertQuery($dbname, 'keu_jurnalht', $dataDet);
    if (!mysql_query($queryH)) {
        $errorDB .= 'Header :'.mysql_error()."\n";
    }
}
if ('' === $errorDB) {
    foreach ($dataRes['detail'] as $key => $dataDet) {
        $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
        if (!mysql_query($queryD)) {
            $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n";
        }
    }
    $queryKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter + 1], "kodeorg='".$param['pt']."' and kodekelompok='".$kodejurnal."'");
    if (!mysql_query($queryKonter)) {
        $errorDB .= 'Update Counter Error :'.mysql_error()."\n".$errorDB.'___'.$queryKonter;
    }
}

if ('' !== $errorDB) {
    foreach ($arrNoJurnal as $key => $nojur) {
        $queryRB = 'delete from `'.$dbname."`.`keu_jurnalht` where nojurnal='".$nojur."'";
        if (!mysql_query($queryRB)) {
            $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n";
        }
    }
    echo 'Error '.$errorDB;
}

?>
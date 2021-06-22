<?php



session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$mode = $_POST['mode'];
$num = $_POST['num'];
$idK = $_POST['karyawanid'];
$where = 'karyawanid='.$idK;
$query = selectQuery($dbname, 'datakaryawan', '*', $where);
$data = fetchData($query);
if ('add' == $mode) {
    foreach ($data[0] as $key => $row) {
        $data[0][$key] = '';
    }
}

$hfrm = ['Utama', 'Perkawinan', 'Pendidikan', 'Alamat', 'Pangalaman Kerja', 'Riwayat', 'Penghargaan', 'Inventaris', 'Kondite'];
$frm = ['Utama', makeElement('perkawinan', 'button', 'Refresh', ['onclick' => "refreshTab('perkawinan','".$mode."')"])."<div id='tabPerkawinan'></div>", makeElement('pendidikan', 'button', 'Refresh', ['onclick' => "refreshTab('pendidikan','".$mode."')"])."<div id='tabPendidikan'></div>", makeElement('alamat', 'button', 'Refresh', ['onclick' => "refreshTab('alamat','".$mode."')"])."<div id='tabAlamat'></div>", makeElement('pengalamankerja', 'button', 'Refresh', ['onclick' => "refreshTab('pengalamankerja','".$mode."')"])."<div id='tabPengalamanKerja'></div>", makeElement('riwayat', 'button', 'Refresh', ['onclick' => "refreshTab('riwayat','".$mode."')"])."<div id='tabRiwayat'></div>", makeElement('penghargaan', 'button', 'Refresh', ['onclick' => "refreshTab('penghargaan','".$mode."')"])."<div id='tabPenghargaan'></div>", makeElement('inventaris', 'button', 'Refresh', ['onclick' => "refreshTab('inventaris','".$mode."')"])."<div id='tabInventaris'></div>", makeElement('kondite', 'button', 'Refresh', ['onclick' => "refreshTab('kondite','".$mode."')"])."<div id='tabKondite'></div>"];
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optPend = makeOption($dbname, 'sdm_5pendidikan', 'levelpendidikan,pendidikan');
$optJab = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optGol = makeOption($dbname, 'sdm_5golongan', 'golongan,keterangan');
$optGender = ['P' => $_SESSION['lang']['pria'], 'W' => $_SESSION['lang']['wanita']];
$optStatMarr = ['Bujang' => $_SESSION['lang']['bujang'], 'Menikah' => $_SESSION['lang']['menikah'], 'Janda' => $_SESSION['lang']['janda'], 'Duda' => $_SESSION['lang']['duda']];
$optAgama = ['Islam' => $_SESSION['lang']['islam'], 'Protestan' => $_SESSION['lang']['protestan'], 'Katolik' => $_SESSION['lang']['katolik'], 'Hindu' => $_SESSION['lang']['hindu'], 'Budha' => $_SESSION['lang']['budha'], 'Konghucu' => $_SESSION['lang']['konghucu'], 'Lainnya' => $_SESSION['lang']['lain']];
$optBlood = ['A+' => 'A+', 'A-' => 'A-', 'B+' => 'B+', 'B-' => 'B-', 'O+' => 'O+', 'O-' => 'O-', 'AB+' => 'AB+', 'AB-' => 'AB-'];
$els = [];
$els[] = [makeElement('nik', 'label', $_SESSION['lang']['nik']), makeElement('nik', 'text', $data[0]['nik'], ['style' => 'width:100px', 'maxlength' => '10'])];
$els[] = [makeElement('namakaryawan', 'label', $_SESSION['lang']['namakaryawan']), makeElement('namakaryawan', 'text', $data[0]['namakaryawan'], ['style' => 'width:250px', 'maxlength' => '40', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('lahir', 'label', $_SESSION['lang']['lahir']), makeElement('tempatlahir', 'text', $data[0]['tempatlahir'], ['style' => 'width:250px', 'maxlength' => '30', 'onkeypress' => 'return tanpa_kutip(event)']).makeElement('tanggallahir', 'text', $data[0]['tanggallahir'], ['style' => 'width:250px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];
$els[] = [makeElement('warganegara', 'label', $_SESSION['lang']['warganegara']), makeElement('warganegara', 'text', $data[0]['warganegara'], ['style' => 'width:250px', 'maxlength' => '45', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('jeniskelamin', 'label', $_SESSION['lang']['jeniskelamin']), makeElement('jeniskelamin', 'select', $data[0]['jeniskelamin'], ['style' => 'width:250px'], $optGender)];
$els[] = [makeElement('statusperkawinan', 'label', $_SESSION['lang']['statusperkawinan']), makeElement('statusperkawinan', 'select', $data[0]['statusperkawinan'], ['style' => 'width:250px'], $optStatMarr)];
$els[] = [makeElement('tanggalmenikah', 'label', $_SESSION['lang']['tanggalmenikah']), makeElement('tanggalmenikah', 'text', $data[0]['tanggalmenikah'], ['style' => 'width:250px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];
$els[] = [makeElement('agama', 'label', $_SESSION['lang']['agama']), makeElement('agama', 'text', $data[0]['agama'], ['style' => 'width:250px'], $optAgama)];
$els[] = [makeElement('golongandarah', 'label', $_SESSION['lang']['golongandarah']), makeElement('golongandarah', 'select', $data[0]['golongandarah'], ['style' => 'width:70px'], $optBlood)];
$els[] = [makeElement('levelpendidikan', 'label', $_SESSION['lang']['levelpendidikan']), makeElement('levelpendidikan', 'select', $data[0]['levelpendidikan'], ['style' => 'width:250px'], $optPend)];
$els[] = [makeElement('alamataktif', 'label', $_SESSION['lang']['alamataktif']), makeElement('alamataktif', 'text', $data[0]['alamataktif'], ['style' => 'width:250px', 'maxlength' => '100', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('provinsi', 'label', $_SESSION['lang']['provinsi']), makeElement('provinsi', 'text', $data[0]['provinsi'], ['style' => 'width:250px', 'maxlength' => '45', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('kota', 'label', $_SESSION['lang']['kota']), makeElement('kota', 'text', $data[0]['kota'], ['style' => 'width:250px', 'maxlength' => '45', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('kodepos', 'label', $_SESSION['lang']['kodepos']), makeElement('kodepos', 'text', $data[0]['kodepos'], ['style' => 'width:250px', 'maxlength' => '5', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('noteleponrumah', 'label', $_SESSION['lang']['noteleponrumah']), makeElement('noteleponrumah', 'text', $data[0]['noteleponrumah'], ['style' => 'width:250px', 'maxlength' => '15', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('nohp', 'label', $_SESSION['lang']['nohp']), makeElement('nohp', 'text', $data[0]['nohp'], ['style' => 'width:250px', 'maxlength' => '15', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('norekeningbank', 'label', $_SESSION['lang']['norekeningbank']), makeElement('norekeningbank', 'text', $data[0]['norekeningbank'], ['style' => 'width:250px', 'maxlength' => '30', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('namabank', 'label', $_SESSION['lang']['namabank']), makeElement('namabank', 'text', $data[0]['namabank'], ['style' => 'width:250px', 'maxlength' => '45', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('sistemgaji', 'label', $_SESSION['lang']['sistemgaji']), makeElement('sistemgaji', 'text', $data[0]['sistemgaji'], ['style' => 'width:250px', 'maxlength' => '3', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('nopaspor', 'label', $_SESSION['lang']['nopaspor']), makeElement('nopaspor', 'text', $data[0]['nopaspor'], ['style' => 'width:250px', 'maxlength' => '30', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('noktp', 'label', $_SESSION['lang']['noktp']), makeElement('noktp', 'text', $data[0]['noktp'], ['style' => 'width:250px', 'maxlength' => '30', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('notelepondarurat', 'label', $_SESSION['lang']['notelepondarurat']), makeElement('notelepondarurat', 'text', $data[0]['notelepondarurat'], ['style' => 'width:250px', 'maxlength' => '15', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('tanggalmasuk', 'label', $_SESSION['lang']['tanggalmasuk']), makeElement('tanggalmasuk', 'text', $data[0]['tanggalmasuk'], ['style' => 'width:250px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];
$els[] = [makeElement('tanggalkeluar', 'label', $_SESSION['lang']['tanggalkeluar']), makeElement('tanggalkeluar', 'text', $data[0]['tanggalkeluar'], ['style' => 'width:250px', 'maxlength' => 'maxlength', 'onmousemove' => 'setCalendar(this.id)'])];
$els[] = [makeElement('tipekaryawan', 'label', $_SESSION['lang']['tipekaryawan']), makeElement('tipekaryawan', 'text', $data[0]['tipekaryawan'], ['style' => 'width:250px', 'maxlength' => '3', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('jumlahanak', 'label', $_SESSION['lang']['jumlahanak']), makeElement('jumlahanak', 'text', $data[0]['jumlahanak'], ['style' => 'width:250px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('jumlahtanggungan', 'label', $_SESSION['lang']['jumlahtanggungan']), makeElement('jumlahtanggungan', 'text', $data[0]['jumlahtanggungan'], ['style' => 'width:250px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('statuspajak', 'label', $_SESSION['lang']['statuspajak']), makeElement('statuspajak', 'text', $data[0]['statuspajak'], ['style' => 'width:250px', 'maxlength' => '4', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('npwp', 'label', $_SESSION['lang']['npwp']), makeElement('npwp', 'text', $data[0]['npwp'], ['style' => 'width:250px', 'maxlength' => '25', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('lokasipenerimaan', 'label', $_SESSION['lang']['lokasipenerimaan']), makeElement('lokasipenerimaan', 'text', $data[0]['lokasipenerimaan'], ['style' => 'width:250px', 'maxlength' => '30', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('kodeorganisasi', 'label', $_SESSION['lang']['kodeorganisasi']), makeElement('kodeorganisasi', 'select', $data[0]['kodeorganisasi'], ['style' => 'width:250px'], $optOrg)];
$els[] = [makeElement('bagian', 'label', $_SESSION['lang']['bagian']), makeElement('bagian', 'text', $data[0]['bagian'], ['style' => 'width:250px', 'maxlength' => '8', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('kodejabatan', 'label', $_SESSION['lang']['kodejabatan']), makeElement('kodejabatan', 'select', $data[0]['kodejabatan'], ['style' => 'width:250px'], $optJab)];
$els[] = [makeElement('kodegolongan', 'label', $_SESSION['lang']['kodegolongan']), makeElement('kodegolongan', 'select', $data[0]['kodegolongan'], ['style' => 'width:250px'], $optGol)];
$els[] = [makeElement('lokasitugas', 'label', $_SESSION['lang']['lokasitugas']), makeElement('lokasitugas', 'text', $data[0]['lokasitugas'], ['style' => 'width:250px', 'maxlength' => '8', 'onkeypress' => 'return tanpa_kutip(event)'])];
$frm[0] = "<div style='width:783px;height:345px;overflow:auto'>".genElementMultiDim('Header Data Karyawan', $els, 2).'</div>';
if ('edit' == $mode) {
    echo makeElement('karyawanid', 'hidden', $idK);
} else {
    echo makeElement('karyawanid', 'hidden', '');
}

drawTab('tabKary', $hfrm, $frm, 80, 775);

?>
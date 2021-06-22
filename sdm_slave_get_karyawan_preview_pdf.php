<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
require_once 'lib/zLib.php';
$karyawanid = $_GET['karyawanid'];
$str = 'select a.namaorganisasi from '.$dbname.'.datakaryawan b left join '.$dbname.".organisasi a \r\n          on b.kodeorganisasi=a.kodeorganisasi where b.karyawanid=".$karyawanid;
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = $bar->namaorganisasi;
}

class PDF extends FPDF
{
    public function Header()
    {
        global $namapt;
		/*
        if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }
		*/
		$path = '';
        //$this->Image($path, 15, 2, 40);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetY(22);
        $this->Cell(60, 5, $namapt, 0, 1, 'C');
        $this->SetFont('Arial', '', 15);
        $this->Cell(190, 5, strtoupper($_SESSION['lang']['inputdatakaryawan']), 0, 1, 'C');
        $this->SetFont('Arial', '', 6);
        $this->SetY(30);
        $this->SetX(163);
        $this->Cell(30, 10, 'PRINT TIME : '.date('d-m-Y H:i:s'), 0, 1, 'L');
        $this->Line(10, 32, 200, 32);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$str = "select *,\r\n      case jeniskelamin when 'L' then 'Laki-Laki'\r\n          else  'Wanita'\r\n          end as jk\r\n          from ".$dbname.'.datakaryawan where karyawanid='.$karyawanid.' limit 1';
$res = mysql_query($str);
$defaulsrc = 'images/user.jpg';
while ($bar = mysql_fetch_object($res)) {
    $pendidikan = '';
    $str1 = 'select kelompok from '.$dbname.'.sdm_5pendidikan where levelpendidikan='.$bar->levelpendidikan;
    $res1 = mysql_query($str1);
    while ($bar1 = mysql_fetch_object($res1)) {
        $pendidikan = $bar1->kelompok;
    }
    $tipekaryawan = '';
    $str2 = 'select * from '.$dbname.'.sdm_5tipekaryawan where id='.$bar->tipekaryawan;
    $res2 = mysql_query($str2);
    while ($bar2 = mysql_fetch_object($res2)) {
        $tipekaryawan = $bar2->tipe;
    }
    $jabatan = '';
    $str3 = 'select * from '.$dbname.'.sdm_5jabatan where kodejabatan='.$bar->kodejabatan." and namajabatan not like '%available' order by kodejabatan";
    $res3 = mysql_query($str3);
    while ($bar3 = mysql_fetch_object($res3)) {
        $jabatan = $bar3->namajabatan;
    }
    $jabatanku = $bar->kodejabatan;
    $photo = ('' == $bar->photo ? $defaulsrc : $bar->photo);
    $karyawanid = $bar->karyawanid;
    $nik = $bar->nik;
    $nama = $bar->namakaryawan;
    $ttlahir = $bar->tempatlahir;
    $tgllahir = tanggalnormal($bar->tanggallahir);
    $wn = $bar->warganegara;
    $jk = $bar->jk;
    $stpkw = $bar->statusperkawinan;
    $tglmenikah = tanggalnormal($bar->tanggalmenikah);
    $agama = $bar->agama;
    $goldar = $bar->golongandarah;
    $pendidikan = $pendidikan;
    $telprumah = $bar->noteleponrumah;
    $hp = $bar->nohp;
    $passpor = $bar->nopaspor;
    $ktp = $bar->noktp;
    $tdarurat = $bar->notelepondarurat;
    $tmasuk = tanggalnormal($bar->tanggalmasuk);
    $tkeluar = tanggalnormal($bar->tanggalkeluar);
    $tipekar = $tipekaryawan;
    $alamataktif = $bar->alamataktif;
    $kota = $bar->kota;
    $provinsi = $bar->provinsi;
    $kodepos = $bar->kodepos;
    $rekbank = $bar->norekeningbank;
    $bank = $bar->namabank;
    $sisgaji = $bar->sistemgaji;
    $jlhanak = $bar->jumlahanak;
    $tanggungan = $bar->jumlahtanggungan;
    $stpjk = $bar->statuspajak;
    $npwp = $bar->npwp;
    $lokpenerimaan = $bar->lokasipenerimaan;
    $kodeorg = $bar->kodeorganisasi;
    $bagian = $bar->bagian;
    $jabatan = $jabatan;
    $golongan = $bar->kodegolongan;
    $lokasitugas = $bar->lokasitugas;
    $email = $bar->email;
    $subbagian = $bar->subbagian;
    $jms = $bar->jms;
    $kecamatan = $bar->kecamatan;
    $desa = $bar->desa;
    $pangkat = $bar->pangkat;
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->setY(32);
    $pdf->Cell(25, 5, '1. '.strtoupper($_SESSION['lang']['datapribadi']), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 8);
    $pdf->setY(40);
    $pdf->SetX(20);
    $pdf->Image($photo, 15, 40, 35);
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['datapribadi'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$karyawanid, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['nik'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$nik, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['nama'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$nama, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['tempatlahir'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$ttlahir, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['tanggallahir'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$tgllahir, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['warganegara'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$wn, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['jeniskelamin'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$jk, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['status'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$stpkw, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['tanggalmenikah'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$tglmenikah, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['agama'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$agama, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['golongandarah'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$goldar, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['pendidikan'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$pendidikan, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['telp'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$telprumah, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['nohp'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$hp, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['noktp'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$ktp, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['passport'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$passpor, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['notelepondarurat'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$tdarurat, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['tanggalmasuk'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$tmasuk, 0, 1, 'L');
    $pdf->SetX(60);
    $pdf->Cell(25, 5, $_SESSION['lang']['tanggalkeluar'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$tkeluar, 0, 0, 'L');
    $pdf->Cell(25, 5, $_SESSION['lang']['tipekaryawan'], 0, 0, 'L');
    $pdf->Cell(40, 5, ': '.$tipekar, 0, 1, 'L');

    $queryGetAlasanKeluar = "SELECT * FROM sdm_exitinterview WHERE karyawanid = '".$karyawanid."'";
    $dataAlasanKeluar = fetchData($queryGetAlasanKeluar);

    if (!empty($dataAlasanKeluar[0]['alasan'])) {
        $pdf->Cell(32, 5, "Alasan Keluar", 0, 0, 'L');
        $pdf->MultiCell(153, 5, ': '.$dataAlasanKeluar[0]['alasan'], 0, 'L');
    }
    

    $pdf->Cell(32, 5, $_SESSION['lang']['alamat'], 0, 0, 'L');
    $pdf->MultiCell(153, 5, ': '.$alamataktif.', '.$kecamatan.', '.$desa.', '.$kota.', '.$provinsi.', '.$kodepos, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['norekeningbank'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$rekbank, 0, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['namabank'], 0, 0, 'L');
    $pdf->Cell(70, 5, ': '.$bank, 0, 1, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['sistemgaji'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$sisgaji, 0, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['jumlahanak'], 0, 0, 'L');
    $pdf->Cell(70, 5, ': '.$jlhanak, 0, 1, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['jumlahtanggungan'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$tanggungan, 0, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['statuspajak'], 0, 0, 'L');
    $pdf->Cell(70, 5, ': '.$stpjk, 0, 1, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['npwp'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$npwp, 0, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['lokasipenerimaan'], 0, 0, 'L');
    $pdf->Cell(70, 5, ': '.$lokpenerimaan, 0, 1, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['orgcode'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$kodeorg, 0, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['bagian'], 0, 0, 'L');
    $pdf->Cell(70, 5, ': '.$bagian, 0, 1, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['functionname'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$jabatan, 0, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['levelname'], 0, 0, 'L');
    $pdf->Cell(70, 5, ': '.$golongan, 0, 1, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['pangkat'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$pangkat, 0, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['lokasitugas'], 0, 0, 'L');
    $pdf->Cell(70, 5, ': '.$lokasitugas, 0, 1, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['email'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$email, 0, 0, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['subbagian'], 0, 0, 'L');
    $pdf->Cell(70, 5, ': '.$subbagian, 0, 1, 'L');
    $pdf->Cell(32, 5, $_SESSION['lang']['jms'], 0, 0, 'L');
    $pdf->Cell(60, 5, ': '.$jms, 0, 1, 'L');
    $pdf->Ln();
}
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 5, '2. '.strtoupper($_SESSION['lang']['pengalamankerja']), 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(6, 4, 'No', 1, 0, 'L', 1);
$pdf->Cell(30, 4, $_SESSION['lang']['pt'], 1, 0, 'C', 1);
$pdf->Cell(30, 4, $_SESSION['lang']['bidangusaha'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['tanggalmasuk'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['tanggalkeluar'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['jabatanterakhir'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['bagian'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['masakerja'], 1, 0, 'C', 1);
$pdf->Cell(35, 4, $_SESSION['lang']['alamat'], 1, 1, 'C', 1);
$str = 'select *,right(bulanmasuk,4) as masup,left(bulanmasuk,2) as busup from '.$dbname.'.sdm_karyawancv where karyawanid='.$karyawanid.' order by masup,busup';
$res = mysql_query($str);
$no = 0;
$mskerja = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $msk = mktime(0, 0, 0, substr(str_replace('-', '', $bar->bulanmasuk), 0, 2), 1, substr($bar->bulanmasuk, 3, 4));
    $klr = mktime(0, 0, 0, substr(str_replace('-', '', $bar->bulankeluar), 0, 2), 1, substr($bar->bulankeluar, 3, 4));
    $dateDiff = $klr - $msk;
    $mskerja = floor($dateDiff / (60 * 60 * 24)) / 365;
    $pdf->Cell(6, 4, $no, 1, 0, 'L', 0);
    $pdf->Cell(30, 4, $bar->namaperusahaan, 1, 0, 'L', 0);
    $pdf->Cell(30, 4, $bar->bidangusaha, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->bulanmasuk, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->bulankeluar, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, $bar->jabatan, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, $bar->bagian, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, number_format($mskerja, 2, ',', '.').' Yrs', 1, 0, 'R', 0);
    $pdf->Cell(35, 4, $bar->alamatperusahaan, 1, 1, 'L', 0);
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 5, '3. '.strtoupper($_SESSION['lang']['pendidikan']), 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(6, 4, 'No', 1, 0, 'L', 1);
$pdf->Cell(12, 4, $_SESSION['lang']['edulevel'], 1, 0, 'C', 1);
$pdf->Cell(33, 4, $_SESSION['lang']['namasekolah'], 1, 0, 'C', 1);
$pdf->Cell(25, 4, $_SESSION['lang']['kota'], 1, 0, 'C', 1);
$pdf->Cell(30, 4, $_SESSION['lang']['jurusan'], 1, 0, 'C', 1);
$pdf->Cell(10, 4, $_SESSION['lang']['tahunlulus'], 1, 0, 'C', 1);
$pdf->Cell(25, 4, $_SESSION['lang']['gelar'], 1, 0, 'C', 1);
$pdf->Cell(10, 4, $_SESSION['lang']['nilai'], 1, 0, 'C', 1);
$pdf->Cell(35, 4, $_SESSION['lang']['keterangan'], 1, 1, 'C', 1);
$str = 'select a.*,b.kelompok from '.$dbname.'.sdm_karyawanpendidikan a,'.$dbname.".sdm_5pendidikan b\r\n                        where a.karyawanid=".$karyawanid." \r\n                        and a.levelpendidikan=b.levelpendidikan\r\n                        order by a.levelpendidikan desc";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->Cell(6, 4, $no, 1, 0, 'L', 0);
    $pdf->Cell(12, 4, $bar->kelompok, 1, 0, 'L', 0);
    $pdf->Cell(33, 4, $bar->namasekolah, 1, 0, 'L', 0);
    $pdf->Cell(25, 4, $bar->kota, 1, 0, 'L', 0);
    $pdf->Cell(30, 4, $bar->spesialisasi, 1, 0, 'L', 0);
    $pdf->Cell(10, 4, $bar->tahunlulus, 1, 0, 'L', 0);
    $pdf->Cell(25, 4, $bar->gelar, 1, 0, 'L', 0);
    $pdf->Cell(10, 4, number_format($bar->nilai, 2, ',', '.'), 1, 0, 'R', 0);
    $pdf->Cell(35, 4, $bar->keterangan, 1, 1, 'L', 0);
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 5, '4. EXTERNAL '.strtoupper($_SESSION['lang']['kursus']), 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(6, 4, 'No', 1, 0, 'L', 1);
$pdf->Cell(30, 4, $_SESSION['lang']['jeniskursus'], 1, 0, 'C', 1);
$pdf->Cell(50, 4, $_SESSION['lang']['legend'], 1, 0, 'C', 1);
$pdf->Cell(50, 4, $_SESSION['lang']['penyelenggara'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['start'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['tanggalsampai'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['sertifikat'], 1, 1, 'C', 1);
$str = "select *,case sertifikat when 0 then 'N' else 'Y' end as bersertifikat \r\n               from ".$dbname.".sdm_karyawantraining\r\n                        where karyawanid=".$karyawanid." \r\n                        order by bulanmulai desc";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->Cell(6, 4, $no, 1, 0, 'L', 0);
    $pdf->Cell(30, 4, $bar->jenistraining, 1, 0, 'L', 0);
    $pdf->Cell(50, 4, $bar->judultraining, 1, 0, 'L', 0);
    $pdf->Cell(50, 4, $bar->penyelenggara, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->bulanmulai, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->bulanselesai, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, $bar->bersertifikat, 1, 1, 'L', 0);
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 5, '5. '.strtoupper($_SESSION['lang']['keluarga']), 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(6, 4, 'No', 1, 0, 'L', 1);
$pdf->Cell(40, 4, $_SESSION['lang']['keluarga'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['jeniskelamin'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['hubungan'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['status'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['pendidikan'], 1, 0, 'C', 1);
$pdf->Cell(30, 4, $_SESSION['lang']['pekerjaan'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['umur'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['tanggungan'], 1, 1, 'C', 1);
$str = "select a.*,case a.tanggungan when 0 then 'N' else 'Y' end as tanggungan1, \r\n                       b.kelompok,COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',a.tanggallahir)/365.25,1),0) as umur\r\n                           from ".$dbname.'.sdm_karyawankeluarga a,'.$dbname.".sdm_5pendidikan b\r\n                                where a.karyawanid=".$karyawanid." \r\n                                and a.levelpendidikan=b.levelpendidikan\r\n                                order by hubungankeluarga";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    if ('EN' == $_SESSION['language']) {
        switch ($bar->hubungankeluarga) {
            case 'Pasangan':
                $val = 'Couple';

                break;
            case 'Anak':
                $val = 'Child';

                break;
            case 'Ibu':
                $val = 'Mother';

                break;
            case 'Bapak':
                $val = 'Father';

                break;
            case 'Adik':
                $val = 'Younger brother/sister';

                break;
            case 'Kakak':
                $val = 'Older brother/sister';

                break;
            case 'Ibu Mertua':
                $val = 'Monther-in-law';

                break;
            case 'Bapak Mertua':
                $val = 'Father-in-law';

                break;
            case 'Sepupu':
                $val = 'Cousin';

                break;
            case 'Ponakan':
                $val = 'Nephew';

                break;
            default:
                $val = 'Foster child';

                break;
        }
    }

    if ('EN' == $_SESSION['language'] && 'Kawin' == $bar->status) {
        $gal = 'Married';
    }

    if ('EN' == $_SESSION['language'] && ('Bujang' == $bar->status || 'Lajang' == $bar->status)) {
        $gal = 'Single';
    }

    ++$no;
    $pdf->Cell(6, 4, $no, 1, 0, 'L', 0);
    $pdf->Cell(40, 4, $bar->nama, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->jeniskelamin, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, $val, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $gal, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, $bar->kelompok, 1, 0, 'L', 0);
    $pdf->Cell(30, 4, $bar->pekerjaan, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, $bar->umur.' Yrs', 1, 0, 'L', 0);
    $pdf->Cell(20, 4, $bar->tanggungan1, 1, 1, 'L', 0);
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 5, '6. '.strtoupper($_SESSION['lang']['alamat']), 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(6, 4, 'No', 1, 0, 'L', 1);
$pdf->Cell(80, 4, $_SESSION['lang']['alamat'], 1, 0, 'C', 1);
$pdf->Cell(25, 4, $_SESSION['lang']['kota'], 1, 0, 'C', 1);
$pdf->Cell(25, 4, $_SESSION['lang']['province'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['kodepos'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['emplasmen'], 1, 0, 'C', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['aktif'], 1, 1, 'C', 1);
$str = "select *,case aktif when 1 then 'Yes' when 0 then 'No' end as status from ".$dbname.'.sdm_karyawanalamat where karyawanid='.$karyawanid.' order by nomor desc';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->Cell(6, 4, $no, 1, 0, 'L', 0);
    $pdf->Cell(80, 4, $bar->alamat, 1, 0, 'L', 0);
    $pdf->Cell(25, 4, $bar->kota, 1, 0, 'L', 0);
    $pdf->Cell(25, 4, $bar->provinsi, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->kodepos, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, $bar->emplasemen, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->status, 1, 1, 'L', 0);
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
if ('EN' == $_SESSION['language']) {
    $pdf->Cell(25, 5, '7. '.strtoupper('History of reprimands'), 0, 1, 'L');
} else {
    $pdf->Cell(25, 5, '7. '.strtoupper('Riwayat Teguran dan SP'), 0, 1, 'L');
}

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(25, 4, 'No', 1, 0, 'L', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['jenis'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['tanggalsurat'], 1, 0, 'C', 1);
$pdf->Cell(10, 4, $_SESSION['lang']['bulan'], 1, 0, 'C', 1);
$pdf->Cell(70, 4, $_SESSION['lang']['pelanggaran'], 1, 0, 'C', 1);
$pdf->Cell(30, 4, $_SESSION['lang']['penandatangan'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['functionname'], 1, 1, 'C', 1);
$str = 'select * from '.$dbname.'.sdm_suratperingatan where karyawanid='.$karyawanid.' order by tanggal desc';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->Cell(25, 4, $bar->nomor, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->jenissp, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, tanggalnormal($bar->tanggal), 1, 0, 'L', 0);
    $pdf->Cell(10, 4, $bar->masaberlaku, 1, 0, 'R', 0);
    $pdf->Cell(70, 4, substr($bar->pelanggaran, 0, 50), 1, 0, 'L', 0);
    $pdf->Cell(30, 4, substr($bar->penandatangan, 0, 20), 1, 0, 'L', 0);
    $pdf->Cell(20, 4, substr($bar->jabatan, 0, 15), 1, 1, 'L', 0);
}
$str = 'select * from '.$dbname.'.sdm_5jabatan';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    $kamusjabatan[$bar->kodejabatan] = $bar->namajabatan;
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
if ('EN' == $_SESSION['language']) {
    $pdf->Cell(25, 5, '8. '.strtoupper('Promotion history'), 0, 1, 'L');
} else {
    $pdf->Cell(25, 5, '8. '.strtoupper('Riwayat Mutasi/Promosi/Demosi'), 0, 1, 'L');
}

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(25, 4, 'No', 1, 0, 'L', 1);
$pdf->Cell(15, 4, $_SESSION['lang']['tipetransaksi'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['tanggalsurat'], 1, 0, 'C', 1);
$pdf->Cell(20, 4, $_SESSION['lang']['tanggalberlaku'], 1, 0, 'C', 1);
$pdf->Cell(40, 4, $_SESSION['lang']['dari'], 1, 0, 'C', 1);
$pdf->Cell(40, 4, $_SESSION['lang']['ke'], 1, 0, 'C', 1);
$pdf->Cell(30, 4, $_SESSION['lang']['penandatangan'], 1, 1, 'C', 1);
$str = 'select * from '.$dbname.'.sdm_riwayatjabatan where karyawanid='.$karyawanid.' order by tanggalsk desc';
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->Cell(25, 4, $bar->nomorsk, 1, 0, 'L', 0);
    $pdf->Cell(15, 4, $bar->tipesk, 1, 0, 'L', 0);
    $pdf->Cell(20, 4, tanggalnormal($bar->tanggalsk), 1, 0, 'L', 0);
    $pdf->Cell(20, 4, tanggalnormal($bar->mulaiberlaku), 1, 0, 'L', 0);
    if ('Mutasi' == $bar->tipesk) {
        $pdf->Cell(40, 4, substr($bar->darikodeorg, 0, 40), 1, 0, 'L', 0);
        $pdf->Cell(40, 4, substr($bar->kekodeorg, 0, 40), 1, 0, 'L', 0);
    } else {
        if ('Promosi' == $bar->tipesk) {
            $pdf->Cell(40, 4, substr($kamusjabatan[$bar->darikodejabatan].' ('.$bar->darikodegolongan, 0, 40).')', 1, 0, 'L', 0);
            $pdf->Cell(40, 4, substr($kamusjabatan[$bar->kekodejabatan].' ('.$bar->kekodegolongan, 0, 40).')', 1, 0, 'L', 0);
        } else {
            if ('Demosi' == $bar->tipesk) {
                $pdf->Cell(40, 4, substr($kamusjabatan[$bar->darikodejabatan].' ('.$bar->darikodegolongan, 0, 40).')', 1, 0, 'L', 0);
                $pdf->Cell(40, 4, substr($kamusjabatan[$bar->kekodejabatan].' ('.$bar->kekodegolongan, 0, 40).')', 1, 0, 'L', 0);
            }
        }
    }

    $pdf->Cell(30, 4, substr($bar->namadireksi, 0, 15), 1, 1, 'L', 0);
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
if ('EN' == $_SESSION['language']) {
    $pdf->Cell(25, 5, '9. '.strtoupper('Training provided By '.$namapt), 0, 1, 'L');
} else {
    $pdf->Cell(25, 5, '9. '.strtoupper('Training di '.$namapt), 0, 1, 'L');
}

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(40, 4, $_SESSION['lang']['kategori'], 1, 0, 'C');
$pdf->Cell(40, 4, $_SESSION['lang']['topik'], 1, 0, 'C');
$pdf->Cell(30, 4, $_SESSION['lang']['tanggalmulai'], 1, 0, 'C');
$pdf->Cell(30, 4, $_SESSION['lang']['tanggalsampai'], 1, 0, 'C');
$pdf->Cell(50, 4, $_SESSION['lang']['catatan'], 1, 1, 'C');
$str = 'select * from '.$dbname.".sdm_matriktraining\r\n        where karyawanid = '".$karyawanid."'\r\n        ";
$sJabat = 'select * from '.$dbname.'.sdm_5matriktraining where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusKategori[$rJabat['matrixid']] = $rJabat['kategori'];
    $kamusTopik[$rJabat['matrixid']] = $rJabat['topik'];
}
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $pdf->Cell(40, 4, $kamusKategori[$bar->matrikxid], 0, 0, 'L');
    $pdf->Cell(40, 4, $kamusTopik[$bar->matrikxid], 0, 0, 'L');
    $pdf->Cell(30, 4, tanggalnormal($bar->tanggaltraining), 0, 0, 'L');
    $pdf->Cell(30, 4, tanggalnormal($bar->sampaitanggal), 0, 0, 'L');
    $pdf->MultiCell(50, 4, $bar->catatan, 0, 'L', false);
    $pdf->Ln();
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
if ('EN' == $_SESSION['language']) {
    $pdf->Cell(25, 5, '10. '.strtoupper('Standard Training'), 0, 1, 'L');
} else {
    $pdf->Cell(25, 5, '10. '.strtoupper('Standard training yang harus diikuti'), 0, 1, 'L');
}

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(40, 4, $_SESSION['lang']['jabatan'], 1, 0, 'C');
$pdf->Cell(40, 4, $_SESSION['lang']['kategori'], 1, 0, 'C');
$pdf->Cell(40, 4, $_SESSION['lang']['topik'], 1, 0, 'C');
$pdf->Ln();
$str = 'select * from '.$dbname.".sdm_5matriktraining\r\n        where kodejabatan = '".$jabatanku."'\r\n        ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $pdf->Cell(40, 4, $kamusjabatan[$bar->kodejabatan], 0, 0, 'L');
    $pdf->Cell(40, 4, $bar->kategori, 0, 0, 'L');
    $pdf->Cell(40, 4, $bar->topik, 0, 0, 'L');
    $pdf->Ln();
}
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
if ('EN' == $_SESSION['language']) {
    $pdf->Cell(25, 5, '11. '.strtoupper('Additional Training'), 0, 1, 'L');
} else {
    $pdf->Cell(25, 5, '11. '.strtoupper('Additional training yang sudah diikuti'), 0, 1, 'L');
}

$pdf->SetFont('Arial', '', 7);
$pdf->Cell(70, 4, $_SESSION['lang']['namatraining'], 1, 0, 'C');
$pdf->Cell(60, 4, $_SESSION['lang']['penyelenggara'], 1, 0, 'C');
$pdf->Cell(30, 4, $_SESSION['lang']['tanggalmulai'], 1, 0, 'C');
$pdf->Cell(30, 4, $_SESSION['lang']['tanggalsampai'], 1, 0, 'C');
$pdf->Ln();
$str = 'select * from '.$dbname.".sdm_5training\r\n        where karyawanid = '".$karyawanid."' and sthrd = '1'\r\n        ";
$sJabat = 'select * from '.$dbname.'.log_5supplier where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusSup[$rJabat['supplierid']] = $rJabat['namasupplier'];
}
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $pdf->Cell(70, 4, $bar->namatraining, 0, 0, 'L');
    $pdf->Cell(60, 4, $kamusSup[$bar->penyelenggara], 0, 0, 'L');
    $pdf->Cell(30, 4, tanggalnormal($bar->tglmulai), 0, 0, 'L');
    $pdf->Cell(30, 4, tanggalnormal($bar->tglselesai), 0, 0, 'L');
    $pdf->Ln();
}
$pdf->Output();

?>
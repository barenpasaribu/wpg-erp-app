<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' === $_POST['pelaksana'] ? ($pelaksana = $_GET['pelaksana']) : ($pelaksana = $_POST['pelaksana']));
('' === $_POST['notransaksi'] ? ($notransaksi = $_GET['notransaksi']) : ($notransaksi = $_POST['notransaksi']));
$arrNmkary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNmKeg = makeOption($dbname, 'it_standard', 'kodekegiatan,keterangan');
$arrKeputusan = [$_SESSION['lang']['diajukan'], $_SESSION['lang']['disetujui'], $_SESSION['lang']['ditolak']];
$ketTolak = $_POST['ketTolak'];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$arrStatus = ['Menunggu', 'Setuju'];
$tlgjmskrng = date('Y-m-d H:i:s');
$standardJam = $_POST['standardJam'];
switch ($proses) {
    case 'loadData':
        echo '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        if ('' !== $pelaksana) {
            $where = " and pelaksana='".$pelaksana."'";
            $ql2 = 'select count(*) as jmlhrow from '.$dbname.".it_request \r\n                      where  pelaksana!=0 ".$where.' order by `tanggal` desc';
            $sCek = 'select * from '.$dbname.".it_request \r\n                        where pelaksana!=0 ".$where." \r\n                        order by `tanggal` desc";
            $slvhc = 'select * from '.$dbname.".it_request \r\n                        where pelaksana!=0  ".$where." \r\n                        order by `tanggal` desc limit ".$offset.','.$limit.' ';
        } else {
            $ql2 = 'select count(*) as jmlhrow from '.$dbname.".it_request \r\n                      where waktuselesai='0000-00-00 00:00:00'  order by `tanggal` desc";
            $sCek = 'select * from '.$dbname.".it_request \r\n                        where waktuselesai='0000-00-00 00:00:00' \r\n                        order by `tanggal` desc";
            $slvhc = 'select * from '.$dbname.".it_request \r\n                        where waktuselesai='0000-00-00 00:00:00' \r\n                        order by `tanggal` desc limit ".$offset.','.$limit.' ';
        }

        $query2 = mysql_query($ql2) || exit(mysql_error($conns));
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            $qlvhc = mysql_query($slvhc) || exit(mysql_error($conns));
            $user_online = $_SESSION['standard']['userid'];
            while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
                ++$no;
                $optKary = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
                $optJenis = $optKary;
                $sKary = 'select distinct karyawanid,namakaryawan from '.$dbname.".datakaryawan where bagian='HO_ITGS' and alokasi=1 order by namakaryawan asc";
                $qKary = mysql_query($sKary) || exit(mysql_error($sKary));
                while ($rKary = mysql_fetch_assoc($qKary)) {
                    if ('0000000000' !== $rlvhc['pelaksana']) {
                        $optKary .= "<option value='".$rKary['karyawanid']."' ".(($rlvhc['pelaksana'] === $rKary['karyawanid'] ? 'selected' : '')).'>'.$rKary['namakaryawan'].'</option>';
                    } else {
                        $optKary .= "<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan'].'</option>';
                    }
                }
                echo "\r\n\t\t<tr class=rowcontent>\r\n                <td  align=center style='width:40px;'>".$no."</td>\r\n                <td  align=center style='width:80px;'>".tanggalnormal($rlvhc['tanggal'])."</td>\r\n                <td  align=left style='width:180px;'>".$optNmKeg[$rlvhc['kodekegiatan']]."</td>\r\n                <td align=left style='width:125px;'>".$arrNmkary[$rlvhc['karyawanid']]."</td>\r\n                <td align=left style='width:125px;'>".$arrNmkary[$rlvhc['atasan']].'</td>';
                if ('0' !== $rlvhc['statusatasan'] && '1' !== $rlvhc['statusatasan']) {
                    echo "<td align=left style='width:100px;'>".substr($rlvhc['statusatasan'], 0, 15).' ...</td>';
                } else {
                    echo "<td align=left style='width:100px;'>".$arrStatus[$rlvhc['statusatasan']].'</td>';
                }

                echo "<td  align=center style='width:80px;'>".tanggalnormal($rlvhc['tanggalatasan'])."</td>\r\n                <td align=left style='width:200px;'><select id=pelaksana_".$no." onchange=savePelaksana('".$rlvhc['notransaksi']."','".$no."')>".$optKary."</select>&nbsp;<button class=mybutton onclick=tolakForm(event,'it_slave_requestManagement.php','".$rlvhc['notransaksi']."')>".$_SESSION['lang']['tolak'].'</button></td>';
                echo "<td align=left style='width:150px;'><input type=text id=standr_".$no." class=myinputtextnumber size=6 maxlenght=8 onkeypress='return angka_doang(event)' value='".$rlvhc['standarwaktu']."' />\r\n                    &nbsp;<button class=mybutton onclick=saveJm('".$rlvhc['notransaksi']."','".$no."')>".$_SESSION['lang']['save'].'</button></td>';
                echo "<td align=center style='width:40px;'> <img src=images/zoom.png class=resicon  title='Print' onclick=\"detailData(event,'it_slave_requestManagement.php','".$rlvhc['notransaksi']."')\"></td>";
            }
            echo "\r\n\t\t</tr><tr class=rowheader><td colspan=11 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        } else {
            echo '<tr class=rowcontent><td colspan=13>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        break;
    case 'updatePelaksana':
        $sCek = 'select distinct * from '.$dbname.".it_request where notransaksi='".$notransaksi."'";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_fetch_assoc($qCek);
        if ('1' !== $rCek['statusatasan']) {
            exit('Error: Status Atasan Tidak di Setujui');
        }

        $sUpdate = 'update '.$dbname.".it_request set pelaksana='".$pelaksana."',waktupelaksanaan='".$tlgjmskrng."',statusmanagerit='1'\r\n                          where notransaksi='".$notransaksi."'";
        if (mysql_query($sUpdate)) {
            $to = getUserEmail($pelaksana);
            $namakaryawan = getNamaKaryawan($pelaksana);
            $subject = '[Notifikasi]Permintaan layanan '.$rGet['keterangan'].' a/n '.$namakaryawan;
            $body = "<html>\r\n                             <head>\r\n                             <body>\r\n                               <dd>Dengan Hormat,</dd><br>\r\n                               <p align=justify>\r\n                               Karyawan a/n: ".getNamaKaryawan($rCek['karyawanid']).' meminta layanan '.$optNmKeg[$rCek['kodekegiatan']].' pada tanggal '.tanggalnormal($rGet['tanggal'])." ke departemen IT\r\n                               dengan deskripsi ".$rGet['deskripsi'].".\r\n                               <br>\r\n                               <br>\r\n                               mohon dibantu, dan jika sudah selesai jangan lupa update status pelaksanaannya dari menu IT->request response\r\n                               <br>\r\n                               <br>\r\n                               Regards,<br>\r\n                               eAgro Plantation Management Software.\r\n                             </body>\r\n                             </head>\r\n                           </html>\r\n                           ";
            $kirim = kirimEmailWindows($to, $subject, $body);
        }

        break;
    case 'updateTolak':
        $sUpdate = 'update '.$dbname.".it_request set statusmanagerit='".$ketTolak."'\r\n                          where notransaksi='".$notransaksi."'";
        if (!mysql_query($sUpdate)) {
            echo ' Gagal:'.addslashes(mysql_error($conn)).'___'.$sUpdate;
        }

        break;
    case 'getDetail':
        $sData = 'select distinct * from '.$dbname.".it_request where notransaksi='".$notransaksi."' ";
        $qData = mysql_query($sData) || exit(mysql_error($conns));
        $rData = mysql_fetch_assoc($qData);
        $dataTab .= '<div style=overflow:auto;width:420px;height:300px;>';
        $dataTab .= '<fieldset><legend>'.$_SESSION['lang']['desc'].'</legend>';
        $dataTab .= '<div align=justify>'.$rData['deskripsi'].'</p>';
        $dataTab .= '</fieldset><br />';
        $dataTab .= '</div>';
        echo $dataTab;

        break;
    case 'getForm':
        $tab .= '<link href="style/generic.css" type="text/css" rel="stylesheet">';
        $tab .= "<script language=javascript1.2 src='js/it_requestManagement.js'></script>";
        $tab .= "<script language=javascript1.2 src='js/generic.js'></script>";
        $tab .= "<div id='progress' style='display:none;border:orange solid 1px;width:150px;position:absolute;right:20px;top:65px;color:#ff0000;font-family:Tahoma;font-size:13px;font-weight:bolder;text-align:center;background-color:#FFFFFF;z-index:10000;'>\r\nPlease wait.....! <br>\r\n<img src='images/progress.gif'>\r\n</div>";
        $tab .= '<fieldset><legend>'.$_SESSION['lang']['ditolak'].'</legend>';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0>';
        $tab .= '<tr><td><textarea id=ketTolak></textarea></td>';
        $tab .= "<tr><td><button class=mybutton onclick=tolakDt('".$notransaksi."');>".$_SESSION['lang']['tolak'].'</button></td></tr>';
        $tab .= '</table>';
        $tab .= '</fieldset>';
        echo $tab;

        break;
    case 'updateJam':
        $sCek = 'select distinct * from '.$dbname.".it_request where notransaksi='".$notransaksi."'";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rCek = mysql_fetch_assoc($qCek);
        if ('1' !== $rCek['statusatasan']) {
            exit('Error: Status Atasan Tidak di Setujui');
        }

        if ('0000000000' === $rCek['pelaksana']) {
            exit('Error: Pelaksana masih kosong');
        }

        $sUpdate = 'update '.$dbname.".it_request set standarwaktu='".$standardJam."'\r\n                          where notransaksi='".$notransaksi."'";
        if (!mysql_query($sUpdate)) {
            echo ' Gagal:'.addslashes(mysql_error($conn)).'___'.$sUpdate;
        }

        break;
    case 'prevPdf':

class PDF extends FPDF extends FPDF
{
    public function Header()
    {
        if ('SSP' === $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' === $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' === $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' === $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        $this->Image($path, 15, 2, 40);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetY(22);
        $this->Cell(60, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'C');
        $this->SetFont('Arial', '', 15);
        $this->Cell(190, 5, '', 0, 1, 'C');
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

        $str = 'select * from '.$dbname.'.sdm_ijin where '.$where.'';
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $jabatan = '';
            $namakaryawan = '';
            $bagian = '';
            $karyawanid = '';
            $strc = "select a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan \r\n\t\t    from ".$dbname.'.datakaryawan a left join  '.$dbname.".sdm_5jabatan b\r\n\t\t\ton a.kodejabatan=b.kodejabatan\r\n\t\t\twhere a.karyawanid=".$bar->karyawanid;
            $resc = mysql_query($strc);
            while ($barc = mysql_fetch_object($resc)) {
                $jabatan = $barc->namajabatan;
                $namakaryawan = $barc->namakaryawan;
                $bagian = $barc->bagian;
                $karyawanid = $barc->karyawanid;
            }
            $perstatus = $bar->stpersetujuan1;
            $tgl = tanggalnormal($bar->tanggal);
            $kperluan = $bar->keperluan;
            $persetujuan = $bar->persetujuan1;
            $jns = $bar->jenisijin;
            $jmDr = $bar->darijam;
            $jmSmp = $bar->sampaijam;
            $koments = $bar->komenst1;
            $ket = $bar->keterangan;
            $periode = $bar->periodecuti;
            $sthrd = $bar->stpersetujuanhrd;
            $hk = $bar->jumlahhari;
            $hrd = $bar->hrd;
            $koments2 = $bar->komenst2;
            $perjabatan = '';
            $perbagian = '';
            $pernama = '';
            $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n\t       ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n\t\t   where karyawanid=".$persetujuan;
            $resf = mysql_query($strf);
            while ($barf = mysql_fetch_object($resf)) {
                $perjabatan = $barf->namajabatan;
                $perbagian = $barf->bagian;
                $pernama = $barf->namakaryawan;
            }
            $perjabatanhrd = '';
            $perbagianhrd = '';
            $pernamahrd = '';
            $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n\t       ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n\t\t   where karyawanid=".$hrd;
            $resf = mysql_query($strf);
            while ($barf = mysql_fetch_object($resf)) {
                $perjabatanhrd = $barf->namajabatan;
                $perbagianhrd = $barf->bagian;
                $pernamahrd = $barf->namakaryawan;
            }
        }
        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->AddPage();
        $pdf->SetY(40);
        $pdf->SetX(20);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(175, 5, strtoupper($_SESSION['lang']['ijin'].'/'.$_SESSION['lang']['cuti']), 0, 1, 'C');
        $pdf->SetX(20);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['tanggal'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$tgl, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['nokaryawan'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$karyawanid, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['namakaryawan'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$namakaryawan, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['bagian'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$bagian, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['functionname'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$jabatan, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['keperluan'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$kperluan, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['jenisijin'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$jns, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['keterangan'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$ket, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, 'Periode Cuti', 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$periode, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['dari'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$jmDr, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, $_SESSION['lang']['tglcutisampai'], 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$jmSmp, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, 'Jumlah hari', 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$hk.' Hari kerja', 0, 1, 'L');
        $pdf->Ln();
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(172, 5, strtoupper($_SESSION['lang']['approval_status']), 0, 1, 'L');
        $pdf->SetX(30);
        $pdf->Cell(30, 5, strtoupper($_SESSION['lang']['bagian']), 1, 0, 'C');
        $pdf->Cell(50, 5, strtoupper($_SESSION['lang']['namakaryawan']), 1, 0, 'C');
        $pdf->Cell(40, 5, strtoupper($_SESSION['lang']['functionname']), 1, 0, 'C');
        $pdf->Cell(37, 5, strtoupper($_SESSION['lang']['keputusan']), 1, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(30);
        $pdf->Cell(30, 5, $perbagian, 1, 0, 'L');
        $pdf->Cell(50, 5, $pernama, 1, 0, 'L');
        $pdf->Cell(40, 5, $perjabatan, 1, 0, 'L');
        $pdf->Cell(37, 5, $arrKeputusan[$perstatus], 1, 1, 'L');
        $pdf->SetX(30);
        $pdf->Cell(30, 5, $perbagianhrd, 1, 0, 'L');
        $pdf->Cell(50, 5, $pernamahrd, 1, 0, 'L');
        $pdf->Cell(40, 5, $perjabatanhrd, 1, 0, 'L');
        $pdf->Cell(37, 5, $arrKeputusan[$sthrd], 1, 1, 'L');
        $pdf->Ln();
        $pdf->SetX(20);
        $pdf->Cell(30, 5, 'Alasan(Atasan)', 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$koments, 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->Cell(30, 5, 'Alasan(HRD)', 0, 0, 'L');
        $pdf->Cell(50, 5, ' : '.$koments2, 0, 1, 'L');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Output();

        break;
    case 'getExcel':
        $tab .= " \r\n                <table class=sortable cellspacing=1 border=1 width=80%>\r\n                <thead>\r\n                <tr  >\r\n                <td align=center bgcolor='#DFDFDF'>No.</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tanggal']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['nama']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['keperluan']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['jenisijin']."</td>  \r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['persetujuan']."</td>    \r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['approval_status']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['dari'].'  '.$_SESSION['lang']['jam']."</td>\r\n                <td align=center bgcolor='#DFDFDF'>".$_SESSION['lang']['tglcutisampai'].'  '.$_SESSION['lang']['jam']."</td>\r\n                </tr>  \r\n                </thead><tbody>";
        $slvhc = 'select * from '.$dbname.'.sdm_ijin   order by `tanggal` desc ';
        $qlvhc = mysql_query($slvhc) || exit(mysql_error($conns));
        $user_online = $_SESSION['standard']['userid'];
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            ++$no;
            $tab .= "\r\n                <tr class=rowcontent>\r\n                <td>".$no."</td>\r\n                <td>".$rlvhc['tanggal']."</td>\r\n                <td>".$arrNmkary[$rlvhc['karyawanid']]."</td>\r\n                <td>".$rlvhc['keperluan']."</td>\r\n                <td>".$rlvhc['jenisijin']."</td>\r\n                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>\r\n                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>\r\n                <td>".$rlvhc['darijam']."</td>\r\n                <td>".$rlvhc['sampaijam'].'</td>';
        }
        $tab .= '</tbody></table>';
        $nop_ = 'listizinkeluarkantor';
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
            closedir($handle);
        }

        break;
    default:
        break;
}

?>
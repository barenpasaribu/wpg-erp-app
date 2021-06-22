<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
require_once 'lib/fpdf.php';
$param = $_POST;
$proses = $_POST['proses'];
$optNmdept = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optNmpend = makeOption($dbname, 'sdm_5pendidikan', 'idpendidikan,kelompok');
$optNmorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNm = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
if ('' == $proses) {
    $proses = $_GET['proses'];
}

$arrstat = [$_SESSION['lang']['wait_approval '], $_SESSION['lang']['disetujui']];
$tglHrini = date('Y-m-d');
switch ($proses) {
    case 'loadData':
        echo '<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%><thead><tr align=center>';
        echo '<td>'.$_SESSION['lang']['namalowongan'].'</td>';
        echo '<td>'.$_SESSION['lang']['unit'].' Peminta</td>';
        echo '<td>'.$_SESSION['lang']['unit'].' '.$_SESSION['lang']['penempatan'].'</td>';
        echo '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        echo '<td>'.$_SESSION['lang']['tgldibutuhkan'].'</td>';
        echo '<td>'.$_SESSION['lang']['kotapenempatan'].'</td>';
        echo '<td>'.$_SESSION['lang']['pendidikan'].'</td>';
        echo '<td>'.$_SESSION['lang']['jurusan'].'</td>';
        echo '<td colspan=2>'.$_SESSION['lang']['action'].'</td>';
        echo '</tr></thead><tbody>';
        $limit = 3;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        if ('' != $_POST['page2']) {
            $page = $_POST['page2'] - 1;
        }

        if ('' != $param['tahun']) {
            $whr = "where tanggal like '".$param['tahun']."%'";
        }

        $offset = $page * $limit;
        $sdata = 'select distinct * from '.$dbname.'.sdm_permintaansdm   '.$whr."\r\n                order by tanggal desc limit ".$offset.','.$limit.' ';
        $qdata = mysql_query($sdata, $conn);
        $rowdata = mysql_num_rows($qdata);
        $saks = 'select distinct * from '.$dbname.".setup_remotetimbangan \r\n               where lokasi='HRDJKRT'";
        $qaks = mysql_query($saks);
        $jaks = mysql_fetch_assoc($qaks);
        $uname2 = $jaks['username'];
        $passwd2 = $jaks['password'];
        $dbserver2 = $jaks['ip'];
        $dbport2 = $jaks['port'];
        $dbdt = $jaks['dbname'];
        $conn2 = mysql_connect($dbserver2, $uname2, $passwd2);
        if (!$conn2) {
            exit('Could not connect: '.mysql_error());
        }

        while ($rdata = mysql_fetch_assoc($qdata)) {
            $sdt = 'select tglakhirdisplay from '.$dbdt.".sdm_lowongan where nopermintaan='".$rdata['notransaksi']."'";
            $qdt = mysql_query($sdt, $conn2);
            $rdt = mysql_fetch_assoc($qdt);
            if ('' == $rdt['tglakhirdisplay']) {
                $rdt['tglakhirdisplay'] = $rdata['tgldibutuhkan'];
            }

            $slisihhari = selisihhari($tglHrini, $rdt['tglakhirdisplay']);
            echo '<tr class=rowcontent>';
            echo '<td>'.$rdata['namalowongan'].'</td>';
            echo '<td>'.$rdata['kodeorg'].'</td>';
            echo '<td>'.$rdata['penempatan'].'</td>';
            echo '<td>'.tanggalnormal($rdata['tanggal']).'</td>';
            echo '<td>'.tanggalnormal($rdata['tgldibutuhkan']).'</td>';
            echo '<td>'.$rdata['kotapenempatan'].'</td>';
            echo '<td>'.$optNmpend[$rdata['pendidikan']].'</td>';
            echo '<td>'.$rdata['jurusan'].'</td>';
            if (0 < $slisihhari) {
                if ($rdata['persetujuan1'] == $_SESSION['standard']['userid'] || $rdata['persetujuan2'] == $_SESSION['standard']['userid']) {
                    echo "<td colspan=2><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;";
                    if (0 == $rdata['stpersetujuan1'] && $rdata['persetujuan1'] == $_SESSION['standard']['userid']) {
                        echo "<button onclick=\"procDt('".$rdata['notransaksi']."','1')\" class=\"mybutton\" >".$_SESSION['lang']['konfirm'].'</button>';
                    }

                    if (0 == $rdata['stpersetujuan2'] && $rdata['persetujuan2'] == $_SESSION['standard']['userid']) {
                        echo "<button onclick=\"procDt('".$rdata['notransaksi']."','2')\" class=\"mybutton\" >".$_SESSION['lang']['konfirm'].'</button>';
                    }

                    echo '</td>';
                } else {
                    if ($rdata['persetujuanhrd'] == $_SESSION['standard']['userid']) {
                        if (0 == $rdata['stpersetujuanhrd'] && $rdata['persetujuanhrd'] == $_SESSION['standard']['userid']) {
                            echo "<td><input type=text class='myinputtext'  onmousemove=setCalendar(this.id) onkeypress=return false;   style='width:150px;' id='tglsmp_".$rdata['notransaksi']."' /></td>";
                            echo "<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;<button onclick=\"procDt2('".$rdata['notransaksi']."')\" class=\"mybutton\" >".$_SESSION['lang']['konfirm'].'</button></td>';
                        } else {
                            echo "<td colspan=2><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\"></td>";
                        }
                    } else {
                        echo "<td colspan=2><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;</td>";
                    }
                }
            } else {
                if ($rdata['persetujuanhrd'] == $_SESSION['standard']['userid']) {
                    echo "<td><input type=text class='myinputtext'  onmousemove=setCalendar(this.id) onkeypress=return false;   style='width:150px;' id='tglsmp_".$rdata['notransaksi']."' /></td>";
                    echo "<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\">&nbsp;<button onclick=\"procDt2('".$rdata['notransaksi']."')\" class=\"mybutton\" >".$_SESSION['lang']['renew'].'</button></td>';
                } else {
                    echo "<td colspan=2><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_permintaansdm','".$rdata['notransaksi']."','','sdm_slave_daftartenagakerja',event);\"></td>";
                }
            }

            echo '</tr>';
        }
        echo "</tbody><tfoot><tr><td colspan=10 align=center><img src=\"images/skyblue/first.png\" onclick='loadData(0)' style='cursor:pointer'>";
        echo "<img src=\"images/skyblue/prev.png\" onclick='loadData(".($page - 1).")'  style='cursor:pointer'>";
        $spage = 'select distinct * from '.$dbname.'.sdm_permintaansdm  '.$whr.' order by tanggal desc';
        $qpage = mysql_query($spage);
        $rpage = mysql_num_rows($qpage);
        echo '<input type=text />';
        $dert .= "<select id='pages' style='width:50px' onchange='loadData(1.1)'>";
        $totalPage = @ceil($rpage / 10);
        for ($starAwal = 1; $starAwal <= $totalPage; ++$starAwal) {
            ('1.1' == $_POST['page'] ? $_POST['page'] : $_POST['page']);
            $dert .= "<option value='".$starAwal."' ".(($starAwal == $_POST['page'] ? 'selected' : '')).'>'.$starAwal.'</option>';
        }
        $dert .= '</select>';
        echo $dert;
        echo "<img src=\"images/skyblue/next.png\" onclick='loadData(".($page + 1).")'  style='cursor:pointer'>";
        echo "<img src=\"images/skyblue/last.png\" onclick='loadData(".(int) $totalPage.")'  style='cursor:pointer'>";
        echo '</td></tr></tfoot></table>';

        break;
    case 'update':
        $saks = 'select distinct * from '.$dbname.".setup_remotetimbangan \r\n               where lokasi='HRDJKRT'";
        $qaks = mysql_query($saks);
        $jaks = mysql_fetch_assoc($qaks);
        $uname2 = $jaks['username'];
        $passwd2 = $jaks['password'];
        $dbserver2 = $jaks['ip'];
        $dbport2 = $jaks['port'];
        $dbdt = $jaks['dbname'];
        $conn2 = mysql_connect($dbserver2, $uname2, $passwd2);
        if (!$conn2) {
            exit('Could not connect: '.mysql_error());
        }

        $scek2 = 'select distinct `persetujuan'.$param['urut'].'`,`stpersetujuan'.$param['urut'].'`,tgldibutuhkan from '.$dbname.".sdm_permintaansdm \r\n           where notransaksi='".$param['notransaksi']."'";
        $qcek2 = mysql_query($scek2, $conn);
        $rcek2 = mysql_fetch_assoc($qcek2);
        $sdt = 'select tglakhirdisplay from '.$dbdt.".sdm_lowongan where nopermintaan='".$param['notransaksi']."'";
        $qdt = mysql_query($sdt, $conn2);
        $rdt = mysql_fetch_assoc($qdt);
        $qdata = mysql_query($sdata, $conn);
        $rowdata = mysql_num_rows($qdata);
        if ('' == $rdt['tglakhirdisplay']) {
            $rdt['tglakhirdisplay'] = $rcek2['tgldibutuhkan'];
        }

        $slisihhari = selisihhari($tglHrini, $rdt['tglakhirdisplay']);
        if (0 < $slisihhari && 0 != $rcek2['stpersetujuan'.$param['urut']]) {
            exit('error: Data Sudah Ada Perrsetujuan');
        }

        $sins = 'update '.$dbname.'.sdm_permintaansdm  set `stpersetujuan'.$param['urut']."`='1' \r\n           where notransaksi='".$param['notransaksi']."' and `persetujuan".$param['urut']."`='".$rcek2['persetujuan'.$param['urut']]."'";
        if (!mysql_query($sins, $conn)) {
            exit('error:'.mysql_error($conn).'__'.$sins);
        }

        break;
    case 'updateDt':
        $scek2 = 'select distinct * from '.$dbname.".sdm_permintaansdm \r\n           where notransaksi='".$param['notransaksi']."'";
        $qcek2 = mysql_query($scek2);
        $rcek2 = mysql_fetch_assoc($qcek2);
        if (0 != $rcek2['stpersetujuanhrd']) {
            exit('error: Data Sudah Ada Perrsetujuan');
        }

        $tgl = explode('-', $param['tglTakhir']);
        $param['tglTakhir'] = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
        $slisihhari = daysbetween($rcek2['tgldibutuhkan'], $param['tglTakhir']);
        if ($slisihhari < 0) {
            exit('Error: Tanggal Display Lowongan Tidak Boleh Lebih Kecil dari Tanggal Di butuhkan');
        }

        $sdtip = 'select distinct * from '.$dbname.".setup_remotetimbangan where lokasi='HRDJKRT'";
        $qdtip = mysql_query($sdtip);
        $rdtip = mysql_fetch_assoc($qdtip);
        $dbserver2 = $rdtip['ip'];
        $dbport2 = $rdtip['port'];
        $dbname2 = $rdtip['dbname'];
        $uname2 = $rdtip['username'];
        $passwd2 = $rdtip['password'];
        $conn2 = mysql_connect($dbserver2.':'.$dbport2, $uname2, $passwd2) || exit('Error/Gagal :Unable to Connect to database '.$dbserver2);
        $sed = 'select distinct * from '.$dbname2.".`sdm_lowongan` where nopermintaan='".$rcek2['notransaksi']."'";
        $qed = mysql_query($sed, $conn2) || exit(mysql_error($conn2));
        $red = mysql_num_rows($qed);
        if (0 == $red) {
            $sinsert = 'insert into '.$dbname2.".`sdm_lowongan` \r\n                              (`notransaksi`,`namalowongan`, `nopermintaan`,`departemen`, `tanggal`, `tgldibutuhkan`, `kotapenempatan`, `pendidikan`, `jurusan`, `pengalaman`, `kompetensi`, `deskpekerjaan`, `maxumur`, `tglakhirdisplay`, `updateby`)\r\n                              values\r\n                              (NULL,'".$rcek2['namalowongan']."','".$rcek2['notransaksi']."','".$rcek2['departemen']."','".$rcek2['tanggal']."','".$rcek2['tgldibutuhkan']."','".$rcek2['kotapenempatan']."','".$rcek2['pendidikan']."','".$rcek2['jurusan']."','".$rcek2['pengalaman']."','".$rcek2['kompetensi']."','".$rcek2['deskpekerjaan']."','".$rcek2['maxumur']."','".$param['tglTakhir']."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sinsert, $conn2)) {
                exit('error:'.$sinsert.'__'.mysql_error($conn2));
            }

            $sins = 'update '.$dbname.".sdm_permintaansdm  set `stpersetujuanhrd`='1' \r\n                        where notransaksi='".$param['notransaksi']."' ";
            if (!mysql_query($sins, $conn)) {
                exit('error: '.$sins.'___'.mysql_error($conn));
            }
        } else {
            $sinsert = 'update '.$dbname2.".`sdm_lowongan` set `namalowongan`='".$rcek2['namalowongan']."',`tanggal`='".$rcek2['tanggal']."', \r\n                             `tgldibutuhkan`='".$rcek2['tgldibutuhkan']."', `kotapenempatan`='".$rcek2['kotapenempatan']."',\r\n                             `pendidikan`='".$rcek2['pendidikan']."', `jurusan`='".$rcek2['jurusan']."', `pengalaman`='".$rcek2['pengalaman']."', \r\n                             `kompetensi`='".$rcek2['kompetensi']."', `deskpekerjaan`='".$rcek2['deskpekerjaan']."', `maxumur`='".$rcek2['maxumur']."', \r\n                             `tglakhirdisplay`='".$param['tglTakhir']."', `updateby`='".$_SESSION['standard']['userid']."' where `nopermintaan`='".$rcek2['notransaksi']."',";
            if (!mysql_query($sinsert, $conn2)) {
                exit('error:'.$sinsert.'__'.mysql_error($conn2));
            }
        }

        break;
    case 'pdfDt':

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $userid;
        global $notransaksi;
        global $kodevhc;
        global $posting;
        global $bar;
        global $arrstat;
        $kodevhc = $test[1];
        $str1 = 'select * from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."' and tipe='PT'";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $namapt = $bar1->namaorganisasi;
            $alamatpt = $bar1->alamat.', '.$bar1->wilayahkota;
            $telp = $bar1->telepon;
        }
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

        $this->Image($path, 15, 5, 35, 20);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(55);
        $this->Cell(60, 5, $namapt, 0, 1, 'L');
        $this->SetX(55);
        $this->Cell(60, 5, $alamatpt, 0, 1, 'L');
        $this->SetX(55);
        $this->Cell(60, 5, 'Tel: '.$telp, 0, 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'U', 12);
        $this->SetY(35);
        $this->Cell(190, 5, 'Detail Permintaan Tenaga Kerja', 0, 1, 'C');
        $this->SetFont('Arial', '', 6);
        $this->SetY(27);
        $this->SetX(163);
        $this->Cell(30, 10, 'PRINT TIME : '.date('d-m-Y H:i:s'), 0, 1, 'L');
        $this->Line(10, 27, 200, 27);
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $test = explode(',', $_GET['column']);
        $notransaksi = $test[0];
        $str = 'select * from '.$dbname.'.'.$_GET['table']."  where notransaksi='".$notransaksi."' ";
        $qstr = mysql_query($str);
        $bar = mysql_fetch_assoc($qstr);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(30, 7, $_SESSION['lang']['unit'].' Peminta', 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $optNmorg[$bar['kodeorg']].' ['.$bar['kodeorg'].']', 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['unit'].' '.$_SESSION['lang']['penempatan'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $optNmorg[$bar['penempatan']].' ['.$bar['penempatan'].']', 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['departemen'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $optNmdept[$bar['departemen']], 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['tanggal'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, tanggalnormal($bar['tanggal']), 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['tgldibutuhkan'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, tanggalnormal($bar['tgldibutuhkan']), 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['kotapenempatan'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $bar['kotapenempatan'], 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['pendidikan'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $optNmpend[$bar['pendidikan']], 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['jurusan'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $bar['jurusan'], 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['maxumur'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $bar['maxumur'], 0, 1, 'L');
        $pdf->Cell(30, 7, 'Jumlah Kebutuhan', 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $bar['jumlah_kebutuhan'].' Orang', 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['pengalamankerja'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->Cell(40, 7, $bar['pengalaman'], 0, 1, 'L');
        $pdf->Cell(30, 7, $_SESSION['lang']['kompetensi'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->MultiCell(155, 7, $bar['kompetensi'], 'J');
        $pdf->Cell(30, 7, $_SESSION['lang']['deskpekerjaan'], 0, 0, 'L');
        $pdf->Cell(3, 7, ' : ', 0, 0, 'L');
        $pdf->MultiCell(155, 7, $bar['deskpekerjaan'], 'J');
        $pdf->Ln(8);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(45, 7, $_SESSION['lang']['persetujuan'].' 1', 1, 0, 'L', 1);
        $pdf->Cell(45, 7, $_SESSION['lang']['persetujuan'].' 2', 1, 0, 'L', 1);
        $pdf->Cell(45, 7, $_SESSION['lang']['persetujuan'].' HRD', 1, 1, 'L', 1);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell(45, 7, $optNm[$bar['persetujuan1']], 1, 0, 'L', 1);
        $pdf->Cell(45, 7, $optNm[$bar['persetujuan2']], 1, 0, 'L', 1);
        $pdf->Cell(45, 7, $optNm[$bar['persetujuanhrd']], 1, 1, 'L', 1);
        $pdf->Cell(45, 7, $arrstat[$bar['stpersetujuan1']], 1, 0, 'L', 1);
        $pdf->Cell(45, 7, $arrstat[$bar['stpersetujuan2']], 1, 0, 'L', 1);
        $pdf->Cell(45, 7, $arrstat[$bar['stpersetujuanhrd']], 1, 1, 'L', 1);
        $pdf->Output();

        break;
}
function daysBetween($s, $e)
{
    $s = strtotime($s);
    $e = strtotime($e);

    return ($e - $s) / (24 * 3600);
}

function selisihHari($tglAwal, $tglAkhir)
{
    $pecah1 = explode('-', $tglAwal);
    list($year1, $month1, $date1) = $pecah1;
    $pecah2 = explode('-', $tglAkhir);
    list($year2, $month2, $date2) = $pecah2;
    $jd1 = gregoriantojd($month1, $date1, $year1);
    $jd2 = gregoriantojd($month2, $date2, $year2);

    return $jd2 - $jd1;
}

?>
<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$periode = $_POST['periode'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
$kdOrg = $_POST['kdOrg'];
if (!$periode) {
    $periode = $_GET['periode'];
}

if (!$kdOrg) {
    $kdOrg = $_GET['kdOrg'];
}

if (!$kdOrg) {
    $kdOrg = $_SESSION['empl']['lokasitugas'];
}

$qwe = explode('-', $periode);
list($tahun, $bulan) = $qwe;
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $nmOrg = $rOrg['namaorganisasi'];
}
if (!$nmOrg) {
    $nmOrg = $kdOrg;
}

$dzArr = [];
$sPrasarana = 'SELECT a.*, sum(a.jumlah) as jumlahnya FROM '.$dbname.".sdm_prasarana a \r\n    WHERE a.kodeorg = '".$kdOrg."' and  \r\n        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun.''.$bulan."' and a.status = '1'\r\n    GROUP BY a.tahunperolehan, a.jenisprasarana\r\n    ORDER BY a.tahunperolehan";
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $dzArr[$res['jenisprasarana']][$res['tahunperolehan']][jenis] = $res['jenisprasarana'];
    $dzArr[$res['jenisprasarana']][$res['tahunperolehan']][tahun] = $res['tahunperolehan'];
    $dzArr[$res['jenisprasarana']][$res['tahunperolehan']][jumlah] += $res['jumlahnya'];
    $tahuntahun[$res['tahunperolehan']] = $res['tahunperolehan'];
}
$sPrasarana = 'SELECT a.*, sum(a.jumlah) as jumlahnya FROM '.$dbname.".sdm_prasarana a \r\n    WHERE a.kodeorg = '".$kdOrg."' and  \r\n        a.tahunperolehan < '".$tahun."' and a.status = '1'\r\n    GROUP BY a.tahunperolehan, a.jenisprasarana\r\n    ORDER BY a.tahunperolehan";
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $awaldzArr[$res['jenisprasarana']][$res['tahunperolehan']] += $res['jumlahnya'];
}
$tahunlalu = $tahun;
$bulanlalu = $bulan - 1;
if (1 == $bulan) {
    $tahunlalu = $tahun - 1;
    $bulanlalu = 12;
}

if (1 == strlen($bulanlalu)) {
    $bulanlalu = '0'.$bulanlalu;
}

$sPrasarana = 'SELECT a.*, sum(a.jumlah) as jumlahnya FROM '.$dbname.".sdm_prasarana a \r\n    WHERE a.kodeorg = '".$kdOrg."' and  \r\n        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahunlalu.''.$bulanlalu."' and a.status = '1'\r\n    GROUP BY a.tahunperolehan, a.jenisprasarana\r\n    ORDER BY a.tahunperolehan";
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $bulanlaludzArr[$res['jenisprasarana']][$res['tahunperolehan']] += $res['jumlahnya'];
}
$sPrasarana = 'SELECT a.*, b.*, sum(b.jumlah) as jumlahnya FROM '.$dbname.".sdm_prasarana a \r\n    LEFT JOIN ".$dbname.".sdm_kondisi_prasarana b ON a.kodeprasarana=b.kodeprasarana\r\n    WHERE a.kodeorg = '".$kdOrg."' and  \r\n        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahunlalu.''.$bulanlalu."' and\r\n        b.tanggal like '".$tahunlalu.''.$bulanlalu."%' and a.status = '1'\r\n    GROUP BY a.tahunperolehan, a.jenisprasarana\r\n    ORDER BY a.tahunperolehan";
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $kondisilaludzArr[$res['jenisprasarana']][$res['tahunperolehan']][$res['kondisi']] += $res['jumlahnya'];
}
$sPrasarana = 'SELECT a.*, sum(a.jumlah) as jumlahnya FROM '.$dbname.".sdm_prasarana a \r\n    WHERE a.kodeorg = '".$kdOrg."' and  \r\n        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun.''.$bulan."' and a.status = '1'\r\n    GROUP BY a.tahunperolehan, a.jenisprasarana\r\n    ORDER BY a.tahunperolehan";
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $bulaninidzArr[$res['jenisprasarana']][$res['tahunperolehan']] += $res['jumlahnya'];
}
$sPrasarana = 'SELECT a.*, b.*, sum(b.jumlah) as jumlahnya FROM '.$dbname.".sdm_prasarana a \r\n    LEFT JOIN ".$dbname.".sdm_kondisi_prasarana b ON a.kodeprasarana=b.kodeprasarana\r\n    WHERE a.kodeorg = '".$kdOrg."' and  \r\n        concat(a.tahunperolehan,'',a.bulanperolehan) <= '".$tahun.''.$bulan."' and\r\n        b.tanggal like '".$tahun.''.$bulan."%' and a.status = '1'\r\n    GROUP BY a.tahunperolehan, a.jenisprasarana\r\n    ORDER BY a.tahunperolehan";
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $kondisiinidzArr[$res['jenisprasarana']][$res['tahunperolehan']][$res['kondisi']] += $res['jumlahnya'];
}
$sPrasarana = 'SELECT a.*, sum(a.jumlah) as jumlahnya FROM '.$dbname.".sdm_prasarana a \r\n    WHERE a.kodeorg = '".$kdOrg."' and  \r\n        concat(a.tahunperolehan,'',a.bulanperolehan) = '".$tahun.''.$bulan."' and a.status = '1'\r\n    GROUP BY a.tahunperolehan, a.jenisprasarana\r\n    ORDER BY a.tahunperolehan";
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $inidzArr[$res['jenisprasarana']][$res['tahunperolehan']] += $res['jumlahnya'];
}
$sPrasarana = 'SELECT * FROM '.$dbname.'.sdm_5kl_prasarana';
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $kl_prasarana[$res['kode']] = $res['kode'];
    $namakl_prasarana[$res['kode']] = $res['nama'];
}
$sPrasarana = 'SELECT * FROM '.$dbname.'.sdm_5jenis_prasarana';
$query = mysql_query($sPrasarana);
while ($res = mysql_fetch_assoc($query)) {
    $jenis_prasarana[$res['jenis']] = $res['jenis'];
    $kelompokjenis_prasarana[$res['jenis']] = $res['kelompok'];
    $namajenis_prasarana[$res['jenis']] = $res['nama'];
    $satuanjenis_prasarana[$res['jenis']] = $res['satuan'];
}
switch ($proses) {
    case 'preview':
        if (empty($dzArr)) {
            echo 'Data Empty.';
            exit();
        }

        echo "<table width=100% cellspacing='1' border='0' class='sortable'>\r\n    <thead class=rowheader>\r\n    <tr>\r\n    <td align=center colspan=2 rowspan=3>".$_SESSION['lang']['klSarana'].'/'.$_SESSION['lang']['jnsPrasarana']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['satuan']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['tahunperolehan']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['posisiawaltahun']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['sdbulanlalu']."</td>\r\n    <td align=center colspan=2>".$_SESSION['lang']['perubahan']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['sdbulanini']."</td>\r\n    <td align=center colspan=8>".$_SESSION['lang']['kondisi']."</td>\r\n    </tr>\r\n    <tr>\r\n    <td align=center rowspan=2>+</td>\r\n    <td align=center rowspan=2>-</td>\r\n    <td align=center colspan=4>".$_SESSION['lang']['sdbulanlalu']."</td>\r\n    <td align=center colspan=4>".$_SESSION['lang']['sdbulanini']."</td>\r\n    </tr>\r\n    <tr>\r\n    <td align=center>B-BD</td>\r\n    <td align=center>B-TD</td>\r\n    <td align=center>R-BD</td>\r\n    <td align=center>R-TD</td>\r\n    <td align=center>B-BD</td>\r\n    <td align=center>B-TD</td>\r\n    <td align=center>R-BD</td>\r\n    <td align=center>R-TD</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>\r\n    ";
        $dummy = '';
        foreach ($kl_prasarana as $kl) {
            foreach ($jenis_prasarana as $jenis) {
                if ($kelompokjenis_prasarana[$jenis] == $kl) {
                    foreach ($tahuntahun as $tetey) {
                        if ('' != $dzArr[$jenis][$tetey][jenis]) {
                            echo '<tr class=rowcontent>';
                            if ($kl != $dummy) {
                                echo '<td>'.$namakl_prasarana[$kl].'</td>';
                                $dummy = $kl;
                            } else {
                                echo '<td></td>';
                            }

                            echo '<td>'.$namajenis_prasarana[$jenis].'</td>';
                            echo '<td>'.$satuanjenis_prasarana[$dzArr[$jenis][$tetey][jenis]].'</td>';
                            echo '<td align=center>'.$dzArr[$jenis][$tetey][tahun].'</td>';
                            echo '<td align=right>'.number_format($awaldzArr[$jenis][$tetey]).'</td>';
                            echo '<td align=right>'.number_format($bulanlaludzArr[$jenis][$tetey]).'</td>';
                            if (0 < number_format($inidzArr[$jenis][$tetey])) {
                                echo '<td align=right>'.number_format($inidzArr[$jenis][$tetey]).'</td>';
                            } else {
                                echo '<td align=right>0</td>';
                            }

                            if (number_format($inidzArr[$jenis][$tetey]) < 0) {
                                echo '<td align=right>'.number_format(-1 * $inidzArr[$jenis][$tetey]).'</td>';
                            } else {
                                echo '<td align=right>0</td>';
                            }

                            echo '<td align=right>'.number_format($bulaninidzArr[$jenis][$tetey]).'</td>';
                            echo '<td align=right>'.number_format($kondisilaludzArr[$jenis][$tetey]['BDB']).'</td>';
                            echo '<td align=right>'.number_format($kondisilaludzArr[$jenis][$tetey]['BTD']).'</td>';
                            echo '<td align=right>'.number_format($kondisilaludzArr[$jenis][$tetey]['RBD']).'</td>';
                            echo '<td align=right>'.number_format($kondisilaludzArr[$jenis][$tetey]['RTD']).'</td>';
                            echo '<td align=right>'.number_format($kondisiinidzArr[$jenis][$tetey]['BDB']).'</td>';
                            echo '<td align=right>'.number_format($kondisiinidzArr[$jenis][$tetey]['BTD']).'</td>';
                            echo '<td align=right>'.number_format($kondisiinidzArr[$jenis][$tetey]['RBD']).'</td>';
                            echo '<td align=right>'.number_format($kondisiinidzArr[$jenis][$tetey]['RTD']).'</td>';
                            echo '</tr>';
                        }
                    }
                }
            }
        }
        echo '</tbody></table>';

        break;
    case 'excel':
        if (empty($dzArr)) {
            echo 'Data Empty.';
            exit();
        }

        $stream .= "\r\n    <table border='0'>\r\n      <tr>\r\n      <td colspan='13' align=left>".strtoupper('INVENTARIS BANGUNAN, SARANA DAN PRASARANA UNIT').' : '.$nmOrg."</td>\r\n      <td colspan='4' align=right>".strtoupper($_SESSION['lang']['bulan']).' : '.$bulan.' '.$tahun."</td>    \r\n      </tr>\r\n      <tr><td colspan='17'>&nbsp;</td></tr>\r\n    </table>";
        $stream .= "<table border='1'>\r\n    <thead class=rowheader>\r\n    <tr>\r\n    <td align=center colspan=2 rowspan=3>".$_SESSION['lang']['klSarana'].'/'.$_SESSION['lang']['jnsPrasarana']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['satuan']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['tahunperolehan']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['posisiawaltahun']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['sdbulanlalu']."</td>\r\n    <td align=center colspan=2>".$_SESSION['lang']['perubahan']."</td>\r\n    <td align=center rowspan=3>".$_SESSION['lang']['sdbulanini']."</td>\r\n    <td align=center colspan=8>".$_SESSION['lang']['kondisi']."</td>\r\n    </tr>\r\n    <tr>\r\n    <td align=center rowspan=2>+</td>\r\n    <td align=center rowspan=2>-</td>\r\n    <td align=center colspan=4>".$_SESSION['lang']['sdbulanlalu']."</td>\r\n    <td align=center colspan=4>".$_SESSION['lang']['sdbulanini']."</td>\r\n    </tr>\r\n    <tr>\r\n    <td align=center>B-BD</td>\r\n    <td align=center>B-TD</td>\r\n    <td align=center>R-BD</td>\r\n    <td align=center>R-TD</td>\r\n    <td align=center>B-BD</td>\r\n    <td align=center>B-TD</td>\r\n    <td align=center>R-BD</td>\r\n    <td align=center>R-TD</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>\r\n";
        $dummy = '';
        foreach ($kl_prasarana as $kl) {
            foreach ($jenis_prasarana as $jenis) {
                if ($kelompokjenis_prasarana[$jenis] == $kl) {
                    foreach ($tahuntahun as $tetey) {
                        if ('' != $dzArr[$jenis][$tetey][jenis]) {
                            $stream .= '<tr class=rowcontent>';
                            if ($kl != $dummy) {
                                $stream .= '<td>'.$namakl_prasarana[$kl].'</td>';
                                $dummy = $kl;
                            } else {
                                $stream .= '<td></td>';
                            }

                            $stream .= '<td>'.$namajenis_prasarana[$jenis].'</td>';
                            $stream .= '<td>'.$satuanjenis_prasarana[$dzArr[$jenis][$tetey][jenis]].'</td>';
                            $stream .= '<td>'.$dzArr[$jenis][$tetey][tahun].'</td>';
                            $stream .= '<td>'.number_format($awaldzArr[$jenis][$tetey]).'</td>';
                            $stream .= '<td>'.number_format($bulanlaludzArr[$jenis][$tetey]).'</td>';
                            if (0 < number_format($inidzArr[$jenis][$tetey])) {
                                $stream .= '<td>'.number_format($inidzArr[$jenis][$tetey]).'</td>';
                            } else {
                                $stream .= '<td>0</td>';
                            }

                            if (number_format($inidzArr[$jenis][$tetey]) < 0) {
                                $stream .= '<td>'.number_format(-1 * $inidzArr[$jenis][$tetey]).'</td>';
                            } else {
                                $stream .= '<td>0</td>';
                            }

                            $stream .= '<td>'.number_format($bulaninidzArr[$jenis][$tetey]).'</td>';
                            $stream .= '<td>'.number_format($kondisilaludzArr[$jenis][$tetey]['BDB']).'</td>';
                            $stream .= '<td>'.number_format($kondisilaludzArr[$jenis][$tetey]['BTD']).'</td>';
                            $stream .= '<td>'.number_format($kondisilaludzArr[$jenis][$tetey]['RBD']).'</td>';
                            $stream .= '<td>'.number_format($kondisilaludzArr[$jenis][$tetey]['RTD']).'</td>';
                            $stream .= '<td>'.number_format($kondisiinidzArr[$jenis][$tetey]['BDB']).'</td>';
                            $stream .= '<td>'.number_format($kondisiinidzArr[$jenis][$tetey]['BTD']).'</td>';
                            $stream .= '<td>'.number_format($kondisiinidzArr[$jenis][$tetey]['RBD']).'</td>';
                            $stream .= '<td>'.number_format($kondisiinidzArr[$jenis][$tetey]['RTD']).'</td>';
                            $stream .= '</tr>';
                        }
                    }
                }
            }
        }
        $stream .= '</tbody></table>';
        $stream .= '<br>Catatan: B-BD = Baik, Bisa Dipakai. B-TD = Baik, Tidak Dipakai. R-DB = Rusak, Bisa Dipakai. R-TD = Rusak, Tidak Dipakai.<br><br>';
        $stream .= 'Print Time:'.date('d-m-Y H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'LaporanPrasarana'.$periode.'__'.$kdOrg;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n    parent.window.alert('Can't convert to excel format');\r\n    </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n    window.location='tempExcel/".$nop_.".xls';\r\n    </script>";
            closedir($handle);
        }

        break;
    case 'pdf':
        if (empty($dzArr)) {
            echo 'Data Empty.';
            exit();
        }

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
        global $title;
        global $periode;
        global $kdOrg;
        global $nmOrg;
        global $bulan;
        global $tahun;
        $cols = 247.5;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 10;
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

        $this->Image($path, $this->lMargin, $this->tMargin, 60);
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $height = 9;
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(80 / 100 * $width - 5, $height, strtoupper('INVENTARIS BANGUNAN, SARANA DAN PRASARANA UNIT').' : '.$nmOrg, '', 0, 'L');
        $this->Cell(20 / 100 * $width - 5, $height, strtoupper($_SESSION['lang']['bulan']).' : '.$bulan.' '.$tahun, '', 0, 'R');
        $this->Ln();
        $this->SetFont('Arial', '', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(30 / 100 * $width, $height, '', TRL, 0, 'C', 1);
        $this->Cell(5 / 100 * $width, $height, '', TRL, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, '', TRL, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, '', TRL, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, '', TRL, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['perubahan'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, '', TRL, 0, 'C', 1);
        $this->Cell(28 / 100 * $width, $height, $_SESSION['lang']['kondisi'], 1, 0, 'C', 1);
        $this->Ln();
        $this->Cell(30 / 100 * $width, $height, $_SESSION['lang']['klSarana'].'/'.$_SESSION['lang']['jnsPrasarana'], RL, 0, 'C', 1);
        $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['satuan'], RL, 0, 'C', 1);
        $this->SetFont('Arial', '', 5);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tahunperolehan'], RL, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['posisiawaltahun'], RL, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['sdbulanlalu'], RL, 0, 'C', 1);
        $this->SetFont('Arial', '', 6);
        $this->Cell(3.515 / 100 * $width, $height, '+', TRL, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, '-', TRL, 0, 'C', 1);
        $this->SetFont('Arial', '', 5);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['sdbulanini'], RL, 0, 'C', 1);
        $this->SetFont('Arial', '', 6);
        $this->Cell(14 / 100 * $width, $height, $_SESSION['lang']['sdbulanlalu'], 1, 0, 'C', 1);
        $this->Cell(14 / 100 * $width, $height, $_SESSION['lang']['sdbulanini'], 1, 0, 'C', 1);
        $this->Ln();
        $this->Cell(30 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(5 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, 'B-BD', 1, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, 'B-TD', 1, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, 'R-BD', 1, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, 'R-TD', 1, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, 'B-BD', 1, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, 'B-TD', 1, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, 'R-BD', 1, 0, 'C', 1);
        $this->Cell(3.5 / 100 * $width, $height, 'R-TD', 1, 0, 'C', 1);
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(80 / 100 * $width, 10, 'Page '.$this->PageNo(), 0, 0, 'L');
        $this->Cell(20 / 100 * $width, 10, 'Print Time:'.date('d-m-Y H:i:s').' By:'.$_SESSION['empl']['name'], 0, 0, 'R');
    }
}

        $pdf = new PDF('P', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 9;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);
        $dummy = '';
        foreach ($kl_prasarana as $kl) {
            foreach ($jenis_prasarana as $jenis) {
                if ($kelompokjenis_prasarana[$jenis] == $kl) {
                    foreach ($tahuntahun as $tetey) {
                        if ('' != $dzArr[$jenis][$tetey][jenis]) {
                            if ($kl != $dummy) {
                                $pdf->Cell(10 / 100 * $width, $height, $namakl_prasarana[$kl], TRL, 0, 'L', 1);
                                $dummy = $kl;
                            } else {
                                $pdf->Cell(10 / 100 * $width, $height, '', RL, 0, 'L', 1);
                            }

                            $pdf->Cell(20 / 100 * $width, $height, $namajenis_prasarana[$jenis], 1, 0, 'L', 1);
                            $pdf->Cell(5 / 100 * $width, $height, $satuanjenis_prasarana[$dzArr[$jenis][$tetey][jenis]], 1, 0, 'L', 1);
                            $pdf->Cell(8 / 100 * $width, $height, $dzArr[$jenis][$tetey][tahun], 1, 0, 'C', 1);
                            $pdf->Cell(8 / 100 * $width, $height, number_format($awaldzArr[$jenis][$tetey]), 1, 0, 'R', 1);
                            $pdf->Cell(7 / 100 * $width, $height, number_format($bulanlaludzArr[$jenis][$tetey]), 1, 0, 'R', 1);
                            if (0 < number_format($inidzArr[$jenis][$tetey])) {
                                $pdf->Cell(3.5 / 100 * $width, $height, number_format($inidzArr[$jenis][$tetey]), 1, 0, 'R', 1);
                            } else {
                                $pdf->Cell(3.5 / 100 * $width, $height, '0', 1, 0, 'R', 1);
                            }

                            if (number_format($inidzArr[$jenis][$tetey]) < 0) {
                                $pdf->Cell(3.5 / 100 * $width, $height, number_format(-1 * $inidzArr[$jenis][$tetey]), 1, 0, 'R', 1);
                            } else {
                                $pdf->Cell(3.5 / 100 * $width, $height, '0', 1, 0, 'R', 1);
                            }

                            $pdf->Cell(7 / 100 * $width, $height, number_format($bulaninidzArr[$jenis][$tetey]), 1, 0, 'R', 1);
                            $pdf->Cell(3.5 / 100 * $width, $height, number_format($kondisilaludzArr[$jenis][$tetey]['BTD']), 1, 0, 'R', 1);
                            $pdf->Cell(3.5 / 100 * $width, $height, number_format($kondisilaludzArr[$jenis][$tetey]['BTD']), 1, 0, 'R', 1);
                            $pdf->Cell(3.5 / 100 * $width, $height, number_format($kondisilaludzArr[$jenis][$tetey]['RBD']), 1, 0, 'R', 1);
                            $pdf->Cell(3.5 / 100 * $width, $height, number_format($kondisilaludzArr[$jenis][$tetey]['RTD']), 1, 0, 'R', 1);
                            $pdf->Cell(3.5 / 100 * $width, $height, number_format($kondisiinidzArr[$jenis][$tetey]['BTD']), 1, 0, 'R', 1);
                            $pdf->Cell(3.5 / 100 * $width, $height, number_format($kondisiinidzArr[$jenis][$tetey]['BTD']), 1, 0, 'R', 1);
                            $pdf->Cell(3.5 / 100 * $width, $height, number_format($kondisiinidzArr[$jenis][$tetey]['RBD']), 1, 0, 'R', 1);
                            $pdf->Cell(3.5 / 100 * $width, $height, number_format($kondisiinidzArr[$jenis][$tetey]['RTD']), 1, 1, 'R', 1);
                        }
                    }
                }
            }
        }
        $pdf->Cell(10 / 100 * $width, $height, '', T, 0, 'L', 1);
        $pdf->Ln();
        $pdf->Cell($width, $height, 'Catatan: B-BD = Baik, Bisa Dipakai. B-TD = Baik, Tidak Dipakai. R-DB = Rusak, Bisa Dipakai. R-TD = Rusak, Tidak Dipakai.', 0, 0, 'L', 1);
        $pdf->Output();

        break;
    default:
        break;
}

?>
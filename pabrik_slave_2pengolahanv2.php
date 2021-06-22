<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if ('' !== $_GET['kdPabrik']) {
    $_POST = $_GET;
}

('' !== $_POST['proses'] ? ($proses = $_POST['proses']) : ($proses = $_GET['proses']));
$kdPabrik = $_POST['kdPabrik'];
$periode = $_POST['periode'];
$optNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optSat = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$whr = "periode='".$periode."' and kodegudang like '".$kdPabrik."%'";
$optHrg = makeOption($dbname, 'log_5saldobulanan', 'kodebarang,hargarata', $whr);
$optNmorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$brd = 0;
if ('excel' === $proses) {
    $brd = 1;
    $bg = 'align=center bgcolor=#DEDEDE';
}

if ('getPeriode' !== $proses) {
    $cols = 'a.tanggal as tanggal,sum(jamstagnasi) as jamstag,sum(jamdinasbruto) as jmdinbruto,sum(jumlahlori) as jumlori,sum(a.tbsdiolah) as tbsdiolah,sum(oer) as oer,sum(oerpk) as oerpk,nopengolahan';
    $where = "a.kodeorg='".$kdPabrik."' and left(a.tanggal,7)='".$periode."'";
    $query = 'select distinct '.$cols.' from '.$dbname.'.pabrik_pengolahan a left join '.$dbname.".pabrik_produksi b \r\n         on (a.kodeorg=b.kodeorg and a.tanggal=b.tanggal) where ".$where.' group by a.tanggal';
    $tmpRes = fetchData($query);
    if (empty($tmpRes)) {
        echo 'Warning : Data empty';
        exit();
    }

    foreach ($tmpRes as $lstData => $dtIsi) {
        $dtTgl[$dtIsi['tanggal']] = $dtIsi['tanggal'];
        $dtJmstag[$dtIsi['tanggal']] = $dtIsi['jamstag'];
        $dtJmBruto[$dtIsi['tanggal']] = $dtIsi['jmdinbruto'];
        $dtJmLori[$dtIsi['tanggal']] = $dtIsi['jumlori'];
        $dtJmTbsDiolah[$dtIsi['tanggal']] = $dtIsi['tbsdiolah'];
        $dtJmoer[$dtIsi['tanggal']] = $dtIsi['oer'];
        $dtJmoerpk[$dtIsi['tanggal']] = $dtIsi['oerpk'];
    }
    $sData = 'select b.*,a.tanggal   from '.$dbname.".pabrik_pengolahanmesin b \r\n        left join ".$dbname.".pabrik_pengolahan a on b.nopengolahan=a.nopengolahan\r\n        where ".$where.'  ';
    $qData = mysql_query($sData);
    while ($rData = mysql_fetch_assoc($qData)) {
        if ($drer !== $rData['nopengolahan']) {
            $drer = $rData['nopengolahan'];
            $derRow = 1;
        }

        $dtStation[$rData['tanggal']][$derRow] = $rData['kodeorg'];
        $dtMesin[$rData['tanggal']][$derRow] = $rData['tahuntanam'];
        $dtJamOperasi[$rData['tanggal']][$derRow] = $rData['jammulai'];
        $dtJamSlsi[$rData['tanggal']][$derRow] = $rData['jamselesai'];
        $dtJamStag[$rData['tanggal']][$derRow] = $rData['jamstagnasi'];
        $dtKet[$rData['tanggal']][$derRow] = $rData['keterangan'];
        $dtprestasi[$rData['tanggal']][$derRow] = $rData['prestasi'];
        $jmlhRow[$rData['tanggal']] = $derRow;
        ++$derRow;
    }
    $sData2 = 'select b.*,a.tanggal   from '.$dbname.".pabrik_pengolahan_barang b \r\n        left join ".$dbname.".pabrik_pengolahan a on b.nopengolahan=a.nopengolahan\r\n        where ".$where.'  ';
    $qData2 = mysql_query($sData2);
    while ($rData2 = mysql_fetch_assoc($qData2)) {
        if ($drer !== $rData2['tanggal']) {
            $drer = $rData2['tanggal'];
            $derRow = 1;
        }

        $dtKdBrg[$rData2['tanggal']][$derRow] = $rData2['kodebarang'];
        $dtJmlh[$rData2['tanggal']][$derRow] = $rData2['jumlah'];
        $jmlhRow2[$rData2['tanggal']] = $derRow;
        ++$derRow;
    }
    $tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable>';
    $tab .= '<thead><tr>';
    $tab .= '<td colspan=7  align=center '.$bg.'>Summary Processing</td><td colspan=7 align=center  '.$bg.'>Detail Processing</td>';
    $tab .= '<td colspan=5  align=center '.$bg.'>Detail Material Usage</td></tr>';
    $tab .= '<tr>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['tanggal'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['jamstagnasi'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['jamoperasional'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['jumlahlori'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['tbsdiolah'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['oer'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['oerpk'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['station'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['mesin'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['jammulai'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['jamselesai'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['jamstagnasi'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['keterangan'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['prestasi'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['namabarang'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['jumlah'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['satuan'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['hargasatuan'].'</td>';
    $tab .= '<td '.$bg.'>'.$_SESSION['lang']['total'].'</td>';
    $tab .= '</tr></thead><tbody>';
    foreach ($dtTgl as $lstTgl => $dataTgl) {
        if (1 === $jmlhRow[$dataTgl]) {
            $aer = 1;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$dataTgl.'</td>';
            $tab .= '<td align=right>'.$dtJmstag[$dataTgl].'</td>';
            $tab .= '<td align=right>'.$dtJmBruto[$dataTgl].'</td>';
            $tab .= '<td align=right>'.$dtJmLori[$dataTgl].'</td>';
            $tab .= '<td align=right>'.number_format($dtJmTbsDiolah[$dataTgl], 0).'</td>';
            $tab .= '<td align=right>'.$dtJmoer[$dataTgl].'</td>';
            $tab .= '<td align=right>'.$dtJmoerpk[$dataTgl].'</td>';
            $tab .= '<td>'.$optNmorg[$dtStation[$dataTgl][$aer]].'</td>';
            $tab .= '<td>'.$optNmorg[$dtMesin[$dataTgl][$aer]].'</td>';
            $tab .= '<td align=right>'.$dtJamOperasi[$dataTgl][$aer].'</td>';
            $tab .= '<td align=right>'.$dtJamSlsi[$dataTgl][$aer].'</td>';
            $tab .= '<td align=right>'.$dtJamStag[$dataTgl][$aer].'</td>';
            $tab .= '<td>'.$dtKet[$dataTgl][$aer].'</td>';
            $tab .= '<td>'.$dtprestasi[$dataTgl][$aer].'</td>';
            $tab .= '<td>'.$optNmBrg[$dtKdBrg[$dataTgl][$aer]].'</td>';
            $tab .= '<td align=right>'.$dtJmlh[$dataTgl][$aer].'</td>';
            $tab .= '<td>'.$optSat[$dtKdBrg[$dataTgl][$aer]].'</td>';
            $tab .= '<td align=right>'.number_format($optHrg[$dtKdBrg[$dataTgl][$aer]], 2).'</td>';
            $totalHrg[$dtKdBrg[$dataTgl][$aer]] = $dtJmlh[$dataTgl][$aer] * $optHrg[$dtKdBrg[$dataTgl][$aer]];
            $tab .= '<td align=right>'.number_format($totalHrg[$dtKdBrg[$dataTgl][$aer]], 2).'</td>';
            $tab .= '</tr>';
        } else {
            for ($aer = 1; $aer <= $jmlhRow[$dataTgl]; ++$aer) {
                $tab .= '<tr class=rowcontent>';
                if (1 === $aer) {
                    $tab .= '<td>'.$dataTgl.'</td>';
                    $tab .= '<td align=right>'.$dtJmstag[$dataTgl].'</td>';
                    $tab .= '<td align=right>'.$dtJmBruto[$dataTgl].'</td>';
                    $tab .= '<td align=right>'.$dtJmLori[$dataTgl].'</td>';
                    $tab .= '<td align=right>'.number_format($dtJmTbsDiolah[$dataTgl], 0).'</td>';
                    $tab .= '<td align=right>'.$dtJmoer[$dataTgl].'</td>';
                    $tab .= '<td align=right>'.$dtJmoerpk[$dataTgl].'</td>';
                } else {
                    if (2 === $aer) {
                        $tab .= "<td rowspan='".($jmlhRow[$dataTgl] - 1)."' colspan=7>&nbsp;</td>";
                    }
                }

                $tab .= '<td>'.$optNmorg[$dtStation[$dataTgl][$aer]].'</td>';
                $tab .= '<td>'.$optNmorg[$dtMesin[$dataTgl][$aer]].'</td>';
                $tab .= '<td align=right>'.$dtJamOperasi[$dataTgl][$aer].'</td>';
                $tab .= '<td align=right>'.$dtJamSlsi[$dataTgl][$aer].'</td>';
                $tab .= '<td align=right>'.$dtJamStag[$dataTgl][$aer].'</td>';
                $tab .= '<td>'.$dtKet[$dataTgl][$aer].'</td>';
                $tab .= '<td>'.$dtprestasi[$dataTgl][$aer].'</td>';
                $tab .= '<td>'.$optNmBrg[$dtKdBrg[$dataTgl][$aer]].'</td>';
                $tab .= '<td align=right>'.$dtJmlh[$dataTgl][$aer].'</td>';
                $tab .= '<td>'.$optSat[$dtKdBrg[$dataTgl][$aer]].'</td>';
                $tab .= '<td align=right>'.number_format($optHrg[$dtKdBrg[$dataTgl][$aer]], 2).'</td>';
                $totalHrg[$dtKdBrg[$dataTgl][$aer]] = $dtJmlh[$dataTgl][$aer] * $optHrg[$dtKdBrg[$dataTgl][$aer]];
                $tab .= '<td align=right>'.number_format($totalHrg[$dtKdBrg[$dataTgl][$aer]], 2).'</td>';
                $tab .= '</tr>';
            }
        }
    }
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'LaporanPengolahan';
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
    case 'getPeriode':
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct left(tanggal,7) as periode from '.$dbname.".pabrik_pengolahan \r\n                   where kodeorg='".$kdPabrik."' order by tanggal desc";
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= "<option value='".$rPeriode['periode']."'>".$rPeriode['periode'].'</option>';
        }
        echo $optPeriode;

        break;
    default:
        break;
}

?>
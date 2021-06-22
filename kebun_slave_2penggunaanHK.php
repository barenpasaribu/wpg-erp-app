<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
require_once 'lib/devLibrary.php';
$proses=$_GET['proses'];
$kodeOrg='';
$periode='';
if (!empty($_POST)){
    $kodeOrg=$_POST['kdUnit'];
    $periode=$_POST['periode'];
} else {
    $kodeOrg=$_GET['kdUnit'];
    $periode=$_GET['periode'];
}
//('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
//('' === $_POST['kdUnit'] ? ($kodeOrg = $_GET['kdUnit']) : ($kodeOrg = $_POST['kdUnit']));
//('' === $_POST['periode'] ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
//$where = " kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."'";
$arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
if ('EN' === $_SESSION['language']) {
    $zz = 'namakegiatan1';
} else {
    $zz = 'namakegiatan1';
}

$optNmkeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,'.$zz);
$optSatkeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
if ('' === $kodeOrg || '' === $periode) {
    exit('Error: All field required');
}

$bln = explode('-', $periode);
$blnLalu = (int) ($bln[1]);
if (1 === $blnLalu) {
    $blnLalu = 12;
    $thnLalu = (int) ($bln[0]) - 1;
} else {
    $blnLalu = $blnLalu - 1;
    if ($blnLalu < 10) {
        $blnLalu = '0'.$blnLalu;
    }

    $thnLalu = (int) ($bln[0]);
}

$arrDtBlnLalu = [];
$hslBlnIni = 'select kodeorg,kodekegiatan,namakegiatan,SUM(hasilkerja) as hasilkerja from '.$dbname.".kebun_perawatan_dan_spk_vw \r\n           where tanggal like '".$periode."%' and kodeorg like '".$kodeOrg."%' group by kodeorg,kodekegiatan\r\n           order by kodekegiatan asc";
$qhslBlnIni = mysql_query($hslBlnIni) ;
while ($rhslBlnini = mysql_fetch_assoc($qhslBlnIni)) {
    if ('' !== $rhslBlnini['kodekegiatan']) {
        $cekKeg[$rhslBlnini['kodekegiatan']][$rhslBlnini['kodeorg']] = 0;
        if (0 !== $rhslBlnini['hasilkerja'] || '' !== $rhslBlnini['hasilkerja']) {
            ++$jmlh[$rhslBlnini['kodekegiatan']];
            $cekKeg[$rhslBlnini['kodekegiatan']][$rhslBlnini['kodeorg']] = 1;
            $arrBlok[$rhslBlnini['kodeorg']] = $rhslBlnini['kodeorg'];
            $arrKeg[$rhslBlnini['kodekegiatan']] = $rhslBlnini['kodekegiatan'];
            $arrDtBlnIni[$rhslBlnini['kodekegiatan']][$rhslBlnini['kodeorg']] = $rhslBlnini['hasilkerja'];
        }
    }
}
$shkBlnIni = 'select kodeorg, kodekegiatan,tipekaryawan,count(karyawanid) as jhk from '.$dbname.".kebun_kehadiran_vw \r\n           where tanggal like '".$periode."%' and kodeorg like '".$kodeOrg."%' group by kodeorg,kodekegiatan,tipekaryawan\r\n           order by kodekegiatan asc";
$qhkBlnIni = mysql_query($shkBlnIni) ;
$arrHk=[];
while ($rhkBlnini = mysql_fetch_assoc($qhkBlnIni)) {
    $cekHk[$rhkBlnini['kodekegiatan']][$rhkBlnini['kodeorg']] = 0;
    if ('' !== $rhkBlnini['kodekegiatan']) {
        $cekHk[$rhkBlnini['kodekegiatan']][$rhkBlnini['kodeorg']] = 1;
        $arrBlok[$rhkBlnini['kodeorg']] = $rhkBlnini['kodeorg'];
        $arrKeg[$rhkBlnini['kodekegiatan']] = $rhkBlnini['kodekegiatan'];
        $arrHk[$rhkBlnini['kodekegiatan'].$rhkBlnini['kodeorg']][$rhkBlnini['tipekaryawan']] += $rhkBlnini['jhk'];
        $arrHk[$rhkBlnini['kodekegiatan'].$rhkBlnini['kodeorg']][$rhkBlnini['tipekaryawan']] += $rhkBlnini['jhk'];
    }
}
$sHslBlnLalu = 'select kodeorg,kodekegiatan,namakegiatan,sum(hasilkerja) as hasilkerja from '.$dbname.".kebun_perawatan_dan_spk_vw \r\n              where tanggal like '".$thnLalu.'-'.$blnLalu."%' and kodeorg like '".$kodeOrg."%' group by kodeorg,kodekegiatan\r\n              order by kodekegiatan asc";
$qHslBlnLalu = mysql_query($sHslBlnLalu) ;
while ($rHslBlnLalu = mysql_fetch_assoc($qHslBlnLalu)) {
    if ('' !== $rHslBlnLalu['kodekegiatan']) {
        $cekKeg[$rHslBlnLalu['kodekegiatan']][$rHslBlnLalu['kodeorg']] = 0;
        if (0 !== $rHslBlnLalu['hasilkerja'] || '' !== $rHslBlnLalu['hasilkerja']) {
            $cekKeg[$rHslBlnLalu['kodekegiatan']][$rHslBlnLalu['kodeorg']] = 1;
            $arrBlok[$rHslBlnLalu['kodeorg']] = $rHslBlnLalu['kodeorg'];
            $arrKeg[$rHslBlnLalu['kodekegiatan']] = $rHslBlnLalu['kodekegiatan'];
            $arrHslKrjBlnLalu[$rHslBlnLalu['kodekegiatan']][$rHslBlnLalu['kodeorg']] = $rHslBlnLalu['hasilkerja'];
        }
    }
}
$shkBlnLalu = 'select kodeorg, kodekegiatan,tipekaryawan,count(karyawanid) as jhk from '.$dbname.".kebun_kehadiran_vw \r\n            where tanggal like '".$thnLalu.'-'.$blnLalu."%' and kodeorg like '".$kodeOrg."%' group by kodeorg,kodekegiatan,tipekaryawan\r\n            order by kodekegiatan asc";
$qhkBlnLalu = mysql_query($shkBlnLalu) ;
$arrHkLalu=[];
while ($rhkBlnLalu = mysql_fetch_assoc($qhkBlnLalu)) {
    $cekHk[$rhkBlnLalu['kodekegiatan']][$rhkBlnLalu['kodeorg']] = 0;
    if ('' !== $rhkBlnLalu['kodekegiatan']) {
        $cekHk[$rhkBlnLalu['kodekegiatan']][$rhkBlnLalu['kodeorg']] = 1;
        $arrBlok[$rhkBlnLalu['kodeorg']] = $rhkBlnLalu['kodeorg'];
        $arrKeg[$rhkBlnLalu['kodekegiatan']] = $rhkBlnLalu['kodekegiatan'];
        $arrHkLalu[$rhkBlnLalu['kodekegiatan'].$rhkBlnLalu['kodeorg']][$rhkBlnLalu['tipekaryawan']] += $rhkBlnLalu['jhk'];
    }
}
$sPanenHslKrj = 'select kodeorg,sum(hasilkerja) as hasilkerja from '.$dbname.".kebun_prestasi_vw \r\n               where kodeorg like '%".$kodeOrg."%' and tanggal like '%".$periode."%' group by kodeorg ";
$qPanenHslKrj = mysql_query($sPanenHslKrj) ;
while ($rPanenHslKrj = mysql_fetch_assoc($qPanenHslKrj)) {
    $panenHslBln[$rPanenHslKrj['kodeorg']] = $rPanenHslKrj['hasilkerja'];
    $lstKodeorg[$rPanenHslKrj['kodeorg']] = $rPanenHslKrj['kodeorg'];
}
$sHkPanenBlnIni = 'select kodeorg,tipekaryawan, count(karyawanid) as hasilkerja from '.$dbname.".kebun_prestasi_vw \r\n                where kodeorg like '%".$kodeOrg."%' and tanggal like '%".$periode."%' group by kodeorg,tipekaryawan";
$qHkPanenBlnIni = mysql_query($sHkPanenBlnIni) ;
$hkBln=[];
while ($rHkPanenBlnIni = mysql_fetch_assoc($qHkPanenBlnIni)) {
    $lstKodeorg[$rHkPanenBlnIni['kodeorg']] = $rHkPanenBlnIni['kodeorg'];
    $hkBln[$rHkPanenBlnIni['kodeorg']][$rHkPanenBlnIni['tipekaryawan']] += $rHkPanenBlnIni['hasilkerja'];
}
$sPanenBlnLalu = 'select kodeorg,sum(hasilkerja) as hasilkerja from '.$dbname.".kebun_prestasi_vw \r\n                where kodeorg like '%".$kodeOrg."%' and tanggal like '".$thnLalu.'-'.$blnLalu."%' group by kodeorg";
$qPanenBlnLalu = mysql_query($sPanenBlnLalu) ;
while ($rPanenBlnLalu = mysql_fetch_assoc($qPanenBlnLalu)) {
    $lstKodeorg[$rPanenBlnLalu['kodeorg']] = $rPanenBlnLalu['kodeorg'];
    $panenHslBlnLalu[$rPanenBlnLalu['kodeorg']] = $rPanenBlnLalu['hasilkerja'];
}
$sHkBlnLalu = 'select kodeorg,tipekaryawan, count(karyawanid) as hasilkerja from '.$dbname.".kebun_prestasi_vw \r\n             where kodeorg like '%".$kodeOrg."%' and tanggal like '".$thnLalu.'-'.$blnLalu."%' group by kodeorg,tipekaryawan";
$qHkBlnLalu = mysql_query($sHkBlnLalu) || exit(mysql_error($sHkBlnLalu));
$hkBlnLalu=[];
while ($rHkBlnLalu = mysql_fetch_assoc($qHkBlnLalu)) {
    $lstKodeorg[$rHkBlnLalu['kodeorg']] = $rHkBlnLalu['kodeorg'];
    $hkBlnLalu[$rHkBlnLalu['kodeorg']][$rHkBlnLalu['tipekaryawan']] += $rHkBlnLalu['hasilkerja'];
}
$brd = 0;
if ('excel' === $proses) {
    $brd = 1;
    $bg = 'bgcolor=#DEDEDE';
}

$dtblokpertama = count($arrKeg);
$dtblokkedua = count($lstKodeorg);
$tab .= $_SESSION['lang']['jhk'].' '.$_SESSION['lang']['pemeltanaman'];
$tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable><thead>';
$tab .= '<tr class=rowheader>';
$tab .= '<td rowspan=3 align=center '.$bg.'>'.$_SESSION['lang']['kodekegiatan'].'</td>';
$tab .= '<td rowspan=3 align=center '.$bg.'>'.$_SESSION['lang']['kegiatan'].'</td>';
$tab .= '<td rowspan=3 align=center '.$bg.'>'.$_SESSION['lang']['blok'].'</td>';
$tab .= '<td colspan=9 align=center '.$bg.'>'.$_SESSION['lang']['blnini'].'</td>';
$tab .= '<td colspan=9 align=center '.$bg.'>'.$_SESSION['lang']['blnlalu'].'</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowheader>';
$tab .= '<td colspan=2 align=center '.$bg.'>'.$_SESSION['lang']['hasilkerjajumlah'].'</td>';
$tab .= '<td colspan=6 align=center '.$bg.'>'.$_SESSION['lang']['jumlahhk'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bg.'>'.$_SESSION['lang']['jumlahhk'].'/'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td colspan=2 align=center '.$bg.'>'.$_SESSION['lang']['hasilkerjajumlah'].'</td>';
$tab .= '<td colspan=6 align=center '.$bg.'>'.$_SESSION['lang']['jumlahhk'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bg.'>'.$_SESSION['lang']['jumlahhk'].'/'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowheader>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['jumlah'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['bulanan'].'</td>';
$tab .= '<td align=center '.$bg.'>ORGANIK</td>';
$tab .= '<td align=center '.$bg.'>SKU</td>';
$tab .= '<td align=center '.$bg.'>SKUP</td>';
$tab .= '<td align=center '.$bg.'>PKWT</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['total'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['jumlah'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['bulanan'].'</td>';
$tab .= '<td align=center '.$bg.'>ORGANIK</td>';
$tab .= '<td align=center '.$bg.'>SKU</td>';
$tab .= '<td align=center '.$bg.'>SKUP</td>';
$tab .= '<td align=center '.$bg.'>PKWT</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['total'].'</td>';
$tab .= '</tr><thead><tbody>';
$ardet='';
if (0 !== $dtblokpertama) {
    foreach ($arrKeg as $dtKeg) {
        foreach ($arrBlok as $dtBlok) {
            if (0 !== $arrDtBlnIni[$dtKeg][$dtBlok] || 0 !== $arrHk[$dtKeg.$dtBlok][ORGANIK] || 0 !== $arrHk[$dtKeg.$dtBlok][SKU] || 0 !== $arrHk[$dtKeg.$dtBlok][SKUP] || 0 !== $arrHk[$dtKeg.$dtBlok][PKWT]) {
                if ($ardet !== $dtKeg) {
                    $bars = 0;
                    $ardet = $dtKeg;
                }

                ++$bars;
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$dtKeg.'</td>';
                $tab .= '<td>'.$optNmkeg[$dtKeg].'</td>';
                $tab .= '<td>'.$dtBlok.'</td>';
                $tab .= '<td align=center>'.$optSatkeg[$dtKeg].'</td>';
                $tab .= '<td align=right>'.number_format($arrDtBlnIni[$dtKeg][$dtBlok], 2).'</td>';
                $tab .= '<td align=right>'.number_format($arrHk[$dtKeg.$dtBlok][ORGANIK], 0).'</td>';
                $tab .= '<td align=right>'.number_format($arrHk[$dtKeg.$dtBlok][SKU], 0).'</td>';
                $tab .= '<td align=right>'.number_format($arrHk[$dtKeg.$dtBlok][SKUP], 0).'</td>';
                $tab .= '<td align=right>'.number_format($arrHk[$dtKeg.$dtBlok][PKWT], 0).'</td>';
                $tab .= '<td align=right>'.number_format($arrHk[$dtKeg.$dtBlok]['Kontrak Karywa (Usia Lanjut)'], 0).'</td>';
                $totSub[$dtKeg][$dtBlok] = $arrHk[$dtKeg.$dtBlok][ORGANIK] + $arrHk[$dtKeg.$dtBlok][SKU] + $arrHk[$dtKeg.$dtBlok][SKUP] + $arrHk[$dtKeg.$dtBlok][PKWT] + $arrHk[$dtKeg.$dtBlok]['Kontrak Karywa (Usia Lanjut)'];
                $hkSatuan[$dtKeg][$dtBlok] = $totSub[$dtKeg][$dtBlok] / $arrDtBlnLalu[$dtKeg][$dtBlok];
                $tab .= '<td align=right>'.number_format($totSub[$dtKeg][$dtBlok], 0).'</td>';
                $tab .= '<td align=right>'.number_format($hkSatuan[$dtKeg][$dtBlok], 2).'</td>';
                $tab .= '<td align=center>'.$optSatkeg[$dtKeg].'</td>';
                $tab .= '<td align=right>'.number_format($arrHslKrjBlnLalu[$dtKeg][$dtBlok], 2).'</td>';
                $tab .= '<td align=right>'.number_format($arrHkLalu[$dtKeg.$dtBlok][ORGANIK], 0).'</td>';
                $tab .= '<td align=right>'.number_format($arrHkLalu[$dtKeg.$dtBlok][SKU], 0).'</td>';
                $tab .= '<td align=right>'.number_format($arrHkLalu[$dtKeg.$dtBlok][SKUP], 0).'</td>';
                $tab .= '<td align=right>'.number_format($arrHkLalu[$dtKeg.$dtBlok][PKWT], 0).'</td>';
                $tab .= '<td align=right>'.number_format($arrHkLalu[$dtKeg.$dtBlok]['Kontrak Karywa (Usia Lanjut)'], 0).'</td>';
                $totSubLalu[$dtKeg][$dtBlok] = $arrHkLalu[$dtKeg.$dtBlok][ORGANIK] + $arrHkLalu[$dtKeg.$dtBlok][SKU] + $arrHkLalu[$dtKeg.$dtBlok][SKUP] + $arrHkLalu[$dtKeg.$dtBlok][PKWT] + $arrHkLalu[$dtKeg.$dtBlok]['PKWT'];
                $hkSatuanLalu[$dtKeg][$dtBlok] = $totSubLalu[$dtKeg][$dtBlok] / $arrHslKrjBlnLalu[$dtKeg][$dtBlok];
                $tab .= '<td align=right>'.number_format($totSubLalu[$dtKeg][$dtBlok], 0).'</td>';
                $tab .= '<td align=right>'.number_format($hkSatuanLalu[$dtKeg][$dtBlok], 2).'</td>';
                $tab .= '</tr>';
                $sbHasil[$dtKeg] += $arrDtBlnIni[$dtKeg][$dtBlok];
                $sbKbl[$dtKeg] += $arrHk[$dtKeg.$dtBlok][ORGANIK];
                $sbKht[$dtKeg] += $arrHk[$dtKeg.$dtBlok][SKU];
                $sbKhl[$dtKeg] += $arrHk[$dtKeg.$dtBlok][SKUP];
                $sbKontrak[$dtKeg] += $arrHk[$dtKeg.$dtBlok][PKWT];
                $sbKkarya[$dtKeg] += $arrHk[$dtKeg.$dtBlok]['Kontrak Karywa (Usia Lanjut)'];
                $stotSub[$dtKeg] += $totSub[$dtKeg][$dtBlok];
                $sHksat[$dtKeg] += $hkSatuan[$dtKeg][$dtBlok];
                $sbHasilL[$dtKeg] += $arrHslKrjBlnLalu[$dtKeg][$dtBlok];
                $sbKblL[$dtKeg] += $arrHkLalu[$dtKeg.$dtBlok][ORGANIK];
                $sbKhtL[$dtKeg] += $arrHkLalu[$dtKeg.$dtBlok][SKU];
                $sbKhlL[$dtKeg] += $arrHkLalu[$dtKeg.$dtBlok][SKUP];
                $sbKontrakL[$dtKeg] += $arrHkLalu[$dtKeg.$dtBlok][PKWT];
                $sbKkaryaL[$dtKeg] += $arrHkLalu[$dtKeg.$dtBlok]['Kontrak Karywa (Usia Lanjut)'];
                $stotSubL[$dtKeg] += $totSubLalu[$dtKeg][$dtBlok];
                $sHksatL[$dtKeg] += $hkSatuanLalu[$dtKeg][$dtBlok];
                if ($bars === $jmlh[$dtKeg]) {
                    $tab .= '<tr class=rowcontent><td colspan=4>'.$_SESSION['lang']['subtotal'].'</td>';
                    $tab .= '<td align=right>'.number_format($sbHasil[$dtKeg], 2).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKbl[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKht[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKhl[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKontrak[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKkarya[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($stotSub[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sHksat[$dtKeg], 0).'</td>';
                    $tab .= '<td>&nbsp;</td>';
                    $tab .= '<td align=right>'.number_format($sbHasilL[$dtKeg], 2).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKblL[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKhtL[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKhlL[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKontrakL[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sbKkaryaL[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($stotSubL[$dtKeg], 0).'</td>';
                    $tab .= '<td align=right>'.number_format($sHksatL[$dtKeg], 0).'</td></tr>';
                }
            }
        }
    }
} else {
    $tab .= '<tr class=rowcontent><td colspan=19>'.$_SESSION['lang']['dataempty'].'</td></tr>';
}


$tab .= '</tbody></table>'.$ard.'<br />';
$tab .= $_SESSION['lang']['jhk'].' '.$_SESSION['lang']['panen'];
$tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable><thead>';
$tab .= '<tr class=rowheader>';
$tab .= '<td rowspan=3 align=center '.$bg.'>'.$_SESSION['lang']['blok'].'</td>';
$tab .= '<td rowspan=3 align=center '.$bg.'>'.$_SESSION['lang']['kodekegiatan'].'</td>';
$tab .= '<td rowspan=3 align=center '.$bg.'>'.$_SESSION['lang']['kegiatan'].'</td>';
$tab .= '<td colspan=9 align=center '.$bg.'>'.$_SESSION['lang']['blnini'].'</td>';
$tab .= '<td colspan=9 align=center '.$bg.'>'.$_SESSION['lang']['blnlalu'].'</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowheader>';
$tab .= '<td colspan=2 align=center '.$bg.'>'.$_SESSION['lang']['hasilkerjajumlah'].'</td>';
$tab .= '<td colspan=6 align=center '.$bg.'>'.$_SESSION['lang']['jumlahhk'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bg.'>'.$_SESSION['lang']['jumlahhk'].'/'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td colspan=2 align=center '.$bg.'>'.$_SESSION['lang']['hasilkerjajumlah'].'</td>';
$tab .= '<td colspan=6 align=center '.$bg.'>'.$_SESSION['lang']['jumlahhk'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bg.'>'.$_SESSION['lang']['jumlahhk'].'/'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '</tr>';
$tab .= '<tr class=rowheader>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['jumlah'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['bulanan'].'</td>';
$tab .= '<td align=center '.$bg.'>KHT</td>';
$tab .= '<td align=center '.$bg.'>KHL</td>';
$tab .= '<td align=center '.$bg.'>KONTRAK</td>';
$tab .= '<td align=center '.$bg.'>Kontrak Karywa (Usia Lanjut)</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['total'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['jumlah'].'</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['bulanan'].'</td>';
$tab .= '<td align=center '.$bg.'>KHT</td>';
$tab .= '<td align=center '.$bg.'>KHL</td>';
$tab .= '<td align=center '.$bg.'>KONTRAK</td>';
$tab .= '<td align=center '.$bg.'>Kontrak Karywa (Usia Lanjut)</td>';
$tab .= '<td align=center '.$bg.'>'.$_SESSION['lang']['total'].'</td>';
$tab .= '</tr><thead><tbody>';


if (0 !== $dtblokkedua) {
    foreach ($lstKodeorg as $dtBlok2) {
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$dtBlok2.'</td>';
        $tab .= '<td>611010101</td>';
        $tab .= '<td>'.$optNmkeg[611010101].'</td>';
        $tab .= '<td align=center>'.$optSatkeg[611010101].'</td>';
        $tab .= '<td align=right>'.number_format($panenHslBln[$dtBlok2], 2).'</td>';
        $tab .= '<td align=right>'.number_format($hkBln[$dtBlok2][ORGANIK], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkBln[$dtBlok2][SKU], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkBln[$dtBlok2][SKUP], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkBln[$dtBlok2][PKWT], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkBln[$dtBlok2]['Kontrak Karywa (Usia Lanjut)'], 0).'</td>';
        $totSubPanen[$dtBlok2] = $hkBln[$dtBlok2][ORGANIK] + $hkBln[$dtBlok2][SKU] + $hkBln[$dtBlok2][SKUP] + $hkBln[$dtBlok2][PKWT] + $hkBln[$dtBlok2]['Kontrak Karywa (Usia Lanjut)'];
        $hkSatuanPanen[$dtBlok2] = $totSub[$dtBlok2] / $panenHslBln[$dtBlok2];
        $tab .= '<td align=right>'.number_format($totSubPanen[$dtBlok2], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkSatuanPanen[$dtBlok2], 2).'</td>';
        $tab .= '<td align=center>'.$optSatkeg[611010101].'</td>';
        $tab .= '<td align=right>'.number_format($panenHslBlnLalu[$dtBlok2], 2).'</td>';
        $tab .= '<td align=right>'.number_format($hkBlnLalu[$dtBlok2][ORGANIK], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkBlnLalu[$dtBlok2][SKU], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkBlnLalu[$dtBlok2][SKUP], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkBlnLalu[$dtBlok2][PKWT], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkBlnLalu[$dtBlok2]['Kontrak Karywa (Usia Lanjut)'], 0).'</td>';
        $totSubPanenLalu[$dtBlok2] = $hkBlnLalu[$dtBlok2][ORGANIK] + $hkBlnLalu[$dtBlok2][SKU] + $hkBlnLalu[$dtBlok2][SKUP] + $hkBlnLalu[$dtBlok2][PKWT] + $hkBlnLalu[$dtBlok2]['Kontrak Karywa (Usia Lanjut)'];
        $hkSatuanPanenLalu[$dtBlok2] = $totSubLalu[$dtBlok2] / $panenHslBlnLalu[$dtBlok2];
        $tab .= '<td align=right>'.number_format($totSubPanenLalu[$dtBlok2], 0).'</td>';
        $tab .= '<td align=right>'.number_format($hkSatuanPanenLalu[$dtBlok2], 2).'</td>';
        $tab .= '</tr>';
    }
} else {
    $tab .= '<tr class=rowcontent><td colspan=21>'.$_SESSION['lang']['dataempty'].'</td></tr>';
}
$tab .= '</tbody></table>';
//echoMessage('tab ',$tab);
switch ($proses) {
    case 'preview':
        echo $tab;
        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $dte = date('His');
        $nop_ = 'laporanPenggunaanHk_'.$dte;
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
                echo "<script language=javascript1.2>\r\n                    parent.window.alert('Can\'t convert to excel format');\r\n                    </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                    window.location='tempExcel/".$nop_.".xls';\r\n                    </script>";
            closedir($handle);
        }

        break;
    case 'pdf':
        if ('' === $kodeOrg || '' === $periode) {
            exit('Error:All fields are reqired');
        }
//        generateTablePDF($tab,true,'Legal','landscape');
//        exit();
        try {
            class PDF extends FPDF
            {
                public function Header()
                {
                    global $dtThnBudget;
                    global $dtKdunit;
                    global $dtJmlhKg;
                    global $dtJjg;
                    global $dtJmlhLuas;
                    global $totKg;
                    global $totJjg;
                    global $totLuas;
                    global $dbname;
                    global $optNm;
                    global $kodeOrg;
                    global $totalUnit;
                    global $modPil;
                    global $spanLt;
                    global $dtJmlhThnTnm;
                    global $totaThntnm;
                    global $arrBln;
                    $sAlmat = 'select namaorganisasi,alamat,telepon from ' . $dbname . ".organisasi where kodeorganisasi='" . $_SESSION['org']['kodeorganisasi'] . "'";
                    $qAlamat = mysql_query($sAlmat);
                    $rAlamat = mysql_fetch_assoc($qAlamat);
                    $width = $this->w - $this->lMargin - $this->rMargin;
                    $height = 10;
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

                    $this->Image($path, $this->lMargin, $this->tMargin, 70);
                    $this->SetFont('Arial', 'B', 9);
                    $this->SetFillColor(255, 255, 255);
                    $this->SetX(100);
                    $this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
                    $this->SetX(100);
                    $this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
                    $this->SetX(100);
                    $this->Cell($width - 100, $height, 'Tel: ' . $rAlamat['telepon'], 0, 1, 'L');
                    $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
                    $this->Ln();
                    $this->Ln();
                    $this->Ln();
                }

                public function Footer()
                {
                    $this->SetY(-15);
                    $this->SetFont('Arial', 'I', 8);
                    $this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
                }
            }

            $pdf = new PDF('L', 'pt', 'A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 15;
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell($width, $height, strtoupper($_SESSION['lang']['penggunaanhk']), 0, 1, 'C');
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$kodeOrg], 0, 1, 'C');
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(650, $height, $_SESSION['lang']['tanggal'], 0, 0, 'R');
            $pdf->Cell(10, $height, ':', '', 0, 0, 'R');
            $pdf->Cell(70, $height, date('d-m-Y H:i'), 0, 1, 'R');
            $pdf->Cell(650, $height, 'User', 0, 0, 'R');
            $pdf->Cell(10, $height, ':', '', 0, 0, 'R');
            $pdf->Cell(70, $height, $_SESSION['standard']['username'], 0, 1, 'R');
            $pdf->ln(18);
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Cell(85, $height, 'HK Perawatan', 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(65, $height, $_SESSION['lang']['kodekegiatan'], 'TLR', 0, 'C', 1);
            $pdf->Cell(165, $height, $_SESSION['lang']['kegiatan'], 'TLR', 0, 'C', 1);
            $pdf->Cell(55, $height, $_SESSION['lang']['blok'], 'TLR', 0, 'C', 1);
            $pdf->Cell(260, $height, $_SESSION['lang']['blnini'], 'TLR', 0, 'C', 1);
            $pdf->Cell(260, $height, $_SESSION['lang']['blnlalu'], 'TLR', 1, 'C', 1);
            $pdf->Cell(65, $height, ' ', 'LR', 0, 'C', 1);
            $pdf->Cell(165, $height, ' ', 'LR', 0, 'C', 1);
            $pdf->Cell(55, $height, ' ', 'LR', 0, 'C', 1);
            $pdf->Cell(70, $height, $_SESSION['lang']['hasilkerjajumlah'], 'TLR', 0, 'C', 1);
            $pdf->Cell(145, $height, $_SESSION['lang']['jumlahhk'], 'TLR', 0, 'C', 1);
            $pdf->Cell(45, $height, $_SESSION['lang']['jumlahhk'] . '/' . $_SESSION['lang']['satuan'], 'TLR', 0, 'C', 1);
            $pdf->Cell(70, $height, $_SESSION['lang']['hasilkerjajumlah'], 'TLR', 0, 'C', 1);
            $pdf->Cell(145, $height, $_SESSION['lang']['jumlahhk'], 'TLR', 0, 'C', 1);
            $pdf->Cell(45, $height, $_SESSION['lang']['jumlahhk'] . '/' . $_SESSION['lang']['satuan'], 'TLR', 1, 'C', 1);
            $pdf->Cell(65, $height, ' ', 'BLR', 0, 'C', 1);
            $pdf->Cell(165, $height, ' ', 'BLR', 0, 'C', 1);
            $pdf->Cell(55, $height, ' ', 'BLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['satuan'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(40, $height, $_SESSION['lang']['jumlah'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['bulanan'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(25, $height, 'KHT', 'TBLR', 0, 'C', 1);
            $pdf->Cell(25, $height, 'KHL', 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['kontrak'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, 'KKarya', 'TBLR', 0, 'C', 1);
            $pdf->Cell(35, $height, $_SESSION['lang']['total'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(45, $height, ' ', 'BLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['satuan'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(40, $height, $_SESSION['lang']['jumlah'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['bulanan'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(25, $height, 'KHT', 'TBLR', 0, 'C', 1);
            $pdf->Cell(25, $height, 'KHL', 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['kontrak'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, 'KKarya', 'TBLR', 0, 'C', 1);
            $pdf->Cell(35, $height, $_SESSION['lang']['total'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(45, $height, ' ', 'BLR', 1, 'C', 1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 6);
            if (0 !== $dtblokpertama) {
                foreach ($arrKeg as $dtKeg) {
                    foreach ($arrBlok as $dtBlok) {
                        if (0 !== $arrDtBlnIni[$dtKeg][$dtBlok] || 0 !== $arrHk[$dtKeg . $dtBlok][ORGANIK] || 0 !== $arrHk[$dtKeg . $dtBlok][SKU] || 0 !== $arrHk[$dtKeg . $dtBlok][SKUP] || 0 !== $arrHk[$dtKeg . $dtBlok][PKWT]) {
                            if ($ardet !== $dtKeg) {
                                $bars = 0;
                                $ardet = $dtKeg;
                            }

                            $pdf->Cell(65, $height, $dtKeg, 'TBLR', 0, 'C', 1);
                            $pdf->Cell(165, $height, $optNmkeg[$dtKeg], 'TBLR', 0, 'L', 1);
                            $pdf->Cell(55, $height, $dtBlok, 'TBLR', 0, 'L', 1);
                            $pdf->Cell(30, $height, $optSatkeg[$dtKeg], 'TBLR', 0, 'C', 1);
                            $pdf->Cell(40, $height, number_format($arrDtBlnIni[$dtKeg][$dtBlok], 2), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(30, $height, number_format($arrHk[$dtKeg . $dtBlok][ORGANIK], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(25, $height, number_format($arrHk[$dtKeg . $dtBlok][SKU], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(25, $height, number_format($arrHk[$dtKeg . $dtBlok][SKUP], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(30, $height, number_format($arrHk[$dtKeg . $dtBlok][PKWT], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(30, $height, number_format($arrHk[$dtKeg . $dtBlok]['Kontrak Karywa (Usia Lanjut)'], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(35, $height, number_format($totSub[$dtKeg][$dtBlok], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(45, $height, number_format($hkSatuan[$dtKeg][$dtBlok], 2), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(30, $height, $optSatkeg[$dtKeg], 'TBLR', 0, 'C', 1);
                            $pdf->Cell(40, $height, number_format($arrHslKrjBlnLalu[$dtKeg][$dtBlok], 2), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(30, $height, number_format($arrHkLalu[$dtKeg . $dtBlok][ORGANIK], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(25, $height, number_format($arrHkLalu[$dtKeg . $dtBlok][SKU], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(25, $height, number_format($arrHkLalu[$dtKeg . $dtBlok][SKUP], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(30, $height, number_format($arrHkLalu[$dtKeg . $dtBlok][PKWT], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(30, $height, number_format($arrHkLalu[$dtKeg . $dtBlok]['Kontrak Karywa (Usia Lanjut)'], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(35, $height, number_format($totSubLalu[$dtKeg][$dtBlok], 0), 'TBLR', 0, 'R', 1);
                            $pdf->Cell(45, $height, number_format($hkSatuanLalu[$dtKeg][$dtBlok], 2), 'TBLR', 1, 'R', 1);
                            if ($bars === $jmlh[$dtKeg]) {
                                $pdf->Cell(315, $height, $_SESSION['lang']['subtotal'], 'TBLR', 0, 'C', 1);
                                $pdf->Cell(40, $height, number_format($sbHasil[$dtKeg], 2), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(30, $height, number_format($sbKbl[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(25, $height, number_format($sbKht[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(25, $height, number_format($sbKhl[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(30, $height, number_format($sbKontrak[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(30, $height, number_format($sbKkarya[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(35, $height, number_format($stotSub[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(45, $height, number_format($sHksat[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(30, $height, ' ', 'TBLR', 0, 'C', 1);
                                $pdf->Cell(40, $height, number_format($sbHasilL[$dtKeg], 2), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(30, $height, number_format($sbKblL[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(25, $height, number_format($sbKhtL[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(25, $height, number_format($sbKhlL[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(30, $height, number_format($sbKontrakL[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(30, $height, number_format($sbKkaryaL[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(35, $height, number_format($stotSubL[$dtKeg], 0), 'TBLR', 0, 'R', 1);
                                $pdf->Cell(45, $height, number_format($sHksatL[$dtKeg], 0), 'TBLR', 1, 'R', 1);
                            }
                        }
                    }
                }
            } else {
                $pdf->Cell(805, $height, $_SESSION['lang']['dataempty'], 'TBLR', 1, 'C', 1);
            }

            $pdf->ln(18);
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Cell(85, $height, 'HK Panen', 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(65, $height, $_SESSION['lang']['kodekegiatan'], 'TLR', 0, 'C', 1);
            $pdf->Cell(165, $height, $_SESSION['lang']['kegiatan'], 'TLR', 0, 'C', 1);
            $pdf->Cell(55, $height, $_SESSION['lang']['blok'], 'TLR', 0, 'C', 1);
            $pdf->Cell(260, $height, $_SESSION['lang']['blnini'], 'TLR', 0, 'C', 1);
            $pdf->Cell(260, $height, $_SESSION['lang']['blnlalu'], 'TLR', 1, 'C', 1);
            $pdf->Cell(65, $height, ' ', 'LR', 0, 'C', 1);
            $pdf->Cell(165, $height, ' ', 'LR', 0, 'C', 1);
            $pdf->Cell(55, $height, ' ', 'LR', 0, 'C', 1);
            $pdf->Cell(70, $height, $_SESSION['lang']['hasilkerjajumlah'], 'TLR', 0, 'C', 1);
            $pdf->Cell(145, $height, $_SESSION['lang']['jumlahhk'], 'TLR', 0, 'C', 1);
            $pdf->Cell(45, $height, $_SESSION['lang']['jumlahhk'] . '/' . $_SESSION['lang']['satuan'], 'TLR', 0, 'C', 1);
            $pdf->Cell(70, $height, $_SESSION['lang']['hasilkerjajumlah'], 'TLR', 0, 'C', 1);
            $pdf->Cell(145, $height, $_SESSION['lang']['jumlahhk'], 'TLR', 0, 'C', 1);
            $pdf->Cell(45, $height, $_SESSION['lang']['jumlahhk'] . '/' . $_SESSION['lang']['satuan'], 'TLR', 1, 'C', 1);
            $pdf->Cell(65, $height, ' ', 'BLR', 0, 'C', 1);
            $pdf->Cell(165, $height, ' ', 'BLR', 0, 'C', 1);
            $pdf->Cell(55, $height, ' ', 'BLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['satuan'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(40, $height, $_SESSION['lang']['jumlah'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['bulanan'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(25, $height, 'KHT', 'TBLR', 0, 'C', 1);
            $pdf->Cell(25, $height, 'KHL', 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['kontrak'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, 'KKarya', 'TBLR', 0, 'C', 1);
            $pdf->Cell(35, $height, $_SESSION['lang']['total'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(45, $height, ' ', 'BLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['satuan'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(40, $height, $_SESSION['lang']['jumlah'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['bulanan'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(25, $height, 'KHT', 'TBLR', 0, 'C', 1);
            $pdf->Cell(25, $height, 'KHL', 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, $_SESSION['lang']['kontrak'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(30, $height, 'KKarya', 'TBLR', 0, 'C', 1);
            $pdf->Cell(35, $height, $_SESSION['lang']['total'], 'TBLR', 0, 'C', 1);
            $pdf->Cell(45, $height, ' ', 'BLR', 1, 'C', 1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 6);
            if (0 !== $dtblokkedua) {
                foreach ($lstKodeorg as $dtBlok2) {
                    $pdf->Cell(65, $height, '611010101', 'TBLR', 0, 'C', 1);
                    $pdf->Cell(165, $height, $optNmkeg[611010101], 'TBLR', 0, 'L', 1);
                    $pdf->Cell(55, $height, $dtBlok2, 'TBLR', 0, 'L', 1);
                    $pdf->Cell(30, $height, $optSatkeg[611010101], 'TBLR', 0, 'C', 1);
                    $pdf->Cell(40, $height, number_format($panenHslBln[$dtBlok2], 2), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(30, $height, number_format($hkBln[$dtBlok2][ORGANIK], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(25, $height, number_format($hkBln[$dtBlok2][SKU], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(25, $height, number_format($hkBln[$dtBlok2][SKUP], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(30, $height, number_format($hkBln[$dtBlok2][PKWT], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(30, $height, number_format($hkBln[$dtBlok2]['Kontrak Karywa (Usia Lanjut)'], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(35, $height, number_format($totSubPanen[$dtBlok2], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(45, $height, number_format($hkSatuanPanen[$dtBlok2], 2), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(30, $height, $optSatkeg[611010101], 'TBLR', 0, 'C', 1);
                    $pdf->Cell(40, $height, number_format($panenHslBlnLalu[$dtBlok2], 2), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(30, $height, number_format($hkBlnLalu[$dtBlok2][ORGANIK], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(25, $height, number_format($hkBlnLalu[$dtBlok2][SKU], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(25, $height, number_format($hkBlnLalu[$dtBlok2][SKUP], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(30, $height, number_format($hkBlnLalu[$dtBlok2][PKWT], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(30, $height, number_format($hkBlnLalu[$dtBlok2]['Kontrak Karywa (Usia Lanjut)'], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(35, $height, number_format($totSubPanenLalu[$dtBlok2], 0), 'TBLR', 0, 'R', 1);
                    $pdf->Cell(45, $height, number_format($hkSatuanPanenLalu[$dtBlok2], 2), 'TBLR', 1, 'R', 1);
                }
            } else {
                $pdf->Cell(805, $height, $_SESSION['lang']['dataempty'], 'TBLR', 1, 'C', 1);
            }

            $pdf->Output();
        } catch(Exception $e){
            echoMessage('Error : ',$e->getMessage());
        }

        break;
    default:
        break;
}

?>
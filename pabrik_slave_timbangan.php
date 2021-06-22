<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$method = $_POST['method'];
$kdBrg = $_POST['kdBrg'];
$noTrans = $_POST['noTrans'];
$idCust = $_POST['idCust'];
$kdPbrk = $_POST['kdPbrk'];
$jmMasuk = explode(':', $_POST['jmMasuk']);
$jmKeluar = explode(':', $_POST['jmKeluar']);
$BuahStat = $_POST['BuahStat'];
$thntnm1 = $_POST['thntnm1'];
$thntnm2 = $_POST['thntnm2'];
$thntnm3 = $_POST['thntnm3'];
$kdKbn = $_POST['kdKbn'];
$statTmbngn = $_POST['statTmbngn'];
$txtSearch = $_POST['txtSearch'];
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if ('' !== $_POST['tglCari']) {
    $tglCari = explode('-', $_POST['tglCari']);
    $tglCari = $tglCari[2].'-'.$tglCari[1].'-'.$tglCari[0];
}

$sCust = 'select kodecustomer,namacustomer  from '.$dbname.'.pmn_4customer order by namacustomer';
$qCust = mysql_query($sCust) || exit(mysql_error($sCust));
while ($rCust = mysql_fetch_assoc($qCust)) {
    $optCust .= '<option value='.$rCust['kodecustomer'].' '.(($rCust['kodecustomer'] === $idCust ? 'selected' : '')).'>'.$rCust['namacustomer'].'</option>';
}
$optPabrik = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
if ('KANWIL' === $_SESSION['empl']['tipelokasitugas'] || 'HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $sPbrik = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK'";
} else {
    $sPbrik = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PABRIK' and kodeorganisasi='".$lksiTugas."'";
}

$qPabrik = mysql_query($sPbrik);
while ($rPabrik = mysql_fetch_assoc($qPabrik)) {
    ('' !== $kdPbrk ? ($optPabrik .= '<option value='.$rPabrik['kodeorganisasi'].' '.(($rPabrik['kodeorganisasi'] === $kdPbrk ? 'selected' : '')).'>'.$rPabrik['namaorganisasi'].'</option>') : ($optPabrik .= '<option value='.$rPabrik['kodeorganisasi'].' >'.$rPabrik['namaorganisasi'].'</option>'));
}
$thn = (int) (date('Y'));
for ($i = 1982; $i <= $thn; ++$i) {
    $optThn .= '<option value='.$i.' '.(($i === $thntnm1 ? 'selected' : '')).'>'.$i.'</option>';
    $optThn2 .= '<option value='.$i.' '.(($i === $thntnm2 ? 'selected' : '')).'>'.$i.'</option>';
    $optThn3 .= '<option value='.$i.' '.(($i === $thntnm3 ? 'selected' : '')).'>'.$i.'</option>';
}
for ($i = 0; $i < 24; ++$i) {
    if (strlen($i) < 2) {
        $i = '0'.$i;
    }

    $jmMsk .= '<option value='.$i.' '.(($i === $jmMasuk[0] ? 'selected' : '')).'>'.$i.'</option>';
    $jmKlr .= '<option value='.$i.' '.(($i === $jmKeluar[0] ? 'selected' : '')).'>'.$i.'</option>';
}
for ($i = 0; $i < 60; ++$i) {
    if (strlen($i) < 2) {
        $i = '0'.$i;
    }

    $mntMsk .= '<option value='.$i.' '.(($i === $jmMasuk[1] ? 'selected' : '')).'>'.$i.'</option>';
    $mntKlr .= '<option value='.$i.' '.(($i === $jmKeluar[1] ? 'selected' : '')).'>'.$i.'</option>';
}
$arrOptIntex = ['External', 'Internal', 'Afiliasi'];
foreach ($arrOptIntex as $isi => $tks) {
    if (isset($_POST['BuahStat'])) {
        $OptIntex .= '<option value='.$isi.' '.(($isi === $BuahStat ? 'selected' : '')).'>'.$tks.'</option>';
    } else {
        $OptIntex .= '<option value='.$isi.' >'.$tks.'</option>';
    }
}
$arrStat = ['On', 'Off'];
foreach ($arrStat as $is => $tks) {
    if (isset($_POST['statTmbngn'])) {
        $optStatTimb .= "<option value='".$is."'  ".(($is === $statTmbngn ? 'selected' : '')).'>'.$tks.'</option>';
    } else {
        $optStatTimb .= "<option value='".$is."'>".$tks.'</option>';
    }
}
switch ($method) {
    case 'GetForm':
        if ('40000001' === $kdBrg || '40000002' === $kdBrg || '40000004' === $kdBrg || '40000003' === $kdBrg) {
            if ('' !== $noTrans) {
                $sGdt = 'select * from '.$dbname.".pabrik_timbangan where notransaksi='".$noTrans."'";
                $qGdt = mysql_query($sGdt);
                $rGdt = mysql_fetch_assoc($qGdt);
                if ('' !== $rGdt['notransaksi']) {
                    $ar = 'disabled';
                    $notransaksi = 'value='.$rGdt['notransaksi'].'';
                    $tgl = 'value='.tanggalnormal($rGdt['tanggal']).'';
                    $nkntrak = 'value='.$rGdt['nokontrak'].' ';
                    $kdNopol = "value='".$rGdt['nokendaraan']."' ";
                    $nodo = "value='".$rGdt['nodo']."'";
                    $nosipb = 'value='.$rGdt['nosipb'].'';
                    $spr = "value='".$rGdt['supir']."'";
                    $brtKsng = " value='".$rGdt['beratmasuk']."' ";
                    $brtBrsh = ' value='.$rGdt['beratbersih'].' ';
                    $brtKlr = " value='".$rGdt['beratkeluar']."'";
                    $statSortasi = "value='".$rGdt['statussortasi']."'";
                    $ptgsSortasi = "value='".$rGdt['petugassortasi']."'";
                }
            } else {
                $brtKsng = " value='0' ";
                $brtBrsh = " value='0' ";
                $brtKlr = " value='0'";
            }

            echo "<input type=hidden id=method name=method value='insertCpk' />    <fieldset>\r\n<legend>".$_SESSION['lang']['form']."</legend>\r\n<table>\r\n <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['notransaksi']."</td><td>\r\n                 <input type=text id=noTrans name=noTrans class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=40 style=\"width:150px;\" ".$notransaksi.' '.$ar." /></td>\r\n          </tr>\r\n           <tr>\r\n                 <td style='valign:top'>".$_SESSION['lang']['tanggal']."</td>\r\n                 <td>\r\n                 <input type=text class=myinputtext id=tglTrans name=tglTrans onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 ".$tgl." style=\"width:150px;\" /></td>\r\n          </tr>\r\n            <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['NoKontrak']."</td><td>\r\n                 <input type=text id=nokontrk name=nokontrk class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=40 style=\"width:150px;\" ".$nkntrak." /></td>\r\n          </tr>\r\n          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['nmcust']."</td><td>\r\n                 <select id=nmCust name=nmCust  style=\"width:150px;\"><option value=></option>".$optCust."</select></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['kdpabrik']."</td><td>\r\n                 <select id=kdPbrik name=kdPbrik  style=\"width:150px;\">".$optPabrik."</select></td>\r\n          </tr>\r\n          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['statTimbangan']."</td><td>\r\n                 <select id=statTmbngn name=statTmbngn  style=\"width:150px;\"><option value=></option>".$optStatTimb."</select></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['nodo']." </td><td>\r\n                 <input type=text id=nodo name=nodo class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=30 style=\"width:150px;\" ".$nodo."   /></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['nosipb']."</td><td>\r\n                 <input type=text id=nosipb name=nosipb class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=30 style=\"width:150px;\" ".$nosipb."   /></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['kodenopol']."</td><td>\r\n                <input type=text id=nopol name=nopol class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=8 style=\"width:150px;\" ".$kdNopol." /></td>\r\n          </tr>\r\n       <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['sopir']."</td><td>\r\n                 <input type=text id=spir name=spir class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=30 style=\"width:150px;\" ".$spr." /></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratkosong']." </td><td>\r\n         <input type=text id=brtKosong name=brtKosong class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtKsng."  /></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratnormal']."</td><td>\r\n                 <input type=text id=brtBersih name=brtBersih class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtBrsh."  /></td>\r\n          </tr>\r\n                <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratKeluar']."</td><td>\r\n                 <input type=text id=brtKeluar name=brtKeluar class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtKlr."  /></td>\r\n          </tr>\r\n                  <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['jammasuk']."</td><td>\r\n                <select id=jmMasuk>".$jmMsk.'</select> : <select id=mntMasuk>'.$mntMsk."</select></td>\r\n          </tr>\r\n          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['jamkeluar']."</td><td>\r\n                <select id=jmKeluar>".$jmKlr.'</select> : <select id=mntKeluar>'.$mntKlr."</select></td>\r\n          </tr>\r\n                <button class=mybutton onclick=saveCpk()>".$_SESSION['lang']['save']."</button>\r\n                <button class=mybutton onclick=clearDt()>".$_SESSION['lang']['cancel']."</button>\r\n     </table>\r\n</fieldset>";
        } else {
            if ('40000004' === $kdBrg) {
                if ('' !== $noTrans) {
                    $sGdt = 'select * from '.$dbname.".pabrik_timbangan where notransaksi='".$noTrans."'";
                    $qGdt = mysql_query($sGdt);
                    $rGdt = mysql_fetch_assoc($qGdt);
                    if ('' !== $rGdt['notransaksi']) {
                        $ar = 'disabled';
                        $notransaksi = 'value='.$rGdt['notransaksi'].'';
                        $tgl = 'value='.tanggalnormal($rGdt['tanggal']).'';
                        $kdNopol = "value='".$rGdt['nokendaraan']."' ";
                        $spr = "value='".$rGdt['supir']."'";
                        $brtKsng = " value='".$rGdt['beratmasuk']."' ";
                        $brtBrsh = ' value='.$rGdt['beratbersih'].' ';
                        $brtKlr = " value='".$rGdt['beratkeluar']."'";
                        $statSortasi = "value='".$rGdt['statussortasi']."'";
                        $ptgsSortasi = "value='".$rGdt['petugassortasi']."'";
                    }
                } else {
                    $brtKsng = " value='0' ";
                    $brtBrsh = " value='0' ";
                    $brtKlr = " value='0'";
                }

                echo "<input type=hidden id=method name=method value='insertJk' /> <fieldset>\r\n<legend>".$_SESSION['lang']['entryForm']."</legend>\r\n<table>\r\n         <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['notransaksi']."</td><td>\r\n                 <input type=text id=noTrans name=noTrans class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=40 style=\"width:150px;\" ".$notransaksi.' '.$ar."  /></td>\r\n          </tr>\r\n          <tr>\r\n                 <td style='valign:top'>".$_SESSION['lang']['tanggal']."</td>\r\n                 <td><input type=text class=myinputtext id=tglTrans name=tglTrans onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 ".$tgl." style=\"width:150px;\"/></td>\r\n          </tr>\r\n\r\n          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['nmcust']."</td><td>\r\n                 <select id=nmCust name=nmCust  style=\"width:150px;\"><option value=></option>".$optCust."</select></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['kdpabrik']."</td><td>\r\n                 <select id=kdPbrik name=kdPbrik  style=\"width:150px;\">".$optPabrik."</select></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['statTimbangan']."</td><td>\r\n                 <select id=statTmbngn name=statTmbngn  style=\"width:150px;\"><option value=></option>".$optStatTimb."</select></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['kodenopol']."</td><td>\r\n                <input type=text id=nopol name=nopol class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=8 style=\"width:150px;\" ".$kdNopol."/></td>\r\n          </tr>\r\n       <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['sopir']."</td><td>\r\n                 <input type=text id=spir name=spir class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=30 style=\"width:150px;\" ".$spr." /></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratkosong']." </td><td>\r\n         <input type=text id=brtKosong name=brtKosong class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtKsng." /></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratnormal']."</td><td>\r\n                 <input type=text id=brtBersih name=brtBersih class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtBrsh."  /></td>\r\n          </tr>\r\n                <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratKeluar']."</td><td>\r\n                 <input type=text id=brtKeluar name=brtKeluar class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtKlr."  /></td>\r\n          </tr>\r\n                  <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['jammasuk']."</td><td>\r\n                <select id=jmMasuk>".$jmMsk.'</select> : <select id=mntMasuk>'.$mntMsk."</select></td>\r\n          </tr>\r\n          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['jamkeluar']."</td><td>\r\n                <select id=jmKeluar>".$jmKlr.'</select> : <select id=mntKeluar>'.$mntKlr."</select></td>\r\n          </tr>\r\n          <tr>\r\n                <td colspan='2'><button class=mybutton onclick=saveJk()>".$_SESSION['lang']['save']."</button>\r\n                <button class=mybutton onclick=clearDt()>".$_SESSION['lang']['cancel']."</button></td></tr>\r\n     </table>\r\n</fieldset>";
            } else {
                if ('40000003' === $kdBrg) {
                    if ('' !== $noTrans) {
                        $sGdt = 'select * from '.$dbname.".pabrik_timbangan where notransaksi='".$noTrans."'";
                        $qGdt = mysql_query($sGdt);
                        $rGdt = mysql_fetch_assoc($qGdt);
                        if ('' !== $rGdt['notransaksi']) {
                            $ar = 'disabled';
                            $notransaksi = "value='".$rGdt['notransaksi']."'";
                            $tgl = "value='".tanggalnormal($rGdt['tanggal'])."'";
                            $nospb = "value='".$rGdt['nospb']."'";
                            $jmlhtndn1 = "value='".$rGdt['jumlahtandan1']."'";
                            $jmlhtndn2 = "value='".$rGdt['jumlahtandan2']."'";
                            $jmlhtndn3 = "value='".$rGdt['jumlahtandan3']."'";
                            $brndlan = "value='".$rGdt['brondolan']."'";
                            $kdNopol = "value='".$rGdt['nokendaraan']."' ";
                            $spr = "value='".$rGdt['supir']."'";
                            $brtKsng = " value='".$rGdt['beratkeluar']."' ";
                            $brtBrsh = " value='".$rGdt['beratbersih']."'";
                            $brtKlr = " value='".$rGdt['beratmasuk']."'";
                            $statSortasi = "value='".$rGdt['statussortasi']."'";
                            $ptgsSortasi = "value='".$rGdt['petugassortasi']."'";
                        }
                    } else {
                        $brtKsng = " value='0' ";
                        $brtBrsh = " value='0' ";
                        $brtKlr = " value='0'";
                        $jmlhtndn1 = "value='0'";
                        $jmlhtndn2 = "value='0'";
                        $jmlhtndn3 = "value='0'";
                        $brndlan = "value='0' ";
                    }

                    echo "<input type=hidden id=method name=method value='insertTbs' />\r\n                         <fieldset>\r\n                        <legend>".$_SESSION['lang']['entryForm']."</legend>\r\n                        <table>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['notransaksi']."</td><td>\r\n                        <input type=text id=noTrans name=noTrans class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=40 style=\"width:150px;\" ".$notransaksi.' '.$ar." /></td>\r\n                        </tr>\r\n                        <tr>\r\n                        <td style='valign:top'>".$_SESSION['lang']['tanggal']."</td>\r\n                        <td><input type=text class=myinputtext id=tglTrans name=tglTrans onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 ".$tgl." style=\"width:150px;\" /></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['kdpabrik']."</td><td>\r\n                        <select id=kdPbrik name=kdPbrik  style=\"width:150px;\">".$optPabrik."</select></td>\r\n                        </tr>\r\n                          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['statTimbangan']."</td><td>\r\n                 <select id=statTmbngn name=statTmbngn  style=\"width:150px;\"><option value=></option>".$optStatTimb."</select></td>\r\n          </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['nospb']." </td><td>\r\n                        <input type=text id=noSpb name=noSpb class=myinputtext onkeypress=\"return angka_doang(event);\"  style=\"width:150px;\" ".$nospb." /></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['statusBuah']." </td><td>\r\n                        <select id=statBuah name=statBuah  style=\"width:150px;\" onchange=getKbn(0,0,0)><option value=></option>".$OptIntex."</select></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['kebun']." </td><td>\r\n                        <select id=kdOrg name=kdOrg  style=\"width:150px;\"><option value=></option></select></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['namasupplier']." </td><td>\r\n                        <select id=suppId name=suppId  style=\"width:150px;\"><option value=></option></select></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['thntanam']." 1</td><td>\r\n                        <select id=thnTnm1 name=thnTnm1  style=\"width:150px;\"><option value=></option>".$optThn."</select></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['jmlhTandan']." 1</td><td>\r\n                        <input type=text id=jmlhTndn1 name=jmlhTndn1 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 style=\"width:150px;\" ".$jmlhtndn1."  /></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['thntanam']." 2</td><td>\r\n                        <select id=thnTnm2 name=thnTnm2  style=\"width:150px;\"><option value=></option>".$optThn2."</select></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['jmlhTandan']." 2</td><td>\r\n                        <input type=text id=jmlhTndn2 name=jmlhTndn2 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 style=\"width:150px;\" ".$jmlhtndn2." /></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['thntanam']." 3</td><td>\r\n                        <select id=thnTnm3 name=thnTnm3  style=\"width:150px;\"><option value=></option>".$optThn3."</select></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['jmlhTandan']." 3</td><td>\r\n                        <input type=text id=jmlhTndn3 name=jmlhTndn3 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 style=\"width:150px;\" ".$jmlhtndn3." /></td>\r\n                        </tr>\r\n                        <tr> \t \r\n                        <td style='valign:top'>".$_SESSION['lang']['brondolan']."</td><td>\r\n                        <input type=text id=brndln name=brndln class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=4 style=\"width:150px;\" ".$brndlan." /></td>\r\n                        </tr>\r\n                         <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['kodenopol']."</td><td>\r\n                <input type=text id=nopol name=nopol class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=8 style=\"width:150px;\" ".$kdNopol."  /></td>\r\n          </tr>\r\n       <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['sopir']."</td><td>\r\n                 <input type=text id=spir name=spir class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=30 style=\"width:150px;\" ".$spr."/></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratkosong']." </td><td>\r\n         <input type=text id=brtKosong name=brtKosong class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtKsng."  /></td>\r\n          </tr>\r\n           <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratnormal']."</td><td>\r\n                 <input type=text id=brtBersih name=brtBersih class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtBrsh."  /></td>\r\n          </tr>\r\n                <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['beratKeluar']."</td><td>\r\n                 <input type=text id=brtKeluar name=brtKeluar class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=5 style=\"width:150px;\" ".$brtKlr."  /></td>\r\n          </tr>\r\n          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['statusSortasi']."</td><td>\r\n                 <input type=text id=statSortasi name=statSortasi class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=5 style=\"width:150px;\" ".$statSortasi."   /></td>\r\n          </tr>\r\n          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['petugasSortasi']."</td><td>\r\n                 <input type=text id=tgsSortasi name=tgsSortasi class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=20 style=\"width:150px;\" ".$ptgsSortasi."  /></td>\r\n          </tr>\r\n                          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['jammasuk']."</td><td>\r\n                <select id=jmMasuk>".$jmMsk.'</select> : <select id=mntMasuk>'.$mntMsk."</select></td>\r\n          </tr>\r\n          <tr> \t \r\n                 <td style='valign:top'>".$_SESSION['lang']['jamkeluar']."</td><td>\r\n                <select id=jmKeluar>".$jmKlr.'</select> : <select id=mntKeluar>'.$mntKlr."</select></td>\r\n          </tr>\r\n                         <tr>\r\n                <td colspan='2'><button class=mybutton onclick=saveTbs()>".$_SESSION['lang']['save']."</button>\r\n                <button class=mybutton onclick=clearDt()>".$_SESSION['lang']['cancel']."</button></td></tr>\r\n                        </table>\r\n          </fieldset>\r\n                        ";
                }
            }
        }

        break;
    case 'loadData':
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.pabrik_timbangan  order by `nokontrak` desc';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.'.pabrik_timbangan  order by `nokontrak` desc limit '.$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        while ($res = mysql_fetch_assoc($qlvhc)) {
            $sCust = 'select namacustomer  from '.$dbname.".pmn_4customer where kodecustomer = '".$res['koderekanan']."'";
            $qCUst = mysql_query($sCust);
            $rCust = mysql_fetch_assoc($qCUst);
            $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where `kodebarang`='".$res['kodebarang']."'";
            $qBrg = mysql_query($sBrg);
            $rBrg = mysql_fetch_assoc($qBrg);
            $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$res['kodept']."'";
            $qOrg = mysql_query($sOrg);
            $rOrg = mysql_fetch_assoc($qOrg);
            ++$no;
            echo "\r\n                        <tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$res['notransaksi']."</td>\r\n                        <td>".tanggalnormal($res['tanggal'])."</td>\r\n                        <td>".$res['kodebarang']."</td>\r\n                        <td>".$rBrg['namabarang']."</td>\r\n                        <td>".$res['jammasuk']."</td>\r\n                        <td>".$res['jamkeluar']."</td>\r\n                        <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."','".$res['kodebarang']."','".$res['kodecustomer']."','".$res['millcode']."','".$res['jammasuk']."','".$res['jamkeluar']."','".$res['intex']."','".$res['thntm1']."','".$res['thntm2']."','".$res['thntm3']."','".$res['kodeorg']."','".$res['timbangonoff']."');\">\r\n<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['notransaksi']."');\" >\t\r\n<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_timbangan','".$res['notransaksi']."','','pabrik_timbanganPdf',event)\"></td>\r\n                        </tr>";
        }
        echo "\r\n                <tr class=rowheader><td colspan=8 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";

        break;
    case 'getkbn':
        if ('0' === $BuahStat) {
            $optkdOrg2 = "<opttion value=''>".$_SESSION['lang']['all'].'</option>';
            $sOrg = 'SELECT namasupplier,supplierid FROM '.$dbname.'.log_5supplier WHERE kodetimbangan is not null';
            echo 'warning:'.$sOrg;
            $qOrg = mysql_query($sOrg);
            while ($rOrg = mysql_fetch_assoc($qOrg)) {
                $optkdOrg2 .= '<option value='.$rOrg['supplierid'].''.(($rOrg['supplierid'] === $idCust ? 'selected' : '')).'>'.$rOrg['namasupplier'].'</option>';
            }
            echo $optkdOrg2.'###'.$BuahStat;
            exit();
        }

        if (1 === $BuahStat) {
            $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk='".$_SESSION['org']['kodeorganisasi']."'";
        } else {
            if (2 === $BuahStat) {
                $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and induk!='".$_SESSION['org']['kodeorganisasi']."'";
            }
        }

        $optkdOrg = "<opttion value=''>".$_SESSION['lang']['all'].'</option>';
        $qOrg = mysql_query($sOrg);
        while ($rOrg = mysql_fetch_assoc($qOrg)) {
            $optkdOrg .= '<option value='.$rOrg['kodeorganisasi'].''.(($rOrg['kodeorganisasi'] === $kdKbn ? 'selected' : '')).'>'.$rOrg['namaorganisasi'].'</option>';
        }
        echo $optkdOrg.'###'.$BuahStat;

        break;
    case 'cariNotransaksi':
        if ('' !== $txtSearch) {
            $where .= "and notransaksi like '%".$txtSearch."%'";
        }

        if ('' !== $kdBrg) {
            $where .= " and kodebarang='".$kdBrg."'";
        }

        if ('' !== $tglCari) {
            $where .= " and tanggal like '%".$tglCari."%'";
        }

        $awk = ' (select distinct kodeorganisasi from '.$dbname.".organisasi where induk='".$_SESSION['org']['kodeorganisasi']."' and tipe='PABRIK')";
        $sCek2 = 'select * from '.$dbname.".pabrik_timbangan where millcode!='' ".$where.' and millcode in  '.$awk.'';
        $qCek = mysql_query($sCek2) || exit(mysql_error($sCek));
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            $limit = 10;
            $page = 0;
            if (isset($_POST['page'])) {
                $page = $_POST['page'];
                if ($page < 0) {
                    $page = 0;
                }
            }

            $offset = $page * $limit;
            $ql2 = 'select count(*) as jmlhrow from '.$dbname.".pabrik_timbangan where millcode!='' ".$where.' and millcode in  '.$awk.' ';
            $query2 = mysql_query($ql2);
            while ($jsl = mysql_fetch_object($query2)) {
                $jlhbrs = $jsl->jmlhrow;
            }
            $slvhc = 'select * from '.$dbname.".pabrik_timbangan where millcode!='' ".$where.' and millcode in  '.$awk.' limit '.$offset.','.$limit.'';
            $qlvhc = mysql_query($slvhc);
            $user_online = $_SESSION['standard']['userid'];
            while ($res = mysql_fetch_assoc($qlvhc)) {
                $sCust = 'select namacustomer  from '.$dbname.".pmn_4customer where kodecustomer = '".$res['koderekanan']."'";
                $qCUst = mysql_query($sCust);
                $rCust = mysql_fetch_assoc($qCUst);
                $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where `kodebarang`='".$res['kodebarang']."'";
                $qBrg = mysql_query($sBrg);
                $rBrg = mysql_fetch_assoc($qBrg);
                ++$no;
                echo "\r\n                                <tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".$res['notransaksi']."</td>\r\n                                <td>".tanggalnormal($res['tanggal'])."</td>\r\n                                <td>".$res['kodebarang']."</td>\r\n                                <td>".$rBrg['namabarang']."</td>\r\n                                <td>".$res['jammasuk']."</td>\r\n                                <td>".$res['jamkeluar']."</td>\r\n                                <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."','".$res['kodebarang']."','".$res['kodecustomer']."','".$res['millcode']."','".$res['jammasuk']."','".$res['jamkeluar']."','".$res['intex']."','".$res['thntm1']."','".$res['thntm2']."','".$res['thntm3']."','".$res['kodeorg']."','".$res['timbangonoff']."');\">\r\n<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['notransaksi']."');\" >\t\r\n<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('pabrik_timbangan','".$res['notransaksi']."','','pabrik_timbanganPdf',event)\"></td>\r\n                                </tr>";
            }
            echo "\r\n                        <tr class=rowheader><td colspan=9 align=center>\r\n                        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                        <button class=mybutton onclick=cariTrk(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                        <button class=mybutton onclick=cariTrk(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                        </td>\r\n                        </tr>";
        } else {
            echo '<tr class=rowheader><td colspan=8 align=center>Not Found</td></tr>';
        }

        break;
    default:
        break;
}

?>
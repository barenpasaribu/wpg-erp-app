<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
echo "<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/budget_upah.js\"></script>\r\n";
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n          where tipe='PT'\r\n              order by namaorganisasi desc";
    $res = mysql_query($str);
    $optpt = '';
    while ($bar = mysql_fetch_object($res)) {
        $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                    where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'\r\n                    or tipe='HOLDING')  and induk!=''\r\n                    ";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
} else {
    $optpt = '';
    $optpt .= "<option value='".$_SESSION['empl']['kodeorganisasi']."'>".$_SESSION['empl']['kodeorganisasi'].'</option>';
    $optgudang .= "<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas'].'</option>';
}

OPEN_BOX('', '<b>'.$_SESSION['lang']['upahharian'].'</b>');
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['upahkerja'].''.$thn_skrg.'</legend>';
$frm[0] .= "<table cellspacing=1 border=0>\r\n    <tr><td>".$_SESSION['lang']['budgetyear']." </td><td>:</td><td>\r\n    <input type=text class=myinputtext onkeyup=\"resetcontainer();\" id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=width:150px; /></td></tr>\r\n    <tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td>\r\n    <select id=kodeorg onchange=\"resetcontainer();\" name=kodeorg style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optgudang."</select></td></tr>\r\n    <tr><td colspan=3>\r\n    <button class=mybutton id=proses name=proses onclick=prosesUpah()>".$_SESSION['lang']['proses']."</button>\r\n    <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >\r\n    </td></tr></table>";
$frm[0] .= '</fieldset>';
$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['list']."</legend>    \r\n<div id=container></div>\r\n    ";
$frm[0] .= '</fieldset>';
$str = 'select tahunbudget from '.$dbname.".bgt_upah\r\n    where kodeorg = '".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n          group by tahunbudget order by tahunbudget";
$res = mysql_query($str);
$opttahun = '';
while ($bar = mysql_fetch_object($res)) {
    $opttahun .= "<option value='".$bar->tahunbudget."'>".$bar->tahunbudget.'</option>';
}
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['close'].'</legend>';
$frm[1] .= "<table cellspacing=1 border=0><thead>\r\n    </thead>\r\n    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>\r\n    <select id=tahunbudget2 onchange=\"resetcontainer2();\" name=tahunbudget2 style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$opttahun."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['kodeorg']."</td><td>:</td><td><label id=kodeorg2>\r\n    ".substr($_SESSION['empl']['lokasitugas'], 0, 4)."</td></tr>\r\n    <tr><td colspan=3>\r\n    <button class=mybutton id=proses name=proses onclick=prosesTutupUpah()>".$_SESSION['lang']['proses']."</button>\r\n    <input type=hidden id=proses_pekerjaan name=proses_pekerjaan value=insert_pekerjaan />\r\n</table>";
$frm[1] .= '</fieldset>';
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n<div id=container2></div>    \r\n    ";
$frm[1] .= '</fieldset>';
$hfrm[0] = $_SESSION['lang']['upahharian'];
$hfrm[1] = $_SESSION['lang']['tutup'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>
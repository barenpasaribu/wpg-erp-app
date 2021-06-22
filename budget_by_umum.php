<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
$frm[0] = '';
$frm[1] = '';
echo "<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script type=\"text/javascript\" src=\"js/budget_by_umum.js\"></script>\r\n";
$tipebudget = substr($_SESSION['empl']['lokasitugas'], 3, 1);
if ('M' === $tipebudget) {
    $tipebudget = 'MILL';
} else {
    if ('E' === $tipebudget) {
        $tipebudget = 'ESTATE';
    } else {
        $tipebudget = $_SESSION['empl']['tipelokasitugas'];
    }
}

$kodeorg = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$str = 'select kodebudget,nama from '.$dbname.".bgt_kode  ";
$optkodebudget = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optkodebudget .= "<option value='".$bar->kodebudget."'>".$bar->nama.'</option>';
}
$str = 'select distinct tahunbudget from '.$dbname.".bgt_budget\r\n        where tipebudget='".$tipebudget."' and kodeorg like '".$kodeorg."%' and kodebudget like 'UMUM%'\r\n            order by tahunbudget desc\r\n            ";
$opttahunbudget = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $opttahunbudget .= "<option value='".$bar->tahunbudget."'>".$bar->tahunbudget.'</option>';
}
if ('ID' === $_SESSION['language']) {
    $dd = 'namaakun as namaakun';
} else {
    $dd = 'namaakun1 as namaakun';
}

$str = 'select noakun,'.$dd.' from '.$dbname.".keu_5akun  where detail=1 and tipeakun = 'Biaya' order by noakun\r\n                    ";
$optakun = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optakun .= "<option value='".$bar->noakun."'>".$bar->noakun.' - '.$bar->namaakun.'</option>';
    $akun[$bar->noakun] = $bar->namaakun;
}
$optVhc = '';
$str = 'select kodevhc,kodeorg from '.$dbname.'.vhc_5master where kodeorg LIKE "' .substr($_SESSION['empl']['lokasitugas'], 0, 3).'%"  order by kodevhc';
//echo $str;
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optVhc .= "<option value='".$bar->kodevhc."'>".$bar->kodeorg." | ".$bar->kodevhc.'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['biayaumum'].'</b>');
echo "<table cellspacing=1 border=0>\r\n    <tr><td>".$_SESSION['lang']['tipeanggaran']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext id=tipebudget name=tipebudget onkeypress=\"return angka_doang(event);\" maxlength=2 disabled=true style=width:150px; value=\"".$tipebudget."\"/></td></tr>\r\n    <tr><td>".$_SESSION['lang']['budgetyear']."</td><td>:</td><td>\r\n        <input type=text class=myinputtext id=tahunbudget name=tahunbudget onkeypress=\"return angka_doang(event);\" maxlength=4 style=width:150px; /></td></tr>\r\n    <tr><td>".$_SESSION['lang']['kodeanggaran']."</td><td>:</td><td>\r\n        <select id=kodebudget onchange=\"updateTab();\" name=kodebudget style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optkodebudget."</select></td></tr>\r\n    <tr><td>".$_SESSION['lang']['jenisbiaya']."</td><td>:</td><td>\r\n        <select name=jenisbiaya id=jenisbiaya style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata'].'</option>'.$optakun."</select></td></tr>\r\n  <tr><td>".$_SESSION['lang']['abkend']."</td>\r\n      <td>:</td>\r\n      <td><select  id=kodevhc style='width:150px;' onchange=\"kalikanRp()\" >\r\n           <option value=''>".$optVhc."</option>\r\n          </select>\r\n      </td>\r\n    </tr>    \r\n\r\n   <tr><td>".$_SESSION['lang']['jamperthn']."</td><td>:</td><td>\r\n        <input type=text class=myinputtextnumber name=jamperthn id=jamperthn onkeypress=\"return angka_doang(event);\" onblur=kalikanRp()></td></tr>\r\n    \r\n<tr><td>".$_SESSION['lang']['jumlahpertahun']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext id=jumlahbiaya name=jumlahbiaya onkeypress=\"return angka_doang(event);\" maxlength=20 style=width:150px; /></td></tr>\r\n    <tr><td>".$_SESSION['lang']['keterangan']." </td><td>:</td><td>\r\n        <input type=text class=myinputtext id=ketUmum name=ketUmum onkeypress=\"return tanpa_kutip(event);\" maxlength=45 style=width:150px; /></td></tr>\r\n    <tr><td colspan=3>\r\n        <button class=mybutton id=simpan name=simpan onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n        <input type=hidden id=tersembunyi name=tersembunyi value=tersembunyi >\r\n    </td></tr></table>";
$frm[0] .= '<fieldset id=tab0><legend>'.$_SESSION['lang']['list']."</legend>    \r\n<div style=overflow:auto;width:100%;height:300px; id=container0>";
$frm[0] .= $_SESSION['lang']['budgetyear']." : <select name=pilihtahun0 id=pilihtahun0 onchange=\"updateTab();\"><option value=''>".$_SESSION['lang']['all'].'</option>'.$opttahunbudget.'</select>';
$frm[0] .= "<table id=container9 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['index']."</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeorg']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['keterangan']."</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
$str = 'select * from '.$dbname.".bgt_budget\r\n        where kodebudget like 'UMUM%' and tipebudget = '".$tipebudget."' and kodeorg = '".$kodeorg."' order by tahunbudget desc, noakun";
$res = mysql_query($str);
$no = 1;
while ($bar = mysql_fetch_object($res)) {
    $frm[0] .= "<tr class=rowcontent>\r\n            <td align=center>".$bar->kunci."</td>\r\n            <td align=center>".$bar->tahunbudget."</td>\r\n            <td align=center>".$bar->kodeorg."</td>\r\n            <td align=center>".$bar->tipebudget."</td>\r\n            <td align=center>".$bar->kodebudget."</td>\r\n            <td align=center>".$bar->noakun."</td>\r\n            <td align=left>".$akun[$bar->noakun]."</td>\r\n             <td align=left>".ucfirst($bar->keterangan)."</td>\r\n            <td align=right>".number_format($bar->rupiah).'</td>';
    if (0 === $bar->tutup) {
        $frm[0] .= '<td align=center><img id="delRow" class="zImgBtn" src="images/application/application_delete.png" onclick="deleteRow('.$bar->kunci.')" title="Hapus"></td>';
    } else {
        $frm[0] .= '<td align=center>&nbsp;</td>';
    }

    $hkef .= "\r\n       </tr>";
    ++$no;
}
$frm[0] .= "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>";
$frm[0] .= "</div>\r\n    ";
$frm[0] .= '</fieldset>';
$frm[1] .= '<fieldset id=tab1><legend>'.$_SESSION['lang']['sebaran']."</legend>\r\n    <div style=overflow:auto;width:100%;height:300px; id=container1>";
$frm[1] .= $_SESSION['lang']['budgetyear']." : <select name=pilihtahun1 id=pilihtahun1 onchange=\"updateTabs();\"><option value=''>".$_SESSION['lang']['all'].'</option>'.$opttahunbudget.'</select>';
$frm[1] .= '<input type=hidden id=hidden1 name=hidden1 value="">';
$arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sept', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
$frm[1] .= "\r\n        <table><tr>";
foreach ($arrBln as $brsBulan => $listBln) {
    $frm[1] .= '<td>'.$listBln.'</td>';
}
$frm[1] .= '</tr>';
$frm[1] .= "<tr>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss1 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss2 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss3 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss4 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss5 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss6 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss7 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss8 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss9 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss10 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss11 value=1></td>\r\n            <td><input type=text class=myinputtextnumber size=3 onkeypress=\"return angka_doang(event);\" id=ss12 value=1></td>\r\n            <td><img src=images/clear.png onclick=bersihkanDonk() style='height:30px;cursor:pointer' title='bersihkan'></td>\r\n        </tr>\r\n        </table>";
$frm[1] .= "<table id=container6 class=sortable cellspacing=1 border=0 width=100%>\r\n     <thead>\r\n        <tr>\r\n            <td></td>\r\n            <td align=center>No</td>\r\n            <td align=center>".$_SESSION['lang']['budgetyear']."</td>\r\n            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>\r\n            <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n            <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n            <td align=center>Jan</td>\r\n            <td align=center>Feb</td>\r\n            <td align=center>Mar</td>\r\n            <td align=center>Apr</td>\r\n            <td align=center>May</td>\r\n            <td align=center>Jun</td>\r\n            <td align=center>Jul</td>\r\n            <td align=center>Aug</td>\r\n            <td align=center>Sep</td>\r\n            <td align=center>Oct</td>\r\n            <td align=center>Nov</td>\r\n            <td align=center>Dec</td>\r\n            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>\r\n            <td align=center>".$_SESSION['lang']['action']."</td>\r\n       </tr>  \r\n     </thead>\r\n     <tbody>";
$str = 'select a.*, b.tutup from '.$dbname.".bgt_budget_detail a\r\n        left join ".$dbname.".bgt_budget b on a.kunci=b.kunci\r\n        where a.tipebudget = '".$tipebudget."' and a.kodeorg = '".$kodeorg."'\r\n            order by a.tahunbudget desc, a.noakun";
$res = mysql_query($str);
$no = 1;
while ($bar = mysql_fetch_object($res)) {
    (0 === $bar->tutup ? ($rpt = ' onclick="sebaran('.$bar->kunci.",event)\" title='Sebaran ".$kodeorg.' '.$akun[$bar->noakun]."' style='cursor:pointer;'") : ($rpt = ' '));
    $frm[1] .= "<tr class=rowcontent style='cursor:pointer;' id=baris".$no.">\r\n            <td><input type=checkbox onclick=sebarkanBoo('".$bar->kunci."',".$no.',this,'.$bar->rupiah.','.$bar->jumlah."); title='Sebarkan sesuai proporsi diatas'></td>\r\n            <td align=center ".$rpt.'>'.$no."</td>\r\n            <td align=center ".$rpt.'>'.$bar->tahunbudget."</td>\r\n            <td align=center ".$rpt.'>'.$bar->kodebudget."</td>\r\n            <td align=center ".$rpt.'>'.$bar->tipebudget."</td>\r\n            <td align=right ".$rpt.'>'.$bar->noakun."</td>\r\n            <td align=left ".$rpt.'>'.$akun[$bar->noakun]."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp01)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp02)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp03)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp04)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp05)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp06)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp07)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp08)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp09)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp10)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp11)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rp12)."</td>\r\n            <td align=right ".$rpt.'>'.number_format($bar->rupiah).'</td>';
    if (0 === $bar->tutup) {
        $frm[1] .= "\r\n            <td align=center>\r\n                <input type=\"image\" id=search src=images/search.png class=dellicon title=".$_SESSION['lang']['sebaran'].' onclick="sebaran('.$bar->kunci.",event)\";>\r\n            </td>";
    } else {
        $frm[1] .= '<td align=center>&nbsp;</td>';
    }

    $hkef .= "\r\n       </tr>";
    ++$no;
}
$frm[1] .= "</tbody>\r\n     <tfoot>\r\n     </tfoot>\t\t \r\n     </table>\r\n    </div>";
$frm[1] .= '</fieldset>';
$frm[2] .= '<fieldset id=tab2><legend>'.$_SESSION['lang']['close'].'</legend>';
$frm[2] .= "<table cellspacing=1 border=0><thead>\r\n    </thead>\r\n    <tr>\r\n    <td>".$_SESSION['lang']['budgetyear']." : <select name=pilihtahun2 id=pilihtahun2 onchange=\"updateTabs2();\"><option value=''>".$_SESSION['lang']['all'].'</option>'.$opttahunbudget."</select>\r\n    </td><td>\r\n        <button class=mybutton id=display2 name=display2 onclick=persiapantutup2()>".$_SESSION['lang']['list']."</button>\r\n    </td><td>\r\n        <button class=mybutton id=tutup2 name=tutup2 onclick=tutup2(1) disabled=true>".$_SESSION['lang']['close']."</button>\r\n        <input type=hidden id=hidden2 name=hidden2 value=>\r\n    </td></tr></table>";
$frm[2] .= '</fieldset>';
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['datatersimpan']."</legend>\r\n<div id=container2></div>    \r\n    ";
$frm[2] .= '</fieldset>';
$hfrm[0] = $_SESSION['lang']['list'];
$hfrm[1] = $_SESSION['lang']['sebaran'];
//$hfrm[2] = $_SESSION['lang']['close'];
drawTab('FRM', $hfrm, $frm, 100, 900);
echo "\r\n";
CLOSE_BOX();
echo close_body();

?>
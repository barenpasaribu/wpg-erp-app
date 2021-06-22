<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/sdm_5hkEfektif.js'></script>\r\n\r\n";
$arr = '##periode##hariminggu##harilibur##hkefektif##catatan';
include 'master_mainMenu.php';
OPEN_BOX();
$today = getdate();
$bulan = $today[mon];
$tahun = $today[year];
for ($i = -3; $i < 18; ++$i) {
    tanggalan($i);
}
echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['hkefektif']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['periode']."</td>\r\n\t   <td><select onchange=\"tambah();\" id=periode style='width:100px'><option value=''>".$optperiode."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['hariminggu']."</td>\r\n\t   <td><input onblur=\"tambah();\" type=text class=myinputtextnumber id=hariminggu name=hariminggu onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=3></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['harilibur']."</td>\r\n\t   <td><input onblur=\"tambah();\" type=text class=myinputtextnumber id=harilibur name=harilibur onkeypress=\"return angka_doang(event);\" style=\"width:100px;\" maxlength=3></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['hkefektif']."</td>\r\n\t   <td><input type=text class=myinputtextnumber id=hkefektif name=hkefektif style=\"width:100px;\" maxlength=3 disabled/></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['catatan']."</td>\r\n\t   <td><input type=text class=myinputtext id=catatan name=catatan onkeypress=\"return tanpa_kutip(event);\" style=\"width:100px;\" /></td>\r\n\t </tr>\r\n\t </table>\r\n         <input type=hidden value=insert id=method>\r\n         <button class=mybutton onclick=savehk('sdm_slave_5hkEfektif','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n         <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset><input type='hidden' id=oldtahunbudget name=oldtahunbudget />";
CLOSE_BOX();
OPEN_BOX();
$str = 'select * from '.$dbname.'.bgt_hk order by tahunbudget desc';
$res = mysql_query($str);
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['periode']."</td>\r\n\t   <td>".$_SESSION['lang']['hariminggu']."</td>\r\n\t   <td>".$_SESSION['lang']['harilibur']."</td>\r\n\t   <td>".$_SESSION['lang']['hkefektif']."</td>\r\n\t   <td>".$_SESSION['lang']['catatan']."</td>\r\n\t   <td>".$_SESSION['lang']['action']."</td>\r\n\t  </tr>\r\n\t </thead>\r\n\t <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n     </tfoot>\r\n     </table></fieldset>";
CLOSE_BOX();
echo close_body();
function tanggalan($minus)
{
    global $bulan;
    global $tahun;
    global $optperiode;
    $bulanan = $bulan + $minus;
    $tahunan = $tahun;
    if ($bulanan < 1) {
        $bulanan = 12 + $bulanan;
        $tahunan = $tahun - 1;
    }

    if (24 < $bulanan) {
        $bulanan = $bulanan - 24;
        $tahunan = $tahun + 2;
    } else {
        if (12 < $bulanan) {
            $bulanan = $bulanan - 12;
            $tahunan = $tahun + 1;
        }
    }

    if (1 == strlen($bulanan)) {
        $bulanan = '0'.$bulanan;
    }

    $optperiode .= "<option value='".$tahunan.'-'.$bulanan."'>".$tahunan.'-'.$bulanan.'</option>';
}

?>
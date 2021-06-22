<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/pabrik_produksi.js></script>\r\n";
include 'master_mainMenu.php';
$str = 'select kodeorganisasi from '.$dbname.".organisasi where tipe='PABRIK'\r\n      order by kodeorganisasi";
$res = mysql_query($str);
$optpabrik = '<option value=*></option>';
while ($bar = mysql_fetch_object($res)) {
    $optpabrik = "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'</option>';
}
$arr[0] = date('Y');
$arr[1] = date('Y') - 1;
$arr[2] = date('Y') - 2;
$optper = '';
for ($x = 0; $x < count($arr); ++$x) {
    $optper .= "<option value='".$arr[$x]."'>".$arr[$x].'</option>';
    for ($y = 12; 1 <= $y; --$y) {
        $optper .= "<option value='".$arr[$x].'-'.str_pad($y, 2, 0, 'STR_PAD_LEFT')."'>".str_pad($y, 2, 0, 'STR_PAD_LEFT').'-'.$arr[$x].'</option>';
    }
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['rprodksiPabrik'].':</b>');
echo "<fieldset style='width:500px'>\r\n      ".$_SESSION['lang']['kodeorganisasi'].':<select id=pabrik>'.$optpabrik."</select>\r\n      ".$_SESSION['lang']['periode'].'<select id=periode>'.$optper."</select>\r\n\t  <button class=mybutton onclick=getLaporanPrdPabrik()>".$_SESSION['lang']['ok']."</button>\r\n\t ";
CLOSE_BOX();
OPEN_BOX('', '');
echo "<div id=container style='width:100%;height:500px overflow:scroll'>\r\n\r\n     </div>";
CLOSE_BOX();
close_body();

?>
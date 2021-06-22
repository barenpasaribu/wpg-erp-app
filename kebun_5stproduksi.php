<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/kebun_5stproduksi.js'></script>\r\n";
$arr = '##bibit##tanah##umur##produksi##method##oldjb##oldkt##oldum';
include 'master_mainMenu.php';
OPEN_BOX();
$optbibit = "<option value=''></option>";
$str = 'select * from '.$dbname.'.setup_jenisbibit order by jenisbibit';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optbibit .= "<option value='".$bar->jenisbibit."'>".$bar->jenisbibit.'</option>';
}
$opttanah = "<option value=''></option>";
$x = readCountry('config/jenistanah.lst');
foreach ($x as $bar => $val) {
    $opttanah .= "<option value='".$val[0]."'>".$val[1].'</option>';
}
echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['standardprodkebun']."</legend>\r\n\t <table>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['jenisbibit']."<input type='hidden' id=oldjb name=oldjb /></td>\r\n\t   <td><select id=bibit style='width:150px;'>".$optbibit."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['klasifikasitanah']."<input type='hidden' id=oldkt name=oldkt /></td>\r\n\t   <td><select id=tanah style='width:150px;'>".$opttanah."</select></td>\r\n\t </tr>\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['umur']."<input type='hidden' id=oldum name=oldum /></td>\r\n\t   <td><input type=text class=myinputtext id=umur name=umur onkeypress=\"return angkadowang(event);\" style=\"width:150px;\" maxlength=2/></td>\r\n\t </tr>\t\r\n\t <tr>\r\n\t   <td>".$_SESSION['lang']['kgproduksi']."/Ha</td>\r\n\t   <td><input type=text class=myinputtext id=produksi name=produksi onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength=10></td>\r\n\t </tr>\t \r\n\t </table>\r\n\t <input type=hidden value=insert id=method>\r\n\t <button class=mybutton onclick=saveFranco('kebun_slave_5stproduksi','".$arr."')>".$_SESSION['lang']['save']."</button>\r\n\t <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>\r\n     </fieldset><input type='hidden' id=hiddenz name=hiddenz />";
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset><legend>'.$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>\r\n     <thead>\r\n\t  <tr class=rowheader>\r\n\t   <td>No</td>\r\n\t   <td>".$_SESSION['lang']['jenisbibit']."</td>\r\n\t   <td>".$_SESSION['lang']['klasifikasitanah']."</td>\r\n\t   <td>".$_SESSION['lang']['umur']."</td>\r\n\t   <td>".$_SESSION['lang']['kgproduksi']."/Ha</td>\r\n\t   <td>".$_SESSION['lang']['action']."</td>\r\n\t  </tr>\r\n     </thead>\r\n     <tbody id=container>";
echo "<script>loadData()</script></tbody>\r\n     <tfoot>\r\n     </tfoot>\r\n     </table></fieldset>";
CLOSE_BOX();
echo close_body();

?>
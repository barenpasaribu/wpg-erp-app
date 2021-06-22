<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_payrollHO.js></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
include 'master_mainMenu.php';
$opt = "<option value='01'>01</option>";
$opt .= "<option value='02'>02</option>";
$opt .= "<option value='03'>03</option>";
$opt .= "<option value='04'>04</option>";
$opt .= "<option value='05'>05</option>";
$opt .= "<option value='06'>06</option>";
$opt .= "<option value='07'>07</option>";
$opt .= "<option value='08'>08</option>";
$opt .= "<option value='09'>09</option>";
$opt .= "<option value='10'>10</option>";
$opt .= "<option value='11'>11</option>";
$opt .= "<option value='12'>12</option>";
for ($x = -1; $x <= 50; ++$x) {
    $opt1 .= "<option value='".(date('Y') - $x)."'>".(date('Y') - $x).'</option>';
}
OPEN_BOX('', '<b>'.$_SESSION['lang']['akunbank'].'</b>');
echo '<center><div>';
echo OPEN_THEME('<font color=white>'.$_SESSION['lang']['akunbanknote'].':</font>');
echo "<fieldset>\r\n\t\t         <legend>\r\n\t\t\t\t <img src=images/info.png align=left height=35px valign=asmiddle>\r\n\t\t\t\t </legend>\r\n\t\t\t\t".$_SESSION['lang']['akunbankinfo']."\t\t\t\t \r\n\t\t      </fieldset>";
$prestr = 'select * from '.$dbname.'.sdm_ho_employee order by karyawanid';
$preres = mysql_query($prestr, $conn);
echo "<table class=sortable cellspacing=1 border=0>\r\n\t\t     <thead>\r\n\t\t\t   <tr class=rowheader>\r\n\t\t\t    <td>".$_SESSION['lang']['pilih']."</td>\r\n\t\t\t    <td>No.</td>\r\n\t\t\t    <td>".$_SESSION['lang']['nokaryawan']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['namabank']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['norekeningbank']."</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['jms']."</td>\r\n\t\t\t\t<td>JMS.Start</td>\r\n\t\t\t\t<td>#1st.Pymnt<br>Period</td>\r\n\t\t\t\t<td>#1st.Vol<br>(%)</td>\r\n\t\t\t\t<td>Last.Pymnt<br>Period</td>\r\n\t\t\t\t<td>Last.Vol<br>(%)</td>\r\n\t\t\t\t<td>".$_SESSION['lang']['save']."</td>\r\n\t\t\t\t</tr>\r\n\t\t\t </thead>\r\n\t\t\t <tbody id=tablebody>\r\n\t\t\t ";
$no = 0;
while ($bar = mysql_fetch_object($preres)) {
    ++$no;
    if ('' == $bar->bank || '' == $bar->bankaccount) {
        $stat = '';
        $ch = 'checked';
    } else {
        $stat = 'disabled';
        $ch = '';
    }

    echo '<tr class=rowcontent id=row'.$no.">\r\n\t\t\t     <td><input type=checkbox id=check".$no.' '.$ch." onclick=vLine(this,'".$no."')></td>\r\n\t\t\t     <td class=firsttd>".$no."</td>\r\n\t\t\t\t <td id=userid".$no.'>'.$bar->karyawanid."</td>\r\n\t\t\t\t <td id=nama".$no.'>'.$bar->name."</td>\r\n\t\t\t\t <td><input type=text class=myinputtext id=bank".$no." value='".$bar->bank."' ".$stat." onkeypress=\"return tanpa_kutip(event);\"></td>\r\n\t\t\t\t <td><input type=text class=myinputtext id=bankac".$no." value='".$bar->bankaccount."' size=8 ".$stat." onkeypress=\"return tanpa_kutip(event);\"></td>\r\n\t\t\t\t <td><input type=text class=myinputtext id=jms".$no." value='".$bar->nojms."' size=6 ".$stat." onkeypress=\"return tanpa_kutip(event);\"></td>\t\t\r\n\t\t\t\t <td>\r\n\t\t\t\t \t<select id='jmsstartbl".$no."'  ".$stat."><option value='".substr($bar->jmsstart, 5, 2)."'>".substr($bar->jmsstart, 5, 2)."</option>\r\n\t\t\t\t\t".$opt."\r\n\t\t\t\t\t</select>\r\n\t\t\t\t \t<select id='jmsstartth".$no."'   ".$stat."><option value='".substr($bar->jmsstart, 0, 4)."'>".substr($bar->jmsstart, 0, 4)."</option>\r\n\t\t\t\t\t".$opt1."\r\n\t\t\t\t\t</select>\t\t\t\t\t\r\n\t\t\t\t </td>\r\n\t\t\t\t <td>\r\n\t\t\t\t \t<select onchange=cekDateUnder('".$no."') id='firstbl".$no."'  ".$stat."> <option value='".substr($bar->firstpayment, 5, 2)."'>".substr($bar->firstpayment, 5, 2)."</option>\r\n\t\t\t\t\t".$opt."\r\n\t\t\t\t\t</select>\r\n\t\t\t\t \t<select onchange=cekDateUnder('".$no."') id='firstth".$no."'   ".$stat."> <option value='".substr($bar->firstpayment, 0, 4)."'>".substr($bar->firstpayment, 0, 4)."</option>\r\n\t\t\t\t\t".$opt1."\r\n\t\t\t\t\t</select>\t\t\t\t\t\r\n\t\t\t\t </td>\r\n\t\t\t\t <td><input type=text class=myinputtext id=firstvol".$no." value='".$bar->firstvol."' size=4 ".$stat." onkeypress=\"return angka_doang(event);\" maxlength=5></td>\r\n\t\t\t\t <td>\r\n\t\t\t\t \t<select onchange=cekDateUnder('".$no."') id='lastbl".$no."'   ".$stat."> <option value='".substr($bar->lastpayment, 5, 2)."'>".substr($bar->lastpayment, 5, 2)."</option>\r\n\t\t\t\t\t".$opt."\r\n\t\t\t\t\t</select>\r\n\t\t\t\t \t<select onchange=cekDateUnder('".$no."') id='lastth".$no."'   ".$stat."> <option value='".substr($bar->lastpayment, 0, 4)."'>".substr($bar->lastpayment, 0, 4)."</option>\r\n\t\t\t\t\t".$opt1."\r\n\t\t\t\t\t</select>\t\t\t\t\t \r\n\t\t\t\t </td>\r\n\t\t\t\t <td><input type=text class=myinputtext id='lastvol".$no."' value='".$bar->lastvol."' size=4 ".$stat."  onkeypress=\"return angka_doang(event);\" maxlength=5></td>\t\t\t\t \r\n\t\t\t\t <td><button class=mybutton id=butt".$no." style='padding:0px;' title='Save this line' ".$stat." onclick=saOneLine('".$no."')><img src='images/save.png' height=12px></button></td>\r\n\t\t\t\t </tr>";
}
echo "</tbody>\r\n\t\t     <tfoot>\r\n\t\t\t </tfoot>\r\n\t\t\t </table>\r\n\t\t\t <center><button class=mybutton onclick=saveAll('".$no."')>Save All Checked</button></center>\r\n\t\t\t ";
echo CLOSE_THEME('');
echo '</div></center>';
CLOSE_BOX();
echo close_body();

?>
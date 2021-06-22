<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include 'lib/zFunction.php';
require_once 'lib/fpdf.php';
$param = $_POST;
$optPend = makeOption($dbname, 'sdm_5pendidikan', 'levelpendidikan,pendidikan');
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optJbtn = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optNmlowongan = makeOption($dbname, 'sdm_permintaansdm', 'notransaksi,namalowongan');
switch ($param['proses']) {
    case 'getData':
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sprd = 'select distinct idpermintaan,namalowongan from '.$dbname.".`sdm_testcalon` \r\n               where periodetest='".$_POST['periodeTest']."' order by periodetest desc";
        $qprd = mysql_query($sprd);
        while ($rprd = mysql_fetch_assoc($qprd)) {
            $optPeriode .= "<option value='".$rprd['idpermintaan']."'>".$rprd['namalowongan'].'</option>';
        }
        echo $optPeriode;

        break;
    case 'loadData':
        $saks = 'select distinct * from '.$dbname.".setup_remotetimbangan \r\n               where lokasi='HRDJKRT'";
        $qaks = mysql_query($saks);
        $jaks = mysql_fetch_assoc($qaks);
        $uname2 = $jaks['username'];
        $passwd2 = $jaks['password'];
        $dbserver2 = $jaks['ip'];
        $dbport2 = $jaks['port'];
        $dbdt = $jaks['dbname'];
        $conn2 = mysql_connect($dbserver2, $uname2, $passwd2);
        if (!$conn2) {
            exit('Could not connect: '.mysql_error());
        }

        if ('' == $param['periodeTest'] || '' == $param['nmLowongan']) {
            exit('error: Semua Field Tidak Boleh Kosong');
        }

        echo "<input type=hidden id=nopermintaan value='".$param['nmLowongan']."' />";
        echo "\r\n        <table border=0><tr><td valign=top>    \r\n        <table cellpadding=2 cellspacing=1 border=0 class=sortable>\r\n               <thead>\r\n\t       <tr class=rowheader><td>No</td>";
        echo '<td>'.$_SESSION['lang']['email'].'</td>';
        echo '<td>'.$_SESSION['lang']['nama'].'</td>';
        echo '<td>'.$_SESSION['lang']['pendidikan'].'</td>';
        echo '<td>'.$_SESSION['lang']['action'].'</td>';
        echo '</tr></thead><tbody id=listData>';
        $sdt = 'select distinct * from '.$dbname.".sdm_testcalon where \r\n              hasilakhir is null and hasiliview is null and idpermintaan='".$param['nmLowongan']."' order by email asc";
        $qdt = mysql_query($sdt, $conn);
        while ($rdt = mysql_fetch_assoc($qdt)) {
            ++$nor;
            $sdt2 = 'select distinct namacalon from '.$dbdt.".datacalon where email='".$rdt['email']."'";
            $qdt2 = mysql_query($sdt2, $conn2) || exit(mysql_error($conn2));
            $rdt2 = mysql_fetch_assoc($qdt2);
            $sdt3 = 'select distinct levelpendidikan from '.$dbdt.".pendidikan where email='".$rdt['email']."'  order by levelpendidikan desc ";
            $qdt3 = mysql_query($sdt3, $conn2) || exit(mysql_error($conn2));
            $rdt3 = mysql_fetch_assoc($qdt3);
            $adert = '##nmLowongan##emailDt_'.$nor.'';
            echo '<tr class=rowcontent>';
            echo '<td>'.$nor.'</td>';
            echo '<td id=emailDt_'.$nor."  value='".$rdt['email']."'>".$rdt['email'].'</td>';
            echo '<td id=namaDt_'.$nor.'>'.$rdt2['namacalon'].'</td>';
            echo '<td>'.$optPend[$rdt3['levelpendidikan']].'</td>';
            echo "<td>\r\n            <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"zPdf('sdm_slave_finalDecison','".$adert."','".$nor."','contentData')\">\r\n            <button class='mybutton' onclick='getFormPenilaian(".$nor.")' >".$_SESSION['lang']['interviewer']."</button>\r\n            </td>";
            echo '</tr>';
        }
        echo '</tbody></table></td><td valign=top><div id=dtForm></div><td></tr></table>';

        break;
    case 'getForm':
        $sdt = 'select distinct tglinterview from '.$dbname.".sdm_interview where email='".$param['emailDt']."'";
        $qrdt = mysql_query($sdt);
        $rdt = mysql_fetch_assoc($qrdt);
        $dFrom = "<div style=\"background-color:#CCCCCC\">\r\n                <fieldset><legend>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['pilih'].' '.$_SESSION['lang']['interviewer']."</legend>\r\n                ".$_SESSION['lang']['tanggal']." Interview : <input type=text class=myinputtext id=tglInter onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value='".tanggalnormal($rdt['tglinterview'])."' />\r\n                <table cellpadding=1 cellspacing=1 border=0>";
        $sdtkar = 'select distinct karyawanid from '.$dbname.".datakaryawan \r\n                 where tipekaryawan='5' and tanggalkeluar>".$_SESSION['org']['period']['start']."\r\n                 and kodejabatan in ('2','3','4','5','7','8','11','97','124','142','179','186','187','195','197','198','199','200','217')\r\n                 order by namakaryawan asc";
        $qdatkar = mysql_query($sdtkar);
        while ($rdatkary = mysql_fetch_assoc($qdatkar)) {
            $dertkary[] = $rdatkary['karyawanid'];
        }
        $der = 0;
        $jmrow = count($dertkary);
        $der = 0;
        while ($der < $jmrow) {
            if ('' != $dertkary[$der]) {
                $erct = '';
                $sdt = 'select distinct email from '.$dbname.".sdm_interview where interviewer='".$dertkary[$der]."' and email='".$param['emailDt']."'";
                $qrdt = mysql_query($sdt);
                $rdt = mysql_num_rows($qrdt);
                if (1 == $rdt) {
                    $erct = 'checked';
                }

                $dFrom .= '<tr>';
                $dFrom .= "<td><input type=checkbox id='interview_".$der."' onclick='saveView(".$der.")' ".$erct.' >'.$optNmKar[$dertkary[$der]].' <input type=hidden id=karyId_'.$der." value='".$dertkary[$der]."' />";
                $dFrom .= '</td>';
                ++$der;
                $erct = '';
                $sdt = 'select distinct email from '.$dbname.".sdm_interview where interviewer='".$dertkary[$der]."' and email='".$param['emailDt']."'";
                $qrdt = mysql_query($sdt);
                $rdt = mysql_num_rows($qrdt);
                if (1 == $rdt) {
                    $erct = 'checked';
                }

                $dFrom .= "<td><input type=checkbox id='interview_".$der."' onclick='saveView(".$der.")' ".$erct.' >'.$optNmKar[$dertkary[$der]].' <input type=hidden id=karyId_'.$der." value='".$dertkary[$der]."' />";
                $dFrom .= '</td>';
                ++$der;
                $erct = '';
                $sdt = 'select distinct email from '.$dbname.".sdm_interview where interviewer='".$dertkary[$der]."' and email='".$param['emailDt']."'";
                $qrdt = mysql_query($sdt);
                $rdt = mysql_num_rows($qrdt);
                if (1 == $rdt) {
                    $erct = 'checked';
                }

                $dFrom .= "<td><input type=checkbox id='interview_".$der."' onclick='saveView(".$der.")' ".$erct.' >'.$optNmKar[$dertkary[$der]].'  <input type=hidden id=karyId_'.$der." value='".$dertkary[$der]."' />";
                $dFrom .= '</td>';
                $dFrom .= '</tr>';
                ++$der;
                $erct = '';
                $sdt = 'select distinct email from '.$dbname.".sdm_interview where interviewer='".$dertkary[$der]."' and email='".$param['emailDt']."'";
                $qrdt = mysql_query($sdt);
                $rdt = mysql_num_rows($qrdt);
                if (1 == $rdt) {
                    $erct = 'checked';
                }
            }
        }
        $dFrom .= "<tr><td colspan=3 align=right><button class=mybutton onclick='closeForm()'>".$_SESSION['lang']['tutup'].'</button></td></tr>';
        $dFrom .= '</table></fieldset></div>';
        $dFrom .= "<input type=hidden id=emailDt value='".$param['emailDt']."' />";
        echo $dFrom;

        break;
    case 'insrData':
        if ('' == $param['tglInterv']) {
            exit('Error:Tanggal Interview Kosong!!');
        }

        $dtgl = explode('-', $param['tglInterv']);
        $prdtest = $dtgl[2].'-'.$dtgl[1];
        if ($param['periode'] != $prdtest) {
            exit('error: Tanggal interview di luar periode');
        }

        $sdel = 'delete from '.$dbname.".sdm_interview where email='".$param['emailDt']."' and interviewer='".$param['karyId']."'";
        if (mysql_query($sdel)) {
            $sinsrt = 'insert into '.$dbname.".sdm_interview (`email`,`interviewer`,`tglinterview`) values\r\n                         ('".$param['emailDt']."','".$param['karyId']."','".tanggalsystem($param['tglInterv'])."')";
            if (!mysql_query($sinsrt)) {
                #exit(mysql_error($conn));
            }

            $to = getUserEmail($param['karyId']);
            $namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
            if ('EN' == $_SESSION['language']) {
                $subject = '[Notifikasi] PR Submission for approval, submitted by: '.$namakaryawan;
                $body = "<html>\r\n                                 <head>\r\n                                 <body>\r\n                                   <dd>Dear Sir/Madam,</dd><br>\r\n                                   <br>\r\n                                   Today,  ".date('d-m-Y').',  on behalf of '.$namakaryawan." submit a PR, requesting for your approval. To follow up, please follow the link below.\r\n                                   <br>\r\n                                   <br>\r\n                                   <br>\r\n                                   Regards,<br>\r\n                                   eAgro Plantation Management Software.\r\n                                 </body>\r\n                                 </head>\r\n                               </html>\r\n                               ";
            } else {
                $subject = '[Notifikasi]Undangan Hadir Wawancara PP a/n '.$namakaryawan;
                $body = "<html>\r\n                                 <head>\r\n                                 <body>\r\n                                   <dd>Dengan Hormat,</dd><br>\r\n                                   <br>\r\n                                   Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan." mengundang\r\n                                   kepada bapak/ibu. Untuk menghadiri wawancara karyawan baru pada tanggal ".$param['tglInterv']."\r\n                                   <br>\r\n                                   <br>\r\n                                   <br>\r\n                                   Regards,<br>\r\n                                   eAgro Plantation Management Software.\r\n                                 </body>\r\n                                 </head>\r\n                               </html>\r\n                               ";
            }

            $kirim = kirimEmailWindows($to, $subject, $body);

            break;
        }

        #exit(mysql_error($conn));
    case 'delData':
        $sdel = 'delete from '.$dbname.".sdm_interview where email='".$param['emailDt']."' and interviewer='".$param['karyId']."'";
        if (!mysql_query($sdel)) {
            #exit(mysql_error($conn));
        }

        break;
}

?>
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
$optJbtn = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optNmlowongan = makeOption($dbname, 'sdm_permintaansdm', 'notransaksi,namalowongan');
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
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
        echo '<td>'.$_SESSION['lang']['status'].'</td>';
        echo '<td>'.$_SESSION['lang']['action'].'</td>';
        echo '</tr></thead><tbody id=listData>';
        $sdt = 'select distinct * from '.$dbname.".sdm_testcalon where \r\n              (hasilakhir is null or hasiliview is null) and idpermintaan='".$param['nmLowongan']."' order by email asc";
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
            echo '<td id=emailDt_'.$nor." value='".$rdt['email']."'>".$rdt['email'].'</td>';
            echo '<td id=namaDt_'.$nor.'>'.$rdt2['namacalon'].'</td>';
            echo '<td>'.$optPend[$rdt3['levelpendidikan']].'</td>';
            echo '<td>'.$rdt['hasiliview'].'</td>';
            echo "<td>\r\n            <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"zPdf('sdm_slave_finalDecison','".$adert."','".$nor."','contentData')\">\r\n            <button class='mybutton' onclick='getFormPenilaian(".$nor.")' >Penilaian</button>\r\n            </td>";
            echo '</tr>';
        }
        echo "</tbody></table></td><td valign=top><div id=dtForm></div><div id=formPen style='display:none'></div><td></tr></table>";

        break;
    case 'getForm':
        $dFrom = "<div style=\"background-color:#CCCCCC\">\r\n            <fieldset><legend>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['nilai']." </legend>\r\n            <table cellpadding=1 cellspacing=1 border=0>";
        $sdt = 'select distinct interviewer from '.$dbname.".sdm_interview where email='".$param['emailDt']."'";
        $qDt = mysql_query($sdt);
        while ($rdt = mysql_fetch_assoc($qDt)) {
            $dertkary[] = $rdt['interviewer'];
        }
        $jmrow = count($dertkary);
        $der = 0;
        while ($der < $jmrow) {
            $dert = '';
            $whered = "email='".$param['emailDt']."' and interviewer='".$dertkary[$der]."'";
            $optstat = makeOption($dbname, 'sdm_interview', 'interviewer,stat', $whered);
            if (1 == $optstat[$dertkary[$der]]) {
                $dert = 'checked=checked ';
            }

            $dFrom .= '<tr>';
            if ('' != $dertkary[$der]) {
                $dFrom .= "<td><input type=radio id='interviewFinal_".$der."' ".$dert." onclick=\"svPenilaian('".$dertkary[$der]."','".$param['idKe']."','".$der."','".$jmrow."')\" title='Keputusan Final Interview' /><a href=# onclick=getFormPen('".$dertkary[$der]."','".$param['idKe']."')>".$optNmKar[$dertkary[$der]].'</a></td>';
                ++$der;
            } else {
                $dFrom .= '<td>&nbsp;</td>';
                ++$der;
            }

            $dert = '';
            $whered = "email='".$param['emailDt']."' and interviewer='".$dertkary[$der]."'";
            $optstat = makeOption($dbname, 'sdm_interview', 'interviewer,stat', $whered);
            if (1 == $optstat[$dertkary[$der]]) {
                $dert = 'checked=checked ';
            }

            if ('' != $dertkary[$der]) {
                $dFrom .= "<td><input type=radio id='interviewFinal_".$der."' ".$dert." onclick=\"svPenilaian('".$dertkary[$der]."','".$param['idKe']."','".$der."','".$jmrow."')\" title='Keputusan Final Interview' /><a href=# onclick=getFormPen('".$dertkary[$der]."','".$param['idKe']."')>".$optNmKar[$dertkary[$der]].'</a></td>';
                ++$der;
            } else {
                $dFrom .= '<td>&nbsp;|</td>';
                ++$der;
            }

            $dert = '';
            $whered = "email='".$param['emailDt']."' and interviewer='".$dertkary[$der]."'";
            $optstat = makeOption($dbname, 'sdm_interview', 'interviewer,stat', $whered);
            if (1 == $optstat[$dertkary[$der]]) {
                $dert = 'checked=checked ';
            }

            if ('' != $dertkary[$der]) {
                $dFrom .= "<td><input type=radio id='interviewFinal_".$der."' ".$dert." onclick=\"svPenilaian('".$dertkary[$der]."','".$param['idKe']."','".$der."','".$jmrow."')\" title='Keputusan Final Interview' /><a href=# onclick=getFormPen('".$dertkary[$der]."','".$param['idKe']."')>".$optNmKar[$dertkary[$der]].'</a></td>';
                $dFrom .= '</tr>';
                ++$der;
            } else {
                $dFrom .= '<td>&nbsp;</td>';
                $dFrom .= '</tr>';
                ++$der;
            }

            $dert = '';
            $whered = "email='".$param['emailDt']."' and interviewer='".$dertkary[$der]."'";
            $optstat = makeOption($dbname, 'sdm_interview', 'interviewer,stat', $whered);
            if (1 == $optstat[$dertkary[$der]]) {
                $dert = 'checked=checked ';
            }
        }
        $dFrom .= "<tr><td colspan=3 align=right><button class=mybutton onclick='closeForm()'>".$_SESSION['lang']['tutup'].'</button></td></tr>';
        $dFrom .= '</table></fieldset></div>';
        $dFrom .= "<input type=hidden id=emailDt value='".$param['emailDt']."' />";
        echo $dFrom;

        break;
    case 'getForm2':
        $sdtkar = 'select distinct karyawanid,namakaryawan,kodejabatan from '.$dbname.".datakaryawan \r\n                 where karyawanid='".$param['karyId']."'\r\n                 order by namakaryawan asc";
        $qdatkar = mysql_query($sdtkar, $conn);
        while ($rdatkary = mysql_fetch_assoc($qdatkar)) {
            $optKar .= "<option value='".$rdatkary['karyawanid']."'>".$rdatkary['namakaryawan'].' - '.$optJbtn[$rdatkary['kodejabatan']].'</option>';
        }
        $dert = 'select distinct * from '.$dbname.".sdm_interview where \r\n               email='".$param['emailDt']."' and  interviewer='".$param['karyId']."'";
        $qdert = mysql_query($dert);
        $rdert = mysql_fetch_assoc($qdert);
        $arrenum = getEnum($dbname, 'sdm_interview', 'hasil');
        foreach ($arrenum as $key => $val) {
            $optGoldar .= "<option value='".$key."' ".(($rdert['hasil'] == $key ? 'selected' : '')).'>'.$val.'</option>';
        }
        $dFrom = "<div style=\"background-color:#CCCCCC\">\r\n                <fieldset><legend>".$_SESSION['lang']['form'].' '.$_SESSION['lang']['nilai']." </legend>\r\n                <table cellpadding=1 cellspacing=1 border=0>";
        $dFrom .= '<tr><td>'.$_SESSION['lang']['nama']."</td><td><input type=text class=myinputtext value='".$param['namacalon']."' style='width:150px' disabled />";
        $dFrom .= '</td></tr>';
        $dFrom .= '<tr><td>'.$_SESSION['lang']['interviewer'].'</td>';
        $dFrom .= '<td><select id=interview style=width:150px;>'.$optKar.'</select></td</tr>';
        $dFrom .= '<tr><td>'.$_SESSION['lang']['hasil'].'</td>';
        $dFrom .= '<td><select id=hasilIntview style=width:150px;>'.$optGoldar.'</select></td</tr>';
        $dFrom .= '<tr><td>'.$_SESSION['lang']['catatan'].'</td>';
        $dFrom .= '<td><textarea id=catatan>'.$rdert['catatan'].'</textarea></td></tr>';
        $dFrom .= '<tr><td>'.$_SESSION['lang']['tanggal'].'</td>';
        $dFrom .= "<td><input type=text class=myinputtext id=tglinterview \r\n                    onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value='".tanggalnormal($rdert['tanggal'])."' /></td></tr>";
        $dFrom .= '</table><button class=mybutton onclick=saveView()>'.$_SESSION['lang']['save']."</button></fieldset>\r\n                 <input type=hidden id=emailDt value='".$param['emailDt']."' />";
        $dFrom .= '</div>';
        echo $dFrom;

        break;
    case 'insrData':
        $sdel = 'delete from '.$dbname.".sdm_interview where email='".$param['emailDt']."' and interviewer='".$param['interviewer']."'";
        if (mysql_query($sdel)) {
            $sinsrt = 'insert into '.$dbname.".sdm_interview (`email`,`interviewer`,`tanggal`,`hasil`,`catatan`) values\r\n                         ('".$param['emailDt']."','".$param['interviewer']."','".tanggalsystem($param['tglInterview'])."','".$param['hasilIntview']."','".$param['cttn']."')";
            if (!mysql_query($sinsrt)) {
                #exit(mysql_error($conn));
            }

            break;
        }

        #exit(mysql_error($conn));
    case 'updateSdmTest':
        $sdt = 'select distinct * from '.$dbname.".sdm_interview where \r\n               email='".$param['emailDt']."' and  interviewer='".$_POST['karyId']."'";
        $qdt = mysql_query($sdt);
        $rdt = mysql_fetch_assoc($qdt);
        $sinsrt = 'update '.$dbname.".sdm_testcalon set tglivew='".$rdt['tanggal']."',hasiliview='".$rdt['hasil']."',\r\n                             keteranganiview='".$rdt['catatan']."' where email='".$param['emailDt']."'\r\n                             and idpermintaan='".$param['idPermintaan']."'";
        if (mysql_query($sinsrt)) {
            $supdate = 'update '.$dbname.".sdm_interview set stat=1 where email='".$param['emailDt']."' and  interviewer='".$_POST['karyId']."'";
            if (!mysql_query($supdate)) {
                #exit(mysql_error($conn));
            }

            $supdate = 'update '.$dbname.".sdm_interview set stat=0 where email='".$param['emailDt']."' and  interviewer<>'".$_POST['karyId']."'";
            if (!mysql_query($supdate)) {
                #exit(mysql_error($conn));
            }

            break;
        }

        #exit(mysql_error($conn));
}

?>
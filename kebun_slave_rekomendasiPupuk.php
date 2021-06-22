<?php
session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$kdBrg = $_POST['kdBrg'];
$periode = $_POST['periode'];
$idKbn = $_POST['idKbn'];
$thnTnm = $_POST['thnTnm'];
$jnsPpk = $_POST['jnsPpk'];
$dosis = $_POST['dosis'];
$dosis2 = $_POST['dosis2'];
$dosis3 = $_POST['dosis3'];
$jnsBibit = $_POST['jnsBibit'];
$satuan = $_POST['satuan'];
$kdAfd = $_POST['kdAfd'];
$kdBlok = $_POST['kdBlok'];
$oldBlok = $_POST['oldBlok'];
switch ($proses) {
    case 'loadData':
        OPEN_BOX();
        echo "<fieldset>\r\n<legend>".$_SESSION['lang']['list'].'</legend>';
        echo "<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_rekomendasipupuk','','','kebun_slave_rekomendasipupukPdf',event);\">&nbsp;<img onclick=dataKeExcel(event,'kebun_slave_rekomendasipupukExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'>
		<table cellspacing=1 border=0 id=rkmndsiPupuk class='sortable'>
		<thead>
		<tr class=rowheader>
		<td>No</td>
		<td>".$_SESSION['lang']['tahunpupuk']."</td>
		<td>".$_SESSION['lang']['afdeling']."</td>
		<td>".$_SESSION['lang']['blok']."</td>
		<td>".$_SESSION['lang']['tahuntanam']."</td>
		<td>".$_SESSION['lang']['jenisPupuk']."</td>
		<td>".$_SESSION['lang']['dosis']." </td>
		<td>Dosis Ekstra</td>
		<td>Jenis Pupuk Ekstra</td>
		<td>".$_SESSION['lang']['satuan']."</td>
		<td>".$_SESSION['lang']['jenisbibit']."</td>
		<td>Action</td>
		</tr></thead>
		<tbody>";
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_rekomendasipupuk where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `periodepemupukan` desc";
        $query2 = mysql_query($sql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $slvhc = 'select * from '.$dbname.".kebun_rekomendasipupuk where  substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `periodepemupukan` desc limit ".$offset.','.$limit.'';
        $qlvhc = mysql_query($slvhc) ;
        while ($res = mysql_fetch_assoc($qlvhc)) {
            $skdBrg = 'select  namabarang,satuan from '.$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
            $qkdBrg = mysql_query($skdBrg) ;
            $rBrg = mysql_fetch_assoc($qkdBrg);
			
            $sBibit = 'select jenisbibit  from '.$dbname.".setup_jenisbibit where jenisbibit='".$res['jenisbibit']."'";
            $qBibit = mysql_query($sBibit) ;
            $rBibit = mysql_fetch_assoc($qBibit);
			
            $skdBrgx = 'select  namabarang,satuan from '.$dbname.".log_5masterbarang where kodebarang='".$res['dosis3']."'";
            $qkdBrgx = mysql_query($skdBrgx) ;
            $rBrgx = mysql_fetch_assoc($qkdBrgx);
			
            $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$res['kodeorg']."'";
            $qOrg = mysql_query($sOrg) ;
            $rOrg = mysql_fetch_assoc($qOrg);
			
            $sOrgx = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$res['blok']."'";
            $qOrgx = mysql_query($sOrgx) ;
            $rOrgx = mysql_fetch_assoc($qOrgx);
			
            ++$no;
            echo "<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$res['periodepemupukan']."</td>
			<td>".$rOrg['namaorganisasi']."</td>
			<td>".$rOrgx['namaorganisasi']."</td>
			<td>".$res['tahuntanam']."</td>
			<td>".$rBrg['namabarang']."</td>
			<td align='right'>".$res['dosis']."</td>
			<td align='right'>".$res['dosis2']."</td>
			<td align='right'>".$rBrgx['namabarang']."</td>
			<td>".$rBrg['satuan']."</td>
			<td>".$rBibit['jenisbibit'].'</td>';
            if (substr($res['kodeorg'], 0, 4) === $_SESSION['empl']['lokasitugas']) {
                echo "<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['periodepemupukan']."','".$res['kodeorg']."','".$res['tahuntanam']."','".$res['blok']."');\">\r\n\t\t\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['periodepemupukan']."','".$res['kodeorg']."','".$res['tahuntanam']."','".$res['blok']."');\" ></td></tr>";
            }
        }
        echo "\r\n\t\t\t\t\t<tr><td colspan=9 align=center>\r\n\t\t\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t\t</tr>";
        echo '</table></fieldset>';
        CLOSE_BOX();

        break;
    case 'getSatuan':
        $skdBrg = 'select  satuan from '.$dbname.".log_5masterbarang where kodebarang='".$kdBrg."'";
        $qkdBrg = mysql_query($skdBrg) ;
        $rBrg = mysql_fetch_assoc($qkdBrg);
        echo $rBrg['satuan'];

        break;
    case 'insert':
        if ('' === $jnsPpk || '' === $dosis) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sCek = 'select kodeorg,tahuntanam,periodepemupukan from '.$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."' and blok='".$kdBlok."' and kodebarang='".$jnsPpk."'";
        $qCek = mysql_query($sCek) ;
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sIns = 'insert into '.$dbname.".kebun_rekomendasipupuk (kodeorg,blok, tahuntanam, kodebarang, dosis, dosis2, dosis3, satuan, periodepemupukan, jenisbibit) values \r\n\t\t\t('".$idKbn."','".$kdBlok."','".$thnTnm."','".$jnsPpk."','".$dosis."','".$dosis2."','".$dosis3."','".$satuan."','".$periode."','".$jnsBibit."')";
            if (mysql_query($sIns)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }

            break;
        }

        echo 'warning:This Data Already Input';
        exit();
    case 'getData':
        $sGet = 'select * from '.$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."' and blok='".$kdBlok."'";
        $qGet = mysql_query($sGet) ;
        $rGet = mysql_fetch_assoc($qGet);
        echo $rGet['kodeorg'].'###'.$rGet['kodebarang'].'###'.$rGet['dosis'].'###'.$rGet['satuan'].'###'.$rGet['periodepemupukan'].'###'.$rGet['jenisbibit'].'###'.$rGet['blok'].'###'.$rGet['tahuntanam'].'###'.$rGet['dosis3'];

        break;
    case 'update':
        if ('' === $jnsPpk || '' === $dosis) {
            echo 'warning:Please Complete The Form';
            exit();
        }

        $sUp = 'update '.$dbname.".kebun_rekomendasipupuk set kodebarang='".$jnsPpk."', dosis='".$dosis."', dosis2='".$dosis2."', dosis3='".$dosis3."', satuan='".$satuan."', jenisbibit='".$jnsBibit."',blok='".$kdBlok."',tahuntanam='".$thnTnm."' where kodeorg='".$idKbn."' and periodepemupukan='".$periode."' and blok='".$oldBlok."'";
        if (mysql_query($sUp)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.".kebun_rekomendasipupuk where kodeorg='".$idKbn."' and blok='".$kdBlok."' and tahuntanam='".$thnTnm."' and periodepemupukan='".$periode."'";
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'cariData':
        OPEN_BOX();
        echo "<fieldset>\r\n<legend>".$_SESSION['lang']['result'].'</legend>';
        echo "<div style=\"width:600px; height:450px; overflow:auto;\">\r\n\t\t\t<table cellspacing=1 border=0 class='sortable'>\r\n\t\t<thead>\r\n<tr class=rowheader>\r\n<td>No</td>\r\n<td>".$_SESSION['lang']['tahunpupuk']."</td>\r\n<td>".$_SESSION['lang']['kebun']."</td>\r\n<td>".$_SESSION['lang']['tahuntanam']."</td>\r\n<td>".$_SESSION['lang']['jenisPupuk']."</td>\r\n<td>".$_SESSION['lang']['dosis']."</td>\r\n<!--td>".$_SESSION['lang']['dosis']." 2</td>\r\n<td>".$_SESSION['lang']['dosis']." 3</td-->\r\n<td>".$_SESSION['lang']['satuan']."</td>\r\n<td>".$_SESSION['lang']['jenisbibit']."</td>\r\n<td>Action</td>\r\n</tr>\r\n</thead>\r\n<tbody>\r\n";
        if ('' !== $periode) {
            $where = " periodepemupukan LIKE  '%".$periode."%'";
        } else {
            if ('' !== $idKbn) {
                $where .= " kodeorg LIKE '%".$idKbn."%'";
            } else {
                if ('' !== $periode && '' !== $idKbn) {
                    $where .= " periodepemupukan LIKE '%".$periode."%' and kodeorg LIKE '%".$idKbn."%'";
                }
            }
        }

        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $sql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_rekomendasipupuk where  '.$where.' order by `periodepemupukan` desc';
        $query2 = mysql_query($sql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $strx = 'select * from '.$dbname.'.kebun_rekomendasipupuk where '.$where.' order by periodepemupukan desc limit '.$offset.','.$limit.'';
        if ($qry = mysql_query($strx)) {
            $numrows = mysql_num_rows($qry);
            if ($numrows < 1) {
                echo '<tr class=rowcontent><td colspan=9>Not Found</td></tr>';
            } else {
                while ($res = mysql_fetch_assoc($qry)) {
                    $skdBrg = 'select  namabarang,satuan from '.$dbname.".log_5masterbarang where kodebarang='".$res['kodebarang']."'";
                    $qkdBrg = mysql_query($skdBrg) ;
                    $rBrg = mysql_fetch_assoc($qkdBrg);
                    $sBibit = 'select jenisbibit  from '.$dbname.".setup_jenisbibit where jenisbibit='".$res['jenisbibit']."'";
                    $qBibit = mysql_query($sBibit) ;
                    $rBibit = mysql_fetch_assoc($qBibit);
                    ++$no;
                    echo "\r\n\t\t\t\t\t<tr class=rowcontent>\r\n\t\t\t\t\t<td>".$no."</td>\r\n\t\t\t\t\t<td>".$res['periodepemupukan']."</td>\r\n\t\t\t\t\t<td>".$res['kodeorg']."</td>\r\n\t\t\t\t\t<td>".$res['tahuntanam']."</td>\r\n\t\t\t\t\t<td>".$rBrg['namabarang']."</td>\r\n\t\t\t\t\t<td>".$res['dosis']."</td>\r\n\t\t\t\t\t<!--td>".$res['dosis2']."</td>\r\n\t\t\t\t\t<td>".$res['dosis3']."</td-->\r\n\t\t\t\t\t<td>".$rBrg['satuan']."</td>\r\n\t\t\t\t\t<td>".$rBibit['jenisbibit'].'</td>';
                    if (substr($res['kodeorg'], 0, 4) === $_SESSION['empl']['lokasitugas']) {
                        echo "\r\n\t\t\t\t\t\t\t<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['notransaksi']."');\">\r\n\t\t\t\t\t\t\t<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$res['notransaksi']."');\" >\r\n\t\t\t\t\t\t</td>\r\n\t\t\t\t\t\t\t</tr>";
                    }
                }
                echo "\r\n\t\t\t\t\t<tr><td colspan=9 align=center>\r\n\t\t\t\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t\t\t\t<button class=mybutton onclick=cariHasil(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t\t\t\t<button class=mybutton onclick=cariHasil(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t\t\t\t</td>\r\n\t\t\t\t\t</tr>";
                echo '</tbody></table></div></fieldset>';
            }
        } else {
            echo 'Gagal,'.mysql_error($conn);
        }

        CLOSE_BOX();

        break;
    case 'getBlok':
        $optBlok = '<option value=></option>';
        $sBlok = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$kdAfd."'";
        $qBlok = mysql_query($sBlok) ;
        while ($rBlok = mysql_fetch_assoc($qBlok)) {
            if ('' !== $kdBlok) {
                $optBlok .= "<option value='".$rBlok['kodeorganisasi']."'  ".(($kdBlok === $rBlok['kodeorganisasi'] ? 'selected' : '')).'>'.$rBlok['namaorganisasi'].'</option>';
            } else {
                $optBlok .= '<option value='.$rBlok['kodeorganisasi'].'>'.$rBlok['namaorganisasi'].'</option>';
            }
        }
        echo $optBlok;

        break;
    case 'getThn':
        $sThn = 'select tahuntanam from '.$dbname.".setup_blok where kodeorg='".$kdBlok."'";
        $qThn = mysql_query($sThn) ;
        while ($rThn = mysql_fetch_assoc($qThn)) {
            $optThn = '<option value='.$rThn['tahuntanam'].'>'.$rThn['tahuntanam'].'</option>';
        }
        echo $optThn;

        break;
    default:
        break;
}

?>
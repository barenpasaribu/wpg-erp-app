<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
$kdBrg = $_POST['kdBrg'];
$custId = $_POST['custId'];
$noKontrak = $_POST['noKontrak'];
$optNma = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$txtSearch = $_POST['txtSearch'];
$txtTgl = tanggalsystem($_POST['txtTgl']);
$noTiket = $_POST['noTiket'];
$kodeVhc = $_POST['kodeVhc'];
$brtCust = $_POST['brtCust'];
$txtKntrk = $_POST['txtKntrk'];
switch ($proses) {
    case 'getCustomer':
        $optCust = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sCust = ' select distinct koderekanan from '.$dbname.'.pmn_kontrakjual where kodebarang='.$kdBrg.' ';
        $qCust = mysql_query($sCust);
        while ($rCust = mysql_fetch_assoc($qCust)) {
            if ('' !== $custId) {
                $optCust .= "<option value='".$rCust['koderekanan']."' ".(($rCust['koderekanan'] === $custId ? 'selected' : '')).'>'.$optNma[$rCust['koderekanan']].'</option>';
            } else {
                $optCust .= "<option value='".$rCust['koderekanan']."'>".$optNma[$rCust['koderekanan']].'</option>';
            }
        }
        echo $optCust;

        break;
    case 'getKontrak':
        $optCust = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sCust = ' select distinct nokontrak from '.$dbname.".pmn_kontrakjual where koderekanan='".$custId."' order by tanggalkontrak desc";
        $qCust = mysql_query($sCust);
        while ($rCust = mysql_fetch_assoc($qCust)) {
            if ('' !== $custId) {
                $optCust .= "<option value='".$rCust['nokontrak']."' ".(($rCust['nokontrak'] === $noKontrak ? 'selected' : '')).'>'.$rCust['nokontrak'].'</option>';
            } else {
                $optCust .= "<option value='".$rCust['nokontrak']."'>".$optNma[$rCust['nokontrak']].'</option>';
            }
        }
        echo $optCust;

        break;
    case 'getForm':
        if ('' === $noKontrak) {
            exit('Error: Contract number required');
        }

        $tab .= '<table cellspacing=1 cellpadding=1 border=0 class=sortable><thead><tr class=rowheader>';
        $tab .= '<td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['notransaksi'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodevhc'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['beratnormal'].' PKS (KG)</td>';
        $tab .= '<td>'.$_SESSION['lang']['beratnormal'].' '.substr($_SESSION['lang']['kodecustomer'], 5).'</td>';
        $tab .= '<td>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $sData = 'select notransaksi,tanggal,beratbersih,nokendaraan,nokontrak,kgpembeli from '.$dbname.".pabrik_timbangan where nokontrak='".$noKontrak."'";
        $qData = mysql_query($sData);
        $row = mysql_num_rows($qData);
        if (0 < $row) {
            while ($rData = mysql_fetch_assoc($qData)) {
                ++$no;
                $tab .= '<tr class=rowcontent id=baris_'.$no.'>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td id=notiket_'.$no.'>'.$rData['notransaksi'].'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td id=kendaran_'.$no.'>'.$rData['nokendaraan'].'</td>';
                $tab .= '<td align=right>'.number_format($rData['beratbersih'], 2).'</td>';
                $tab .= "<td><input type='text' class='myinputtextnumber' onkeypress='return angka_doang(event)' id='brtCust_".$no."' value='".$rData['kgpembeli']."' /></td>";
                $tab .= '<td><button class=mybutton id=simTmbl2_'.$no." onclick=saveForm('".$rData['notransaksi']."','".$rData['nokendaraan']."','".$rData['nokontrak']."','".$no."')>".$_SESSION['lang']['save'].'</button></td>';
                $tab .= '</tr>';
            }
            $tab .= '<thead><tr><td colspan=7 align=center><button class=mybutton id=simTmbl_'.$no.' onclick=saveAll(1)>'.$_SESSION['lang']['save'].' '.$_SESSION['lang']['all'].'</button><button class=mybutton id=dtlForm onclick=cancelForm()>'.$_SESSION['lang']['cancel'].'</button></td></tr>';
        } else {
            $tab .= '<tr><td colspan=7 align=center>'.$_SESSION['lang']['dataempty'].'</td></tr>';
            $tab .= '<thead><tr><td colspan=7 align=center><button class=mybutton id=dtlForm onclick=cancelForm()>'.$_SESSION['lang']['cancel'].'</button></td></tr></thead>';
        }

        $tab .= "</tbody></table><input type='hidden' id=nokontrak2 value='".$noKontrak."' /><input type='hidden' id=jmlhRow value='".$no."' />";
        echo $tab;

        break;
    case 'loadData':
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".pabrik_timbangan where nokontrak!='' order by tanggal desc";
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $tab .= '<table cellspacing=1 cellpadding=1 border=0 class=sortable><thead><tr class=rowheader>';
        $tab .= '<td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['notransaksi'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['NoKontrak'].' WB</td>';
        $tab .= '<td>'.$_SESSION['lang']['NoKontrak'].' Sales</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodevhc'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['beratnormal'].' PKS (KG)</td>';
        $tab .= '<td>'.$_SESSION['lang']['beratnormal'].' '.substr($_SESSION['lang']['kodecustomer'], 5).'</td>';
        $tab .= '<td>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $sData = 'select notransaksi,tanggal,beratbersih,nokendaraan,nokontrak,kodebarang,kgpembeli from '.$dbname.".pabrik_timbangan where nokontrak!=''  order by tanggal desc limit ".$offset.','.$limit.'';
        $qData = mysql_query($sData);
        $row = mysql_num_rows($qData);
        if (0 < $row) {
            while ($rData = mysql_fetch_assoc($qData)) {
                $sCust = ' select distinct nokontrak,koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$rData['nokontrak']."' ";
                $qCust = mysql_query($sCust);
                $rCust = mysql_fetch_assoc($qCust);
                ++$no;
                $tab .= '<tr class=rowcontent id=baris_'.$no.'>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td id=notiket_'.$no.'>'.$rData['notransaksi'].'</td>';
                $tab .= '<td id=nokontrak_'.$no.'>'.$rData['nokontrak'].'</td>';
                $tab .= '<td id=nokontrak_'.$no.'>'.$rCust['nokontrak'].'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td id=kendaran_'.$no.'>'.$rData['nokendaraan'].'</td>';
                $tab .= '<td align=right>'.number_format($rData['beratbersih'], 2).'</td>';
                $tab .= '<td align=right>'.number_format($rData['kgpembeli'], 2).'</td>';
                $tab .= "<td align='center'>\r\n                        <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['kodebarang']."','".$rCust['koderekanan']."','".$rCust['nokontrak']."');\">\r\n                        &nbsp;\r\n                        <img src=images/application/application_link.png class=resicon  title='Loco ".$rCust['nokontrak']."' onclick=\"locoData('".$rData['kodebarang']."','".$rCust['koderekanan']."','".$rCust['nokontrak']."');\">    \r\n                        </td>";
                $tab .= '</tr>';
            }
        } else {
            $tab .= '<tr class=rowcontent><td colspan=9 align=center>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        $tab .= "\r\n                </tr><tr class=rowheader><td colspan=9 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr></tbody></table>";
        echo $tab;

        break;
    case 'cariTransaksi':
        if ('' !== $txtSearch) {
            $where .= " and notransaksi like '%".$txtSearch."%'";
        }

        if ('' !== $txtTgl) {
            $thn = substr($txtTgl, 0, 4);
            $bln = substr($txtTgl, 4, 2);
            $tgl = substr($txtTgl, 6, 2);
            $txtTgl = $thn.'-'.$bln.'-'.$tgl;
            $where .= " and substr(tanggal,1,10)='".$txtTgl."'";
        }

        if ('' !== $txtKntrk) {
            $where .= " and nokontrak='".$txtKntrk."'";
        }

        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".pabrik_timbangan where nokontrak!='' ".$where.' order by tanggal desc';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $tab .= '<table cellspacing=1 cellpadding=1 border=0 class=sortable><thead><tr class=rowheader>';
        $tab .= '<td>No.</td>';
        $tab .= '<td>'.$_SESSION['lang']['notransaksi'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['NoKontrak'].' WB</td>';
        $tab .= '<td>'.$_SESSION['lang']['NoKontrak'].' Sales</td>';
        $tab .= '<td>'.$_SESSION['lang']['tanggal'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['kodevhc'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['beratnormal'].' PKS (KG)</td>';
        $tab .= '<td>'.$_SESSION['lang']['beratnormal'].' '.substr($_SESSION['lang']['kodecustomer'], 5).'</td>';
        $tab .= '<td>'.$_SESSION['lang']['action'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $sData = 'select notransaksi,tanggal,beratbersih,nokendaraan,nokontrak,kodebarang,kgpembeli from '.$dbname.".pabrik_timbangan where nokontrak!='' ".$where.' order by tanggal desc limit '.$offset.','.$limit.'';
        $qData = mysql_query($sData);
        $row = mysql_num_rows($qData);
        if (0 < $row) {
            while ($rData = mysql_fetch_assoc($qData)) {
                $sCust = ' select distinct nokontrak,koderekanan from '.$dbname.".pmn_kontrakjual where nokontrak='".$rData['nokontrak']."' ";
                $qCust = mysql_query($sCust);
                $rCust = mysql_fetch_assoc($qCust);
                ++$no;
                $tab .= '<tr class=rowcontent>';
                $tab .= '<td>'.$no.'</td>';
                $tab .= '<td id=notiket_'.$no.'>'.$rData['notransaksi'].'</td>';
                $tab .= '<td id=nokontrak_'.$no.'>'.$rData['nokontrak'].'</td>';
                $tab .= '<td id=nokontrak_'.$no.'>'.$rCust['nokontrak'].'</td>';
                $tab .= '<td>'.tanggalnormal($rData['tanggal']).'</td>';
                $tab .= '<td id=kendaran_'.$no.'>'.$rData['nokendaraan'].'</td>';
                $tab .= '<td align=right>'.number_format($rData['beratbersih'], 2).'</td>';
                $tab .= '<td align=right>'.number_format($rData['kgpembeli'], 2).'</td>';
                $tab .= "<td align='center'>\r\n                        <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['kodebarang']."','".$rCust['koderekanan']."','".$rData['nokontrak']."');\">\r\n                        &nbsp;\r\n                        <img src=images/application/application_link.png class=resicon  title='Loco ".$rData['nokontrak']."' onclick=\"locoData('".$rData['kodebarang']."','".$rCust['koderekanan']."','".$rData['nokontrak']."');\">\r\n                        </td>";
                $tab .= '</tr>';
            }
        } else {
            $tab .= '<tr class=rowcontent><td colspan=9 align=center>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        $tab .= "\r\n                </tr><tr class=rowheader><td colspan=9 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariBastTransaksi(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariBastTransaksi(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr></tbody></table>";
        echo $tab;

        break;
    case 'updateTimAll':
        $sUpdate = 'update '.$dbname.".pabrik_timbangan set kgpembeli='".$brtCust."' where notransaksi='".$noTiket."'";
        if (!mysql_query($sUpdate)) {
            echo 'DB Error : '.$sUpdate."\n".mysql_error($conn);
        }

        break;
    case 'updateKgTimbangan':
        $sData = 'select notransaksi,tanggal,beratbersih,nokendaraan,nokontrak,kgpembeli from '.$dbname.".pabrik_timbangan where nokontrak='".$noKontrak."'";
        $qData = mysql_query($sData);
        $row = mysql_num_rows($qData);
        if (0 < $row) {
            while ($rData = mysql_fetch_assoc($qData)) {
                $sUpdate = 'update '.$dbname.".pabrik_timbangan set kgpembeli='".$rData['beratbersih']."' where notransaksi='".$rData['notransaksi']."'";
                if (!mysql_query($sUpdate)) {
                    echo 'DB Error : '.$sUpdate."\n".mysql_error($conn);
                }
            }

            break;
        }

        exit('Error: '.$_SESSION['lang']['dataempty']);
    default:
        break;
}

?>
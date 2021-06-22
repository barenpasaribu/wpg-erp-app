<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
if ('' != isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    if ('' != isset($_GET['proses'])) {
        $proses = $_GET['proses'];
    }
}

$idRemote = $_POST['idRemote'];
$dbnm = $_POST['dbnm'];
$prt = $_POST['prt'];
$pswrd = $_POST['pswrd'];
$ipAdd = $_POST['ipAdd'];
$usrName = $_POST['usrName'];
$lksiServer = $_POST['lksiServer'];
$nmTable = $_POST['nmTable'];
$idCustomer = $_POST['idCustomer'];
$kdTimbangan = $_POST['kdTimbangan'];
$nmCust = $_POST['nmCust'];
$lksiTugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
if ('mssipb' === $nmTable && 'uploadData2' != $proses) {
    $proses = 'getDataSipb';
}

$kntrk = $_POST['kntrk'];
$nosipb = $_POST['nosipb'];
$tglsibp = $_POST['tglsibp'];
$kdbrg = $_POST['kdbrg'];
$trpcode = $_POST['trpcode'];
$trpname = $_POST['trpname'];
$ketdt = $_POST['ketdt'];
switch ($proses) {
    case 'preview':
        if ('' === $lksiServer) {
            echo 'warning:Lokasi Harus Di Isi';
            exit();
        }

        $arr = '##dbnm##prt##pswrd##ipAdd##usrName##lksiServer##nmTable';
        $corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd);
        $sColom = 'SHOW COLUMNS FROM '.$dbnm.'.'.$nmTable.'';
        $qColom = mysql_query($sColom, $corn);
        $i = 0;
        $tColom = [];
        for ($tmpCol = ''; $rColom = mysql_fetch_assoc($qColom); ++$i) {
            $tColom[$i] = $rColom['Field'];
        }
        $a = 0;
        foreach ($tColom as $dt => $isi) {
            if ('' === $tmpCol) {
                $tmpCol .= $isi;
            } else {
                $tmpCol .= ','.$isi;
            }
        }
        $sCob = 'select '.$tmpCol.' from '.$dbnm.'.'.$nmTable." where uploadStat is NULL or uploadStat=''";
        $res = mysql_query($sCob, $corn);
        $row = mysql_num_rows($res);
        if (0 < $row) {
            echo "<button class=mybutton onclick=uploadData('".$row."','".$arr."') id=btnUpload>".$_SESSION['lang']['startUpload']."</button>\r\n\t<div style='overflow:auto;height:50%;max-width:100%;'>\r\n\t <table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n\t<td>No.</td>\r\n\t<td>".$_SESSION['lang']['kodecustomer'].' / '.$_SESSION['lang']['kodesupplier']."</td>\r\n\t<td>".$_SESSION['lang']['nmcust'].' / '.$_SESSION['lang']['namasupplier']."</td>\r\n\t</tr>\r\n\t</thead><tbody id=ListData>";
            while ($hsl = mysql_fetch_array($res)) {
                ++$no;
                echo '<tr class=rowcontent id=row_'.$no." >\r\n\t\t\t<td >".$no."</td>\r\n\t\t\t<td id=kdTimbangan_".$no.'>'.$hsl[0]."</td>\r\n\t\t\t<td id=nmCust_".$no.'>'.$hsl[1]."</td>\r\n\t\t\t</tr>\r\n\t\t\t";
            }
        } else {
            echo " <table class=sortable cellspacing=1 border=0>\r\n\t<thead>\r\n\t<tr class=rowheader>\r\n\t<td>No.</td>\r\n\t<td>".$_SESSION['lang']['kodecustomer'].' / '.$_SESSION['lang']['kodesupplier']."</td>\r\n\t<td>".$_SESSION['lang']['nmcust'].' / '.$_SESSION['lang']['namasupplier']."</td>\r\n\t</tr>\r\n\t</thead><tbody><tr class=rowcontent align=center><td colspan=3>Not Found</td></tr>";
        }

        echo '</tbody></table></div>';

        break;
    case 'uploadData':
        if ('5' === substr($kdTimbangan, 0, 1)) {
            $sCek = 'select kodetimbangan from '.$dbname.".log_5supplier where kodetimbangan='".$kdTimbangan."'";
            $qCek = mysql_query($sCek);
            $rCek = mysql_num_rows($qCek);
            if ($rCek < 1) {
                $sNo = 'select supplierid,kodekelompok from '.$dbname.".log_5supplier where kodekelompok like 'S003%' order by `supplierid` desc limit 1";
                $qNo = mysql_query($sNo);
                $rNo = mysql_fetch_assoc($qNo);
                $no = substr($rNo['supplierid'], 4, 6);
                $supplierId = (int) $no;
                ++$supplierId;
                $supplierId = $rNo['kodekelompok'].$supplierId;
                $sIns = 'INSERT INTO '.$dbname.".`log_5supplier` (`supplierid`, `namasupplier`,`kodekelompok`,`kodetimbangan`) VALUES ('".$supplierId."','".$nmCust."','".$rNo['kodekelompok']."','".$kdTimbangan."')";
                if (mysql_query($sIns)) {
                    $corn = mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);
                    $sUp = 'update '.$dbnm.".msvendortrp set uploadStat='1' where TRPCODE='".$kdTimbangan."'";
                    if (mysql_query($sUp, $corn)) {
                        $stat = 1;
                        echo $stat;
                        exit();
                    }
                } else {
                    echo 'DB Error : '.mysql_error($conn);
                    $stat = 0;
                    echo $stat;
                    exit();
                }
            } else {
                $corn = mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);
                $sUp = 'update '.$dbnm.".msvendortrp set uploadStat='1' where TRPPCODE='".$kdTimbangan."'";
                if (mysql_query($sUp, $corn)) {
                    $stat = 1;
                    echo $stat;
                    exit();
                }
            }
        } else {
            if ('1' === substr($kdTimbangan, 0, 1)) {
                $sCek = 'select kodetimbangan from '.$dbname.".pmn_4customer where kodetimbangan='".$kdTimbangan."'";
                $qCek = mysql_query($sCek);
                $rCek = mysql_num_rows($qCek);
                if ($rCek < 1) {
                    $sNo = 'select kodecustomer,klcustomer from '.$dbname.'.pmn_4customer  order by `kodecustomer` desc limit 1';
                    $qNo = mysql_query($sNo);
                    $rNo = mysql_fetch_assoc($qNo);
                    $no = substr($rNo['kodecustomer'], 1, 4);
                    $kdCust = (int) $no;
                    ++$kdCust;
                    $kdCust = 'C'.addZero($kdCust, 4);
                    $sIns = 'INSERT INTO '.$dbname.".`pmn_4customer` (`kodecustomer`, `namacustomer`,`klcustomer`,`kodetimbangan`) VALUES ('".$kdCust."','".$nmCust."','".$rNo['klcustomer']."','".$kdTimbangan."')";
                    if (mysql_query($sIns)) {
                        $corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);
                        $supd = 'update '.$dbnm.'.'.$nmTable." set uploadStat=1 where BUYERCODE='".$kdTimbangan."'";
                        if (mysql_query($supd, $corn)) {
                            $stat = 1;
                            echo $stat;
                            exit();
                        }
                    } else {
                        echo 'DB Error : '.mysql_error($conn);
                        $stat = 0;
                        echo $stat;
                    }
                } else {
                    $corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);
                    $supd = 'update '.$dbnm.'.'.$nmTable." set uploadStat=1 where BUYERCOE='".$kdTimbangan."'";
                    if (mysql_query($supd, $corn)) {
                        $stat = 1;
                        echo $stat;
                        exit();
                    }
                }
            }
        }

        break;
    case 'getDataLokasi':
        $sql = 'select * from '.$dbname.".setup_remotetimbangan where id='".$idRemote."'";
        $query = mysql_query($sql);
        $res = mysql_fetch_assoc($query);
        echo $res['ip'].'###'.$res['port'].'###'.$res['dbname'].'###'.$res['username'].'###'.$res['password'];

        break;
    case 'getTable':
        $corn = mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);
        $sCob = 'SHOW TABLES FROM '.$dbnm." LIKE '%msvendor%' ";
        $res = mysql_query($sCob, $corn);
        while ($row = mysql_fetch_row($res)) {
            $optTable .= '<option value='.$row[0].'>'.$row[0].'</option>';
        }
        echo $optTable;

        break;
    case 'getDataSipb':
        $arr = '##dbnm##prt##pswrd##ipAdd##usrName##lksiServer##nmTable';
        $corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);
        $sGetDt = 'select * from '.$dbnm.'.'.$nmTable.' where uploadStat=0';
        $qGetDt = mysql_query($sGetDt, $corn) || exit(mysql_error($corn));
        $row = mysql_num_rows($qGetDt);
        $tab .= "<button class=mybutton onclick=uploadData2('".$row."','".$arr."') id=btnUpload>".$_SESSION['lang']['startUpload'].'</button>';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr>';
        $tab .= '<td>No.</td>';
        $tab .= '<td>No. Kontrak</td>';
        $tab .= '<td>No. SIPB</td>';
        $tab .= '<td>Tanggal</td>';
        $tab .= '<td>Kodebarang</td>';
        $tab .= '<td>Kode Transporter</td>';
        $tab .= '<td>Nama Transporter</td>';
        $tab .= '<td>Keterangan</td>';
        $tab .= '</tr></thead><tbody>';
        while ($rGetDt = mysql_fetch_assoc($qGetDt)) {
            $sNm = 'select TRPNAME from '.$dbnm.".msvendortrp where TRPCODE='".$rGetDt['TRPCODE']."'";
            $qNm = mysql_query($sNm, $corn) || exit(mysql_error($corn));
            $rNm = mysql_Fetch_assoc($qNm);
            ++$no;
            $tab .= '<tr class=rowcontent id=row_'.$no.'><td>'.$no.'</td>';
            $tab .= '<td id=kontrak_'.$no.'>'.$rGetDt['CTRNO'].'</td>';
            $tab .= '<td id=sipb_'.$no.'>'.$rGetDt['SIPBNO'].'</td>';
            $tab .= '<td id=tgl_sipb_'.$no.'>'.$rGetDt['SIPBDATE'].'</td>';
            $tab .= '<td id=kdbrg_'.$no.'>'.$rGetDt['PRODUCTCODE'].'</td>';
            $tab .= '<td id=trpcod_'.$no.'>'.$rGetDt['TRPCODE'].'</td>';
            $tab .= '<td id=trp_nm_'.$no.'>'.$rNm['TRPNAME'].'</td>';
            $tab .= '<td id=ket_'.$no.'>'.$rGetDt['DESCRIPTION'].'</td></tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'uploadData2':
        $sCek = 'select * from '.$dbname.".pabrik_mssipb where CTRNO='".$kntrk."' and SIPBNO='".$nosipb."'";
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if ($rCek < 1) {
            $sMsk = 'insert into '.$dbname.'.pabrik_mssipb (CTRNO,SIPBNO,SIPBDATE,PRODUCTCODE,TRPCODE,DESCRIPTION)';
            $sMsk .= "values ('".$kntrk."','".$nosipb."','".$tglsibp."','".$kdbrg."','".$trpcode."','".$ketdt."')";
            if (mysql_query($sMsk)) {
                $sDck = 'select kodetimbangan from '.$dbname.".log_5supplier where kodetimbangan='".$trpcode."'";
                $qDck = mysql_query($sDck);
                $rDck = mysql_num_rows($qDck);
                if ($rDck < 1) {
                    $sNo = 'select supplierid,kodekelompok from '.$dbname.".log_5supplier where kodekelompok like 'S%' order by `supplierid` desc limit 1";
                    $qNo = mysql_query($sNo);
                    $rNo = mysql_fetch_assoc($qNo);
                    $no = substr($rNo['supplierid'], 4, 6);
                    $rNo['kodekelompok'] = 'S003';
                    $supplierId = (int) $no;
                    ++$supplierId;
                    $supplierId = $rNo['kodekelompok'].$supplierId;
                    $sIns = 'INSERT INTO '.$dbname.".`log_5supplier` (`supplierid`, `namasupplier`,`kodekelompok`,`kodetimbangan`) \r\n                               VALUES ('".$supplierId."','".$trpname."','".$rNo['kodekelompok']."','".$trpcode."')";
                    if (mysql_query($sIns)) {
                        $corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);
                        $supd = 'update '.$dbnm.'.'.$nmTable." set uploadStat=1 where CTRNO='".$kntrk."' and SIPBNO='".$nosipb."'";
                        if (mysql_query($supd, $corn)) {
                            $stat = 1;
                            echo $stat;
                            exit();
                        }
                    } else {
                        echo 'DB Error : '.mysql_error($conn);
                        $stat = 0;
                        echo $stat;
                        exit();
                    }
                } else {
                    $corn = @mysql_connect($ipAdd.':'.$prt, $usrName, $pswrd) || exit('Error/Gagal :Unable to Connect to database : '.$ipAdd);
                    $supd = 'update '.$dbnm.'.'.$nmTable." set uploadStat=1 where CTRNO='".$kntrk."' and SIPBNO='".$nosipb."'";
                    if (mysql_query($supd, $corn)) {
                        $stat = 1;
                        echo $stat;
                        exit();
                    }
                }

                break;
            }

            echo 'DB Error : '.mysql_error($conn);
            $stat = 0;
            echo $stat;
            exit();
        }

        $stat = 1;
        echo $stat;
        exit();
    default:
        break;
}

?>
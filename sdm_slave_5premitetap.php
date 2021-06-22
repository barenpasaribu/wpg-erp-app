<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$param = $_POST;
$method = $_POST['method'];
$tpKary = $_POST['tpKary'];
$optThn = $_POST['optThn'];
$pilInp = $_POST['pilInp'];
$karyawanId = $_POST['karyawanId'];
$idKomponen = $_POST['idKomponen'];
$jmlhDt = $_POST['jmlhDt'];
$thn = $_POST['thn'];
$optTip = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optTipe = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');
$optKomponen = makeOption($dbname, 'sdm_ho_component', 'id,name');
switch ($method) {
    case 'getTipe':
        $optPil = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        if ('0' == $param['tpTransaksi']) {
            $sjbtn = 'select distinct * from '.$dbname.'.sdm_5jabatan order by namajabatan';
            $qjbtn = mysql_query($sjbtn);
            while ($rjbtn = mysql_fetch_assoc($qjbtn)) {
                if ('' != $param['kdjbn']) {
                    $optPil .= "<option value='".$rjbtn['kodejabatan']."' ".(($rjbtn['kodejabatan'] == $param['kdjbn'] ? 'selected' : '')).'>'.$rjbtn['namajabatan'].'</option>';
                } else {
                    $optPil .= "<option value='".$rjbtn['kodejabatan']."'>".$rjbtn['namajabatan'].'</option>';
                }
            }
        } else {
            if ('1' == $param['tpTransaksi']) {
                $sjbtn = 'select distinct * from '.$dbname.'.sdm_5tipekaryawan order by tipe';
                $qjbtn = mysql_query($sjbtn);
                while ($rjbtn = mysql_fetch_assoc($qjbtn)) {
                    if ('' != $param['kdjbn']) {
                        $optPil .= "<option value='".$rjbtn['id']."' ".(($rjbtn['id'] == $param['kdjbn'] ? 'selected' : '')).'>'.$rjbtn['tipe'].'</option>';
                    } else {
                        $optPil .= "<option value='".$rjbtn['id']."'>".$rjbtn['tipe'].'</option>';
                    }
                }
            }
        }

        echo $optPil;

        break;
    case 'loadData':
        $arrd = ['Premi Tetap', 'Insentif'];
        $limit = 30;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $no = 0;
        if (0 == $param['tpTransaksi']) {
            $optdata = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
            $str = 'select * from '.$dbname.".sdm_5premitetap  \r\n                          where kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
        } else {
            $optdata = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
            $str = 'select tipekaryawan as kodejabatan,insentif as premitetap from '.$dbname.".sdm_5insentif   \r\n                          where kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
        }

        $res = mysql_query($str);
        $oow = mysql_num_rows($res);
        if (0 == $oow) {
            echo '<tr class=rowcontent><td colspan=5>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        } else {
            while ($bar = mysql_fetch_assoc($res)) {
                echo "<tr class=rowcontent>\r\n                    <td>".$arrd[$param['tpTransaksi']]."</td>    \r\n                    <td>".$optdata[$bar['kodejabatan']]."</td> \r\n                    <td align=right>".number_format($bar['premitetap'], 0)."</td>  \r\n                    <td align=center>\r\n                              <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$param['tpTransaksi']."','".$bar['kodejabatan']."','".$bar['premitetap']."');\">\r\n                      </td>\r\n                    </tr>";
            }
        }

        break;
    case 'getForm':
        $tab .= '<fieldset><legend>'.$_SESSION['lang']['kodejabatan'].'/'.$_SESSION['lang']['tipekaryawan'].'</legend>';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0>';
        $tab .= '<tr><td>'.$_SESSION['lang']['find']."</td><td><input type=text class=myinputtext id=no_brg style=width:150px />\r\n                      <button class=mybutton onclick=cariTipe()>".$_SESSION['lang']['find'].'</button></td></tr>';
        $tab .= '</table></fieldset>';
        $tab .= '<fieldset><legend>'.$_SESSION['lang']['result']."</legend><div id=container2 style=overflow:auto;width:300px;height:200px;></div></fieldset><input type=hidden id=tptrans value='".$param['tpTransaksi']."' />";
        echo $tab;

        break;
    case 'cariTipe':
        if ('0' == $param['tpTransaksi']) {
            $sjbtn = 'select distinct kodejabatan as id,namajabatan as tipe from '.$dbname.".sdm_5jabatan \r\n                        where alias like '%".$param['txtfind']."%'  order by namajabatan";
            $qjbtn = mysql_query($sjbtn);
        } else {
            if ('1' == $param['tpTransaksi']) {
                $sjbtn = 'select distinct * from '.$dbname.".sdm_5tipekaryawan\r\n                            where tipe like '%".$param['txtfind']."%' order by tipe";
                $qjbtn = mysql_query($sjbtn);
            }
        }

        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>id</td><td>Tipe</td></tr></thead><tbody>';
        while ($rjbtn = mysql_fetch_assoc($qjbtn)) {
            $tab .= "<tr class=rowcontent onclick=setPo('".$rjbtn['id']."')>";
            $tab .= '<td>'.$rjbtn['id'].'</td>';
            $tab .= '<td>'.$rjbtn['tipe'].'</td></tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'insert':
        if ('' == $param['premiIns']) {
            exit('error:Premi/Insetif Tidak boleh kosong');
        }

        if ('' == $param['pilInp']) {
            exit('error:'.$_SESSION['lang']['kodejabatan'].'/'.$_SESSION['lang']['tipekaryawan'].' Tidak boleh kosong');
        }

        if ('' == $param['tpTransaksi']) {
            exit('error:Tipe transaksi tidak boleh kosong');
        }

        if (0 == $param['tpTransaksi']) {
            $sins = 'delete from '.$dbname.".sdm_5premitetap where \r\n                           kodeorg='".$_SESSION['empl']['lokasitugas']."' and kodejabatan='".$param['pilInp']."'";
            if (mysql_query($sins)) {
                $sinsd = 'insert into '.$dbname.".sdm_5premitetap (`kodeorg`,`kodejabatan`,`premitetap`,`updateby`)\r\n                                values ('".$_SESSION['empl']['lokasitugas']."','".$param['pilInp']."','".$param['premiIns']."','".$_SESSION['standard']['userid']."')";
                if (!mysql_query($sinsd)) {
                    exit("error:\n".$sinsd.mysql_error($conn));
                }
            } else {
                exit("error:\n".$sinsd.mysql_error($conn));
            }
        } else {
            if (1 == $param['tpTransaksi']) {
                $sins = 'delete from '.$dbname.".sdm_5insentif where \r\n                           kodeorg='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan='".$param['pilInp']."'";
                if (mysql_query($sins)) {
                    $sinsd = 'insert into '.$dbname.".sdm_5insentif (`kodeorg`,`tipekaryawan`,`insentif`,`updateby`)\r\n                                values ('".$_SESSION['empl']['lokasitugas']."','".$param['pilInp']."','".$param['premiIns']."','".$_SESSION['standard']['userid']."')";
                    if (!mysql_query($sinsd)) {
                        exit("error:\n".$sinsd.mysql_error($conn));
                    }
                } else {
                    exit("error:\n".$sinsd.mysql_error($conn));
                }
            }
        }

        break;
}

?>
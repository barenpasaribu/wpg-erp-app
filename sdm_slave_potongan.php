<?php



session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$optLokasiTugas = makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas');
$periodeAkutansi = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
$whrPot = "name like 'Potongan%'";
$whrPrdData = "updateby='".$_SESSION['standard']['userid']."' and periodegaji='".$param['periode']."' and tipepotongan='".$param['tipePot']."'";
$optNmPotongan = makeOption($dbname, 'sdm_ho_component', 'id,name', $whrPot);
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$tgl = date('Y-m-d');
$optTipe = makeOption($dbname, 'organisasi', 'kodeorganisasi,tipe');
switch ($param['proses']) {
    case 'loadNewData':
        echo "<table cellspacing='1' border='0' class='sortable'>\r\n                     <thead>\r\n                     <tr class=rowheader>\r\n                     <td>No.</td>\r\n                     <td>".$_SESSION['lang']['kodeorg']."</td>\r\n                     <td>".$_SESSION['lang']['namaorganisasi']."</td>\r\n                     <td>".$_SESSION['lang']['periodegaji']."</td>\r\n                     <td>".$_SESSION['lang']['potongan']."</td>\r\n                     <td>Action</td>\r\n                     </tr>\r\n                     </thead><tbody>";
        if ('' != $param['periodecr']) {
            $whrCr .= " and periodegaji like '%".$param['periodecr']."%'";
        }

        if ('' != $param['tipePotCr']) {
            $whrCr .= " and tipepotongan= '".$param['tipePotCr']."'";
        }

        if ('' != $param['kdOrgCr']) {
            $whrCr .= " and kodeorg= '".$param['kdOrgCr']."'";
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
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".sdm_potonganht \r\n                                  where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."'  ".$whrCr.' order by `periodegaji` desc';
        $slvhc = 'select * from '.$dbname.".sdm_potonganht \r\n                                    where substring(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."'  ".$whrCr."\r\n                                    order by `periodegaji` desc limit ".$offset.','.$limit.'';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $qlvhc = mysql_query($slvhc);
        $user_online = $_SESSION['standard']['userid'];
        $no = 0;
        while ($rlvhc = mysql_fetch_assoc($qlvhc)) {
            $thnPeriod = substr($rlvhc['tanggal'], 0, 7);
            $whrd = "kodeorganisasi='".$rlvhc['kodeorg']."'";
            $optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whrd);
            ++$no;
            echo "\r\n                        <tr class=rowcontent>\r\n                        <td>".$no."</td>\r\n                        <td>".$rlvhc['kodeorg']."</td>\r\n                        <td>".$optNmOrg[$rlvhc['kodeorg']]."</td>\r\n                        <td>".$rlvhc['periodegaji']."</td>\r\n                        <td>".$optNmPotongan[$rlvhc['tipepotongan']]."</td>\r\n                        <td>";
            $arr = '##kdorg##per';
            if ($rlvhc['periodegaji'] == $periodeAkutansi) {
                if ($_SESSION['standard']['userid'] == $rlvhc['updateby']) {
                    echo "\r\n                                <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."');\">\r\n                                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."');\" >\t\r\n                                <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_potonganht','".$rlvhc['kodeorg'].','.$rlvhc['periodegaji'].','.$rlvhc['tipepotongan']."','','sdm_slave_potonganPdf',event)\">\r\n                            \t <img onclick=excel(event,'".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."') src=images/excel.jpg class=resicon title='MS.Excel'>";
                } else {
                    echo "<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_potonganht','".$rlvhc['kodeorg'].','.$rlvhc['periodegaji'].','.$rlvhc['tipepotongan']."','','sdm_slave_potonganPdf',event)\">\r\n                           \t\t\t\t <img onclick=excel(event,'".$rlvhc['kodeorg']."','".$rlvhc['periodegaji']."','".$rlvhc['tipepotongan']."') src=images/excel.jpg class=resicon title='MS.Excel'>";
                }
            } else {
                echo "<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_potonganht','".$rlvhc['kodeorg'].','.$rlvhc['periodegaji'].','.$rlvhc['tipepotongan']."','','sdm_slave_potonganPdf',event)\">";
            }

            echo "</td>\r\n                        </tr>\r\n                        ";
        }
        echo "</tbody><tfoot>\r\n                        <tr><td colspan=6 align=center>\r\n                        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                        <button class=mybutton onclick=loadData(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                        <button class=mybutton onclick=loadData(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                        </td>\r\n                        </tr>";
        echo '</tfoot></table>';

        break;
    case 'getPrd':
        $optPrd .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sGet = 'select distinct periode from '.$dbname.".sdm_5periodegaji \r\n                           where kodeorg='".$param['kdOrg']."' and sudahproses=0 and jenisgaji='H' order by periode desc";
        $qGet = mysql_query($sGet);
        while ($rGet = mysql_fetch_assoc($qGet)) {
            $optPrd .= '<option value='.$rGet['periode'].'>'.$rGet['periode'].'</option>';
        }
        echo $optPrd;

        break;
    case 'saveData':
        if (0 == $param['rupPot'] || '' == $param['rupPot']) {
            exit('error: '.$_SESSION['lang']['potongan']." can't empty");
        }

        if ('' == $param['krywnId']) {
            exit('error: '.$_SESSION['lang']['namakaryawan']." can't empty");
        }

        $optData = makeOption($dbname, 'sdm_potonganht', 'periodegaji,tipepotongan', $whrPrdData);
        $scek = 'select distinct * from '.$dbname.".sdm_potonganht where periodegaji='".$param['periode']."' "." and tipepotongan='".$param['tipePot']."' and kodeorg='".$param['kdOrg']."'";
        $qcek = mysql_query($scek);
        $rcek = mysql_num_rows($qcek);
        $sInsHt = 'insert into '.$dbname.'.sdm_potonganht (`kodeorg`,`periodegaji`,`tipepotongan`,`updateby`) values ';
        $sDet = 'insert into '.$dbname.'.sdm_potongandt (`kodeorg`,`periodegaji`,`keterangan`,`nik`,`jumlahpotongan`,`tipepotongan`,`updateby`) values';
        if ($rcek < 1) {
            $sInsHt .= "('".$param['kdOrg']."','".$param['periode']."','".$param['tipePot']."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sInsHt)) {
                exit('error: DB Error '.mysql_error($conn).'___'.$sInsHt);
            }

            $sDet .= "('".$optLokasiTugas[$param['krywnId']]."','".$param['periode']."','".$param['ketPot']."','".$param['krywnId']."','".$param['rupPot']."'\r\n                                    ,'".$param['tipePot']."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sDet)) {
                exit('error: DB Error '.mysql_error($conn).'___'.$sDet);
            }
        } else {
            $sDet .= "('".$optLokasiTugas[$param['krywnId']]."','".$param['periode']."','".$param['ketPot']."','".$param['krywnId']."','".$param['rupPot']."'\r\n                                    ,'".$param['tipePot']."','".$_SESSION['standard']['userid']."')";
            if (!mysql_query($sDet)) {
                exit('error: DB Error '.mysql_error($conn).'___'.$sDet);
            }
        }

        break;
    case 'updateDetail':
        if ('' == $param['rupPot'] || '0' == (int) ($param['rupPot'])) {
            exit('error: '.$_SESSION['lang']['potongan']." can't empty");
        }

        $sUpd = 'update '.$dbname.'.sdm_potongandt set';
        $sUpd .= " jumlahpotongan='".$param['rupPot']."',keterangan='".$param['ketPot']."'";
        $sUpd .= " where tipepotongan='".$param['tipePot']."' and nik='".$param['krywnId']."' \r\n                             and kodeorg='".$optLokasiTugas[$param['krywnId']]."' and periodegaji='".$param['periode']."'";
        if (!mysql_query($sUpd)) {
            exit('error: db error'.mysql_error($conn).'___'.$sUpd);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.'.sdm_potonganht where '.$whrPrdData.'';
        if (mysql_query($sDel)) {
            $sDelDetail = 'delete from '.$dbname.'.sdm_potongandt where '.$whrPrdData.'';
            if (mysql_query($sDelDetail)) {
                echo '';
            } else {
                echo 'DB Error : '.mysql_error($conn);
            }
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'delDetail':
        $sDel = 'delete from '.$dbname.'.sdm_potongandt where '.$whrPrdData." and nik='".$param['krywnId']."'";
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    case 'createTable':
        if (1 != $param['statUpdate']) {
            $whrPrd = "kodeorg='".$param['kdOrg']."' and periode='".$param['periode']."'";
            $optPeriodeAkn = makeOption($dbname, 'setup_periodeakuntansi', 'periode,tutupbuku', $whrPrd);
            $optData = makeOption($dbname, 'sdm_potonganht', 'periodegaji,tipepotongan', $whrPrdData);
            if (1 == $optPeriodeAkn[$param['periode']]) {
                exit('Error: Accounting period has been closed');
            }

            if ('' != $optData[$param['periode']]) {
                exit('error: This date and Organization Name already exist');
            }
        }

        $where = " lokasitugas='".$param['kdOrg']."' and (tanggalkeluar is NULL or tanggalkeluar<'".$tgl."')";
        if ('KANWIL' == $optTipe[$param['kdOrg']]) {
            $where = ' lokasitugas in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')"." and (tanggalkeluar is NULL or tanggalkeluar < '".$tgl."')";
        }

        $optKry .= "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sKry = 'select namakaryawan,nik,karyawanid,lokasitugas from '.$dbname.'.datakaryawan where '.$where.' order by namakaryawan asc';
        $qKry = mysql_query($sKry);
        while ($rKry = mysql_fetch_assoc($qKry)) {
            $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['nik'].' - '.$rKry['namakaryawan'].' ['.$rKry['lokasitugas'].']</option>';
        }
        $table = "<table id='ppDetailTable' cellspacing='1' border='0' class='sortable'>\r\n                <thead>\r\n                <tr class=rowheader>\r\n                <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n                <td>".$_SESSION['lang']['potongan']."</td>\r\n                <td>".$_SESSION['lang']['keterangan']."</td>\r\n                <td>Action</td>\r\n                </tr></thead>\r\n                <tbody id='detailBody'>";
        $table .= "<tr class=rowcontent>\r\n                <td><select id=krywnId name=krywnId style='width:200px'>".$optKry."</select>\r\n                <img class='zImgBtn' style='position:relative;top:5px' src='images/onebit_02.png' onclick=\"getKary('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namakaryawan']."','1',event);\"  />\r\n                </td>\r\n                <td><input type=text class='myinputtextnumber' id=rpPot style=width:150px onkeypress='return angka_doang(event)' /></td>\r\n                <td><input type=text class=myinputtext id=ketPot style=width:150px onkeypress='return tanpa_kutip(event)' /></td>\r\n                <td align=center><img id='detail_add' title='Simpan' class=zImgBtn onclick=\"addDetail()\" src='images/save.png'/></td>\r\n                </tr>\r\n                ";
        $table .= '</tbody></table>';
        echo $table;

        break;
    case 'loadDetail':
        $sDet = 'select * from '.$dbname.".sdm_potongandt \r\n                           where ".$whrPrdData.' order by nik';
        $qDet = mysql_query($sDet);
        while ($rDet = mysql_fetch_assoc($qDet)) {
            ++$no;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$no.'</td>';
            $tab .= '<td>'.$optNmKar[$rDet['nik']].'</td>';
            $tab .= '<td align=right>'.number_format($rDet['jumlahpotongan'], 0).'</td>';
            $tab .= '<td>'.$rDet['keterangan'].'</td>';
            $tab .= "<td>\r\n                            <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editDetail('".$rDet['nik']."','".$rDet['jumlahpotongan']."','".$rDet['keterangan']."');\">\r\n                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDetail('".$rDet['kodeorg']."','".$rDet['periodegaji']."','".$rDet['nik']."','".$rDet['tipepotongan']."');\" >\t</td>";
            $tab .= '</tr>';
            $tot += $rDet['jumlahpotongan'];
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td align=right>'.number_format($tot, 0).'</td><td  colspan=2>&nbsp;</td></tr>';
        echo $tab;

        break;
    case 'getKary':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead>';
        $tab .= '<tr><td>'.$_SESSION['lang']['nik'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['lokasitugas'].'</td>';
        $tab .= '</tr></thead><tbody>';
        $where = " lokasitugas='".$param['kdOrg']."' and (tanggalkeluar is NULL or tanggalkeluar<'".$tgl."')  and tipekaryawan!=5 ";
        if ('KANWIL' == $optTipe[$param['unit']]) {
            $where = ' lokasitugas in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')"." and (tanggalkeluar is NULL or tanggalkeluar<'".$tgl."') and tipekaryawan!=5 ";
        }

        if ('' != $param['nmkary']) {
            $where .= "and (namakaryawan like '%".$param['nmkary']."%' or nik like '%".$param['nmkary']."%')";
        }

        $sKry = 'select namakaryawan,nik,karyawanid,lokasitugas from '.$dbname.'.datakaryawan where '.$where.' order by namakaryawan asc';
        $qDt = mysql_query($sKry);
        while ($rDt = mysql_fetch_assoc($qDt)) {
            $clid = "onclick=setKary('".$rDt['karyawanid']."') style=cursor:pointer;";
            $tab .= '<tr '.$clid.' class=rowcontent><td>'.$rDt['nik'].'</td>';
            $tab .= '<td>'.$rDt['namakaryawan'].'</td>';
            $tab .= '<td>'.$rDt['lokasitugas'].'</td>';
            $tab .= '</tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    default:
        break;
}

?>
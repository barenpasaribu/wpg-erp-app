<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
include_once 'lib/rTable.php';
$proses = $_GET['proses'];
$param = $_POST;
$total = "<fieldset style='height:114px'><legend><b>Total</b></legend>";
$total .= '<table>';
$total .= '<tr>';
$total .= "<td colspan='2'><b>".$_SESSION['lang']['prestasi'].'</b></td>';
$total .= "<td colspan='2'><b>".$_SESSION['lang']['absensi'].'</b></td>';
$total .= '</tr>';
$total .= '<tr>';
$total .= '<td>'.$_SESSION['lang']['jumlahhk'].'</td>';
$total .= '<td>'.makeElement('totalPresHk', 'textnum', 0, ['style' => 'width:70px', 'disabled' => 'disabled', 'realValue' => 0]).'</td>';
$total .= '<td>'.$_SESSION['lang']['jumlahhk'].'</td>';
$total .= '<td>'.makeElement('totalAbsHk', 'textnum', 0, ['style' => 'width:70px', 'disabled' => 'disabled', 'realValue' => 0]).'</td>';
$total .= '</tr>';
$total .= '<tr>';
$total .= '<td>'.$_SESSION['lang']['umr'].'</td>';
$total .= '<td>'.makeElement('totalPresUmr', 'textnum', 0, ['style' => 'width:70px', 'disabled' => 'disabled', 'realValue' => 0]).'</td>';
$total .= '<td>'.$_SESSION['lang']['umr'].'</td>';
$total .= '<td>'.makeElement('totalAbsUmr', 'textnum', 0, ['style' => 'width:70px', 'disabled' => 'disabled', 'realValue' => 0]).'</td>';
$total .= '</tr>';
$total .= '<tr>';
$total .= '<td>'.$_SESSION['lang']['insentif'].'</td>';
$total .= '<td>'.makeElement('totalPresIns', 'textnum', 0, ['style' => 'width:70px', 'disabled' => 'disabled', 'realValue' => 0]).'</td>';
$total .= '<td>'.$_SESSION['lang']['insentif'].'</td>';
$total .= '<td>'.makeElement('totalAbsIns', 'textnum', 0, ['style' => 'width:70px', 'disabled' => 'disabled', 'realValue' => 0]).'</td>';
$total .= '</tr></table>';
$total .= makeElement('tmpValHk', 'hidden', 0);
$total .= makeElement('tmpValUmr', 'hidden', 0);
$total .= makeElement('tmpValIns', 'hidden', 0);
$total .= '</fieldset>';
switch ($proses) {
    case 'showHeadList':
        if (isset($param['where'])) {
            $tmpW = str_replace('\\', '', $param['where']);
            $arrWhere = json_decode($tmpW, true);
            $where = '';
            if (!empty($arrWhere)) {
                foreach ($arrWhere as $key => $r1) {
                    if ($key == 0) {
                        $where .= $r1[0]." like '%".$r1[1]."%'";
                    } else {
                        if ($key == 2) {
                            if ($r1[1] == 1) {
                                $where .= ' and notransaksi in (select distinct notransaksi from '.$dbname.'.kebun_pakaimaterial)';
                            } else {
                                $where .= ' and notransaksi not in (select distinct notransaksi from '.$dbname.'.kebun_pakaimaterial)';
                            }
                        } else {
                            $where .= ' and '.$r1[0]." like '%".$r1[1]."%'";
                        }
                    }
                }
            } else {
                $where = null;
            }
        } else {
            $where = null;
        }

        if ($param['tipe'] == 'PNN') {
            $header = [$_SESSION['lang']['nomor'], $_SESSION['lang']['organisasi'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['nikmandor'], $_SESSION['lang']['nikmandor1'], 'Recorder', $_SESSION['lang']['keraniproduksi'], 'updateby'];
        } else {
            $header = [$_SESSION['lang']['nomor'], $_SESSION['lang']['organisasi'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['nikmandor'], $_SESSION['lang']['nikmandor1'], $_SESSION['lang']['asisten'], $_SESSION['lang']['keraniafdeling'], 'updateby', $_SESSION['lang']['namakegiatan']];
        }

        if ($where == null) {
            if ($_SESSION['empl']['subbagian'] == '') {
                $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            } else {
                $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and updateby='".$_SESSION['standard']['userid']."'";
            }
        } else {
            if ($_SESSION['empl']['subbagian'] == '') {
                $where .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            } else {
                $where .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            }
        }

        if (strlen($param['tipe']) == 2) {
            $where .= " and substr(notransaksi,15,2)='".$param['tipe']."' and substr(notransaksi,17,1)='/'";
        } else {
            if (strlen($param['tipe']) == 3) {
                $where .= " and substr(notransaksi,15,3)='".$param['tipe']."'";
            }
        }

        $cols = 'notransaksi,kodeorg,tanggal,nikmandor,nikmandor1,nikasisten,keranimuat,jurnal,updateby';
        $query = selectQuery($dbname, 'kebun_aktifitas', $cols, $where, 'tanggal desc, notransaksi desc', false, $param['shows'], $param['page']);
        $data = fetchData($query);
        $totalRow = getTotalRow($dbname, 'kebun_aktifitas', $where);
        if (!empty($data)) {
            $whereKarRow = 'karyawanid in (';
            $notFirst = false;
            foreach ($data as $key => $row) {
                if ($row['jurnal'] == 1) {
                    $data[$key]['switched'] = true;
                }

                $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
                unset($data[$key]['jurnal']);
                if ($notFirst == false) {
                    if ($row['nikmandor'] != '') {
                        $whereKarRow .= $row['nikmandor'];
                        $notFirst = true;
                    }

                    if ($row['nikmandor1'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['nikmandor1'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikmandor1'];
                        }
                    }

                    if ($row['nikasisten'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['nikasisten'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikasisten'];
                        }
                    }

                    if ($row['keranimuat'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['keranimuat'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['keranimuat'];
                        }
                    }

                    if ($row['updateby'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['updateby'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['updateby'];
                        }
                    }
                } else {
                    if ($row['nikmandor'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['nikmandor'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikmandor'];
                        }
                    }

                    if ($row['nikmandor1'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['nikmandor1'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikmandor1'];
                        }
                    }

                    if ($row['nikasisten'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['nikasisten'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikasisten'];
                        }
                    }

                    if ($row['keranimuat'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['keranimuat'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['keranimuat'];
                        }
                    }

                    if ($row['updateby'] != '') {
                        if ($notFirst == false) {
                            $whereKarRow .= $row['updateby'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['updateby'];
                        }
                    }
                }
            }
            $whereKarRow .= ')';
        } else {
            $whereKarRow = '';
        }

        $whereTrans = '';
        foreach ($data as $trans) {
            if (!empty($whereTrans)) {
                $whereTrans .= ',';
            }

            $whereTrans .= "'".$trans['notransaksi']."'";
        }
        if (!empty($whereTrans)) {
            $qTrans = "select a.notransaksi,b.namakegiatan from ".$dbname.".kebun_prestasi a ".
                "left join ".$dbname.".setup_kegiatan b on a.kodekegiatan=b.kodekegiatan ".
                "where a.notransaksi in (".$whereTrans.")";
            $resTrans = fetchData($qTrans);
        } else {
            $resTrans = [];
        }

        $optKeg = [];
        foreach ($resTrans as $row) {
            $optKeg[$row['notransaksi']] = $row['namakegiatan'];
        }

        $optKarRow = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKarRow);
        $dataShow = $data;
        foreach ($dataShow as $key => $row) {
            if ($param['tipe'] != 'PNN') {
                $data[$key]['namakegiatan'] = (isset($optKeg[$row['notransaksi']]) ? $optKeg[$row['notransaksi']] : '');
                $dataShow[$key]['namakegiatan'] = (isset($optKeg[$row['notransaksi']]) ? $optKeg[$row['notransaksi']] : '');
            }

			$dataShow[$key]['nikmandor'] = $optKarRow[$row['nikmandor']];
			$dataShow[$key]['nikmandor1'] = $optKarRow[$row['nikmandor1']];
			$dataShow[$key]['nikasisten'] = $optKarRow[$row['nikasisten']];
			$dataShow[$key]['keranimuat'] = $optKarRow[$row['keranimuat']];
			$dataShow[$key]['updateby'] = $optKarRow[$row['updateby']];
/*
            isset($optKarRow[$row['nikmandor']]);
            (isset($optKarRow[$row['nikmandor']]) ? $dataShow[$key]['nikmandor'] : null);
            isset($optKarRow[$row['nikmandor1']]);
            (isset($optKarRow[$row['nikmandor1']]) ? $dataShow[$key]['nikmandor1'] : null);
            isset($optKarRow[$row['nikasisten']]);
            (isset($optKarRow[$row['nikasisten']]) ? $dataShow[$key]['nikasisten'] : null);
            isset($optKarRow[$row['keranimuat']]);
            (isset($optKarRow[$row['keranimuat']]) ? $dataShow[$key]['keranimuat'] : null);
            isset($optKarRow[$row['updateby']]);
            (isset($optKarRow[$row['updateby']]) ? $dataShow[$key]['updateby'] : null);
*/
        }
        if ($param['tipe'] == 'PNN') {
            $app = 'panen';
        } else {
            $app = 'rawatkebun';
        }

        $qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', "kodeaplikasi='".$app."'");
        $tmpPost = fetchData($qPosting);
        $postJabatan = $tmpPost[0]['jabatan'];
        $tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
        $tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
        $tHeader->_actions[0]->addAttr($param['tipe']);
        $tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
        $tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');
        $tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');
        if ($postJabatan != $_SESSION['empl']['kodejabatan']) {
            $tHeader->_actions[2]->_name = '';
        }

        $tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');
        $tHeader->_actions[3]->addAttr('event');
        $tHeader->_actions[3]->addAttr($param['tipe']);
        $tHeader->_switchException = ['detailPDF'];
        if ($param['tipe'] != 'PNN') {
            $tHeader->addAction('detailData', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/zoom.png');
            $tHeader->_actions[4]->addAttr('event');
            $tHeader->_actions[4]->addAttr($param['tipe']);
            $tHeader->_switchException[] = 'detailData';
        }

        $tHeader->pageSetting($param['page'], $totalRow, $param['shows']);
        $tHeader->setWhere($arrWhere);
        $tHeader->renderTable();

        break;
    case 'showAdd':
        echo formHeader('add', $_POST['tipe'], []);
        if ($param['tipe'] != 'PNN') {
            echo $total;
        }

        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'showEdit':
        $query = selectQuery($dbname, 'kebun_aktifitas', '*', "notransaksi='".$param['notransaksi']."'");
        $tmpData = fetchData($query);
        $data = $tmpData[0];
        $data['tanggal'] = tanggalnormal($data['tanggal']);
        echo formHeader('edit', $_SESSION['tmp']['kebun']['tipeTrans'], $data);
        if ($param['tipe'] != 'PNN') {
            echo $total;
        }

        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'add':
        $data =   $_POST;
        if ($data['tanggal'] == '') {
            echo 'Validation Error : Date must not empty';

            break;
        }

        $sekarang = tanggalsystemw($data['tanggal']);
        if ($sekarang < $_SESSION['org']['period']['start']) {
            echo 'Validation Error : Date out or range';

            break;
        }

        $data['tipetransaksi'] = $_GET['tipe'];
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $fWhere = "tanggal='".$data['tanggal']."' and kodeorg='".$data['kodeorg']."' and tipetransaksi='".$data['tipetransaksi']."'";
        $fQuery = selectQuery($dbname, 'kebun_aktifitas', 'notransaksi', $fWhere);
        $tmpNo = fetchData($fQuery);
        if (count($tmpNo) == 0) {
            $data['notransaksi'] = $data['tanggal'].'/'.$data['kodeorg'].'/'.$data['tipetransaksi'].'/001';
        } else {
            $maxNo = 1;
            foreach ($tmpNo as $row) {
                $tmpRow = explode('/', $row['notransaksi']);
                $noUrut = (int) $tmpRow[3];
                if ($maxNo < $noUrut) {
                    $maxNo = $noUrut;
                }
            }
            $currNo = addZero($maxNo + 1, 3);
            $data['notransaksi'] = $data['tanggal'].'/'.$data['kodeorg'].'/'.$data['tipetransaksi'].'/'.$currNo;
        }

        $data['updateby'] = $_SESSION['standard']['userid'];

        $cols = ['notransaksi', 'kodeorg', 'tanggal', 'nikmandor', 'nikmandor1', 'nikasisten', 'keranimuat', 'asistenpanen', 'tipetransaksi', 'updateby'];
        $cols2="";
        $values="";
        foreach ($cols as $item){
            $cols2 .= $item.",";
            $values.="'".$data[$item]."',";
        }
         $cols2 = substr($cols2,0,strlen($cols2)-1);
        $values = substr($values,0,strlen($values)-1);
        $query =  "insert into $dbname.kebun_aktifitas (" .$cols2 . ") values (".$values.")"; //insertQuery($dbname, 'kebun_aktifitas', $data, $cols);


        if (executeQuery($query)){
            echo $data['notransaksi'];
        }
//        if (!mysql_query($query)) {
//            echo 'DB Error 23 : '.mysql_error();
//        } else {
//            echo $data['notransaksi'];
//        }

        break;
    case 'edit':
        $data = $_POST;
        $where = "notransaksi='".$data['notransaksi']."'";
        unset($data['notransaksi']);
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $data['updateby'] = $_SESSION['standard']['userid'];
        $query = updateQuery($dbname, 'kebun_aktifitas', $data, $where);
        executeQuery($query);
//        if (!mysql_query($query)) {
//            echo 'DB Error 33 : '.mysql_error();
//        }

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_aktifitas` where '.$where;
        if (!executeQuery($query)){exit();}
//        if (!mysql_query($query)) {
//            echo 'DB Error 33: '.mysql_error();
//            exit();
//        }

        break;
    default:
        break;
}
function formHeader($mode, $tipe, $data)
{
    global $dbname;
    global $param;
    if (empty($data)) {
        $data['notransaksi'] = '';
        $data['kodeorg'] = '';
        $data['tanggal'] = '';
        $data['nikmandor'] = '';
        $data['nikmandor1'] = '';
        $data['nikasisten'] = '';
        $data['keranimuat'] = '';
        $data['asistenpanen'] = '';
    }

    if ($mode == 'edit') {
        $disabled = 'disabled';
    } else {
        $disabled = '';
    }

    if ($mode == 'edit') {
        $whereOrg = "kodeorganisasi='".$data['kodeorg']."' and tipe<>'BLOK'";
    } else {
        $whereOrg = "left(kodeorganisasi,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."' and tipe='KEBUN'";
    }

    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg);
    $whereKary = "a.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
    $whereKary .= " and (a.tanggalkeluar is NULL or a.tanggalkeluar='0000-00-00' or a.tanggalkeluar > '".$_SESSION['org']['period']['start']."')";
    $qKary = "select a.karyawanid,a.namakaryawan,a.nik,b.namajabatan,b.alias from $dbname.datakaryawan a ".
        "left join $dbname.sdm_5jabatan b on a.kodejabatan=b.kodejabatan ".
        "where (b.alias like '%Mandor%' or b.alias like '%Asisten%' or ".
        "b.alias like '%Kerani%' or b.alias like '%Askep Estate%' or b.alias like '%Admin%') and ".
        $whereKary.' order by a.nik asc';
		//echo "warning: ".$qKary;
		//exit();
    $resKary = fetchData($qKary);
    $optMandor = $optAsisten = $optKrani = $optRecorder = $optOfficer = $optConductor = $optAdmin = ['' => ''];
    foreach ($resKary as $row) {
        if (preg_match('/Mandor/i', $row['alias'])) {
            $optMandor[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Asisten Divisi/i', $row['alias'])) {
            $optAsisten[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Kerani Panen/i', $row['alias']) || preg_match('/Kerani Divisi/i', $row['alias'])) {
            $optKrani[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Kerani Divisi/i', $row['alias']) || preg_match('/Kerani Panen/i', $row['alias'])) {
            $optRecorder[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Asisten/i', $row['alias'])) {
            // if (preg_match('/Askep Estate/i', $row['alias']) || preg_match('/Kerani/i', $row['alias'])) {
            $optOfficer[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Mandor Satu/i', $row['alias'])) {
            $optConductor[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Kerani Divisi/i', $row['alias'])) {
            $optAdmin[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }
    }
    $els = [];
    $els[] = [makeElement('notransaksi', 'label', $_SESSION['lang']['notransaksi']), makeElement('notransaksi', 'text', $data['notransaksi'], ['style' => 'width:200px', 'disabled' => 'disabled'])];
    $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:200px', $disabled => $disabled], $optOrg)];
    $els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)', $disabled => $disabled])];
    $els[] = [makeElement('nikmandor', 'label', $_SESSION['lang']['nikmandor']), makeElement('nikmandor', 'selectsearch', $data['nikmandor'], ['style' => 'width:300px'], $optMandor)];
//    if ($_SESSION['empl']['lokasitugas'] == 'BMAE' || $_SESSION['empl']['lokasitugas'] == 'BMLE') {
        $els[] = [makeElement('nikmandor1', 'label', $_SESSION['lang']['nikmandor1']), makeElement('nikmandor1', 'selectsearch', $data['nikmandor1'], ['style' => 'width:300px'], $optMandor)];
/*
    } else {
        $els[] = [makeElement('nikmandor1', 'label', $_SESSION['lang']['nikmandor1']), makeElement('nikmandor1', 'selectsearch', $data['nikmandor1'], ['style' => 'width:300px', 'disabled' => 'disabled'], $optConductor)];
    }
*/
    if ($param['tipe'] == 'PNN') {
        $els[] = [makeElement('keranimuat', 'label', $_SESSION['lang']['keraniproduksi']), makeElement('keranimuat', 'selectsearch', $data['keranimuat'], ['style' => 'width:300px'], $optKrani)];
        $els[] = [makeElement('asistenpanen', 'label', 'Asisten'), makeElement('asistenpanen', 'selectsearch', $data['asistenpanen'], ['style' => 'width:300px'], $optOfficer)];
        $els[] = [makeElement('nikasisten', 'label', 'Pencatat'), makeElement('nikasisten', 'selectsearch', $data['nikasisten'], ['style' => 'width:300px'], $optRecorder)];
    } else {
        $els[] = [makeElement('nikasisten', 'label', $_SESSION['lang']['nikasisten']), makeElement('nikasisten', 'selectsearch', $data['nikasisten'], ['style' => 'width:300px'], $optOfficer)];
        $els[] = [makeElement('keranimuat', 'label', $_SESSION['lang']['keraniafdeling']), makeElement('keranimuat', 'selectsearch', $data['keranimuat'], ['style' => 'width:300px'], $optAdmin)];
        $els[] = ['', makeElement('asistenpanen', 'hidden', $data['asistenpanen'], ['style' => 'width:300px'], ['' => ''])];
    }

    if ($mode == 'add') {
        $els['btn'] = [makeElement('addHead', 'btn', $_SESSION['lang']['save'], ['onclick' => "addDataTable('".$tipe."')"])];
    } else {
        if ($mode == 'edit') {
            $els['btn'] = [makeElement('editHead', 'btn', $_SESSION['lang']['save'], ['onclick' => "editDataTable('".$tipe."')"])];
        }
    }

    if ($mode == 'add') {
        return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
    }

    if ($mode == 'edit') {
        return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
    }
}

?>
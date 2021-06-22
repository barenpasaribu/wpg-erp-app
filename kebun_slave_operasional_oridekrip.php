<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
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
                    if (0 === $key) {
                        $where .= $r1[0]." like '%".$r1[1]."%'";
                    } else {
                        if (2 === $key) {
                            if (1 === $r1[1]) {
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

        if ('PNN' === $param['tipe']) {
            $header = [$_SESSION['lang']['nomor'], $_SESSION['lang']['organisasi'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['nikmandor'], $_SESSION['lang']['nikmandor1'], 'Recorder', $_SESSION['lang']['keraniproduksi'], 'updateby'];
        } else {
            $header = [$_SESSION['lang']['nomor'], $_SESSION['lang']['organisasi'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['nikmandor'], $_SESSION['lang']['nikmandor1'], $_SESSION['lang']['asisten'], $_SESSION['lang']['keraniafdeling'], 'updateby', $_SESSION['lang']['namakegiatan']];
        }

        if (null === $where) {
            if ('' === $_SESSION['empl']['subbagian']) {
                $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            } else {
                $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and updateby='".$_SESSION['standard']['userid']."'";
            }
        } else {
            if ('' === $_SESSION['empl']['subbagian']) {
                $where .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            } else {
                $where .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
            }
        }

        if (2 === strlen($param['tipe'])) {
            $where .= " and substr(notransaksi,15,2)='".$param['tipe']."' and substr(notransaksi,17,1)='/'";
        } else {
            if (3 === strlen($param['tipe'])) {
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
                if (1 === $row['jurnal']) {
                    $data[$key]['switched'] = true;
                }

                $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
                unset($data[$key]['jurnal']);
                if (false === $notFirst) {
                    if ('' !== $row['nikmandor']) {
                        $whereKarRow .= $row['nikmandor'];
                        $notFirst = true;
                    }

                    if ('' !== $row['nikmandor1']) {
                        if (false === $notFirst) {
                            $whereKarRow .= $row['nikmandor1'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikmandor1'];
                        }
                    }

                    if ('' !== $row['nikasisten']) {
                        if (false === $notFirst) {
                            $whereKarRow .= $row['nikasisten'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikasisten'];
                        }
                    }

                    if ('' !== $row['keranimuat']) {
                        if (false === $notFirst) {
                            $whereKarRow .= $row['keranimuat'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['keranimuat'];
                        }
                    }

                    if ('' !== $row['updateby']) {
                        if (false === $notFirst) {
                            $whereKarRow .= $row['updateby'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['updateby'];
                        }
                    }
                } else {
                    if ('' !== $row['nikmandor']) {
                        if (false === $notFirst) {
                            $whereKarRow .= $row['nikmandor'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikmandor'];
                        }
                    }

                    if ('' !== $row['nikmandor1']) {
                        if (false === $notFirst) {
                            $whereKarRow .= $row['nikmandor1'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikmandor1'];
                        }
                    }

                    if ('' !== $row['nikasisten']) {
                        if (false === $notFirst) {
                            $whereKarRow .= $row['nikasisten'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['nikasisten'];
                        }
                    }

                    if ('' !== $row['keranimuat']) {
                        if (false === $notFirst) {
                            $whereKarRow .= $row['keranimuat'];
                            $notFirst = true;
                        } else {
                            $whereKarRow .= ','.$row['keranimuat'];
                        }
                    }

                    if ('' !== $row['updateby']) {
                        if (false === $notFirst) {
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
            $qTrans = 'select a.notransaksi,b.namakegiatan from '.$dbname.'.kebun_prestasi a left join '.''.$dbname.'.setup_kegiatan b on a.kodekegiatan=b.kodekegiatan where a.notransaksi in ('.$whereTrans.')';
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
            if ('PNN' !== $param['tipe']) {
                $data[$key]['namakegiatan'] = (isset($optKeg[$row['notransaksi']]) ? $optKeg[$row['notransaksi']] : '');
                $dataShow[$key]['namakegiatan'] = (isset($optKeg[$row['notransaksi']]) ? $optKeg[$row['notransaksi']] : '');
            }

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
        }
        if ('PNN' === $param['tipe']) {
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
        if ($postJabatan !== $_SESSION['empl']['kodejabatan']) {
            $tHeader->_actions[2]->_name = '';
        }

        $tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');
        $tHeader->_actions[3]->addAttr('event');
        $tHeader->_actions[3]->addAttr($param['tipe']);
        $tHeader->_switchException = ['detailPDF'];
        if ('PNN' !== $param['tipe']) {
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
        if ('PNN' !== $param['tipe']) {
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
        if ('PNN' !== $param['tipe']) {
            echo $total;
        }

        echo "<div id='detailField' style='clear:both'></div>";

        break;
    case 'add':
        $data = $_POST;
        if ('' === $data['tanggal']) {
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
        if (0 === count($tmpNo)) {
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
        $query = insertQuery($dbname, 'kebun_aktifitas', $data, $cols);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        } else {
            echo $data['notransaksi'];
        }

        break;
    case 'edit':
        $data = $_POST;
        $where = "notransaksi='".$data['notransaksi']."'";
        unset($data['notransaksi']);
        $data['tanggal'] = tanggalsystemw($data['tanggal']);
        $data['updateby'] = $_SESSION['standard']['userid'];
        $query = updateQuery($dbname, 'kebun_aktifitas', $data, $where);
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
        }

        break;
    case 'delete':
        $where = "notransaksi='".$param['notransaksi']."'";
        $query = 'delete from `'.$dbname.'`.`kebun_aktifitas` where '.$where;
        if (!mysql_query($query)) {
            echo 'DB Error : '.mysql_error();
            exit();
        }

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

    if ('edit' === $mode) {
        $disabled = 'disabled';
    } else {
        $disabled = '';
    }

    if ('edit' === $mode) {
        $whereOrg = "kodeorganisasi='".$data['kodeorg']."' and tipe<>'BLOK'";
    } else {
        $whereOrg = "left(kodeorganisasi,4)='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."' and tipe='KEBUN'";
    }

    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg);
    $whereKary = "a.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
    $whereKary .= " and (a.tanggalkeluar is NULL or a.tanggalkeluar > '".$_SESSION['org']['period']['start']."')";
    $qKary = 'select a.karyawanid,a.namakaryawan,a.nik,b.namajabatan from '.$dbname.'.datakaryawan a '.'left join '.$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where (b.alias like '%Mandor%' or "."b.alias like '%Asisten%' or b.alias like '%Kerani%' or b.alias like '%Askep Estate%' or b.alias like '%Admin%') and ".$whereKary.' order by a.nik asc';
    $resKary = fetchData($qKary);
    $optMandor = $optAsisten = $optKrani = $optRecorder = $optOfficer = $optConductor = $optAdmin = ['' => ''];
    foreach ($resKary as $row) {
        if (preg_match('/Mandor/i', $row['namajabatan'])) {
            $optMandor[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Asisten Divisi/i', $row['namajabatan'])) {
            $optAsisten[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Kerani Panen/i', $row['namajabatan']) || preg_match('/Kerani Divisi/i', $row['namajabatan'])) {
            $optKrani[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Kerani Divisi/i', $row['namajabatan']) || preg_match('/Kerani Panen/i', $row['namajabatan'])) {
            $optRecorder[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Askep Estate/i', $row['namajabatan']) || preg_match('/Kerani/i', $row['namajabatan'])) {
            $optOfficer[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Mandor Satu/i', $row['namajabatan'])) {
            $optConductor[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }

        if (preg_match('/Kerani Divisi/i', $row['namajabatan'])) {
            $optAdmin[$row['karyawanid']] = $row['nik'].' - '.$row['namakaryawan'];
        }
    }
    $els = [];
    $els[] = [makeElement('notransaksi', 'label', $_SESSION['lang']['notransaksi']), makeElement('notransaksi', 'text', $data['notransaksi'], ['style' => 'width:200px', 'disabled' => 'disabled'])];
    $els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', $data['kodeorg'], ['style' => 'width:200px', $disabled => $disabled], $optOrg)];
    $els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', $data['tanggal'], ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)', $disabled => $disabled])];
    $els[] = [makeElement('nikmandor', 'label', $_SESSION['lang']['nikmandor']), makeElement('nikmandor', 'selectsearch', $data['nikmandor'], ['style' => 'width:300px'], $optMandor)];
    if ('BMAE' === $_SESSION['empl']['lokasitugas'] || 'BMLE' === $_SESSION['empl']['lokasitugas']) {
        $els[] = [makeElement('nikmandor1', 'label', $_SESSION['lang']['nikmandor1']), makeElement('nikmandor1', 'selectsearch', $data['nikmandor1'], ['style' => 'width:300px'], $optMandor)];
    } else {
        $els[] = [makeElement('nikmandor1', 'label', $_SESSION['lang']['nikmandor1']), makeElement('nikmandor1', 'selectsearch', $data['nikmandor1'], ['style' => 'width:300px', 'disabled' => 'disabled'], $optConductor)];
    }

    if ('PNN' === $param['tipe']) {
        $els[] = [makeElement('keranimuat', 'label', $_SESSION['lang']['keraniproduksi']), makeElement('keranimuat', 'selectsearch', $data['keranimuat'], ['style' => 'width:300px'], $optKrani)];
        $els[] = [makeElement('asistenpanen', 'label', 'Officer'), makeElement('asistenpanen', 'selectsearch', $data['asistenpanen'], ['style' => 'width:300px'], $optOfficer)];
        $els[] = [makeElement('nikasisten', 'label', 'Recorder'), makeElement('nikasisten', 'selectsearch', $data['nikasisten'], ['style' => 'width:300px', 'disabled' => 'disabled'], $optRecorder)];
    } else {
        $els[] = [makeElement('nikasisten', 'label', $_SESSION['lang']['nikasisten']), makeElement('nikasisten', 'selectsearch', $data['nikasisten'], ['style' => 'width:300px'], $optAsisten)];
        $els[] = [makeElement('keranimuat', 'label', $_SESSION['lang']['keraniafdeling']), makeElement('keranimuat', 'selectsearch', $data['keranimuat'], ['style' => 'width:300px'], $optAdmin)];
        $els[] = ['', makeElement('asistenpanen', 'hidden', $data['asistenpanen'], ['style' => 'width:300px'], ['' => ''])];
    }

    if ('add' === $mode) {
        $els['btn'] = [makeElement('addHead', 'btn', $_SESSION['lang']['save'], ['onclick' => "addDataTable('".$tipe."')"])];
    } else {
        if ('edit' === $mode) {
            $els['btn'] = [makeElement('editHead', 'btn', $_SESSION['lang']['save'], ['onclick' => "editDataTable('".$tipe."')"])];
        }
    }

    if ('add' === $mode) {
        return genElementMultiDim($_SESSION['lang']['addheader'], $els, 2);
    }

    if ('edit' === $mode) {
        return genElementMultiDim($_SESSION['lang']['editheader'], $els, 2);
    }
}

?>
<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo "<script src='js/zTools.js'></script>\r\n";
if (!isset($_SESSION['tmp']['actStat'])) {
    echo 'Error : Plant type is missing';
    exit();
}

$blokStatus = $_SESSION['tmp']['actStat'];
switch ($blokStatus) {
    case 'lc':
        $title = 'Land Clearing';
        $tipe = 'tipetransaksi';
        $_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'TB';
        $whereCont = "tipetransaksi='TB'";
        $whereContArr = [];
        $whereContArr[] = ['tipetransaksi', 'TB'];

        break;
    case 'bibit':
        $title = $_SESSION['lang']['pembibitan'];
        $tipe = 'tipetransaksi';
        $_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'BBT';
        $whereCont = "tipetransaksi='BBT'";
        $whereContArr = [];
        $whereContArr[] = ['tipetransaksi', 'BBT'];

        break;
    case 'tbm':
        $title = 'UPKEEP-'.$_SESSION['lang']['tbm'];
        $tipe = 'tipetransaksi';
        $_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'TBM';
        $whereCont = "tipetransaksi='TBM'";
        $whereContArr = [];
        $whereContArr[] = ['tipetransaksi', 'TBM'];

        break;
    case 'tm':
        $title = 'UPKEEP-'.$_SESSION['lang']['tm'];
        $tipe = 'tipetransaksi';
        $_SESSION['tmp']['kebun']['tipeTrans'] = $tipeVal = 'TM';
        $whereCont = "tipetransaksi='TM'";
        $whereContArr = [];
        $whereContArr[] = ['tipetransaksi', 'TM'];

        break;
    default:
        echo 'Error : Planting type undefined';
        exit();
}
if ('' == $_SESSION['empl']['subbagian']) {
    $whereCont .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
} else {
    $whereCont .= " and kodeorg='".$_SESSION['empl']['lokasitugas']."' ";
}

$whereContArr[] = ['kodeorg', $_SESSION['empl']['lokasitugas']];
echo "<script language=javascript1.2>\r\n    function goToPages(page,shows,where) {\r\n\tif(typeof where != 'undefined') {\r\n\t    var newWhere = where.replace(/'/g,'\"');\r\n\t}\r\n\tvar workField = document.getElementById('workField');\r\n\tvar param = \"page=\"+page;\r\n\tparam += \"&shows=\"+shows+\"&tipe=";
echo $tipeVal;

echo "\";\r\n\tif(typeof where != 'undefined') {\r\n\t    param+=\"&where=\"+newWhere;\r\n\t}\r\n\t\r\n\tfunction respon() {\r\n\t    if (con.readyState == 4) {\r\n\t\tif (con.status == 200) {\r\n\t\t    busy_off();\r\n\t\t    if (!isSaveResponse(con.responseText)) {\r\n\t\t\talert('ERROR TRANSACTION,\\n' + con.responseText);\r\n\t\t    } else {\r\n\t\t\t//== Success Response\r\n\t\t\tworkField.innerHTML = con.responseText;\r\n\t\t    }\r\n\t\t} else {\r\n\t\t    busy_off();\r\n\t\t    error_catch(con.status);\r\n\t\t}\r\n\t    }\r\n\t}\r\n\t\r\n\tpost_response_text('kebun_slave_operasional.php?proses=showHeadList', param, respon);\r\n    }\r\n</script>\r\n<script language=javascript src=js/zMaster.js></script>\r\n<script language=javascript src=js/zSearch.js></script>\r\n<script language=javascript1.2 src='js/kebun_operasional.js'></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$optPost = ['Not Posted', 'Posted'];
$optBrg = ['Tidak Ada', 'Ada'];
$ctl = [];
$tmpWhere = json_encode($whereContArr);
$jsWhere = str_replace('"', "'", $tmpWhere);
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".$_SESSION['lang']['new']."' onclick=\"showAdd('".$tipeVal."')\"><br><span align='center'>".$_SESSION['lang']['new'].'</span></div>';
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".$_SESSION['lang']['list']."' onclick=\"defaultList('".$tipeVal."')\"><br><span align='center'>".$_SESSION['lang']['list'].'</span></div>';
$ctl[] = '<fieldset><legend><b>'.$_SESSION['lang']['find'].'</b></legend>'.makeElement('sNoTrans', 'label', $_SESSION['lang']['notransaksi']).makeElement('sNoTrans', 'text', '').makeElement('jurnal', 'label', $_SESSION['lang']['posting']).makeElement('jurnal', 'select', '', [], $optPost).makeElement('barang', 'label', 'Barang').makeElement('barang', 'select', '', [], $optBrg).makeElement('sFind', 'btn', $_SESSION['lang']['find'], ['onclick' => "searchTrans('".$tipe."','".$tipeVal."')"]).'</fieldset>';
$header = [$_SESSION['lang']['nomor'], $_SESSION['lang']['organisasi'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['nikmandor'], $_SESSION['lang']['nikmandor1'], $_SESSION['lang']['asisten'], $_SESSION['lang']['keraniafdeling'], 'updateby', $_SESSION['lang']['namakegiatan']];
$cols = 'notransaksi,kodeorg,tanggal,nikmandor,nikmandor1,nikasisten,keranimuat,jurnal,updateby';
// $query = selectQuery($dbname, 'kebun_aktifitas', $cols, $whereCont, 'tanggal desc, notransaksi desc', false, 10, 1);
// $data = fetchData($query);
// $totalRow = getTotalRow($dbname, 'kebun_aktifitas', $whereCont);
$query = "SELECT notransaksi,kodeorg,tanggal,jurnal,updateby,
(SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=ka.nikmandor) AS mandor,
(SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=ka.nikmandor1) AS mandor1,
(SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=ka.nikasisten) AS asisten,
(SELECT namakaryawan FROM datakaryawan d WHERE d.karyawanid=ka.keranimuat) AS keranimuat
FROM kebun_aktifitas ka ".
($whereCont==''?'':" where ".$whereCont). 
" order by ka.tanggal desc,ka.notransaksi desc limit 0,10";
$totalRow = getRowCount($query);
$data = fetchData($query);
if (!empty($data)) {
    $whereKarRow = 'karyawanid in (';
    $notFirst = false;
    foreach ($data as $key => $row) {
        if (1 == $row['jurnal']) {
            $data[$key]['switched'] = true;
        }

        $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
        unset($data[$key]['jurnal']);
        if (false == $notFirst) {
            if ('' != $row['nikmandor']) {
                $whereKarRow .= $row['nikmandor'];
                $notFirst = true;
            }

            if ('' != $row['nikmandor1']) {
                if (false == $notFirst) {
                    $whereKarRow .= $row['nikmandor1'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikmandor1'];
                }
            }

            if ('' != $row['nikasisten']) {
                if (false == $notFirst) {
                    $whereKarRow .= $row['nikasisten'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikasisten'];
                }
            }

            if ('' != $row['keranimuat']) {
                if (false == $notFirst) {
                    $whereKarRow .= $row['keranimuat'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['keranimuat'];
                }
            }

            if ('' != $row['updateby']) {
                if (false == $notFirst) {
                    $whereKarRow .= $row['updateby'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['updateby'];
                }
            }
			
        } else {
            if ('' != $row['nikmandor']) {
                if (false == $notFirst) {
                    $whereKarRow .= $row['nikmandor'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikmandor'];
                }
            }

            if ('' != $row['nikmandor1']) {
                if (false == $notFirst) {
                    $whereKarRow .= $row['nikmandor1'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikmandor1'];
                }
            }

            if ('' != $row['nikasisten']) {
                if (false == $notFirst) {
                    $whereKarRow .= $row['nikasisten'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikasisten'];
                }
            }

            if ('' != $row['keranimuat']) {
                if (false == $notFirst) {
                    $whereKarRow .= $row['keranimuat'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['keranimuat'];
                }
            }

            if ('' != $row['updateby']) {
                if (false == $notFirst) {
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
    $data[$key]['namakegiatan'] = (isset($optKeg[$row['notransaksi']]) ? $optKeg[$row['notransaksi']] : '');
    $dataShow[$key]['namakegiatan'] = (isset($optKeg[$row['notransaksi']]) ? $optKeg[$row['notransaksi']] : '');
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
$qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', "kodeaplikasi='rawatkebun'");
$tmpPost = fetchData($qPosting);
$postJabatan = $tmpPost[0]['jabatan'];
$tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
$tHeader->_printAttr = [$tipeVal];
$tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
$tHeader->_actions[0]->addAttr($tipeVal);
$tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
$tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');
if ($postJabatan != $_SESSION['empl']['kodejabatan']) {
    $tHeader->_actions[2]->_name = '';
}

$tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');
$tHeader->_actions[3]->addAttr('event');
$tHeader->_actions[3]->addAttr($tipeVal);
$tHeader->addAction('detailData', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/zoom.png');
$tHeader->_actions[4]->addAttr('event');
$tHeader->_actions[4]->addAttr($tipeVal);
$tHeader->pageSetting(1, $totalRow, 10);
$tHeader->setWhere($whereContArr);
$tHeader->_switchException = ['detailData', 'detailPDF'];
OPEN_BOX();
echo "<input type='hidden' id='tipeTransHid' value='".$tipeVal."' />";
echo "<div align='center'><h3>".$title.'</h3></div>';
echo "<div><table align='center'><tr>";
foreach ($ctl as $el) {
    echo "<td v-align='middle' style='min-width:100px'>".$el.'</td>';
}
echo '</tr></table></div>';
CLOSE_BOX();
OPEN_BOX();
echo "<div id='workField'>";
$tHeader->renderTable();
echo '</div>';
CLOSE_BOX();

?>
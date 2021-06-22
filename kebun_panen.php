<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php'; 
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/zMaster.js?v=".mt_rand()."></script>
<script language=javascript src=js/zSearch.js?v=".mt_rand()."></script>
<script language=javascript1.2 src='js/zTools.js?v=".mt_rand()."'></script>
<script language=javascript1.2 src='js/kebun_panen.js?v=".mt_rand()."'></script>
<script languange=javascript1.2 src='js/formTable.js?v=".mt_rand()."'></script>
<link rel=stylesheet type=text/css href='style/zTable.css?v=".mt_rand()."'>";
$tipe = 'tipetransaksi';
$tipeVal = 'PNN';
if ($_SESSION['empl']['subbagian']=='') {
    $whereCont = "tipetransaksi='PNN'";
} else {
    $whereCont = "tipetransaksi='PNN' and updateby='".$_SESSION['standard']['userid']."'";
}

$whereContArr = [];
$whereContArr[] = ['tipetransaksi', 'PNN'];
$ctl = [];
$tmpWhere = json_encode($whereContArr);
$jsWhere = str_replace('"', "'", $tmpWhere);
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".$_SESSION['lang']['new']."' onclick=\"showAdd('".$tipeVal."')\"><br><span align='center'>".$_SESSION['lang']['new'].'</span></div>';
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".$_SESSION['lang']['list']."' onclick=\"defaultList('".$tipeVal."')\"><br><span align='center'>".$_SESSION['lang']['list'].'</span></div>';
$ctl[] = '<fieldset><legend><b>'.$_SESSION['lang']['find'].'</b></legend>'.makeElement('sNoTrans', 'label', $_SESSION['lang']['notransaksi']).makeElement('sNoTrans', 'text', '').makeElement('sFind', 'btn', $_SESSION['lang']['find'], ['onclick' => "searchTrans('".$tipe."','".$tipeVal."')"]).'</fieldset>';
$header = [$_SESSION['lang']['nomor'], $_SESSION['lang']['organisasi'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['nikmandor'], $_SESSION['lang']['nikmandor1'], 'Recorder', $_SESSION['lang']['keraniproduksi'], 'updateby'];
$cols = 'notransaksi,kodeorg,tanggal,nikmandor,nikmandor1,nikasisten,keranimuat,jurnal,updateby';
$query = selectQuery($dbname, 'kebun_aktifitas', $cols, $whereCont." and kodeorg='".$_SESSION['empl']['lokasitugas']."'", 'tanggal desc, notransaksi desc', false, 10, 1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname, 'kebun_aktifitas', $whereCont);
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
            if ($row['nikmandor']!='') {
                $whereKarRow .= $row['nikmandor'];
                $notFirst = true;
            }

            if ($row['nikmandor1']!='') {
                if ($notFirst == false) {
                    $whereKarRow .= $row['nikmandor1'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikmandor1'];
                }
            }

            if ($row['nikasisten']!='') {
                if ($notFirst==false) {
                    $whereKarRow .= $row['nikasisten'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikasisten'];
                }
            }

            if ($row['keranimuat']!='') {
                if ($notFirst == false) {
                    $whereKarRow .= $row['keranimuat'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['keranimuat'];
                }
            }

            if ($row['updateby']!='') {
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

            if ($row['nikmandor1'] !='') {
                if ($notFirst == false) {
                    $whereKarRow .= $row['nikmandor1'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikmandor1'];
                }
            }

            if ($row['nikasisten'] !='') {
                if ($notFirst == false) {
                    $whereKarRow .= $row['nikasisten'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['nikasisten'];
                }
            }

            if ($row['keranimuat'] !='') {
                if ($notFirst == false) {
                    $whereKarRow .= $row['keranimuat'];
                    $notFirst = true;
                } else {
                    $whereKarRow .= ','.$row['keranimuat'];
                }
            }

            if ($row['updateby'] !='') {
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

$optKarRow = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whereKarRow, '0', true);
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['nikmandor'] = $optKarRow[$row['nikmandor']];
    $dataShow[$key]['nikmandor1'] = $optKarRow[$row['nikmandor1']];
    $dataShow[$key]['nikasisten'] = $optKarRow[$row['nikasisten']];
    $dataShow[$key]['keranimuat'] = $optKarRow[$row['keranimuat']];
    $dataShow[$key]['updateby'] = $optKarRow[$row['updateby']];
}
$qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', "kodeaplikasi='panen'");
$tmpPost = fetchData($qPosting);
$postJabatan = $tmpPost[0]['jabatan'];
$tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
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
$tHeader->pageSetting(1, $totalRow, 10);
$tHeader->setWhere($whereContArr);
$tHeader->_switchException = ['detailPDF'];
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['panen'].'</h3></div>';
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
echo close_body();

?>
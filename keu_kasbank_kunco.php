<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/zMaster.js></script> \r\n<script language=javascript src=js/zSearch.js></script>\r\n<script languange=javascript1.2 src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/keu_kasbank.js'></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$ctl = [];
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".$_SESSION['lang']['new']."' onclick=\"showAdd()\"><br><span align='center'>".$_SESSION['lang']['new'].'</span></div>';
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".$_SESSION['lang']['list']."' onclick=\"defaultList()\"><br><span align='center'>".$_SESSION['lang']['list'].'</span></div>';
$whereJam = " kasbank=1 and detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."' or pemilik='".$_SESSION['empl']['induklokasitugas']."')";
if ('EN' == $_SESSION['language']) {
    $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun1', $whereJam, null, true);
} else {
    $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereJam, null, true);
}

$optSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', '', null, true);
$ctl[] = '<fieldset><legend><b>'.$_SESSION['lang']['find'].'</b></legend>'.makeElement('sNoTrans', 'label', $_SESSION['lang']['notransaksi']).makeElement('sNoTrans', 'text', '').'&nbsp;'.makeElement('sAkun', 'label', $_SESSION['lang']['noakun']).makeElement('sAkun', 'select', '', [], $optAkun).makeElement('sTanggal', 'label', $_SESSION['lang']['tanggal']).makeElement('sTanggal', 'date', '').makeElement('sRupiah', 'label', $_SESSION['lang']['jumlah']).makeElement('sRupiah', 'text', '').makeElement('sSup', 'label', $_SESSION['lang']['supplier']).makeElement('sSup', 'text', '').makeElement('sFind', 'btn', $_SESSION['lang']['find'], ['onclick' => 'searchTrans()']).'</fieldset>';
$header = [$_SESSION['lang']['notransaksi'], $_SESSION['lang']['unit'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['noakun'], $_SESSION['lang']['tipe'], $_SESSION['lang']['jumlah'], 'Balance', $_SESSION['lang']['remark'], $_SESSION['lang']['nobayar']];
$align = explode(',', 'C,L,C,L,C,R,C');
$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$cols = "notransaksi,kodeorg,tanggal,noakun,tipetransaksi,jumlah,'balan',keterangan,nobayar,posting";
$query = selectQuery($dbname, 'keu_kasbankht', $cols, $where, 'tanggal desc, notransaksi desc', false, 10, 1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname, 'keu_kasbankht', $where);
$whereAkun = '';
$whereOrg = '';
$i = 0;
foreach ($data as $key => $row) {
    if (1 == $row['posting']) {
        $data[$key]['switched'] = true;
    }

    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
    unset($data[$key]['posting']);
    if (0 == $i) {
        $whereAkun .= "noakun='".$row['noakun']."'";
        $whereOrg .= "kodeorganisasi='".$row['kodeorg']."'";
    } else {
        $whereAkun .= " or noakun='".$row['noakun']."'";
        $whereOrg .= " or kodeorganisasi='".$row['kodeorg']."'";
    }

    $i++;
}
$qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', "kodeaplikasi='keuangan'");
$tmpPost = fetchData($qPosting);
$postJabatan = $tmpPost[0]['jabatan'];
if ('EN' == $_SESSION['language']) {
    $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun1', $whereAkun);
} else {
    $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereAkun);
}

$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg);
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['jumlah'] = number_format($row['jumlah'], 2);
    $dataShow[$key]['noakun'] = $optAkun[$row['noakun']];
    $dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
    $str = 'select sum(jumlah) as jumlah from '.$dbname.".keu_kasbankdt \r\n          where notransaksi='".$data[$key]['notransaksi']."' \r\n          and kodeorg='".$data[$key]['kodeorg']."' \r\n          and tipetransaksi='".$data[$key]['tipetransaksi']."'\r\n          and noakun2a='".$data[$key]['noakun']."'";
    $res = mysql_query($str);
    $bar = mysql_fetch_object($res);
    $balan = 0;
    $balan = $bar->jumlah;
    $balan = $balan - $row['jumlah'];
    $dataShow[$key]['balan'] = number_format($balan, 2);
}
$tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);
$tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
$tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
$tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');
$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');
if ($postJabatan != $_SESSION['empl']['kodejabatan'] && 'HOLDING' != $_SESSION['empl']['tipelokasitugas']) {
    $tHeader->_actions[2]->_name = '';
}

$tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');
$tHeader->_actions[3]->addAttr('event');
$tHeader->addAction('tampilDetail', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/zoom.png');
$tHeader->_actions[4]->addAttr('event');
$tHeader->_switchException = ['tampilDetail'];
$tHeader->pageSetting(1, $totalRow, 10);
$tHeader->_switchException = ['detailPDF', 'tampilDetail'];
$tHeader->setAlign($align);
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['kasbank'].'</h3></div>';
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
<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/zMaster.js></script>\r\n<script language=javascript src=js/zSearch.js></script>\r\n<script language=javascript1.2 src=js/keu_5mesinlaporan.js></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$ctl = [];
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".$_SESSION['lang']['new']."' onclick=\"showAdd()\"><br><span align='center'>".$_SESSION['lang']['new'].'</span></div>';
$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".$_SESSION['lang']['list']."' onclick=\"defaultList()\"><br><span align='center'>".$_SESSION['lang']['list'].'</span></div>';
$ctl[] = '<fieldset><legend><b>'.$_SESSION['lang']['find'].'</b></legend>'.makeElement('sNoTrans', 'label', $_SESSION['lang']['namalaporan']).makeElement('sNoTrans', 'text', '').makeElement('sFind', 'btn', $_SESSION['lang']['find'], ['onclick' => 'searchTrans()']).'</fieldset>';
$header = [$_SESSION['lang']['kodeorg'], $_SESSION['lang']['namalaporan'], $_SESSION['lang']['periode'], $_SESSION['lang']['ket1']];
$cols = 'kodeorg,namalaporan,periode,ket1';
$query = selectQuery($dbname, 'keu_5mesinlaporanht', $cols, '', '', false, 10, 1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname, 'keu_5mesinlaporanht');
$tHeader = new rTable('headTable', 'headTableBody', $header, $data);
$tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');
$tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');
$tHeader->pageSetting(1, $totalRow, 10);
OPEN_BOX();
echo "<div align='center'><h3>".$_SESSION['lang']['mesinlaporan'].'</h3></div>';
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
<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script languange=javascript1.2 src='js/zSearch.js'></script>\r\n<script languange=javascript1.2 src='js/formTable.js'></script>\r\n<script languange=javascript1.2 src='js/formReport.js'></script>\r\n<script languange=javascript1.2 src='js/zGrid.js'></script>\r\n<script languange=javascript1.2 src='js/keu_jurnal_audit.js'></script>\r\n<script languange=javascript1.2>\r\n    //zGrid.column.push(1);\r\n    theGrid[1].addColumn('nourut','";
echo $_SESSION['lang']['nourut'];
echo "','textnum',0,'R',10);\r\n    theGrid[1].addColumn('noakun','";
echo $_SESSION['lang']['noakun'];
echo "','text','-','L',14);\r\n    theGrid[1].addColumn('keterangan','";
echo $_SESSION['lang']['keterangan'];
echo "','text','-','L',50);\r\n    theGrid[1].addPrimColumn('nojurnal','nojurnal');\r\n    theGrid[1].target = \"keu_slave_jurnal_manage_detail\";\r\n</script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$org = $_SESSION['org'];
$period = $_SESSION['org']['period'];
if ('' === $period) {
    echo 'Error : There ain`t active accounting period';
    CLOSE_BOX();
    echo close_body();
    exit();
}

$where = " tanggal ='".(int) (date('Y') - 1)."-12-31'"." and substr(nojurnal,10,4)='".$_SESSION['empl']['lokasitugas']."' and kodejurnal='M'".' and revisi!=0';
$query = selectQuery($dbname, 'keu_jurnalht', 'kodejurnal,nojurnal,tanggal,noreferensi,matauang,totaldebet,totalkredit,revisi', $where);
$resTab = fetchData($query);
$header = [$_SESSION['lang']['kodeabs'], $_SESSION['lang']['nomor'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['noreferensi'], $_SESSION['lang']['matauang'], $_SESSION['lang']['debet'], $_SESSION['lang']['kredit'], $_SESSION['lang']['revisi']];
$table = "<table id='listHeader' class='sortable'>";
$table .= "<thead><tr class='rowheader'>";
$table .= "<td colspan='2'>".$_SESSION['lang']['action'].'</td>';
foreach ($header as $head) {
    $table .= '<td>'.$head.'</td>';
}
$table .= '</tr></thead>';
$table .= "<tbody id='bodyListHeader'>";
foreach ($resTab as $key => $row) {
    $table .= "<tr id='tr_".$key."' class='rowcontent' style='cursor:pointer'>";
    $table .= "<td id='pdf_".$key."'><img src='images/".$_SESSION['theme']."/pdf.jpg' ";
    $table .= "class='zImgBtn' onclick='detailPDF(".$key.",event)'></td>";
    $table .= "<td id='delHead_".$key."'>";
    $table .= "<img src='images/".$_SESSION['theme']."/delete.png' ";
    $table .= "class='zImgBtn' onclick='delHead(".$key.")'></td>";
    foreach ($row as $col => $dat) {
        if ('tanggal' === $col) {
            $dat = tanggalnormal($dat);
        }

        $dtplus = 0;
        $dtmin = 0;
        $krngan = 0;
        $sData = 'select distinct sum(jumlah) as plus from '.$dbname.".keu_jurnaldt where nojurnal='".$row['nojurnal']."' and jumlah>0";
        $qData = mysql_query($sData);
        $rData = mysql_fetch_assoc($qData);
        $dtplus = $rData['plus'];
        $sData = 'select distinct sum(jumlah) as min from '.$dbname.".keu_jurnaldt where nojurnal='".$row['nojurnal']."' and jumlah<0";
        $qData = mysql_query($sData);
        $rData = mysql_fetch_assoc($qData);
        $dtmin = $rData['min'] * -1;
        $sCekData = 'select sum(jumlah) as selisih from '.$dbname.".keu_jurnaldt where nojurnal='".$row['nojurnal']."'";
        $qCekData = mysql_query($sCekData);
        $rCekData = mysql_fetch_assoc($qCekData);
        $dbgr = '';
        if (0 !== (int) ($rCekData['selisih'])) {
            $dbgr = "bgcolor='red'";
        }

        if ('totaldebet' === $col) {
            $table .= "<td id='".$col.'_'.$key."' onclick='passEditHeader(".$key.")' align=right ".$dbgr." title='".$_SESSION['lang']['selisih'].' '.(int) ($rCekData['selisih'])."'>".number_format($dtplus, 0).'</td>';
        } else {
            if ('totalkredit' === $col) {
                $table .= "<td id='".$col.'_'.$key."' onclick='passEditHeader(".$key.")' align=right ".$dbgr." title='".$_SESSION['lang']['selisih'].' '.(int) ($rCekData['selisih'])."'>".number_format($dtmin, 0).'</td>';
            } else {
                $table .= "<td id='".$col.'_'.$key."' onclick='passEditHeader(".$key.")' ".$dbgr.'>'.$dat.'</td>';
            }
        }
    }
    $table .= '</tr>';
}
$table .= '</tbody>';
$table .= '<tfoot></tfoot></table>';
$optCurr = makeOption($dbname, 'setup_matauang', 'kode,matauang');
$optJCode = makeOption($dbname, 'keu_5kelompokjurnal', 'kodekelompok,keterangan', "kodeorg='".$org['kodeorganisasi']."' and kodekelompok='M'");
for ($i = 1; $i <= 5; ++$i) {
    $optRev[$i] = $i;
}
$els = [];
$els[] = [makeElement('nojurnal', 'label', $_SESSION['lang']['nojurnal']), makeElement('nojurnal', 'text', '', ['style' => 'width:120px', 'readonly' => 'readonly', 'disabled' => 'disabled']).' *)  <i>Journal Number Automatic</i>'];
$els[] = [makeElement('kodejurnal', 'label', $_SESSION['lang']['kodejurnal']), makeElement('kodejurnal', 'select', '', ['style' => 'width:200px', 'disabled' => 'disabled'], $optJCode)];
$els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'text', '31-12-'.(date('Y') - 1), ['style' => 'width:80px', 'readonly' => 'readonly', 'onkeypress' => 'return tanpa_kutip(event)', 'disabled' => 'disabled'])];
$els[] = [makeElement('noreferensi', 'label', $_SESSION['lang']['nodok']), makeElement('noreferensi', 'text', '', ['style' => 'width:80px', 'maxlength' => '20', 'onkeypress' => 'return tanpa_kutip(event)', 'disabled' => 'disabled', 'onkeyup' => 'var tmp = this.value;this.value = tmp.toUpperCase()'])];
$els[] = [makeElement('matauang', 'label', $_SESSION['lang']['matauang']), makeElement('matauang', 'select', 'IDR', ['style' => 'width:70px', 'disabled' => 'disabled'], $optCurr)];
$els[] = [makeElement('revisi', 'label', $_SESSION['lang']['revisi']), makeElement('revisi', 'select', '', ['style' => 'width:70px', 'disabled' => 'disabled'], $optRev)];
$els['btn'] = [makeElement('saveButton', 'button', $_SESSION['lang']['save'], ['disabled' => 'disabled'])];
echo "<fieldset id='fieldListTable' style='float:left;clear:left;min-height:200px;height:85%;overflow:auto'><legend><b> Header List</b></legend>";
echo "<img id='addHeadBtn' src='images/".$_SESSION['theme']."/plus.png' style='cursor:pointer' onclick=\"addModeForm('".$_SESSION['theme']."')\" />"."<a style='cursor:pointer' onclick=\"addModeForm('".$_SESSION['theme']."')\">Add Header</a>";
echo $table;
echo '</fieldset>';
echo makeElement('startPeriod', 'hidden', $_SESSION['org']['period']['start']);
echo makeElement('endPeriod', 'hidden', $_SESSION['org']['period']['end']);
echo "<fieldset id='fieldFormHeader' style='clear:right;min-height:200px;'><legend><b>Form Header</b></legend>";
echo genElement($els);
echo "</fieldset><fieldset id='fieldListDetail' style='clear:both'>";
echo '<legend><b>'.$_SESSION['lang']['list'].' '.$_SESSION['lang']['detail'].'</b></legend>';
echo "<div id='divDetail'></div></fieldset>";
CLOSE_BOX();
echo close_body();

?>
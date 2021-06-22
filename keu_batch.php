<?php
include_once 'lib/devLibrary.php';
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$kodeorg = $_SESSION['org']['kodeorganisasi'];
function prepareQuery1($param2)
{
    $arrayFilters = [];
    $arrayFilters[] = " nobatch like '" . $_SESSION['org']['kodeorganisasi'] . "%' ";
    if (isset($param2['where'])) {
        $arrWhere = json_decode(str_replace('\\', '', $param2['where']), true);
        if (!empty($arrWhere)) {
            foreach ($arrWhere as $key => $r1) {
                if ($r1[1] != '') {
                    $arrayFilters[] .= $r1[0] . " like '%" . $r1[1] . "%'";
                }
            }
        }
    }
    $filter = generateFilter($arrayFilters);
    $sql = "select * from keu_batchht $filter order by status,tglbatch desc";
    return $sql;
}

$param=$_POST;
if (sizeof($param)==0)  $param=$_GET;
empty($param['page']) ? $page=1 : $page=$param['page'];
empty($param['shows']) ? $shows=SHOW_ROW_COUNT : $shows=$param['shows'];
$where = $param['where'];
$offset=($page-1)*$shows;
$proses = $_GET['proses'];
$kode = empty($param['code']) ? '': $param['code'];

switch ( $proses) {
    case 'getinvoices':
        $dat = '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>';
        $dat .= '<div style=overflow:auto;width:100%;height:500px;>';
        $dat .= "<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";
        $dat .= "<tr class='rowheader'><td style='width: 2%'>No.</td>";
        $dat .= '<td>' . $_SESSION['lang']['noinvoice'] . '</td>';
        $dat .= '<td>' . $_SESSION['lang']['nopo'] . '</td>';
        $dat .= '<td>' . $_SESSION['lang']['namasupplier'] . '</td>';
        $dat .= '<td>' . $_SESSION['lang']['nilaiinvoice'] . '</td>';
        $dat .= '<td>' . $_SESSION['lang']['nilaippn'] . '</td>';
        $dat .= '<td> Pph </td>';
        $dat .= '<td>' . $_SESSION['lang']['noakun'] . '</td>';
        $dat .= '</tr></thead><tbody>';
        $sql = "select s.namasupplier,t.noinvoice,t.nopo,t.nilaiinvoice,t.nilaippn,t.perhitunganpph as nilaipph,t.noakun,t.kodesupplier
                from keu_tagihanht t
                inner join log_5supplier s on s.supplierid=t.kodesupplier ";
        $arrayFilters = [];
        $arrayFilters[] = " t.kodeorg = '" . $_SESSION['org']['kodeorganisasi'] . "' ";
        $arrayFilters[] = " (t.nobatch is null) AND t.noinvoice not in (select distinct(noinv) from keu_vp_inv ) and t.posting=1";
        if ($param['idSupplier'] != '') {
            $arrayFilters[] = " s.namasupplier like '%" . $param['idSupplier'] . "%'  ";
        }
        if ($param['nopocr'] != '') {
            $arrayFilters[] = " t.nopo like '%" . $param['nopocr'] . "%'  ";
        }
        if ($param['txtfind'] != '') {
            $arrayFilters[] = " t.noinvoice like '" . $param['txtfind'] . "%'  ";
        }
        $filter = generateFilter($arrayFilters);
        $sPo = '';
        $sql2 = $sql . $filter . " order by tanggal desc";
        $res = mysql_query($sql2);
        $no = 1;
        while ($row = mysql_fetch_assoc($res)) {
            $key = [];
            foreach ($row as $k => $v) {
                $key[$k] = $v;
            }
            $parameter = json_encode($key);
            $parameter = str_replace('"', '\'', $parameter);
//            echoMessage('param ',$parameter);
            $dat .= "<tr class='rowcontent' onclick=\"var data=" . $parameter . "; setPo(ConstructPage.entrySection,data);\"><td>" . $no . "</td>";
            $dat .= '<td>' . $row['noinvoice'] . '</td>';
            $dat .= '<td>' . $row['nopo'] . '</td>';
            $dat .= '<td>' . $row['namasupplier'] . '</td>';
            $dat .= '<td>' . number_format($row['nilaiinvoice'], 2) . '</td>';
            $dat .= '<td>' . number_format($row['nilaippn'], 2) . '</td>';
            $dat .= '<td>' . number_format($row['nilaipph'], 2) . '</td>';
            $dat .= '<td>' . $row['noakun'] . '</td></tr>';
            $no++;
        }
        $dat .= '</tbody> 
         
        </table></div></fieldset>';
        echo $dat;

        break;
    case "getinvoiceform":
        $form = "<fieldset style=float: left;>
			<legend>" . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['noinvoice'] . "</legend>"
            . $_SESSION['lang']['find'] . '<input type=text class=myinputtext id=no_brg value=' . date('Y') . ">&nbsp;
			Suppl/Cust <input id=supplierIdcr style=width:150px>&nbsp;"
            . $_SESSION['lang']['nopo'] . "<input id=nopocr style=width:150px>&nbsp;
			<button class=mybutton onclick=findNoinvoice()>Find</button>
			</fieldset><div id=container2><fieldset><legend>" . $_SESSION['lang']['result'] . '</legend></fieldset></div>';
        echo $form;
        break;
    case "saveheader":
        $echo = array('success' => false, 'message' => '', 'data' => array());
        $tglbatch = tanggalsystem($_POST['tglbatch']);
        $lastuser = $_SESSION['empl']['karyawanid'];
        $format = $_SESSION['org']['kodeorganisasi'] . date('Ym');
        $batch = getRows("select max(nobatch) as maxbatch from keu_batchht where nobatch like '$format%'");
        /*
         * do this to make (nobatch+1) from the last nobatch number
         */
        if ($batch['maxbatch'] == null) {
            $total = 1;
        } else {
            $number = str_replace($format, '', $batch['maxbatch']);
            $total = $number + 1;
        }
        $number_s = $format . str_pad($total, 4, '0', STR_PAD_LEFT);
        $insert = "insert into keu_batchht (nobatch,tglbatch,kodeorg,totalinvoice,totalppn,totalpph,status,lastuser) " .
            "values('$number_s','$tglbatch','$kodeorg',0,0,0,0,'$lastuser')";
        if (mysql_query($insert)) {
            $echo['success'] = true;
            $echo['data'] = getRows("select * from keu_batchht where nobatch='$number_s' ");
        } else {
            $echo['message'] = "DB Error : \r\n" .
                "Your query : " . $insert . "\r\n\r\n" .
                "Error Message :" . mysql_error();
        }
        echo json_encode($echo);
        break;
    case "deletedetail":
        $nobatch = $param['nobatch'];
        $noinvoice = $param['noinvoice'];

        $update = "update keu_tagihanht set nobatch = NULL where nobatch='".$nobatch."' AND noinvoice='".$noinvoice."'";
        mysql_query($update);

        $sqld = "select b.nobatch,  concat(b.noinvoice,'___________') as noinvoice, b.nopo, s.namasupplier,b.kodesupplier, b.nilaiinvoice, b.nilaippn, b.nilaipph 
                from keu_batchdt b
                left outer join log_5supplier s on s.supplierid=b.kodesupplier 
                where b.nobatch='" . $nobatch . "' and noinvoice='$noinvoice'";
        $row = getRows($sqld);
        $echo['data'] = [];
        $echo['success'] = true;
        if (count($row) != 0) {
            $update = "update keu_batchht set " .
                "totalinvoice=totalinvoice-" . $row['nilaiinvoice'] . "," .
                "totalppn=totalppn-" . $row['nilaippn'] . "," .
                "totalpph=totalpph-" . $row['nilaipph'] . " " .
                "where nobatch='$nobatch'";
            if (mysql_query($update)) {
                $echo['data']['header'] = getRows("select * from keu_batchht where nobatch='$nobatch' ");
            } else {
                $echo['success'] = false;
                $echo['message'] = "DB Error : \r\n" .
                    "Your query : " . $update . "\r\n\r\n" .
                    "Error Message :" . mysql_error();
            }
            $delete = "delete from keu_batchdt  where nobatch='$nobatch' and noinvoice='$noinvoice'";
            if (mysql_query($delete)) {
                $sqld = "select b.nobatch,  concat(b.noinvoice,'___________') as noinvoice, b.nopo, s.namasupplier,b.kodesupplier, b.nilaiinvoice, b.nilaippn, b.nilaipph
                from keu_batchdt b
                left outer join log_5supplier s on s.supplierid=b.kodesupplier 
                where b.nobatch='" . $nobatch . "' ";
                $echo['data']['detail']['action'] = 'delete';
                $count = getRowCount($sqld);
                if ($count==1){
                    $echo['data']['detail']['rows'] = [getRows($sqld)];
                } else {
                    $echo['data']['detail']['rows'] = getRows($sqld);
                }
            } else {
                $echo['success'] = false;
                $echo['message'] = "DB Error : \r\n" .
                    "Your query : " . $delete . "\r\n\r\n" .
                    "Error Message :" . mysql_error();
            }
        } else {
            $echo['success'] = false;
            $echo['message'] = "No Invoice : $noinvoice tidak ditemukan pada batch : $nobatch";
        }
        echo json_encode($echo);
        break;
    case "deleteheader":
        $echo['success'] = true;
        $nobatch = $param['code'];
        $echo['page'] = $param['page'];

        $update = "update keu_tagihanht set nobatch = NULL where nobatch='".$nobatch."'";
        mysql_query($update);

        $delete = "delete from keu_batchdt where nobatch='$nobatch' ";
        if (mysql_query($delete)) {
            $delete = "delete from keu_batchht where nobatch='$nobatch' ";
            if (mysql_query($delete)) {
            } else {
                $echo['success'] = false;
                $echo['message'] = "DB Error : \r\n" .
                    "Your query : " . $delete . "\r\n\r\n" .
                    "Error Message :" . mysql_error();
            }
        } else {
            $echo['success'] = false;
            $echo['message'] = "DB Error : \r\n" .
                "Your query : " . $delete . "\r\n\r\n" .
                "Error Message :" . mysql_error();
        }

        echo json_encode($echo);
        break;
    case "savedetail":
//        nobatch=BNM2019090002&&noinvoice=20190620070650&nopo=929/05/2019/PO/HO/SSP&nilaiinvoice=62345000&kodesupplier=S001180443&nilaippn=6234500
        $nobatch = $param['nobatch'];
        $noinvoice = $param['noinvoice'];
        $nopo = $param['nopo'];
        $nilaiinvoice = $param['nilaiinvoice'];
        $nilaippn = $param['nilaippn'];
        $nilaipph = $param['nilaipph'];
        $kodesupplier = $param['kodesupplier'];
        $echo['data'] = [];
        $echo['success'] = true;


        $srt="select distinct(kodesupplier) as kodesupplier from keu_batchdt where nobatch='".$nobatch."'";
        $qry=mysql_query($srt);
        $res=mysql_fetch_assoc($qry);
        if(mysql_num_rows($qry)>0 && ($res['kodesupplier']!=$kodesupplier)){

            exit('Warning: Supplier harus sama dengan supplier yang sudah ada di notransaksi ini');
        }

        $sqlcheck = getRowCount("select * from keu_batchdt where noinvoice='$noinvoice'");
        if ($sqlcheck != 0) {
            $echo['success'] = false;
            $echo['message'] = 'No Invoice sudah digunakan ';
        } else {
            $insert = "insert into keu_batchdt (nobatch,noinvoice,nopo,kodesupplier,nilaiinvoice,nilaippn,nilaipph) values(" .
                "'$nobatch','$noinvoice','$nopo','$kodesupplier','$nilaiinvoice','$nilaippn','$nilaipph')";

            if (mysql_query($insert)) {
                $update = "update keu_tagihanht set nobatch='$nobatch' where noinvoice='$noinvoice'";
                mysql_query($update);
                $sqld = "select b.nobatch,  concat(b.noinvoice,'___________') as noinvoice, b.nopo, s.namasupplier,
                b.kodesupplier, b.nilaiinvoice,b.nilaippn,b.nilaipph 
                from keu_batchdt b
                left outer join log_5supplier s on s.supplierid=b.kodesupplier 
                where b.nobatch='" . $nobatch . "'";
/*
                $res = mysql_query($sqld);
                while ($row = mysql_fetch_assoc($res)) {
                    $result[]=$row;
                }
*/  
  
                $echo['sql'] = $sqld;
                $echo['data']['detail']['action'] = 'add';
  //              $echo['data']['detail']['rows'] = $result;

                $count = getRowCount($sqld);
                if ($count==1){
                    $echo['data']['detail']['rows'] = [getRows($sqld)];
                } else {
                    $echo['data']['detail']['rows'] = getRows($sqld);
                }
  

                $update = "update keu_batchht set " .
                    "totalinvoice=(select sum(nilaiinvoice) from keu_batchdt where nobatch='$nobatch')," .
                    "totalppn=(select sum(nilaippn) from keu_batchdt where nobatch='$nobatch'), " .
                    "totalpph=(select sum(nilaipph) from keu_batchdt where nobatch='$nobatch') " .
                    "where nobatch='$nobatch'";
                if (mysql_query($update)) {
                    $echo['data']['header'] = getRows("select 
                    nobatch,tglbatch,kodeorg,format(totalinvoice,0) as totalinvoice,
                    format(totalppn,0) as totalppn, format(totalpph,0) as totalpph,status,lastuser,lastupdate
                    from keu_batchht where nobatch='$nobatch' ");
                }
            } else {
                $echo['success'] = false;
                $echo['message'] = "DB Error : \r\n" .
                    "Your query : " . $insert . "\r\n\r\n" .
                    "Error Message :" . mysql_error();
            }
        }
        echo json_encode($echo);
        break;
    case "entry":
    case "edit":
        $entry = array('header' => array(), 'detail' => array(), 'detailList' => array());
        $headerFields = [];
        $detailFields = [];
        $entry['header']['inputs'] = [
            array('field' => 'nobatch', 'caption' => 'No Batch', 'elements' => [
                array('field' => 'nobatch', 'type' => 'text', 'class' => 'myinputtext',
                    'style' => 'width:150px', 'maxlength' => 25, 'disabled' => 'disabled', 'value' => '')
            ]),
            array('field' => 'tglbatch', 'caption' => 'Tanggal Batch', 'elements' => [
                array('field' => 'tglbatch', 'type' => 'text','caption' => 'Tanggal Batch', 'required'=>'required',
                    'class' => 'myinputtext',
                    'onkeypress' => 'return tanpa_kutip(event)', 'style' => 'width:150px',
                    'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)', 'value' => '')]
            ),
            array('field' => 'totalinvoice', 'caption' => 'Total Invoice', 'elements' => [
                array('field' => 'totalinvoice', 'type' => 'text', 'class' => 'myinputtextnumber',
                    'style' => 'width:150px',
                    'disabled' => 'disabled', 'value' => '')]
            ),
            array('field' => 'totalppn', 'caption' => 'Total Ppn', 'elements' => [
                array('field' => 'totalppn', 'type' => 'text', 'class' => 'myinputtextnumber',
                    'style' => 'width:150px',
                    'disabled' => 'disabled', 'value' => '')]
            ),
               array('field' => 'totalpph', 'caption' => 'Total Pph', 'elements' => [
                array('field' => 'totalpph', 'type' => 'text', 'class' => 'myinputtextnumber',
                    'style' => 'width:150px',
                    'disabled' => 'disabled', 'value' => '')]
            ),
//            array('field' => 'status', 'caption' => 'Sudah digunakan', 'elements' => [
//                array('field' => 'status', 'type' => 'checkbox', 'class' => 'myinputtextnumber',
//                    'disabled' => 'disabled', 'value' => '')]
//            )
        ];
        $sqlh = "select nobatch,tglbatch,kodeorg,
                format(totalinvoice,0) as totalinvoice,
                format(totalppn,0) as totalppn,
                format(totalpph,0) as totalpph,
                lastupdate,lastuser
                from keu_batchht where nobatch='" . $kode . "'";
        $res = mysql_query($sqlh);
        $inputs=&$entry['header']['inputs'];
        while ($row = mysql_fetch_assoc($res)) {
            foreach ($row as $key => $value) {
                for ($i = 0; $i <= count($inputs) - 1; $i++) {
                    if ($inputs[$i]['field'] == $key) {
                        for ($j = 0; $j <= count($inputs[$i]['elements']) - 1; $j++) {
                            if ($inputs[$i]['elements'][$j]['field'] == $key) {
                                $inputs[$i]['elements'][$j]['value'] = $value;
                                break;
                            }
                        }
                        break;
                    }
                }
            }
        }

        $entry['detail'] = [
//            array('field' => 'nobatch', 'caption' => 'No Batch', 'elements' => [
//                array('field' => 'nobatch', 'type' => 'text', 'class' => 'myinputtext',
//                    'style' => 'width:150px', 'maxlength' => 25, 'disabled' => 'disabled', 'value' => '')
//            ]),
            array('field' => 'noinvoice', 'caption' => 'No Invoice', 'elements' => [
                /*
                 *  <input id='kodebarang' name='kodebarang' class='myinputtext' type='text' style='width:50px' value='' disabled=''>&nbsp;
                                        <input id='kodebarang_name' name='kodebarang_name' class='myinputtext' value='' type='text' style='width:150px' disabled=''>
                                        <button id='kodebarang' name='kodebarang' class='mybutton' disabled='' onclick='getInvName(event,'kodebarang','','')'>Cari</button>
                 */
                array('field' => 'noinvoice', 'type' => 'text', 'class' => 'myinputtext',
                    'style' => 'width:150px',
                    'disabled' => 'disabled', 'value' => ''),
                array('field' => 'noinvoice', 'type' => 'button', 'caption' => 'Cari',
                    'onclick' => 'getNoInvoice(\'Cari No Invoice\',\'<div id=formPencariandata></div>\',event)', 'style' => 'width:50px'),
            ]
            ),
            array('field' => 'nopo', 'caption' => 'No PO', 'elements' => [
                array('field' => 'nopo', 'type' => 'text', 'class' => 'myinputtext',
                    'style' => 'width:150px',
                    'disabled' => 'disabled', 'value' => '')]
            ),
            array('field' => 'nilaiinvoice', 'caption' => 'Nilai Invoice', 'elements' => [
                array('field' => 'nilaiinvoice', 'type' => 'text', 'class' => 'myinputtextnumber',
                    'disabled' => 'disabled', 'style' => 'width:150px', 'value' => '')]
            ),
            array('field' => 'kodesupplier', 'caption' => 'Supplier', 'elements' => [
                array('field' => 'kodesupplier', 'type' => 'text', 'class' => 'myinputtext',
                    'style' => 'width:50px',
                    'disabled' => 'disabled', 'value' => ''),
                array('field' => 'namasupplier', 'type' => 'text', 'class' => 'myinputtext',
                    'style' => 'width:150px',
                    'disabled' => 'disabled', 'value' => ''),
                //               <img id="kodevhc_find" onclick="z.elSearch(&quot;kodevhc&quot;,event)" class="zImgBtn" src="images/onebit_02.png" style="position:relative;top:5px">
            ]
            ),
            array('field' => 'nilaippn', 'caption' => 'Nilai Ppn', 'elements' => [
                array('field' => 'nilaippn', 'type' => 'text', 'class' => 'myinputtextnumber',
                    'disabled' => 'disabled', 'style' => 'width:150px', 'value' => '')]
            ),
            array('field' => 'nilaipph', 'caption' => 'Nilai Pph', 'elements' => [
                array('field' => 'nilaipph', 'type' => 'text', 'class' => 'myinputtextnumber',
                    'disabled' => 'disabled', 'style' => 'width:150px', 'value' => '')]
            ),

        ];

        $sqld = "select b.nobatch,  concat(b.noinvoice,'___________') as noinvoice, b.nopo, s.namasupplier, b.kodesupplier, b.nilaiinvoice, b.nilaippn, b.nilaipph
                from keu_batchdt b
                left outer join log_5supplier s on s.supplierid=b.kodesupplier 
                where b.nobatch='" . $kode . "'";
        $entry['detailList'] = array(
            'tableHeader' => ['No Batch', 'No Invoice', 'No PO', 'Supplier', 'Nilai Invoice', 'Nilai Ppn', 'Nilai Pph'],
            'rowFields' => ['nobatch', 'noinvoice', 'nopo', 'namasupplier', 'nilaiinvoice', 'nilaippn','nilaipph'],
            'tableRows' => [],
            'sql' => $sqld
        );

        $res = mysql_query($sqld);
        while ($row = mysql_fetch_assoc($res)) {
            $entry['detailList']['tableRows'][] = $row;
        }
        echo json_encode($entry);
        break;
    case "functional":
        $ctl = [];
        $ctl[] = "<div align='center'><img class=delliconBig src=images/" . $_SESSION['theme'] . "/addbig.png title='" . $_SESSION['lang']['new'] . "' onclick=\"ConstructPage.showAdd()\"><br><span align='center'>" . $_SESSION['lang']['new'] . '</span></div>';
        $ctl[] = "<div align='center'><img class=delliconBig src=images/" . $_SESSION['theme'] . "/list.png title='" . $_SESSION['lang']['list'] . "' onclick=\"ConstructPage.defaultList()\"><br><span align='center'>" . $_SESSION['lang']['list'] . '</span></div>';
        $ctl[] = "<fieldset><legend><b>" . $_SESSION['lang']['find'] . "</b></legend>" .
            makeElement('searchNoBatch', 'label', 'No Batch') .
            makeElement('searchNoBatch', 'text', '') . '&nbsp;' .
            makeElement('searchTglBatch', 'label', 'Tgl Batch') .
            makeElement('searchTglBatch', 'date', '') .
            makeElement('searchFind', 'btn', $_SESSION['lang']['find'], ['onclick' => 'ConstructPage.searchTrans()']) .
            "</fieldset>";
        echo "<div align='center'><h3>Batch</h3></div>";
        echo "<div><table align='center'><tr>";
        foreach ($ctl as $el) {
            echo "<td v-align='middle' style='min-width:100px'>" . $el . '</td>';
        }
        echo '</tr></table></div>';
        break;
    case "list":
        $theTable = '';
        $theTable .= "<fieldset style='clear:left'  id='mainFieldset'>";
        $theTable .= "<legend><b>Batch</b></legend>";
        $theTable .= "<table  id='listTable'  class='sortable' cellspacing='1' style='width:100%' border='0'>";
        $theTable .= "<thead id='headTable'></thead>";
        $theTable .= " <tbody>";
        $theTable .= "</tbody><tfoot id='footTable'></tfoot>";
        $theTable .= "</table></fieldset>";
        echo $theTable;
        break;
    case "listrow":
        $sql1 = prepareQuery1($param);
        $totalRows = getRowCount($sql1);
        $sql1 .= "  limit $offset,$shows";
        $res = mysql_query($sql1);
        $row = array('datas' => array(), 'totalrow' => 0);
        while ($bar = mysql_fetch_assoc($res)) {
            $row['datas'][] = $bar;
        }
        $row['totalrows'] = $totalRows;
        $row['sql'] = $sql1;
        $row['param'] = $param;
        echo json_encode($row);
        break;
    case "init":
        echo open_body();
        include 'master_mainMenu.php';
        echo echoStyleJS([
            array('filename' => 'style/zTable.css', 'type' => 'css'),
            array('filename' => 'js/zMaster.js', 'type' => 'js'),
            array('filename' => 'js/zSearch.js', 'type' => 'js'),
            array('filename' => 'js/zTools.js', 'type' => 'js'),
            array('filename' => 'js/devLibrary.js', 'type' => 'js'),
            array('filename' => 'js/keu_batch.js', 'type' => 'js'),
        ]);
        OPEN_BOX('', '', 'menu', 'menu');
        CLOSE_BOX();

        OPEN_BOX('', '', 'list', 'list');
        CLOSE_BOX();
        break;
}

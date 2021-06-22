<?php

include_once 'lib/devLibrary.php';

require_once 'master_validation.php';

include_once 'lib/eagrolib.php';

include_once 'lib/zLib.php';



include_once 'lib/zPdfMaster.php';

include_once 'lib/terbilang.php';



$kodeorg = $_SESSION['org']['kodeorganisasi'];

function prepareQuery1($param2)

{

    $arrayFilters = [];

    $arrayFilters[] = " novp like '[" . $_SESSION['empl']['lokasitugas'] . "]%' ";

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

    $sql = "select * from keu_vpht $filter order by posting,tanggal desc";

    return $sql;

}



function extractArrayData($data,$delimiter=',')

{

    $ret = '';

    $array=[];

    $data=str_replace('[','',$data);

    $data=str_replace(']','',$data);

    $data=str_replace('\'','',$data);

    $array=explode($delimiter,$data);

    for ($i = 0; $i < count($array); $i++) {

        $ret .= $array[$i] . ($i == count($array) - 1 ? "" : ";");

    }

    return $ret;

}



function getNoPOsInvoices($id,$isnopos){

    $sql = "select * from ". ($isnopos ? "keu_vp_nopo":"keu_vp_inv") ." where novp='$id'";

    $data='';

    $result=[];

    $res = mysql_query($sql);

    while ($row = mysql_fetch_assoc($res)) {

        $result[]=$row[($isnopos?'nopo':'noinv')];

    }

    for($i=0;$i<count($result);$i++){

        $data.=$result[$i].($i==count($result)-1 ? "":";");

    }

    return $data;

}



$param=$_POST;

if (sizeof($param)==0)  $param=$_GET;

empty($param['page']) ? $page=1 : $page=$param['page'];

empty($param['shows']) ? $shows=SHOW_ROW_COUNT : $shows=$param['shows'];

$where = $param['where'];

$offset=($page-1)*$shows;

$proses = $_GET['proses'];

$kode = empty($param['code']) ? '': $param['code'];

$sqld  = "select concat(vp.noakun,' - ',a.namaakun) as namaakun,vp.novp,

                        concat(vp.noakun,'___________') as noakun,vp.kurs,vp.matauang,vp.jumlah,

                        case 

                            when vp.jumlah<0 then 'K' 

                            else 'D'

                        end as dk

                        from keu_vpdt vp

                        inner join keu_5akun a on a.noakun=vp.noakun ";

switch ($proses) {

    case "deletedetail":

        $novp= isset($param['novp'])?$param['novp']:'';

        $noakun= isset($param['noakun'])?$param['noakun']:'';

        $result = dbTransaction(function() {

            global $novp;

            global $noakun;

            global $sqld;

            $echo = array('success' => true, 'message' => '', 'data' => array());

            $delete = "delete from keu_vpdt  where novp='$novp' and noakun='$noakun'";

            $echo['sqldelete']=$delete;

            if (mysql_query($delete)) {

                $sql = $sqld . " where novp='$novp'";

                $echo['data']['detail']['action'] = 'delete';

                $count = getRowCount($sql);

                if ($count==1){

                    $echo['data']['detail']['rows'] = [getRows($sql)];

                } else {

                    $echo['data']['detail']['rows'] = getRows($sql);

                }

				

				$count = getRowCount("select * from keu_vp_nopo where novp='$novp'");

                    $arrNopos=[];

                    if ($count == 1) {

                        $arrNopos = [getRows("select * from keu_vp_nopo where novp='$novp'")];

                    } else {

                        $arrNopos = getRows("select * from keu_vp_nopo where novp='$novp'");

                    }



                    $count = getRowCount("select * from keu_vp_inv where novp='$novp'");

                    $arrInvoices=[];

                    if ($count == 1) {

                        $arrInvoices = [getRows("select * from keu_vp_inv where novp='$novp'")];

                    } else {

                        $arrInvoices = getRows("select * from keu_vp_inv where novp='$novp'");

                    }

                    $echo['data']['header'] = getRows("select * from keu_vpht where novp='$novp' ");                

                    $echo['data']['header']['nopos'] = $arrNopos;

                    $echo['data']['header']['invoices'] = $arrInvoices;

                    $str="select sum(if(jumlah>0,jumlah,0)) as totaldebet, sum(if(jumlah<0,jumlah,0)) as totalkredit, (sum(if(jumlah>0,jumlah,0)) + sum(if(jumlah<0,jumlah,0))) as balance1 from  keu_vpdt where novp='".$novp."' ";
                    $qry=mysql_query($str);
                    $res=mysql_fetch_assoc($qry);
                    $echo['data']['header']['totaldebet']=number_format($res['totaldebet'],2);
                    $echo['data']['header']['totalkredit']=number_format($res['totalkredit'],2);
                    $echo['data']['header']['balance1']=$res['balance1'];

            } else {

                $echo['success'] = false;

                $echo['message'] = "DB Error : \r\n" .

                    "Your query : " . $delete . "\r\n\r\n" .

                    "Error Message :" . mysql_error();

            }

            return $echo;

        });

        echo json_encode($result);

        break;

    case "deleteheader":

        $novp= isset($param['code'])?$param['code']:'';

        $page= $param['page'];

        $result = dbTransaction(function() {

            global $novp;

            global $page;

            $echo = array('success' => true, 'message' => '', 'data' => array());

            $echo['page'] =$page;

            $delete = "delete from keu_vpdt where novp='$novp' ";

            $echo['delete_dt'] = $delete;

            if (mysql_query($delete)) {

                $delete = "delete from keu_vpht where novp='$novp' ";

                $echo['delete_ht'] = $delete;

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

            return $echo;

        });



        echo json_encode($result);

        break;

    case "savedetail":

        $data = [];

        $data[] = array('novp' => $param['novp']);

        $data[] = array('noakun' => $param['noakun']);

        $data[] = array('kurs' => $param['kurs']);

        $data[] = array('matauang' => $param['matauang']);

        $dk = $param['dk'];

        $novp= isset($param['novp'])?$param['novp']:'';

        $noakun= isset($param['noakun'])?$param['noakun']:'';

        $jumlah = str_replace(',','',$param['jumlah']);

        if ($jumlah < 0) {

			$data[] = array('jumlah' => $jumlah * ($dk == 'D' ? -1 : 1)); 

		} else {

            $data[] = array('jumlah' => $jumlah * ($dk == 'K' ? -1 : 1));

		}

        $dataexist=getRows("select * from keu_vpdt where novp='$novp' and noakun='$noakun'");

        $result=array('success' => true, 'message' => '', 'data' => array());

        $action='add';

        if (count($dataexist)!=0 || $dataexist!=null){

            $action='edit';

            $result = dbTransaction(function() {

                global $param;

                global $sqld;

                $echo = array('success' => true, 'message' => '', 'data' => array());

                $novp = $param['novp'];

                $noakun = $param['noakun'];

                $delete = "delete from keu_vpdt  where novp='$novp' and noakun='$noakun'";

                $echo['sqldelete']=$delete;

                if (mysql_query($delete)) {

                    $sql = $sqld . " where novp='$novp'";

//                    $echo['data'] = getRows($sql);

                } else {

                    $echo['success'] = false;

                    $echo['message'] = "DB Error : \r\n" .

                        "Your query : " . $delete . "\r\n\r\n" .

                        "Error Message :" . mysql_error();

                }

                return $echo;

            });

        }

        if ($result['success']) {

            $result=array('success' => true, 'message' => '', 'data' => array());

            $result = dbTransaction(function () {

                global $data;

                global $jumlah;

                global $dk;

                global $novp;

                global $noakun;

                global $sqld;

                global $action;

                $echo = array('success' => false, 'message' => '', 'data' => array());

                $echo['key'] = $novp;

                $sql = '';

                /*

                 * insert detail

                 */

				//$echo['_fields_']=$data;

                if (insertRow('keu_vpdt', $data)) {

                    $echo['success'] = true;

                    $sql .= $sqld . " where vp.novp='$novp' ";// and vp.noakun ='$noakun'";

                    $echo['data']['detail']['sql'] = $sql;

                    $echo['data']['detail']['sqlinsert'] = insertRow('keu_vpdt', $data, true);

                    $echo['data']['detail']['action'] = $action; 

                    $count = getRowCount($sql);

                    if ($count == 1) {

                        $echo['data']['detail']['count1'] = $count; 

						$echo['data']['detail']['rows'] = [getRows($sql)];

                    } else { 

						$echo['data']['detail']['count~1'] =getRows($sql); 

                        $echo['data']['detail']['rows'] = getRows($sql);

                    }

                    $count = getRowCount("select * from keu_vp_nopo where novp='$novp'");

                    $arrNopos=[];

                    if ($count == 1) {

                        $arrNopos = [getRows("select * from keu_vp_nopo where novp='$novp'")];

                    } else {

                        $arrNopos = getRows("select * from keu_vp_nopo where novp='$novp'");

                    }



                    $count = getRowCount("select * from keu_vp_inv where novp='$novp'");

                    $arrInvoices=[];

                    if ($count == 1) {

                        $arrInvoices = [getRows("select * from keu_vp_inv where novp='$novp'")];

                    } else {

                        $arrInvoices = getRows("select * from keu_vp_inv where novp='$novp'");

                    }



                    $echo['data']['header'] = getRows("select * from keu_vpht where novp='$novp' ");

                    $echo['data']['header']['nopos'] = $arrNopos;

                    $echo['data']['header']['invoices'] = $arrInvoices;

                    $str="select sum(if(jumlah>0,jumlah,0)) as totaldebet, sum(if(jumlah<0,jumlah,0)) as totalkredit, (sum(if(jumlah>0,jumlah,0)) + sum(if(jumlah<0,jumlah,0))) as balance1 from  keu_vpdt where novp='".$novp."' ";
                    $qry=mysql_query($str);
                    $res=mysql_fetch_assoc($qry);
                    $echo['data']['header']['totaldebet']=number_format($res['totaldebet'],2);
                    $echo['data']['header']['totalkredit']=number_format($res['totalkredit'],2);
                    $echo['data']['header']['balance1']=$res['balance1'];

                } else {

                    $echo['success'] = false;

                    $echo['message'] = getErrorDB(insertRow('keu_vpdt', $data, true));

                }

                return $echo;

            });

        }

        echo json_encode($result);

        break;

    case "saveheader":

//        $echo = array('success' => false, 'message' => '', 'data' => array());

        $data = [];

        $novp = '[' . $_SESSION['empl']['lokasitugas'] . ']' . date('YmdHis');

        $data[] = array('novp' => $novp);

        $data[] = array('tanggal' => tanggalsystem($param['tanggal']));

        $data[] = array('tanggalterima' => tanggalsystem($param['tanggalterima']));

        $data[] = array('tanggalbayar' => tanggalsystem($param['tanggalbayar']));

        $data[] = array('tanggaljatuhtempo' => tanggalsystem($param['tanggaljatuhtempo']));

        $data[] = array('penjelasan' => $param['penjelasan']);

        $data[] = array('kodeorg' => $_SESSION['empl']['lokasitugas']);

        $data[] = array('updateby' => $_SESSION['standard']['userid']);

        $nobatch= isset($param['nobatch'])?$param['nobatch']:'';

        $nopos = extractArrayData($param['nopos']);

        $noinvoices = extractArrayData($param['noinvoices']);

       $rpInvoice= str_replace(',','',$param['rpInvoice']);

//        $data[] = array('nopo' => $nopos);

        $result = dbTransaction(function() {

            global $data;

            global $param;

            global $nobatch;

            global $novp;

            global $rpInvoice;

            $echo = array('success' => true, 'message' => '', 'data' => array());

            global $noinvoices;

            global $nopos;



            /*

             * insert header

             */

            $noinvoices__=explode(';',$noinvoices);

            $nopos__= explode(';',$nopos); 

            $data[] = array('nopo' => $nopos);

            $data[] = array('noinv1' => $noinvoices);

            $data[] = array('totaljumlah' =>$rpInvoice );

            if (insertRow('keu_vpht', $data)) {

                $data1 = [];

                $data2 = [];

                foreach ($nopos__ as $nopo) {

                    
                    $sql="select * from keu_vp_nopo where novp='".$novp."' and nopo='".$nopo."'";
                    $v_sql=mysql_query($sql);
                    if(mysql_num_rows($v_sql)<1){

                    $data1 = [];
                    $data1[] = array('novp' => $novp);
                    $data1[] = array('nopo' => $nopo);

                        if (insertRow('keu_vp_nopo', $data1)) {

                        } else {

                            $echo['success'] = false;
                            $echo['message'] = getErrorDB(insertRow('keu_vp_nopo', $data1, true));

                        return $echo;

                        }
                    }

                }

                foreach ($noinvoices__ as $noinv) {

                    $data1 = [];

                    $data1[] = array('novp' => $novp);

                    $data1[] = array('noinv' => $noinv);

                    if (insertRow('keu_vp_inv', $data1)) {

                    } else {

                        $echo['success'] = false;

                        $echo['message'] = getErrorDB(insertRow('keu_vp_inv', $data1, true));

                        return $echo;

                    }

                }

                /*

                 * if invoices take from batch, update status batch header and detail

                 */

                if ($nobatch != '') {

                    $data2[] = array('status' => true);

                    $where = " noinvoice in (";

                    for ($i = 0; $i < count($noinvoices__); $i++) {

                        $where .= "'$noinvoices__[$i]'" . ($i == count($noinvoices__) - 1 ? "" : ",");

                    }

                    $where .= ")";

                    updateRow('keu_batchdt', $data2, " nobatch ='$nobatch' and $where");



                    /*

                     * update status batch header when all invoices used

                     */

                    $row = getRows("select count(*) as totalrow,sum(status) as totalused from keu_batchdt where nobatch='$nobatch' group by nobatch");

                    if ($row['totalrow'] == $row['totalused']) {

                        $data2[] = array('status' => true);

                        updateRow('keu_batchht', $data2, " nobatch ='$nobatch'");

                    }

                }

                $echo['success'] = true;

                $echo['sqlinsert'] = insertRow('keu_vpht', $data, true);

                $echo['key'] = $novp;

                $echo['data'] = getRows("select * from keu_vpht where novp='$novp' ");

				

				$count = getRowCount("select * from keu_vp_nopo where novp='$novp'");

                $arrNopos=[];

                    if ($count == 1) {

                        $arrNopos = [getRows("select * from keu_vp_nopo where novp='$novp'")];

                    } else {

                        $arrNopos = getRows("select * from keu_vp_nopo where novp='$novp'");

                    }



                    $count = getRowCount("select * from keu_vp_inv where novp='$novp'");

                    $arrInvoices=[];

                    if ($count == 1) {

                        $arrInvoices = [getRows("select * from keu_vp_inv where novp='$novp'")];

                    } else {

                        $arrInvoices = getRows("select * from keu_vp_inv where novp='$novp'");

                    }

					

                    $echo['data']['nopos'] = $arrNopos;

                    $echo['data']['invoices'] = $arrInvoices;

            } else {

                $echo['success'] = false;

                $echo['message'] = getErrorDB(insertRow('keu_vpht', $data, true));

                return $echo;

            }

            return $echo;

        });

        echo json_encode($result);

        break;

    case "preview":

           try {

            $vp = getRows("select *, (select sum(jumlah) from keu_vpdt d where d.novp=h.novp and jumlah > 0) as jumlah

        from keu_vpht h where novp='$kode'");

            $vpd = getRows("select * from (

                        select 

                        1 as lvl,

                        concat(vp.noakun,' - ',a.namaakun) as namaakun,

                        abs(vp.jumlah) as jumlah,vp.noakun

                        from keu_vpdt vp

                        inner join keu_5akun a on a.noakun=vp.noakun

                        where vp.novp like '$kode' and vp.jumlah>0

                        

                        union all

                        

                        select 

                        2 as lvl,

                        concat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',vp.noakun,' - ',a.namaakun)  as namaakun, 

                        abs(vp.jumlah) as jumlah,vp.noakun

                        from keu_vpdt vp

                        inner join keu_5akun a on a.noakun=vp.noakun

                        where vp.novp like '$kode' and vp.jumlah<0

                        ) x

                        order by lvl, noakun");

            $tgl=date_create($vp['tanggal']);

            $tglt=date_create($vp['tanggalterima']);

            $tglb=date_create($vp['tanggalbayar']);

            $table = "<link rel='stylesheet' type='text/css' href='style/generic.css'>";

            $table .= "<fieldset><legend>'.$title.'</legend>";

            $table .= "<table cellpadding=0 cellspacing=1 border=0 width=100% class='sortable'>";

            $table .= "<tbody class='rowcontent'>";

            $table .= "<tr>";

            $table .= "<td colspan='3'>" . $_SESSION['org']['namaorganisasi'] . "</td>";

            $table .= "<td  style='width:14%' align='right'>No Voucher Payable&nbsp;</td>";

            $table .= "<td  style='width:1%'>&nbsp;:&nbsp;</td>";

            $table .= "<td  style='width:14%'>$kode</td>";

            $table .= "</tr>";

            $table .= "<tr>";

            $table .= "<td colspan='3'>&nbsp;</td>";

            $table .= "<td  style='width:14%' align='right'>Tanggal&nbsp;</td>";

            $table .= "<td  style='width:1%'>&nbsp;:&nbsp;</td>";

            $table .= "<td  style='width:14%'>" .  date_format($tgl,"d-m-Y") . "</td>";

//            $table .= "<td  style='width:14%'>" . (new DateTime($vp['tanggal']))->format('d-m-Y') . "</td>";

            $table .= "</tr>";

            $table .= "<tr><td colspan='6'>&nbsp;</td></tr>";

            $table .= "<tr><td colspan='6'>&nbsp;</td></tr>";

            $table .= "<tr>

                    <td style='width:40%'>C R E D I T O R</td>

                    <td style='width:1%'>&nbsp;:&nbsp;</td>

                    <td style='width:29%' colspan='4'></td>

                    </tr>

                    <tr>

                    <td style='width:40%'>PURCHASED ORDER NO</td>

                    <td style='width:1%'>&nbsp;:&nbsp;</td>

                    <td style='width:29%' colspan='4'>&nbsp;" . $vp['nopo'] . "</td>

                    </tr>

                    <tr>

                    <td style='width:40%'>DATE OF INVOICE RECEIVED</td>

                    <td style='width:1%'>&nbsp;:&nbsp;</td>

                    <td style='width:29%' colspan='4'>&nbsp;" . date_format($tglt,"d-m-Y") . "</td>

                    </tr>

                    <tr>

                    <td style='width:40%'>DATE OF PAYMENT</td>

                    <td style='width:1%'>&nbsp;:&nbsp;</td>

                    <td style='width:29%' colspan='4'>&nbsp;" . date_format($tglb,"d-m-Y") . "</td>

                    </tr>

                    <tr>

                    <td style='width:40%'>EXPLANATION</td>

                    <td style='width:1%'>&nbsp;:&nbsp;</td>

                    <td style='width:29%' colspan='4'>&nbsp;" . $vp['penjelasan'] . "</td>

                    </tr>

                    <tr><td colspan='6'>&nbsp;</td></tr>

                    <tr>

                    <td style='width:40%'>TOTAL AMOUNT (Rupiah)</td>

                    <td style='width:1%'>&nbsp;:&nbsp;</td>

                    <td style='width:29%' colspan='4'>&nbsp;Rp." . number_format($vp['jumlah'], 0) . "</td>

                    </tr>

                    <tr><td colspan='6'>&nbsp;</td></tr>

                    <tr><td colspan='4' align='center'>&nbsp;VOUCHER PAYABLE SYSTEM</td> 

                    <td colspan='2' rowspan='6' style='width:50%'>

                        <table class='sortable' border='0' cellpadding='0' cellspacing='0'>

                            <tbody class='rowcontent' >

                                <tr class='rowcontent'><td colspan='3'>&nbsp;</td></tr>

                                <tr class='rowcontent'><td>&nbsp;PREPARED BY</td><td>&nbsp;:&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

                                <tr class='rowcontent'><td colspan='3'>&nbsp;</td></tr>

                                <tr class='rowcontent'><td>&nbsp;VERIFIED BY</td><td>&nbsp;:&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

                                <tr class='rowcontent'><td colspan='3'>&nbsp;</td></tr>

                                <tr class='rowcontent'><td>&nbsp;APPROVED BY</td><td>&nbsp;:&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

                                <tr class='rowcontent'><td colspan='3'>&nbsp;</td></tr>

                                <tr class='rowcontent'><td>&nbsp;POSTED BY</td><td>&nbsp;:&nbsp;</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>

                                <tr class='rowcontent'><td colspan='3'>&nbsp;</td></tr>

                            </tbody>

                        </table>

                    </td>

                    </tr>

                    <tr><td colspan='2' align='left' style='width:25%'>&nbsp;Account Code</td><td colspan='2' align='right' style='width:25%'>Amount&nbsp;</td></tr>

                    <tr><td colspan='2' align='left' style='width:25%'>&nbsp;</td><td colspan='2' align='right' style='width:25%'>&nbsp;</td></tr>";

            $i = 1;

            foreach ($vpd as $row) {

                $table .= "<tr >

            <td colspan = '2' align = 'left' style = 'width:25%' >&nbsp;" . $row['namaakun'] . " </td >

            <td colspan = '2' align = 'right' style = 'width:25%' >" . number_format($row['jumlah'], 0) . "&nbsp;</td ></tr >;

                    ";

                $i++;

            }

            $table .= " <tr><td colspan='4'>&nbsp;</td><td colspan='2'>&nbsp;</td></tr></tbody></table>";

            if ($proses == 'preview') {

                echo $table;

            }

            if ($proses == 'pdf') {

                echo generateTablePDF($table, true, 'Legal', 'landscape');

            }

        } catch (Exception $e){

            echoMessage('Error : ',$e->getMessage());

        }

        break;



    case "pdf":



           $pdf = new zPdfMaster('P', 'pt', 'A4');

       $pdf->_noThead = true;

       $pdf->setAttr1($title, $align, $length, []);

       $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;

       $height = 15;

       $pdf->AddPage();

       $pdf->SetFillColor(255, 255, 255);

       $pdf->SetFont('Arial', 'B', 9);



                   $vp = getRows("select *, (select sum(jumlah) from keu_vpdt d where d.novp=h.novp and jumlah > 0) as jumlah

        from keu_vpht h where novp='$kode'");

            $vpd = getRows("select * from (

                        select 

                        1 as lvl,

                        concat(vp.noakun,' - ',a.namaakun) as namaakun,

                        vp.jumlah as jumlah,vp.noakun

                        from keu_vpdt vp

                        inner join keu_5akun a on a.noakun=vp.noakun

                        where vp.novp like '$kode' and vp.jumlah>0

                        

                        union all

                        

                        select 

                        2 as lvl,

                        concat('',vp.noakun,' - ',a.namaakun)  as namaakun, 

                        vp.jumlah as jumlah,vp.noakun

                        from keu_vpdt vp

                        inner join keu_5akun a on a.noakun=vp.noakun

                        where vp.novp like '$kode' and vp.jumlah<0

                        ) x

                        order by lvl, noakun");



            $tgl=date_create($vp['tanggal']);

            $tglt=date_create($vp['tanggalterima']);

            $tglb=date_create($vp['tanggalbayar']);



            $pdf->Cell(100, 10, 'No Voucher Payable ' );

            $pdf->Cell(10, 10, ' : ');

            $pdf->Cell(300, 10, $kode, 0, 1, 'L', 1);

            $pdf->Cell(100, 10, 'Tanggal ' );

            $pdf->Cell(10, 10, ' : ');

            $pdf->Cell(300, 10, date_format($tgl,"d-m-Y"), 0, 1, 'L', 1);

            

            $pdf->Ln(20);

/*            $pdf->Cell(200, 10, 'C R E D I T O R ',1 );

            $pdf->Cell(10, 10, ' : ',1);

            $pdf->Cell(300, 10,'' , 1, 1, 'L', 1);

*/

            $pdf->Cell(200, 10, 'NO PO',1 );

            $pdf->Cell(10, 10, ' : ',1);

            $pdf->MultiCell(300, 10,  $vp['nopo'] , 1, 1, 'L', 1);



            $pdf->Cell(200, 10, 'Tanggal Terima ',1 );

            $pdf->Cell(10, 10, ' : ',1);

            $pdf->Cell(300, 10, date_format($tglt,"d-m-Y") , 1, 1, 'L', 1);



            $pdf->Cell(200, 10, 'Tanggal Bayar ',1 );

            $pdf->Cell(10, 10, ' : ',1);

            $pdf->Cell(300, 10, date_format($tglb,"d-m-Y") , 1, 1, 'L', 1);



            $pdf->Cell(200, 10, 'Keterangan ',1 );

            $pdf->Cell(10, 10, ' : ',1);

            $pdf->MultiCell(300, 10, $vp['penjelasan'] , 1, 1, 'L', 1);



            $pdf->Ln(20);

            $pdf->Cell(200, 10, 'Nilai Invoice (Rupiah) ',1 );

            $pdf->Cell(10, 10, ' : ',1);

            $pdf->Cell(300, 10,number_format($vp['totaljumlah'], 2) , 1, 1, 'L', 1);



            $pdf->Ln(20);

            $pdf->Cell(510, 10, 'VOUCHER PAYABLE SYSTEM ',1 ,1,'C',0);

            $pdf->Cell(255, 10, ' Akun ',1 );

            $pdf->Cell(255, 10,' Jumlah ', 1 , 1, 'R', 1);



            $i = 1;

           foreach($vpd as $row) {



            $pdf->Cell(255, 10, $row['namaakun'],1 );

            if($row['jumlah']<0){
            	$pdf->Cell(255, 10,'('.number_format($row['jumlah'],2).')', 1 , 1, 'R', 1);
            }else{
            	$pdf->Cell(255, 10,number_format($row['jumlah'],2), 1 , 1, 'R', 1);
            }

            $i++;

            }

            $pdf->Ln(20);

            $pdf->Cell(170, 10,' Diperiksa ', 'TL', 0, 'C' );

            $pdf->Cell(170, 10,' Disetujui ', 'T', 0, 'C');

            $pdf->Cell(170, 10,' Dibuat Oleh ', 'TR', 1, 'C');

            $pdf->Cell(510, 30,'  ', 'RL', 1, 'C' );

            $pdf->Cell(170, 10,'', 'BL', 0, 'C' );

            $pdf->Cell(170, 10,'', 'B', 0, 'C');

            $pdf->Cell(170, 10,'', 'BR', 1, 'C');

            

            $pdf->Output();





        break;

    case 'getpo':

        $dat = '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>';

        $dat .= '<div style=overflow:auto;width:100%;height:500px;>';

        $dat .= "<table cellpadding=1 cellspacing=1 border=0 class='sortable'><thead>";

        $dat .= "<tr class='rowheader'><td style='width: 2%'>No.</td>";

        $dat .= '<td>' . $_SESSION['lang']['noinvoice'] . '</td>';

        $dat .= '<td>' . $_SESSION['lang']['nopo'] . '</td>';

        $dat .= '<td>' . $_SESSION['lang']['namasupplier'] . '</td>';

        $dat .= '<td>' . $_SESSION['lang']['nilaiinvoice'] . '</td>';

        $dat .= '<td>' . $_SESSION['lang']['nilaippn'] . '</td>';

        $dat .= '<td>' . $_SESSION['lang']['noakun'] . '</td>';

        $dat .= '</tr></thead><tbody>';

        $sql = "select s.namasupplier,t.noinvoice,t.nopo,t.nilaiinvoice,t.nilaippn,t.noakun,t.kodesupplier

                from keu_tagihanht t

                inner join log_5supplier s on s.supplierid=t.kodesupplier ";

        $arrayFilters = [];

        $arrayFilters[] = " t.kodeorg = '" . $_SESSION['org']['kodeorganisasi'] . "' ";

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

            $dat .= '<td>' . $row['noakun'] . '</td></tr>';

            $no++;

        }

        $dat .= '</tbody> 

         

        </table></div></fieldset>';

        echo $dat;



        break;

    case "getinvoiceform":

        $tipe = $_GET['tipe'];

        $param = $_GET;

        $optTipe = array('po' => $_SESSION['lang']['po'],

            'k' => $_SESSION['lang']['kontrak'],

            'sj' => $_SESSION['lang']['suratjalan'],

            'ns' => $_SESSION['lang']['konosemen'],

            'b' => 'Batch',

            'ot' => $_SESSION['lang']['lain'],

        );

        $form = "<div style='margin:10px 0 15px 5px'><label for='po'>" . $_SESSION['lang']['find'] . "</label>" .

            makeElement('tipe', 'select', 'po', array(), $optTipe) .

            makeElement('po', 'text', '', array('onkeypress' => 'key=getKey(event);if(key==13){findPO();}')) .

            "    <button class=mybutton onclick='findPO()'>" .

            $_SESSION['lang']['find'] .

            "</button></div><fieldset><legend>" .

            $_SESSION['lang']['hasil'] .

            "</legend><div id='hasilPO'></div><div id='hasilInvoice' style='display:none'></div></fieldset><div id='progress'></div>";

        echo $form;

        break;

    case "entry":

    case "edit":

        $entry = array('header' => array(), 'detail' => array(), 'detailList' => array());

        $headerFields = [];

        $detailFields = [];

                    $str="select sum(if(jumlah>0,jumlah,0)) as totaldebet, sum(if(jumlah<0,jumlah,0)) as totalkredit, (sum(if(jumlah>0,jumlah,0)) + sum(if(jumlah<0,jumlah,0))) as balance1 from  keu_vpdt where novp='".$novp."' ";
                    $qry=mysql_query($str);
                    $res=mysql_fetch_assoc($qry);
                    $echo['data']['header']['totaldebet']=number_format($res['totaldebet'],2);
                    $echo['data']['header']['totalkredit']=number_format($res['totalkredit'],2);
                    $echo['data']['header']['balance1']=$res['balance1'];


        $nopos = array('currentValue' => '',

            getOptionFromRows("select * from keu_vp_nopo where novp='$kode'", "nopo", "nopo")

        );

        $noinvoices = array('currentValue' => '',

            getOptionFromRows("select * from keu_vp_inv where novp='$kode'", "noinv", "noinv")

        );

        $entry['header']['inputs'] = [

            array('field' => 'novp', 'caption' => 'No VP', 'elements' => [

                array('field' => 'novp', 'type' => 'text', 'caption' => 'No VP', 'class' => 'myinputtext',

                    'style' => 'width:150px', 'maxlength' => 25, 'disabled' => 'disabled', 'value' => '')

            ]),

            array('field' => 'tanggalterima', 'caption' => 'Tanggal Terima', 'elements' => [

                array('field' => 'tanggalterima', 'type' => 'text', 'caption' => 'Tanggal Terima', 'class' => 'myinputtext',

                    'onkeypress' => 'return tanpa_kutip(event)','required'=>'required', 'style' => 'width:150px',

                    'onmousemove' => 'setCalendar(this.id)', 'value' => '', 'autocomplete'=>'off' )]

            ),

            array('field' => 'tanggal', 'caption' => 'Tanggal', 'elements' => [

                array('field' => 'tanggal', 'type' => 'text','caption' => 'Tanggal',  'class' => 'myinputtext',

                    'onkeypress' => 'return tanpa_kutip(event)', 'required'=>'required','style' => 'width:150px',

                    'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)', 'value' => '' , 'autocomplete'=>'off')]

            ),

            array('field' => 'tanggalbayar', 'caption' => 'Tanggal Bayar', 'elements' => [

                array('field' => 'tanggalbayar', 'type' => 'text', 'caption' => 'Tanggal Bayar', 'required'=>'required','class' => 'myinputtext',

                    'onkeypress' => 'return tanpa_kutip(event)', 'style' => 'width:150px',

                    'onmousemove' => 'setCalendar(this.id)', 'value' => '', 'autocomplete'=>'off')]

            ),

            /*

             * options should be

             * array('currentValue'=>'somecurrentValue',[{value:caption},..{value:caption}])

             */

            array('field' => 'nopos', 'caption' => 'No PO', 'elements' => [

                array('field' => 'nopos', 'type' => 'select', 'caption' => 'No PO', 'multiple' => 'multiple','disabled'=>'disabled',

                    'style' => 'width:150px', 'value' => '', 'required'=>'required','style' => 'height: 50px; width: 150px',

                    'options' => $nopos),

                array('field' => 'noinvoice', 'type' => 'button', 'caption' => 'Get PO',

                    'onclick' => 'getPO(event)', 'style' => 'width:50px'),

            ]

            ),

            array('field' => 'tanggaljatuhtempo', 'caption' => 'Tanggal Jatuh Tempo', 'elements' => [

                array('field' => 'tanggaljatuhtempo', 'type' => 'text', 'caption' => 'Tanggal Jatuh Tempo', 'required'=>'required','class' => 'myinputtext',

                    'onkeypress' => 'return tanpa_kutip(event)', 'style' => 'width:150px',

                    'onmousemove' => 'setCalendar(this.id)', 'value' => '', 'autocomplete'=>'off')]

            ),

            array('field' => 'noinvoices', 'caption' => 'No Invoice', 'elements' => [

                array('field' => 'noinvoices', 'type' => 'select', 'caption' => 'No Invoice','required'=>'required','multiple' => 'multiple',

                    'value' => '', 'style' => 'height: 50px; width: 150px','disabled'=>'disabled',

                    'options' => $noinvoices),]

            ),

            array('field' => 'penjelasan', 'caption' => 'Keterangan', 'elements' => [

                array('field' => 'penjelasan', 'required'=>'required', 'caption' => 'Keterangan','type' => 'textarea',

                    'value' => '', 'style' => 'height: 50px; width: 150px',

                ),]

            ),

        ];

        $sqlh = "select * from keu_vpht where novp='" . $kode . "'";

        $entry['header']['sql']=$sqlh;

        $entry['header']['data']=getRows($sqlh);
        $entry['header']['data']['tanggal']=tanggalnormal($entry['header']['data']['tanggal']);
        $entry['header']['data']['tanggalterima']=tanggalnormal($entry['header']['data']['tanggalterima']);
        $entry['header']['data']['tanggalbayar']=tanggalnormal($entry['header']['data']['tanggalbayar']);
        $entry['header']['data']['tanggaljatuhtempo']=tanggalnormal($entry['header']['data']['tanggaljatuhtempo']);


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

 /*       $akun = array('currentValue' => '',

            getOptionFromRows("select * from keu_5akun 

            where detail=1 and left(noakun,3) in ('213','117','821','211','212','113','116','121','822','713','118','115','811','114','711','611','621') 

            order by noakun", "noakun", "namaakun")

*/

            $akun = array('currentValue' => '',

            getOptionFromRows("select * from keu_5akun 

            where detail=1 order by noakun", "noakun", "namaakun")





        );

        $dk = array('currentValue' => '',

            [array('D' => 'Debet'), array('K' => 'Kredit')]

        );

        $currency = array('currentValue' => '',

            [array('IDR' => 'Rupiah'), array('USD' => 'USD')]

        );

        $entry['detail'] = [

            array('field' => 'noakun', 'caption' => 'No Akun', 'elements' => [

                array('field' => 'noakun', 'type' => 'select', 'caption' => 'No Akun',

                    'style' => 'width:150px', 'required'=>'required','value' => '', 'showOptionValue' => true,

                    'options' => $akun),

            ]

            ),

            array('field' => 'matauang', 'caption' => 'Mata Uang', 'elements' => [

                array('field' => 'matauang', 'required'=>'required','caption' => 'Mata Uang', 'type' => 'select',

                    'style' => 'width:150px', 'value' => '',

                    'options' => $currency),

            ]

            ),

            array('field' => 'kurs', 'caption' => 'Kurs', 'elements' => [

                array('field' => 'kurs', 'type' => 'text', 'caption' => 'Kurs', 'required'=>'required','class' => 'myinputtextnumber',

                    'style' => 'width:150px', 'value' => '')]

            ),

            array('field' => 'dk', 'caption' => 'Debet/Kredit', 'elements' => [

                array('field' => 'dk', 'type' => 'select','caption' => 'Debet/Kredit', 'required'=>'required',

                    'style' => 'width:150px', 'value' => '',

                    'options' => $dk),

            ]

            ),

            array('field' => 'jumlah', 'caption' => 'Jumlah', 'elements' => [

                array('field' => 'jumlah', 'type' => 'text',  'caption' => 'Jumlah', 'class' => 'myinputtextnumber',

                     'style' => 'width:150px', 'value' => '')]

            ),

        ];

        $sql = $sqld." where  vp.novp='" . $kode . "'";

        $entry['detailList'] = array(

            'tableHeader' => ['Nomor Akun', 'Mata Uang', 'Kurs', 'Debet/Kredit', 'Jumlah'],

            'rowFields' => ['namaakun', 'matauang', 'kurs', 'dk', 'jumlah'],

            'tableRows' => [],

            'sql' => $sql

        );



        $res = mysql_query($sql);

        while ($row = mysql_fetch_assoc($res)) {

            $entry['detailList']['tableRows'][] = $row;

        }

        echo json_encode($entry);

        break;

    case "list":

        $theTable = '';

        $theTable .= "<fieldset style='clear:left'  id='mainFieldset'>";

        $theTable .= "<legend><b>Voucher Payable</b></legend>";

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

    case "functional":

        $ctl = [];

        $ctl[] = "<div align='center'><img class=delliconBig src=images/" . $_SESSION['theme'] . "/addbig.png title='" . $_SESSION['lang']['new'] . "' onclick=\"ConstructPage.showAdd()\"><br><span align='center'>" . $_SESSION['lang']['new'] . '</span></div>';

        $ctl[] = "<div align='center'><img class=delliconBig src=images/" . $_SESSION['theme'] . "/list.png title='" . $_SESSION['lang']['list'] . "' onclick=\"ConstructPage.defaultList()\"><br><span align='center'>" . $_SESSION['lang']['list'] . '</span></div>';

        $ctl[] = "<fieldset><legend><b>" . $_SESSION['lang']['find'] . "</b></legend>" .

            makeElement('searchNoVP', 'label', 'No Transaksi') .

            makeElement('searchNoVP', 'text', '') . '&nbsp;' .

            makeElement('searchTglVP', 'label', 'Tgl Transaksi') .

            makeElement('searchTglVP', 'date', '') .

            makeElement('searchFind', 'btn', $_SESSION['lang']['find'], ['onclick' => 'ConstructPage.searchTrans()']) .

            "</fieldset>";

        echo "<div align='center'><h3>Voucher Payable</h3></div>";

        echo "<div><table align='center'><tr>";

        foreach ($ctl as $el) {

            echo "<td v-align='middle' style='min-width:100px'>" . $el . '</td>';

        }

        echo '</tr></table></div>';

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

            array('filename' => 'js/keu_vp.js', 'type' => 'js'),

        ]);

        OPEN_BOX('', '', 'menu', 'menu');

        CLOSE_BOX();



        OPEN_BOX('', '', 'list', 'list');

        CLOSE_BOX();

        break;

}



//require_once 'master_validation.php';

//include_once 'lib/eagrolib.php';

//include_once 'lib/zLib.php';

//include_once 'lib/rTable.php';

//echo open_body();

//include 'master_mainMenu.php';

//echo "<script language=javascript src=js/zMaster.js></script> \n<script language=javascript src=js/zSearch.js></script>\n<script language=javascript src='js/zTools.js'></script>\n<script language=javascript1.2 src='js/keu_vp.js'></script>\n<script languange=javascript1.2 src='js/formTable.js'></script>\n<link rel=stylesheet type=text/css href='style/zTable.css'>\n";//$ctl = [];

//$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/addbig.png title='".$_SESSION['lang']['new']."' onclick=\"showAdd()\"><br><span align='center'>".$_SESSION['lang']['new'].'</span></div>';

//$ctl[] = "<div align='center'><img class=delliconBig src=images/".$_SESSION['theme']."/list.png title='".$_SESSION['lang']['list']."' onclick=\"defaultList()\"><br><span align='center'>".$_SESSION['lang']['list'].'</span></div>';

//$ctl[] = '<fieldset><legend><b>'.$_SESSION['lang']['find'].'</b></legend>'.makeElement('sNoTrans', 'label', $_SESSION['lang']['notransaksi']).makeElement('sNoTrans', 'text', '').'&nbsp;'.makeElement('sTanggal', 'label', $_SESSION['lang']['tanggal']).makeElement('sTanggal', 'date', '').makeElement('sFind', 'btn', $_SESSION['lang']['find'], ['onclick' => 'searchTrans()']).'</fieldset>';

//$header = [$_SESSION['lang']['novp'], $_SESSION['lang']['tanggal'], $_SESSION['lang']['nopo'], $_SESSION['lang']['keterangan']];

//$align = explode(',', 'C,C,C,L');

//$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";

//$cols = 'novp,tanggal,nopo,penjelasan,posting';

//$query = selectQuery($dbname, 'keu_vpht', $cols, $where, 'tanggal desc, novp desc', false, 10, 1);

//$data = fetchData($query);

//$totalRow = getTotalRow($dbname, 'keu_vpht', $where);

//$whereAkun = '';

//$whereOrg = '';

//$i = 0;

//foreach ($data as $key => $row) {

//    if (1 === $row['posting']) {

//        $data[$key]['switched'] = true;

//    }

//

//    $data[$key]['tanggal'] = tanggalnormal($row['tanggal']);

//    unset($data[$key]['posting']);

//    ++$i;

//}

//$qPosting = selectQuery($dbname, 'setup_posting', 'jabatan', "kodeaplikasi='keuangan'");

//$tmpPost = fetchData($qPosting);

//$postJabatan = $tmpPost[0]['jabatan'];

//$dataShow = $data;

//$tHeader = new rTable('headTable', 'headTableBody', $header, $data, $dataShow);

//$tHeader->addAction('showEdit', 'Edit', 'images/'.$_SESSION['theme'].'/edit.png');

//$tHeader->addAction('deleteData', 'Delete', 'images/'.$_SESSION['theme'].'/delete.png');

//$tHeader->addAction('postingData', 'Posting', 'images/'.$_SESSION['theme'].'/posting.png');

//$tHeader->_actions[2]->setAltImg('images/'.$_SESSION['theme'].'/posted.png');

//if ($postJabatan !== $_SESSION['empl']['kodejabatan'] && 'HOLDING' !== $_SESSION['empl']['tipelokasitugas']) {

//    $tHeader->_actions[2]->_name = '';

//}

//

//$tHeader->addAction('detailPDF', 'Print Data Detail', 'images/'.$_SESSION['theme'].'/pdf.jpg');

//$tHeader->addAction('zoom', 'Lihat Detail', 'images/'.$_SESSION['theme'].'/zoom.png');

//$tHeader->_actions[3]->addAttr('event');

//$tHeader->_actions[4]->addAttr('event');

//$tHeader->_switchException = ['detailPDF', 'zoom'];

//$tHeader->pageSetting(1, $totalRow, 10);

//$tHeader->setAlign($align);

//OPEN_BOX();

//echo "<div align='center'><h3>".$_SESSION['lang']['vp'].'</h3></div>';

//echo "<div><table align='center'><tr>";

//foreach ($ctl as $el) {

//    echo "<td v-align='middle' style='min-width:100px'>".$el.'</td>';

//}

//echo '</tr></table></div>';

//CLOSE_BOX();

//OPEN_BOX();

//echo "<div id='workField'>";

//$tHeader->renderTable();

//echo '</div>';

//CLOSE_BOX();

//echo close_body();

//

//?>
<?php
require_once 'lib/dompdf/lib/html5lib/Parser.php';
require_once 'lib/dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
require_once 'lib/dompdf/lib/php-svg-lib/src/autoload.php';
require_once 'lib/dompdf/src/Autoloader.php';
//require_once 'lib/constansta.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
// const PP_MAX_APPROVAL=5;
const SHOW_ROW_COUNT=10;
/*
 * General Functions
 */
function echoMessage($title,$message,$die=false){
    echo $title." ".json_encode($message)."<br>";
    if ($die==true) die();
}

function getNow($format="Y-m-d H:i:s",$timezone="Asia/Jakarta"){
	date_default_timezone_set($timezone);   
	return date($format);
}

function sdmJabatanQuery($where=''){
    return 
    "select * from (".
    "select d.*,s.namajabatan,s.alias  from $dbname.datakaryawan d ".
    "inner join $dbname.sdm_5jabatan s on s.kodejabatan=d.kodejabatan ".
    //"where d.lokasitugas='".$_SESSION['empl']['lokasituxgas']."' and s.alias like '%Security%' ". //kodejabatan in (select kodejabatan from ".$dbname.".sdm_5jabatan where alias like '%Security%') order by namakaryawan asc";
    "order by d.namakaryawan) x ".
    ($where!='' ? "where $where ":'');
}
function searcFieldArray($arrays,$fieldtosearch){
    $return=[];
    foreach ($arrays as $array){
        if ($array['field']==$fieldtosearch) {
            $return = $array ;
            break;
        }
    }
    return $return;
}

function echoStyleJS($files ){
    /* $files are array files css and javascript (include it folder)
    *  $files : [array('filename'=>'filename-1,'type'=>(js or css)),array('filename'=>'filename-2,'type'=>(js or css)),..,array('filename'=>'filename-n,'type'=>(js or css))]
    */
    $css_js='';
    foreach ($files as $file) {
        if (strtoupper($file['type'])=='CSS'){
            $css_js.= "<link rel=stylesheet type=text/css href='".$file['filename']."?v=".mt_rand()."'/>\r\n";
        }
        if (strtoupper($file['type'])=='JS'){
            $css_js.= "<script language=javascript src='".$file['filename']."?v=".mt_rand()."'></script>\r\n";
        }
    }
    return $css_js;
}

function getRowCount($sql){
    $total = 0;
    $sql = "select count(*) as total from ($sql) x";
    $res = mysql_query($sql);
   // echoMessage('count sql ',$sql);
    while ($bar = mysql_fetch_assoc($res)) {
        $total=$bar['total'];
    }
    return $total;
}

function checkLastApprovalIndexPP($row){
    $index = 5;//PP_MAX_APPROVAL;
    for ($i=1;$i<=5;$i++){
        if ($row["persetujuan$i"] == '') {
            $index=$i-1;
            break;
        }
    }
    return $index;
}

function generateTablePDF($table,$usecss=true,$papersize="A4",$paperorientataion="landscape'")
{
    ob_start();
    $ob = ob_get_clean();
    $dompdf = new Dompdf();
    $tab=$table;
    if( $usecss) {
        $tab = "<html>
<head>
    <meta http-equiv='Content-Type' content='charset=utf-8' />
	<link rel=stylesheet type='text/css' href='style/genericGray.css'>
	<link rel=stylesheet type='text/css' href='style/zTable.css'>
	</head>
<body><br/><br/><br/><fieldset style='clear:both'>  
			 	 
			 	<div id='printContainer' style='overflow:auto;height:auto;max-width:100%;'> " . $table . "</div> 
			 </fieldset> </body></html>";
    }
    $dompdf->loadHtml($tab);
    $dompdf->setPaper($papersize, $paperorientataion);
    $dompdf->set_option('defaultMediaType', 'all');
    $dompdf->set_option('isFontSubsettingEnabled', true);
    $dompdf->render();
    ob_end_clean();
    $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
    unset($dompdf);
    unset($ob);
}

function generateFilter($arrayFilters ,$isAnd = true)
{
    $size = sizeof($arrayFilters);
    $return = '';
    $filter = '';
    for ($i = 0; $i <= $size - 1; $i++) {
        $filter .= $arrayFilters[$i] . " " . ($i == $size - 1 ? "" : ($isAnd ? " and " : " or "));
        $return = " where " . $filter;
    }
    return $return;
}

function createDialogBox($idcontainer,$idinput,$title,$imagesearchfunction,$dialogfunction){
    //$title = $_SESSION['lang']['findRkn']
    return
        '<img src=images/search.png class=resicon title=\'' .$title . '\' '.
        'onclick="'.$imagesearchfunction.'(\''.$title . '\', '. '\'<fieldset>'.
        '<legend>'. $_SESSION['lang']['find'] . '</legend>' . $_SESSION['lang']['find'] . '&nbsp; '.
        '<input type=text class=myinputtext id='.$idinput.'><button class=mybutton onclick='.$dialogfunction.'()>' .
        $_SESSION['lang']['find'] . '</button></fieldset>'.
        '<div id='.$idcontainer.' style=overflow=auto;height=380;width=485></div>\',event);">';
}

function executeQuery2($query){
    $return =mysql_query($query); 
    if (!$return){
        echo "DB Error : \r\n".
           "Your query : ".$query."\r\n\r\n".
           "Error Message :" .mysql_error();
    }
    return $return;
}
function executeQuery($query){
    $return =mysql_query($query);
//    if (!$return){
//        echo "DB Error : \r\n".
//            "Your query : ".$query."\r\n\r\n".
//            "Error Message :" .mysql_error();
//    }
//    echoMessage('return ',$return);
    return $return;
}

function getErrorDB($query){
    return  "DB Error : \r\n".
        "Your query : ".$query."\r\n\r\n".
        "Error Message :" .mysql_error();
}

/*
 * $column_values should contain :
 * [{'columnname-1':'columnvalue-1'},..,{'columnname-2':'columnvalue-2'}]
 */
function insertRow($tablename,$column_values,$justgetquery=false)
{
    $insert = "";
    $columns = "(";
    $values = "(";
    for ($i = 0; $i < count($column_values); $i++) {
        foreach ($column_values[$i] as $key => $value) {
            $columns .= $key . ($i == count($column_values) - 1 ? "" : ",");
             if (is_string($value)) {
                $values .= "'$value'" . ($i == count($column_values) - 1 ? "" : ",");
            } else { 
                $values .= ($value==null?"null":"$value") . ($i == count($column_values) - 1 ? "" : ",");
            }
        }
    }
    $columns .= ")";
    $values .= ")";
    $insert = " insert into $tablename $columns values $values ";
//    echoMessage("insert ",$insert);
    if ($justgetquery) {
        return $insert;
    } else {
        return executeQuery($insert);
    }
}

function updateRow($tablename,$column_values,$where,$justgetquery=false)
{
    $update = '';
    $columnvalue = '';
    for ($i = 0; $i < count($column_values); $i++) {
        foreach ($column_values[$i] as $key => $value) {
            if (is_string($value)) {
                $columnvalue .= "$key='$value'" . ($i == count($column_values) - 1 ? "" : ", ");
            } else {
                $columnvalue .= ($value==null?"$key=null":"$key=$value") . ($i == count($column_values) - 1 ? "" : ", ");
            } 
        }
    }
    $update = " update $tablename set $columnvalue where $where "; 
//    echoMessage("update ",$update);
     if ($justgetquery) {
        return $update;
    } else {
        return executeQuery($update);
    }
}
/*
 * db transaction use for transaction db
 * when all transaction successed then data saved to database
 * parameter :
 * - $dbTransacationCallback : callback function that contain CRUD transaction
 */
function dbTransaction(callable $dbTransacationCallback){
    $return = [];
    mysql_query("START TRANSACTION");
    if ($dbTransacationCallback!=''){
        $return = $dbTransacationCallback();
        if ($return['success']){
            mysql_query("COMMIT");
        } else {
            mysql_query("ROLLBACK");
        }
    }
    return $return;
}

function getRows($query){
    $result = [];
    $result2 = [];
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
        $result[]=$row;
    }
	$result2=$result;
    if (count($result)==1) {$result2=$result[0];}
    return $result2;
}

function getOptionFromRows($query,$optionvalue,$optioncaption,$allownull=false){
    $result = [];
    if ($allownull){
        $result[]=array(''=>'Pilih data');
    }
    $res = mysql_query($query);
    while ($row = mysql_fetch_assoc($res)) {
        $result[]=array($row[$optionvalue]=>$row[$optioncaption]);
    }
    return $result;
}

function eventOnScrollDB($query,&$definedVar,callable $callback){
    /* this function purpose to loop all data from the result executed query
     * parameters :
     * - $query      : query to execute
     * - $definedVar : default variable provided from user when this function called
     * - $callback   : callback function has parameters :
     *                 - $row        : mysql_fetch data from executed query
     *                 - $funcVar    : variable which processed then passed to callback
     *                 - $definedVar : user defined which included in the process
     */
    $var =array(
        "linenumber"=>0,
        "variable1"=>'',
        "variable2"=>'',
    );
    if ($query!='' && $callback !='' ) {
        $res = mysql_query($query);
        while ($row = mysql_fetch_assoc($res)) {
            if ($callback != '') { 
                $var['linenumber'] += 1;
                $callback($row,$var,$definedVar);
            }
        }
        mysql_free_result($res);
    }
    return null;
}

function get1DataFromQuery($query,$fieldcolumn){
    /* this function purpose to get data only 1 row from executed query
     * parameters :
     * - $query       : query to execute
     * - $fieldcolumn :
     *   - set value '*' to get data from all columns
     *   - set field name to get date from given name
     */
    $return = [];
    if ($query!='') {
        $res = mysql_query($query);
        while ($bar = mysql_fetch_assoc($res)) {
            foreach ($bar as $key => $value) {
                if ($fieldcolumn != '*') {
                    if ($fieldcolumn == $key) $return[$key] = $value;
                } else {
                    $return[$key] = $value;
                }
            }
        }
        mysql_free_result($res);
    }
    return $return;
}

function makeOption2($query,$valueandcaptioninit,$valueandcaptionfield,callable $callback=null,$showkey=false){
    /*
     * $valueandcaptioninit diisi dengan
     * array("valueinit"=>valueinit,"captioninit"=>captioninit);
     *
     * $valueandcaption diisi dengan field yang akan diambil dari database untuk value dan caption option
     * array("valuefield"=>valuefieldfromdb,"captionfield"=>captionfieldfromdb);
     *
     * &callback ini dipergunakan jika valu dan caption option ditampilkan setelah proses terlebih dahulu
     * &callback mempunyai parameter ($option,$value,$caption);
     * parameter :
     * - $option  : parameter ini akan di hardcore value nya, yaitu : init atau noninit
     * - $value   : parameter value yang melalui sebuah proses sebelum ditampilkan
     * = $caption : parameter caption yang melalui sebuah proses sebelum ditampilkan
     * return yang dihasilkan harus seperti berikut :
     * array("newvalue"=>value,"newcaption"=>caption);
     */
    $val22 = '';
    $capt22='';
    $ret='';
    if (!empty($valueandcaptioninit)) {
        $val22 =  $valueandcaptioninit['valueinit'];
        $capt22 =  $valueandcaptioninit['captioninit'];
        if ($callback!=null){
            $ret2 = $callback("init",$val22,$capt22);
            $val22 = $ret2['newvalue'];
            $capt22 = $ret2['newcaption'];
        }
        $ret .= '<option value=\'' . $val22 . '\'>' . ($showkey==true? $val22.' - ' : '').$capt22 . '</option>';
    }
    if ($query!='') {
        $res = mysql_query($query);
        while ($bar = mysql_fetch_assoc($res)) {
            $val22 = $bar[$valueandcaptionfield['valuefield']];
            $capt22 = $bar[$valueandcaptionfield['captionfield']];
            if ($callback != null) {
                $ret2 = $callback("noninit", $val22, $capt22);
                $val22 = $ret2['newvalue'];
                $capt22 = $ret2['newcaption'];
            }
            $ret .= '<option value=\'' . $val22 . '\'>' . ($showkey==true? $val22.' - ' : '').$capt22 . '</option>';
        }
        mysql_free_result($res);
    } else {
        $val22 =  $valueandcaptioninit['valueinit'];
        $capt22 =  $valueandcaptioninit['captioninit'];
        $ret .= '<option value=\'' . $val22 . '\'>' . ($showkey==true? $val22.' - ' : '').$capt22 . '</option>';
    }
    return $ret;
}

/*
 * Modul Pernolia
 */

function getQuery($forquery)
{
    $str = "";
    switch ($forquery) {
        case "kontraktor":
            $str = "select * from log_5supplier l1 ".
                "inner join log_5klsupplier l2 on l1.kodekelompok=l2.kode ".
                "where l2.tipe='KONTRAKTOR' ".
                "order by l1.namasupplier";
            break;
        case "filterpo":
            $str = " ( RIGHT(nopo,3) IN ".
                "( ".
                "SELECT kodeorganisasi ".
                "FROM datakaryawan ".
                "WHERE karyawanid IN ( ".
                "SELECT distinct purchaser FROM log_prapodt) AND kodeorganisasi= ".
                "( ".
                "SELECT kodeorganisasi FROM datakaryawan d ".
                "INNER JOIN user u ON u.karyawanid=d.karyawanid ".
                "WHERE u.namauser='".$_SESSION['standard']['username'] ."'".
                ") ".
                "))";
            break;
        case "po":
            $kodeorg=substr($_SESSION['empl']['lokasitugas'], 0,3);
            $str = "SELECT DISTINCT nopo, namasupplier FROM log_po_vw WHERE kodeorg like '%".$kodeorg."%' ";
        
        /*    $str = "SELECT DISTINCT nopo, namasupplier FROM log_po_vw ".
                "WHERE RIGHT(nopo,3) IN ".
                "( ".
                "SELECT kodeorganisasi ".
                "FROM datakaryawan ".
                "WHERE karyawanid IN ( ".
                "SELECT distinct purchaser FROM log_prapodt) AND kodeorganisasi= ".
                "( ".
                "SELECT kodeorganisasi FROM datakaryawan d ".
                "INNER JOIN user u ON u.karyawanid=d.karyawanid ".
                "WHERE u.namauser='".$_SESSION['standard']['username'] ."'".
                ") ".
                ")";
        */
            break;
        case "pp":
            if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
                $str = "select * from    log_prapoht  " .
                    "where right(nopp,4) in (".
                    "SELECT " .
                    "o.kodeorganisasi " .
                    "FROM  organisasi o    " .
                    "WHERE o.induk = '" . $_SESSION['empl']['kodeorganisasi'] . "' )";
            } else {
                $str = "select * from    log_prapoht  " .
                    "where right(nopp,4) in (".
                    "SELECT   o.kodeorganisasi  " .
                    "FROM datakaryawan d " .
                    "INNER JOIN user u on u.karyawanid=d.karyawanid " .
                    "INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi " .
                    "WHERE u.namauser= '" . $_SESSION['standard']['username'] . "' )";
            }
            break;
        case "supplier":
            $str="select * from ( ".
                "select DISTINCT b.* from log_poht a ".
                " left join log_5supplier b on a.kodesupplier=b.supplierid ".
                " WHERE RIGHT(a.nopo,3) in  ".
                " ( ".
                "  SELECT d.kodeorganisasi FROM datakaryawan d ".
                "INNER join user u ON u.karyawanid=d.karyawanid ".
                 "WHERE u.namauser='".$_SESSION['standard']['username'] ."'".
                ")) x ".
                " WHERE NOT (supplierid IS NULL) ".
                "order by namasupplier ASC";
            break;
        case "purchaser":
            $str = "SELECT * ".
                "FROM datakaryawan ".
                "WHERE karyawanid IN ( ".
                "SELECT distinct purchaser FROM log_prapodt) AND kodeorganisasi= ".
                "( ".
                "SELECT kodeorganisasi FROM datakaryawan d ".
                "INNER JOIN user u ON u.karyawanid=d.karyawanid ".
                "WHERE u.namauser='" .$_SESSION['standard']['username'] ."'".
                ")";
            break;
        case "unitkebun":
            $str = "SELECT o.kodeorganisasi,o.namaorganisasi from  setup_blok s
                    INNER JOIN  organisasi o ON o.kodeorganisasi=s.kodeorg
                    where s.statusblok='BBT' ";
//            if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
                $str.= " AND LEFT(s.kodeorg,4) IN (SELECT kodeorganisasi FROM organisasi WHERE induk = '".$_SESSION['empl']['induklokasitugas']."' )";
//            } else {
//                $str .= "and s.kodeorg like '" . $_SESSION['empl']['lokasitugas'] . "%'
//                    order by kodeorg ASC";
//            }
            $str .= " order by kodeorg ASC";
            break;
        case "pt":
        case "unit":
            $str="select * from organisasi where tipe='PT' and kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
            break;
        case "lokasitugas":
            if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
                $str = " SELECT " .
                    "o.kodeorganisasi,o.namaorganisasi,o.induk " .
                    "FROM  organisasi o    " .
                    "WHERE o.induk = '" . $_SESSION['empl']['kodeorganisasi'] . "'";
            } else {
                $str = "SELECT 1 as level,d.karyawanid, d.namakaryawan, " .
                    "o.kodeorganisasi,o.namaorganisasi,o.induk " .
                    "FROM datakaryawan d " .
                    "INNER JOIN user u on u.karyawanid=d.karyawanid " .
                    "INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi " .
                    "WHERE u.namauser= '" . $_SESSION['standard']['username'] . "'";
            }
            break;
        case "gudang":
            if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
                $str="SELECT kodeorganisasi,namaorganisasi FROM organisasi ".
                "WHERE tipe LIKE 'GUDANG%' ".
                "AND LEFT(kodeorganisasi,4) in ".
                "( ".
                "SELECT ".
                "o.kodeorganisasi ".
                "FROM  organisasi o  ".
                "WHERE o.induk in ( ".
                "SELECT o.kodeorganisasi ".
                "FROM datakaryawan d ".
                "INNER JOIN user u on u.karyawanid=d.karyawanid ".
                "INNER JOIN organisasi o ON d.kodeorganisasi=o.kodeorganisasi ".
                "WHERE u.namauser='" .$_SESSION['standard']['username'] ."' ".
                ")) ";
            } else {
                $str = "SELECT kodeorganisasi,namaorganisasi FROM organisasi " .
                    "WHERE tipe LIKE 'GUDANG%' " .
                    "AND LEFT(kodeorganisasi,4) in " .
                    "( " .
//                "SELECT " .
//                "o.kodeorganisasi " .
//                "FROM  organisasi o  " .
//                "WHERE o.induk in ( " .
                    "SELECT d.lokasitugas " .
                    "FROM datakaryawan d " .
                    "INNER JOIN user u on u.karyawanid=d.karyawanid " .
                    "INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi " .
                    "WHERE u.namauser='" . $_SESSION['standard']['username'] . "' " .
                    ")";//") ";
            }
//            $str = "SELECT kodeorganisasi,namaorganisasi FROM organisasi " .
//                "WHERE tipe LIKE 'GUDANG%' " .
//                "AND LEFT(kodeorganisasi,4) = left(  " .
//                "( " .
//                "SELECT " .
//                "o.kodeorganisasi " .
//                "FROM  organisasi o  " .
//                "WHERE o.induk in ( " .
//                "SELECT o.kodeorganisasi " .
//                "FROM datakaryawan d " .
//                "INNER JOIN user u on u.karyawanid=d.karyawanid " .
//                "INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi " .
//                "WHERE u.namauser='" . $_SESSION['standard']['username'] . "' " .
//                ")),4) ";
//            $str="select * from organisasi where tipe='GUDANG' and left(kodeorganisasi,4)='".$_SESSION['empl']['lokasitugas']."'";


            break;
    }
    return $str;
}

/*
 * Modul Keuangan
 */

function getPPhOptions(){
    return
        array('' => '[Pilih Pph]','pph21Final' => 'PPh 21 Final', 'pph22' => 'PPh 22', 'pph23' => 'PPh 23', 'pph15' => 'PPh 15', 'pph4(2)' => 'PPh 4 (2)');
}

function getPPnOptions(){
    $optPpn=array(''=>'[Pilih PPn]');
    eventOnScrollDB("SELECT DISTINCT * FROM Keu_5akun WHERE namaakun LIKE '%ppn%'",
        $optPpn,function($row,$var,&$definedVar){
//			array_merge($definedVar,array($row['noakun']=>$row['namaakun']));
            array_push($definedVar[$row['noakun']]=$row['noakun']."-".$row['namaakun']);
        });
    return $optPpn;
}
?>
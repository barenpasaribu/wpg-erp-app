<?php
require_once 'config/connection.php';
require_once 'lib/detailSession.php';
/*
 * General Functions
 */
function echoMessage($title,$message,$die=false){
    echo $title." ".json_encode($message)."<br>";
    if ($die==true) die();
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

function makeOption2($query,$valueandcaptioninit,$valueandcaptionfield,callable $callback=null){
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
        $ret = '<option value=\''.$val22.'\'>' .$capt22 .'</option>';
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
            $ret .= '<option value=\'' . $val22 . '\'>' . $capt22 . '</option>';
        }
        mysql_free_result($res);
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
function setSession()
{
    /* this function was purposed only used to set session empl,org
     * used ini Personalia
     */
    $str2 = "SELECT u.namauser,d.namakaryawan,d.lokasitugas,o.kodeorganisasi,o.namaorganisasi,o.tipe ,o.induk " .
        "FROM user u " .
        "INNER JOIN datakaryawan d ON d.karyawanid=u.karyawanid " .
        "INNER JOIN organisasi o ON o.kodeorganisasi=d.lokasitugas " .
        "WHERE u.namauser='" . $_SESSION['standard']['username'] . "'";
    $tipelokasitugas = get1DataFromQuery($str2, '*');
    $_SESSION['empl']['tipelokasitugas'] = $tipelokasitugas['tipe'];
    $_SESSION['org']['induk'] = $tipelokasitugas['induk'];
    $_SESSION['empl']['lokasitugas'] = $tipelokasitugas['lokasitugas'];
    $_SESSION['empl']['namalokasitugas'] = $tipelokasitugas['namaorganisasi'];
//    $str2 = "SELECT * FROM setup_periodeakuntansi " .
//        "WHERE kodeorg='" . $tipelokasitugas['lokasitugas'] . "' AND tutupbuku=0 and " .
//        "periode = (SELECT MAX(periode) FROM setup_periodeakuntansi)";
//    $org = get1DataFromQuery($str2, '*');
//    $_SESSION['org']['period']['start'] = $org['tanggalmulai'];
//    $_SESSION['org']['period']['end'] = $org['tanggalsampai'];
//    $_SESSION['org']['period']['bulan'] = substr($org['periode'],0,4);
//    $_SESSION['org']['period']['tahun'] = substr($org['periode'],5,2);


//    setEmplSession($conn, $_SESSION['standard']['userid'], $dbname);
//    getPrivillageType($conn, $dbname);
//    getPrivillages($conn, $_SESSION['standard']['username'], $dbname);
//    setEmployer($conn, $dbname);
    return null;
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
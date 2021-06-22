<?php
include_once 'lib/devLibrary.php';
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';

$param=$_POST;
if (sizeof($param)==0)  $param=$_GET;
$proses = $_GET['proses']; 
$code = empty($param['code']) ? '': $param['code'];
$parent = empty($param['parent']) ? '': $param['parent'];

switch ($proses) {
    case "saveheader":
        $data = [];
        $code = $param['kodeorganisasi'];
        $data[] = array('namaorganisasi' => $param['namaorganisasi']);
        $data[] = array('kodeorganisasi' => $param['kodeorganisasi']);
        $data[] = array('induk' => $param['induk']);
        $data[] = array('tipe' => $param['tipe']);
        $data[] = array('detail' => $param['detail']);
        $data[] = array('alamat' => $param['alamat']);
        $data[] = array('wilayahkota' => $param['wilayahkota']);
        $data[] = array('telepon' => $param['telepon']);
        $data[] = array('email' => $param['email']);
        $data[] = array('negara' => $param['negara']);
        $data[] = array('kodepos' => $param['kodepos']);
        $data[] = array('noakun' => $param['noakun']);
        $data[] = array('alokasi' => $param['alokasi']);
        $data[] = array('lastuser' => $_SESSION['standard']['userid']);

        $result = dbTransaction(function () {
            global $data;
            global $code;
            $echo = array('success' => false, 'message' => '', 'data' => array());
            $row = getRows("select * from organisasi where kodeorganisasi='$code' ");
            if (count($row) == 0) {
                if (insertRow('organisasi', $data)) {
                    $echo['success'] = true;
                    $echo['data'] = getRows("select * from organisasi where kodeorganisasi='$code' ");;
                } else {
                    $echo['success'] = false;
                    $echo['message'] = getErrorDB(insertRow('organisasi', $data, true));
                }
            } else {
                if (updateRow('organisasi', $data, " kodeorganisasi='$code'")) {
                    $echo['success'] = true;
                    $echo['data'] = $row;
                } else {
                    $echo['success'] = false;
                    $echo['message'] = getErrorDB(updateRow('organisasi', $data, " kodeorganisasi='$code'", true));
                }
            }
            return $echo;
        });
        echo json_encode($result);
        break;
    case "checkcode":
        $sql = "select * from organisasi where kodeorganisasi='$code'";
        $row = getRowCount($sql);
        $data = array('codeExist' => $row != 0, 'sql' => $sql);
        echo json_encode($data);
        break;
    case "entry":
    case "edit":
        $entry = array('header' => array(), 'detail' => array(), 'detailList' => array());
        $headerFields = [];

        $types = array('currentValue' => '',
            getOptionFromRows("select distinct tipe from organisasi order by tipe", "tipe", "tipe")
        );
        $allocations = array('currentValue' => '',
            getOptionFromRows("select * from organisasi where tipe='PT'", "kodeorganisasi", "namaorganisasi", true)
        );

        $accounts = array('currentValue' => '',
            getOptionFromRows("select noakun,namaakun from keu_5akun where detail=1 order by noakun", "noakun", "namaakun", true)
        );
         $sqlhcek = "select * from organisasi where induk='".$parent."' and kodeorganisasi='".$code."'";
          $querysqlhcek = mysql_query($sqlhcek) ;
        while ($tipe = mysql_fetch_object($querysqlhcek)) {
            $detail = $tipe->detail;
        }
        if($detail=='1'){
            $entry['header']=[];
        $entry['header']['inputs'] = [
            array('field' => 'induk', 'caption' => 'Induk', 'elements' => [
                array('field' => 'induk', 'type' => 'text', 'caption' => 'Induk', 'class' => 'myinputtext',
                    'style' => 'width:80px', 'maxlength' => 25, 'disabled' => 'disabled', 'value' => '')
            ]),
            array('field' => 'kodeorganisasi', 'caption' => 'Kode Organisasi', 'elements' => [
                array('field' => 'kodeorganisasi', 'type' => 'text', 'caption' => 'Kode Organisasi', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:80px', 'value' => '',
                )]
            ),
            array('field' => 'namaorganisasi', 'caption' => 'Nama Organisasi', 'elements' => [
                array('field' => 'namaorganisasi', 'type' => 'text', 'caption' => 'Nama Organisasi', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:250px', 'value' => '')]
            ),
            array('field' => 'tipe', 'caption' => 'Tipe', 'elements' => [
                array('field' => 'tipe', 'type' => 'select', 'caption' => 'Tipe', 'required' => 'required',
                    'value' => '', 'style' => 'width: 250px',
                    'options' => $types),]
            ),
            array('field' => 'alamat', 'caption' => 'Alamat', 'elements' => [
                array('field' => 'alamat', 'required' => 'required', 'caption' => 'Alamat', 'type' => 'textarea',
                    'value' => '', 'style' => 'height: 50px; width: 230px',
                ),]
            ),
            array('field' => 'wilayahkota', 'caption' => 'Wilayah Kota', 'elements' => [
                array('field' => 'wilayahkota', 'type' => 'text', 'caption' => 'Wilayah Kota', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:150px', 'value' => '')]
            ),
            array('field' => 'telepon', 'caption' => 'Telepon', 'elements' => [
                array('field' => 'telepon', 'type' => 'text', 'caption' => 'Telepon', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:150px', 'value' => '')]
            ),
            array('field' => 'email', 'caption' => 'Email', 'elements' => [
                array('field' => 'email', 'type' => 'text', 'caption' => 'Email', 'class' => 'myinputtext',
                    'style' => 'width:150px', 'value' => '')]
            ),
            array('field' => 'negara', 'caption' => 'Negara', 'elements' => [
                array('field' => 'negara', 'type' => 'text', 'caption' => 'Negara', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:70px', 'value' => '')]
            ),
            array('field' => 'kodepos', 'caption' => 'Kode Pos', 'elements' => [
                array('field' => 'kodepos', 'type' => 'text', 'caption' => 'Kode Pos', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:70px', 'value' => '')]
            ),
            array('field' => 'alokasi', 'caption' => 'Alokasi', 'elements' => [
                array('field' => 'alokasi', 'type' => 'select', 'caption' => 'Alokasi',
                    'value' => '', 'style' => 'width: 250px', 'showOptionValue' => true,
                    'options' => $allocations),]
            ),
            array('field' => 'noakun', 'caption' => 'No Akun', 'elements' => [
                array('field' => 'noakun', 'type' => 'select', 'caption' => 'No Akun',
                    'value' => '', 'style' => 'width: 250px', 'showOptionValue' => true,
                    'options' => $accounts),]
            ),
            array('field' => 'detail', 'caption' => 'Detail', 'elements' => [
                array('field' => 'detail', 'type' => 'checkbox', 'value' => '','checked' =>'checked')]
            )
        ];

        }else{
            $entry['header']=[];
        $entry['header']['inputs'] = [
            array('field' => 'induk', 'caption' => 'Induk', 'elements' => [
                array('field' => 'induk', 'type' => 'text', 'caption' => 'Induk', 'class' => 'myinputtext',
                    'style' => 'width:80px', 'maxlength' => 25, 'disabled' => 'disabled', 'value' => '')
            ]),
            array('field' => 'kodeorganisasi', 'caption' => 'Kode Organisasi', 'elements' => [
                array('field' => 'kodeorganisasi', 'type' => 'text', 'caption' => 'Kode Organisasi', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:80px', 'value' => '',
                )]
            ),
            array('field' => 'namaorganisasi', 'caption' => 'Nama Organisasi', 'elements' => [
                array('field' => 'namaorganisasi', 'type' => 'text', 'caption' => 'Nama Organisasi', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:250px', 'value' => '')]
            ),
            array('field' => 'tipe', 'caption' => 'Tipe', 'elements' => [
                array('field' => 'tipe', 'type' => 'select', 'caption' => 'Tipe', 'required' => 'required',
                    'value' => '', 'style' => 'width: 250px',
                    'options' => $types),]
            ),
            array('field' => 'alamat', 'caption' => 'Alamat', 'elements' => [
                array('field' => 'alamat', 'required' => 'required', 'caption' => 'Alamat', 'type' => 'textarea',
                    'value' => '', 'style' => 'height: 50px; width: 230px',
                ),]
            ),
            array('field' => 'wilayahkota', 'caption' => 'Wilayah Kota', 'elements' => [
                array('field' => 'wilayahkota', 'type' => 'text', 'caption' => 'Wilayah Kota', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:150px', 'value' => '')]
            ),
            array('field' => 'telepon', 'caption' => 'Telepon', 'elements' => [
                array('field' => 'telepon', 'type' => 'text', 'caption' => 'Telepon', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:150px', 'value' => '')]
            ),
            array('field' => 'email', 'caption' => 'Email', 'elements' => [
                array('field' => 'email', 'type' => 'text', 'caption' => 'Email', 'class' => 'myinputtext',
                    'style' => 'width:150px', 'value' => '')]
            ),
            array('field' => 'negara', 'caption' => 'Negara', 'elements' => [
                array('field' => 'negara', 'type' => 'text', 'caption' => 'Negara', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:70px', 'value' => '')]
            ),
            array('field' => 'kodepos', 'caption' => 'Kode Pos', 'elements' => [
                array('field' => 'kodepos', 'type' => 'text', 'caption' => 'Kode Pos', 'class' => 'myinputtext',
                    'required' => 'required', 'style' => 'width:70px', 'value' => '')]
            ),
            array('field' => 'alokasi', 'caption' => 'Alokasi', 'elements' => [
                array('field' => 'alokasi', 'type' => 'select', 'caption' => 'Alokasi',
                    'value' => '', 'style' => 'width: 250px', 'showOptionValue' => true,
                    'options' => $allocations),]
            ),
            array('field' => 'noakun', 'caption' => 'No Akun', 'elements' => [
                array('field' => 'noakun', 'type' => 'select', 'caption' => 'No Akun',
                    'value' => '', 'style' => 'width: 250px', 'showOptionValue' => true,
                    'options' => $accounts),]
            ),
            array('field' => 'detail', 'caption' => 'Detail', 'elements' => [
                array('field' => 'detail', 'type' => 'checkbox', 'value' => '')]
            )
        ];
        }
	
        if ($code != '') {
            $el = &$entry['header']['inputs'][1]['elements'][0];
            $el['disabled'] = 'disabled';
        }

        $sqlh = "select * from organisasi where induk='$parent' and kodeorganisasi='$code'";
	$row = getRows($sqlh);
        if (count($row) == 0) {
            $row = array('induk' => $parent);
        }
        $entry['header']['sql'] = $sqlh;
        $entry['header']['data'] = $row; 
        echo json_encode($entry);
        break;
    case "listrow":
        $sql1 = "select * from $dbname.organisasi where induk='$parent'";
        $totalRows = getRowCount($sql1);
        $res = mysql_query($sql1);
        $row = array('datas' => array(), 'totalrows' => 0, 'parent' => $parent);
        while ($bar = mysql_fetch_assoc($res)) {
            $row['datas'][] = $bar;
        }
        $row['totalrows'] = $totalRows;
        $row['sql'] = $sql1;
        echo json_encode($row);
        break;
    case "init":
        echo open_body();
        include('master_mainMenu.php');
        echo echoStyleJS([
            array('filename' => 'style/orgchart.css', 'type' => 'css'),
            array('filename' => 'js/menusetting.js', 'type' => 'js'),
            array('filename' => 'js/generic.js', 'type' => 'js'),
            array('filename' => 'js/zTools.js', 'type' => 'js'),
            array('filename' => 'js/devLibrary.js', 'type' => 'js'),
            array('filename' => 'js/orgChart.js', 'type' => 'js'),
        ]);
        OPEN_BOX();
        echo OPEN_THEME($_SESSION['lang']['orgchartcap'] . ':');
        echo "<div class=maincontent>
              <fieldset class=legend><legend>" . "" . ":</legend>
              " . $_SESSION['lang']['orgremark'] . "
              </fildset>";
        echo "<ul id='org_'>";
        echo "</ul>";
        echo "</div>";

        echo CLOSE_THEME();
        echo close_body();
        break;
}
//exit();
//require_once('master_validation.php');
//include 'lib/eagrolib.php';
//include 'lib/devLibrary.php';
//echo open_body();

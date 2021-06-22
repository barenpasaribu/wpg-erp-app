<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
$afdeling = $_POST['afdeling'];
$kebun = $_POST['kebun'];
$where1 = "(tipe='BLOK' or tipe='BIBITAN') and induk='$afdeling'";
$query = selectQuery($dbname, 'organisasi', 'kodeorganisasi', $where1);
$data = fetchData($query);
$where2 = array();

foreach ($data as $key => $row) {
	$where2[] = array('kodeorg' => $row['kodeorganisasi']);
}
if (count($where2) < 1) {
	exit('Error:Tidak ada data');
}

$master = "
                <fieldset>
                    <legend><b>Susunan Data : setup_blok</b></legend> <img src='images/pdf.jpg' title='PDF Format' style='width:20px;height:20px;cursor:pointer' >&nbsp;<img src='images/printer.png' title='Print Page' style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>
                    <div style='height:170px;overflow:auto'>
                        <table id='masterTable' class='sortable' cellspacing='1' border='0'>
                            <thead>
                                <tr class='rowheader'>
                                    <td align='left'>Kode Organisasi</td>
                                    <td align='left'>Blok Lama</td>
                                    <td align='left'>Tahun Tanam</td>
                                    <td align='left'>Luas Planted</td>
                                    <td align='left'>Luas Unplanted</td>
                                    <td align='left'>Jumlah Pokok Mati</td>
                                    <td align='left'>Jumlah Pokok</td>
                                    <td align='left'>Jumlah Pokok Total</td>
                                    <td align='left'>Status Blok</td>
                                    <td colspan='2' align='left'>Mulai Panen</td>
                                    <td align='left'>Kode Tanah</td>
                                    <td align='left'>Klasifikasi Tanah</td>
                                    <td align='left'>Topografi</td>
                                    <td align='left'>Inti Plasma</td>
                                    <td align='left'>Jenis Bibit</td>
                                    <td align='left'>Tanggal</td>
                                    <td align='left'>Cadangan</td>
                                    <td align='left'>Okupasi</td>
                                    <td align='left'>Rendahan</td>
                                    <td align='left'>Sungai</td>
                                    <td align='left'>Rumah</td>
                                    <td align='left'>Kantor</td>
                                    <td align='left'>Pabrik</td>
                                    <td align='left'>Jalan</td>
                                    <td align='left'>Kolam</td>
                                    <td align='left'>Umum</td>
                                    <td colspan='2'>Aksi</td>
                                </tr>
                            </thead>
                            <tbody id='mTabBody'>

";

$query = "select * from setup_blok where kodeorg in (select kodeorganisasi from organisasi where $where1) order by kodeorg,tahuntanam";
$res = mysql_query($query);
$index=0;
while ($bar = mysql_fetch_assoc($res)) { 
	$data = str_replace('"','\'', json_encode($bar));
	$master .= "<tr class='rowcontent'>";
	$master .= "<td>".$bar['kodeorg']."</td>".
				 "<td>&nbsp;".$bar['bloklama']."&nbsp;</td>".
				 "<td>&nbsp;".$bar['tahuntanam']."</td>".
				 "<td align='right'>&nbsp;".number_format($bar['luasareaproduktif'],2)."&nbsp;</td>".
				 "<td align='right'>&nbsp;".number_format($bar['luasareanonproduktif'],2)."&nbsp;</td>".
				 "<td align='right'>&nbsp;".number_format($bar['jumlahpokokmati'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['jumlahpokok'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format((($bar['jumlahpokok'])-($bar['jumlahpokokmati'])),2)."&nbsp;</td>". 
				 "<td>&nbsp;".$bar['statusblok']."&nbsp;</td>". 
				 "<td>&nbsp;".$bar['bulanmulaipanen']."&nbsp;</td>". 
				 "<td>&nbsp;".$bar['tahunmulaipanen']."&nbsp;</td>". 
				 "<td>&nbsp;".$bar['kodetanah']."&nbsp;</td>". 
				 "<td>&nbsp;".$bar['klasifikasitanah']."&nbsp;</td>". 
				 "<td>&nbsp;".$bar['topografi']."&nbsp;</td>". 
				 "<td>&nbsp;".$bar['intiplasma']."&nbsp;</td>". 
				 "<td>&nbsp;".$bar['jenisbibit']."&nbsp;</td>". 
				 "<td>&nbsp;".date_format($bar['tanggalpengakuan'],'d-m-Y')."&nbsp;</td>". 
				 "<td>&nbsp;".number_format($bar['cadangan'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['okupasi'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['rendahan'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['sungai'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['rumah'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['kantor'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['pabrik'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['jalan'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['kolam'],2)."&nbsp;</td>". 
				 "<td align='right'>&nbsp;".number_format($bar['umum'],2)."&nbsp;</td>".
				 "<td><img title='Edit' onclick=\"var data=".$data."; editBlok(data);\" class='zImgBtn' src='images/001_45.png'></td>".
				 "<td><img title='Hapus' onclick=\"var data=".$data."; delBlok(data,'".$kebun."','".$afdeling."',".$index.");\" class='zImgBtn' src='images/delete_32.png'></td>";
	$index++;
	$master .= "</tr>";
}

$master .= "</tbody></table>";
// $where2['sep'] = 'OR';
// $fieldStr = '##kodeorg##bloklama##tahuntanam##luasareaproduktif##luasareanonproduktif' .
// 	'##jumlahpokok##statusblok##bulanmulaipanen##tahunmulaipanen##kodetanah' .
// 	'##klasifikasitanah##topografi##intiplasma##jenisbibit##tanggalpengakuan' .
// 	'##cadangan##okupasi##rendahan##sungai##rumah##kantor##pabrik##jalan##kolam##umum'.
// 	'##jumlahpokoksisipan1##tahunjumlahpokoksisipan1##jumlahpokoksisipan2##tahunjumlahpokoksisipan2##jumlahpokoksisipan3##tahunjumlahpokoksisipan3##jumlahpokokabnormal';

// $fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
// $head = array();
// $head[0]['name'] = $_SESSION['lang']['kodeorg'];
// $head[1]['name'] = $_SESSION['lang']['bloklama'];
// $head[2]['name'] = $_SESSION['lang']['tahuntanam'];
// $head[3]['name'] = $_SESSION['lang']['luasareaproduktif'];
// $head[4]['name'] = $_SESSION['lang']['luasareanonproduktif'];
// $head[5]['name'] = $_SESSION['lang']['jumlahpokok'];
// $head[6]['name'] = $_SESSION['lang']['statusblok'];
// $head[7]['name'] = $_SESSION['lang']['mulaipanen'];
// $head[8]['name'] = $_SESSION['lang']['kodetanah'];
// $head[9]['name'] = $_SESSION['lang']['klasifikasitanah'];
// $head[10]['name'] = $_SESSION['lang']['topografi'];
// $head[11]['name'] = $_SESSION['lang']['intiplasma'];
// $head[12]['name'] = $_SESSION['lang']['jenisbibit'];
// $head[13]['name'] = $_SESSION['lang']['tanggal'];
// $head[14]['name'] = $_SESSION['lang']['cadangan'];
// $head[15]['name'] = $_SESSION['lang']['okupasi'];
// $head[16]['name'] = $_SESSION['lang']['rendahan'];
// $head[17]['name'] = $_SESSION['lang']['sungai'];
// $head[18]['name'] = $_SESSION['lang']['rumah'];
// $head[19]['name'] = $_SESSION['lang']['kantor'];
// $head[20]['name'] = $_SESSION['lang']['pabrik'];
// $head[21]['name'] = $_SESSION['lang']['jalan'];
// $head[22]['name'] = $_SESSION['lang']['kolam'];
// $head[23]['name'] = $_SESSION['lang']['umum'];
// $head[7]['span'] = '2';
// $conSetting = array();
// $conSetting['luasareaproduktif']['type'] = 'currency';
// $conSetting['luasareanonproduktif']['type'] = 'currency';
// $conSetting['jumlahpokok']['type'] = 'numeric';
// $conSetting['bulanmulaipanen']['type'] = 'month';
// $conSetting['cadangan']['type'] = 'numeric';
// $conSetting['okupasi']['type'] = 'numeric';
// $conSetting['rendahan']['type'] = 'numeric';
// $conSetting['sungai']['type'] = 'numeric';
// $conSetting['rumah']['type'] = 'numeric';
// $conSetting['kantor']['type'] = 'numeric';
// $conSetting['pabrik']['type'] = 'numeric';
// $conSetting['jalan']['type'] = 'numeric';
// $conSetting['kolam']['type'] = 'numeric';
// $conSetting['umum']['type'] = 'numeric';
// $master = masterTableBlok($dbname, 'setup_blok', 1, $fieldArr, $head, $conSetting, $where2, array(),
// 	'setup_slave_blok_pdf');

try {
	echo $master;
}
catch (Exception $e) {
	echo 'Create Table Error';
}

?>

<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<?php
#======Select Prep======
# Get Data
$where = "`tipe`='HOLDING' and length(kodeorganisasi)=3";
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',$where,'0');
$optCurr = makeOption($dbname,'setup_matauang','kode,matauang');
$optTipeAkun = array(
  'Aktiva'=>$_SESSION['lang']['aktiva'],
  'Passiva'=>'Passiva',
  'Modal'=>$_SESSION['lang']['modal'],
  'Penjualan'=>$_SESSION['lang']['penjualan'],
  'Biaya'=>$_SESSION['lang']['biaya'],
  'Lain-lain'=>$_SESSION['lang']['lain']
);
#======End Select Prep======
#=======Form============
echo "<div style='height:530px;overflow:scroll;'><div style='margin-bottom:30px;'>";
$els = array();
# Fields
$els[] = array(
  makeElement('kodeorg','label',$_SESSION['lang']['kodeorg']),
  makeElement('kodeorg','select','',array('style'=>'width:150px'),$optOrg)
);
$els[] = array(
  makeElement('noakun','label',$_SESSION['lang']['noakun']),
  makeElement('noakun','text','',array('style'=>'width:150px','maxlength'=>'7',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('namaakun','label',$_SESSION['lang']['namaakun']),
  makeElement('namaakun','text','',array('style'=>'width:250px','maxlength'=>'80',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('namaakun1','label',$_SESSION['lang']['namaakun'])."(EN)",
  makeElement('namaakun1','text','',array('style'=>'width:250px','maxlength'=>'80',
    'onkeypress'=>'return tanpa_kutip(event)'))
);
$els[] = array(
  makeElement('tipeakun','label',$_SESSION['lang']['tipeakun']),
  makeElement('tipeakun','select','',array('style'=>'width:150px'),$optTipeAkun)
);
$els[] = array(
  makeElement('level','label',$_SESSION['lang']['level']),
  makeElement('level','text','',array('style'=>'width:150px','maxlength'=>'2',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('matauang','label',$_SESSION['lang']['matauang']),
  makeElement('matauang','select','',array('style'=>'width:150px'),$optCurr)
);
$els[] = array(
  makeElement('detail','label',$_SESSION['lang']['detail']),
  makeElement('detail','check','',array())
);
$els[] = array(
  makeElement('kasbank','label',$_SESSION['lang']['kasbank']),
  makeElement('kasbank','check','0',array())
);
$els[] = array(
  makeElement('fieldaktif','label',$_SESSION['lang']['fieldaktif']),
  makeElement('fieldaktif','text','',array('style'=>'width:150px','maxlength'=>'7',
    'onkeypress'=>'return angka_doang(event)'))
);
$els[] = array(
  makeElement('pemilik','label',$_SESSION['lang']['namapemilik']),
  makeElement('pemilik','text','',array('style'=>'width:150px','maxlength'=>'7',
    'onkeypress'=>'return angka_doang(event)'))
);

# Fields
$fieldStr = '##noakun##namaakun##namaakun1##tipeakun##kasbank##level##matauang##kodeorg##detail##fieldaktif##pemilik';
$fieldArr = explode("##",substr($fieldStr,2,strlen($fieldStr)-2));

# Button
$els['btn'] = array(
  genFormBtn($fieldStr,
    'keu_5akun',"##noakun##kodeorg",null,null,true)
);

# Generate Field
echo genElTitle($_SESSION['lang']['daftarperkiraan'],$els);
echo "</div>";
#=======End Form============

#=======Table===============
# Display Table
$kolom = 'noakun,namaakun';
echo "<div style='clear:both;float:left;'>";
echo masterTable($dbname,'keu_5akun',"*",array(),array(),array(),array(),'keu_slave_5daftarperkiraan_pdf');
echo "</div></div>";
#=======End Table============

CLOSE_BOX();
echo close_body();
?>
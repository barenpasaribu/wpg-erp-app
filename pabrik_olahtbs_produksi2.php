<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include 'lib/zMysql.php';

include_once 'lib/zLib.php';

echo open_body();

//echo "<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script   language=javascript1.2 src='js/pabrik_5SoundingRev.js'></script>\r\n";
echo "<script language=javascript src=js/zMaster.js></script>\r\n<script language=javascript src=js/formTable.js></script>\r\n<script language=javascript src=js/log_transaksi_mill.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n";

include 'master_mainMenu.php';

OPEN_BOX();

$jam = $mnt = 0;

for ($i = 0; $i < 24; ++$i) {

    if (strlen($i) < 2) {

        $i = '0'.$i;

    }



    $jam .= '<option value='.$i.'>'.$i.'</option>';

}

for ($i = 0; $i < 60; ++$i) {

    if (strlen($i) < 2) {

        $i = '0'.$i;

    }



    $mnt .= '<option value='.$i.'>'.$i.'</option>';

}

$optSebabRusak = "<option value='UMUM'>UMUM</option>";

$optSebabRusak .= "<option value='KECELAKAAN'>KECELAKAAN</option>";

$optTraksi = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$sGet = selectQuery($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='TRAKSI'");

$qGet = mysql_query($sGet);

while ($rGet = mysql_fetch_assoc($qGet)) {

    $optTraksi .= '<option value='.$rGet['kodeorganisasi'].'>'.$rGet['namaorganisasi'].'</option>';

}

$optKaryawan = '';

$sGet = selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "right(lokasitugas,2)='RO' and kodegolongan>='3' and karyawanid<>'0999999999'");

$qGet = mysql_query($sGet);

while ($rGet = mysql_fetch_assoc($qGet)) {

    $optKaryawan .= '<option value='.$rGet['karyawanid'].'>'.$rGet['namakaryawan'].'</option>';

}

$optKaryawan2 = '';

$sGet = selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and left(kodegolongan,1)>=3 and karyawanid<>'0999999999'");

$qGet = mysql_query($sGet);

while ($rGet = mysql_fetch_assoc($qGet)) {

    $optKaryawan2 .= '<option value='.$rGet['karyawanid'].'>'.$rGet['namakaryawan'].'</option>';

}

// Organisasi

$where = "WHERE kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' and tipe='PABRIK'";
$i = 'select kodeorganisasi,namaorganisasi from '.$dbname.'.organisasi ' . $where.'';

$j = mysql_query($i);

while ($k = mysql_fetch_assoc($j)) {
    $optPer = '<option value=\'0\'>Silakan Pilih...</option>';
    $optPer .= "<option value='".$k['kodeorganisasi']."'>".$k['namaorganisasi'].'</option>';
}

// end organisasi

echo "<fieldset style='width:500px;'>\r\n    <legend>OLAH TBS</legend>\r\n  
<table cellspacing=1 border=0>\r\n   
<tr><td>".$_SESSION['lang']['kodeorg']."</td>\r\n        <td><select id=kodeorg onchange=getNotrans(0) style='width:150px;'>".$optPer."</select></td>\r\n    </tr>\r\n   
<tr><td>Nomer Transaksi</td>\r\n        <td><input type=text id=nomertransaksi  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>".$_SESSION['lang']['tanggal']."</td>\r\n
<td><input type=text class=myinputtext id=tanggal onmousemove=setCalendar(this.id) onblur=getSisaawal() false;  size=10 maxlength=10 style=\"width:100px;\"/></td>\r\n </tr>\r\n
<tr><td>Sisa Awal</td>\r\n        <td><input type=text id=sisaawal  onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>TBS Masuk</td>\r\n        <td><input type=text id=tbsmasuk onblur=ambilisiform()  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Total Buah</td>\r\n        <td><input type=text id=totalbuah onkeypress=\"return angka_doang(event);\"   disabled=\"disabled\"   class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Lori Olah :</td>\r\n        <td><input type=text id=loriolah onblur=ambilisiform()  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Lori Dalam Rebusan :</td>\r\n        <td><input type=text id=loridalamrebusan onblur=ambilisiform()  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Lori Restan Depan Rebusan :</td>\r\n        <td><input type=text id=lorirestandepanrebusan onblur=ambilisiform()  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Lori Restan Belakang Rebusan :</td>\r\n        <td><input type=text id=lorirestanbelakangrebusan onblur=ambilisiform()  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Estimasi Diperon:</td>\r\n        <td><input type=text id=estimasidiperon onblur=ambilisiform()  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Total Lori :</td>\r\n        <td><input type=text id=total2 onkeypress=\"return angka_doang(event);\"   disabled=\"disabled\"   class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Total Rata-Rata Buah PerLori :</td>\r\n        <td><input type=text id=ratabuahperlori onkeypress=\"return angka_doang(event);\"   disabled=\"disabled\"   class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Potongan Sortasi (Kg):</td>\r\n        <td><input type=text id=kgpotsortasi onblur=getPotsortasi()  class=myinputtextnumber style=\"width:150px;\"><input type=checkbox  onblur=getPotsortasi()  class=myinputtextnumber style=\"width:50px;\">Load Data</td>\r\n    </tr>\r\n
<tr><td>Potongan (%):</td>\r\n        <td><input type=text id=persenpotsortasi onblur=ambilisiform() disabled=\"disabled\" class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>TBS Masuk (After) :</td>\r\n        <td><input type=text id=tbsmasukafter onblur=ambilisiform() disabled=\"disabled\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>TBS Olah:</td>\r\n        <td><input type=text id=tbsolah onblur=ambilisiform() disabled=\"disabled\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>TBS Olah After:</td>\r\n        <td><input type=text id=tbsolahafter onblur=ambilisiform() disabled=\"disabled\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Sisa Akhir:</td>\r\n        <td><input type=text id=sisaakhir onblur=ambilisiform() disabled=\"disabled\"  class=myinputtextnumber style=\"width:150px;\"></td>\r\n    </tr>\r\n
<tr><td>Refesh Calculation</td>\r\n        <td><input type=checkbox  onblur=ambilisiform()  class=myinputtextnumber style=\"width:150px;\"></td>\r\n</tr>\r\n
<tr></table>\r\n    <input type=hidden id=id>\r\n    <input type=hidden value='' id=notransaksi>\r\n    <button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>\r\n    <button class=mybutton onclick=batal()>".$_SESSION['lang']['new']."</button>\t \r\n    </table></fieldset>";

CLOSE_BOX();

$table = 'log_transaksi_mill LIMIT 0,10';

$query = 'select sisaakhir,tbsolahafter,persenpotsortasi,kgpotsortasi,tbsmasukafter,totalbuah,tbsmasuk,sisaawal,ratabuahlori,totallori,notrans_tbsolah,kodeorg,tanggal,id,loriolah,loridalamrebusan,lorirestandepanrebusan,lorirestanbelakangrebusan,estimasidiperon,tbsolah from '.$dbname.'.'.$table ;

$res = mysql_query($query);

$j = mysql_num_fields($res);

$i = 0;

$field = [];

$fieldStr = '';

$primary = [];

for ($primaryStr = ''; $i < $j; ++$i) {

    $meta = mysql_fetch_field($res, $i);

    $field[] = strtolower($meta->name);

    $fieldStr .= '##'.strtolower($meta->name);

    if ('1' === $meta->primary_key) {

        $primary[] = strtolower($meta->name);

        $primaryStr .= '##'.strtolower($meta->name);

    }

}

$fForm = $field;

$result = [];

while ($bar = mysql_fetch_assoc($res)) {

    $result[] = $bar;

}

$tables = '<fieldset><legend>'.$_SESSION['lang']['list'].'</legend>';

//$tables .= "<img src='images/pdf.jpg' title='PDF Format'\r\n  style='width:20px;height:20px;cursor:pointer' onclick=\"masterPDF('".$table."','*',null,'slave_master_pdf',event)\">&nbsp;";

//$tables .= "<img src='images/printer.png' title='Print Page'\r\n  style='width:20px;height:20px;cursor:pointer' onclick='javascript:print()'>";

$tables .= "<div style='overflow:auto'>";

$tables .= "<table id='masterTable' class='sortable' cellspacing='1' border='0'>";

$tables .= "<thead><tr class='rowheader'>";
/*
foreach ($field as $hName) {

    $tables .= '<td>'.$_SESSION['lang'][$hName].'</td>';

}
*/
$tables .= '<td>Kode Organisasi</td>';
$tables .= '<td>Tanggal</td>';
$tables .= '<td>Lori Olah</td>';
$tables .= '<td>Lori Dalam Rebusan</td>';
$tables .= '<td>Lori Restan Depan Rebusan</td>';
$tables .= '<td>Lori Restan Belakang Rebusan</td>';
$tables .= '<td>Estimasi Di Peron</td>';
$tables .= '<td>TBS Olah</td>';
$tables .= "<td colspan='3'>Action</td>";

$tables .= '</tr></thead>';


$tables .= "<tbody id='mTabBody'>";

$i = 0;

foreach ($result as $row) {

    $tables .= "<tr id='tr_".$i."' class='rowcontent'>";

    $tmpVal = '';

    $tmpKey = '';

    $j = 0;



  //  $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow('".$row['kodeorg']."','".$row['tanggal']."','".$row['loriolah']."','".$row['loridalamrebusan']."',".$row['lorirestandepanrebusan'].", '".$row['lorirestanbelakangrebusan']."','".$row['estimasiperon']."','".$row['tbsolah']."','".$row['id']."')\"\r\n    class='zImgBtn' src='images/001_45.png' /></td>";
 
    $tables .= "<td>".$row['kodeorg']."</td>";
    $tables .= "<td>".$row['tanggal']."</td>";
    $tables .= "<td>".$row['loriolah']."</td>";
    $tables .= "<td>".$row['loridalamrebusan']."</td>";
    $tables .= "<td>".$row['lorirestandepanrebusan']."</td>";
    $tables .= "<td>".$row['lorirestanbelakangrebusan']."</td>";
    $tables .= "<td>".$row['estimasidiperon']."</td>";
    $tables .= "<td>".$row['tbsolah']."</td>";
    $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow('".$row['sisaakhir']."','".$row['tbsolahafter']."','".$row['persenpotsortasi']."','".$row['kgpotsortasi']."','".$row['tbsmasukafter']."','".$row['totalbuah']."','".$row['tbsmasuk']."','".$row['sisaawal']."','".$row['ratabuahlori']."','".$row['totallori']."','".$row['notrans_tbsolah']."','".$row['kodeorg']."','".$row['tanggal']."','".$row['id']."','".$row['loriolah']."','".$row['loridalamrebusan']."','".$row['lorirestandepanrebusan']."','".$row['lorirestanbelakangrebusan']."','".$row['estimasidiperon']."','".$row['tbsolah']."')\"\r\n    class='zImgBtn' src='images/001_45.png' /></td>";
    $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow('".$row['id']."')\"\r\n    class='zImgBtn' src='images/delete_32.png' /></td>";
    
   
    //  $tables .= "<td><img id='editRow".$i."' title='Edit' onclick=\"editRow('".$row['kodeorg']."','".$row['tanggal']."')\"\r\n    class='zImgBtn' src='images/001_45.png' /></td>";

  //  $tables .= "<td><img id='delRow".$i."' title='Hapus' onclick=\"delRow('".$row['kodeorg']."','".$row['kodetangki']."')\"\r\n    class='zImgBtn' src='images/delete_32.png' /></td>";    

    $tables .= '</tr>';
    
    ++$i;

}
//$tables .="<tr class=rowheader><td colspan=9 align=center>".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br /><button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button><button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t</td>\r\n\t</tr>";
//$tables .="<tr class=rowheader><td colspan=9 align=center>".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br /></td>\r\n\t</tr>";

 
$tables .= '</tbody>';

$tables .= '<tfoot></tfoot>';

$tables .= '</table></div></fieldset>';

echo "<div style='clear:both;float:left'>";

echo $tables;

echo '</div>';

CLOSE_BOX();

echo close_body();

?>
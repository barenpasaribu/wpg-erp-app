<?

require_once('master_validation.php');

include('lib/nangkoelib.php');

include('lib/zMysql.php');

include_once('lib/zLib.php');

echo open_body();

?>

<script language="javascript" src="js/zMaster.js"></script>

<script   language=javascript1.2 src='js/vhc_project.js'></script>

<?

include('master_mainMenu.php');

OPEN_BOX();

/*

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')

{

$str="select kodeorganisasi, namaorganisasi, induk from ".$dbname.".organisasi

    where length(kodeorganisasi)=4

    order by induk, tipe, namaorganisasi";

}

else

{

    $str="select kodeorganisasi, namaorganisasi, induk from ".$dbname.".organisasi

    where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'

    order by induk, tipe, namaorganisasi";

}

*/

    $str="select kodeorganisasi, namaorganisasi, induk from ".$dbname.".organisasi

    where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and length(kodeorganisasi)=4 and kodeorganisasi  not like '%HO'

    order by induk, tipe, namaorganisasi";

    

$res=mysql_query($str);

$optunit="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

while($bar=mysql_fetch_object($res))

{

    $optunit.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." - ".$bar->namaorganisasi."</option>";

}

if($_SESSION['language']=='EN'){

    $dd='namatipe1 as namatipe';

}else{



    $dd='namatipe';

}    

$str="select kodetipe, ".$dd." from ".$dbname.".sdm_5tipeasset

    order by kodetipe";

$res=mysql_query($str);

$optaset="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

while($bar=mysql_fetch_object($res))

{

    $optaset.="<option value='".$bar->kodetipe."'>".$bar->kodetipe." - ".$bar->namatipe."</option>";

}



$kamusjenis['AK']='Aktiva Dalam Konstruksi/Activa Under Construction';

$kamusjenis['PB']='Pabrikasi';



$optjenis="";

$arrjenis=getEnum($dbname,'project','tipe');

foreach($arrjenis as $kei=>$fal)

{

    if($fal=='PB')

    {

     #Pabrikasi  belum aktif  karena akunnya belum ada, pastikan akunnya sudah ada dan didaftar  pada parameter jurnal dengan kode

    #PAB       

    } 

    else{

          $optjenis.="<option value='".$kei."'>".$fal." ".$kamusjenis[$fal]."</option>";

    }

    

} 	



echo"<fieldset style='width:500px;'>

    <legend>Project</legend>

    <table cellspacing=1 border=0>

    <tr><td align=right>

        ".$_SESSION['lang']['unit']."

    </td><td>

        <select id=unit style='width:200px;'>".$optunit."</select>

    </td></tr>

    <tr><td align=right>

        ".$_SESSION['lang']['aset']."

    </td><td>

        <select id=aset style='width:200px;'>".$optaset."</select>

    </td></tr>

    <tr><td align=right>

        ".$_SESSION['lang']['jenis']."

    </td><td>

        <select id=jenis style='width:200px;'>".$optjenis."</select>

    </td></tr>

    <tr><td align=right>

        ".$_SESSION['lang']['nama']."

    </td><td>

        <input type=text id=nama class=myinputtext maxlength=100 onkeypress=\"return tanpa_kutip(event);\" style='width:200px;'>

    </td></tr>

    <tr><td align=right>

        ".$_SESSION['lang']['tanggalmulai']."

    </td><td>

        <input style='width:200px;' id=tanggalmulai class=myinputtext maxlength=10 onkeypress=\"return false;\" size=10 onmousemove=setCalendar(this.id) value=".date('d-m-Y').">

    </td></tr>

    <tr><td align=right>

        ".$_SESSION['lang']['tanggalsampai']."

    </td><td>

        <input style='width:200px;' id=tanggalselesai class=myinputtext maxlength=10 onkeypress=\"return false;\" size=10 onmousemove=setCalendar(this.id) value=".date('d-m-Y').">

    </td></tr>

    </table>

    <input type=hidden value=insert id=method>

    <input type=hidden value='' id=kode>

    <button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>

    <button class=mybutton onclick=batal()>".$_SESSION['lang']['cancel']."</button>	 

    </fieldset>";



//$qwe=addZero('qwe',5);

//echo "qwe:".$qwe;



echo "<div id=dataDisimpan><fieldset style=width:800px;><legend>".$_SESSION['lang']['datatersimpan']."</legend><div style='height:290px;width:800px;overflow:scroll;'>

      <table class=sortable border=0 cellspacing=1>

	  <thead>

	  <tr>

	  <td>".$_SESSION['lang']['kode']."</td>

	  <td>".$_SESSION['lang']['unit']."</td>

	  <td>".$_SESSION['lang']['jenis']."</td>

	  <td>".$_SESSION['lang']['nama']."</td>

	  <td>".$_SESSION['lang']['tanggalmulai']."</td>

	  <td>".$_SESSION['lang']['tanggalsampai']."</td>

                      <td>".$_SESSION['lang']['updateby']."</td>

	  <td>".$_SESSION['lang']['action']."</td>

	  </tr>

	  </thead>

	  <tbody id=container>";

echo"<script>loadData()</script>";

//$str1="select * from ".$dbname.".project order by substring(kode, -7) desc";

//if($res1=mysql_query($str1))

//{

//    $no=0;

//    while($bar1=mysql_fetch_object($res1))

//    {

//        $qwe=substr($bar1->kode,3,3);

//        $asd=substr($qwe,-1);

//        if($asd=='0')$aset=substr($qwe,0,2);

//        else $aset=$qwe;

//

//        $no+=1;

//        echo"<tr class=rowcontent>

//            <td>".$bar1->kode."</td>

//            <td>".$bar1->kodeorg."</td>

//            <td>".$bar1->tipe."</td>

//            <td>".$bar1->nama."</td>

//            <td>".tanggalnormal($bar1->tanggalmulai)."</td>

//            <td>".tanggalnormal($bar1->tanggalselesai)."</td>

//            <td>";

//            if($bar1->posting==0){

//                echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$aset."','".$bar1->tipe."','".$bar1->nama."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalselesai)."','update','".$bar1->kode."');\">

//                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"hapus('".$bar1->kode."');\">

//                <img src=images/nxbtn.png class=resicon  title='Detail' onclick=\"detailForm('".$bar1->kode."');\">";

//            }else{

//                echo"";

//            }

//            echo"</td></tr>";

//    }

//}

echo "</tbody>

    <tfoot>

    </tfoot>

    </table>

    </div></fieldset></div>";

echo"<div id=detailInput style=display:none>";

$frmdt.="<fieldset style=width:800px;><legend>".$_SESSION['lang']['detail']." ".$kode."</legend>";

$frmdt.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";

$frmdt.="<thead><tr><td>".$_SESSION['lang']['kode']."</td>";

$frmdt.="<td>".$_SESSION['lang']['namakegiatan']."</td>";

$frmdt.="<td>".$_SESSION['lang']['tanggalmulai']."</td>";

$frmdt.="<td>".$_SESSION['lang']['tanggalsampai']."</td>";

$frmdt.="<td>".$_SESSION['lang']['action']."</td></tr></thead><tbody>";

$frmdt.="<tr class=rowcontent><td><input type=text id=kdProj class=myinputtext maxlength=20 onkeypress=\"return tanpa_kutip(event);\" style='width:200px;'  disabled></td>";

$frmdt.="<td><input type=text id=namaKeg class=myinputtext onkeypress=\"return tanpa_kutip(event);\" style='width:150px;'></td>";

$frmdt.="<td><input style='width:100px;' id=tanggalMulai class=myinputtext maxlength=10 onkeypress=\"return false;\" size=10 onmousemove=setCalendar(this.id) value=".date('d-m-Y')."></td>";

$frmdt.="<td><input style='width:100px;' id=tanggalSampai class=myinputtext maxlength=10 onkeypress=\"return false;\" size=10 onmousemove=setCalendar(this.id) value=".date('d-m-Y')."></td>";

$frmdt.="<td><img src='images/save.png' class='zImgBtn' style='cursor:pointer;' onclick=addDetail() /></td></tr></tbody></table> <button class=mybutton onclick=doneSlsi()>".$_SESSION['lang']['selesai']."</button></fieldset><input type=hidden id=kegId />";

$frmdt.="<div>";

$frmdt.="<fieldset style=width:800px;><legend>".$_SESSION['lang']['detail']." ".$kode."</legend>";

$frmdt.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";

$frmdt.="<thead><tr><td>".$_SESSION['lang']['kode']."</td>";

$frmdt.="<td>".$_SESSION['lang']['namakegiatan']."</td>";

$frmdt.="<td>".$_SESSION['lang']['tanggalmulai']."</td>";

$frmdt.="<td>".$_SESSION['lang']['tanggalsampai']."</td>";

$frmdt.="<td>".$_SESSION['lang']['action']."</td></tr></thead><tbody id=printDat>";

$frmdt.="</tbody></table></fieldset>";

$frmdt.="</div>";

echo $frmdt;

echo"</div>";

CLOSE_BOX();

echo close_body();

?>


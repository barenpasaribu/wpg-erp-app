<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include('lib/zMysql.php');
include_once('lib/zLib.php');

$unit =$_POST['unit'];
$aset =$_POST['aset'];
$jenis =$_POST['jenis'];
$nama =$_POST['nama'];
$tanggalmulai=tanggalsystem($_POST['tanggalmulai']);
$tanggalselesai=tanggalsystem($_POST['tanggalselesai']);
$method =$_POST['method'];
$kode =$_POST['kode'];
$optLokasi=makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas');
switch($method)
{
    case 'update':	
    $str="update ".$dbname.".project set nama='".$nama."',
        tanggalmulai='".$tanggalmulai."',tanggalselesai='".$tanggalselesai."',
        updateby='".$_SESSION['standard']['userid']."'
        where kode='".$kode."'";
    if(mysql_query($str))
    {
        
    }
    else
    {
        echo " Gagal,".addslashes(mysql_error($conn));
    }
    break;
    case 'insert':
        // cari nomor terakhir
        $str="select kode from ".$dbname.".project
            order by substring(kode, -7) desc
            limit 1";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $belakangnya=intval(substr($bar->kode,-7));
        }
        $belakangnya+=1;
        
        $belakangnya=addZero($belakangnya,10-strlen($aset));
        $kode=$jenis."-".$aset.$belakangnya;
        $str="insert into ".$dbname.".project (kode, nama, tipe, kodeorg,
            tanggalmulai,tanggalselesai,updateby)
            values('".$kode."','".$nama."','".$jenis."',
            '".$unit."','".$tanggalmulai."','".$tanggalselesai."',".$_SESSION['standard']['userid'].")";
        if(mysql_query($str))
        {
            
        }
        else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }	
    break;
    case 'delete':
        $str="delete from ".$dbname.".project
        where kode='".$kode."'";
        if(mysql_query($str))
        {

        }
        else
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
    break;
    case'loadData':
        //$str1="select * from ".$dbname.".project where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by substring(kode, -7) desc";
     $str1="select a.*,b.namakaryawan from ".$dbname.".project a 
                 left join ".$dbname.".datakaryawan b on a.updateby=b.karyawanid order by substring(kode, -7) desc";   
    if($res1=mysql_query($str1))
    {
        $rowd=mysql_num_rows($res1);
        if($rowd==0)
        {
            echo"<tr class=rowcontent><td colspan=7>".$_SESSION['lang']['dataempty']."</td></tr>";
        }
        else
        {
            $no=0;
            while($bar1=mysql_fetch_object($res1))
            {
                $qwe=substr($bar1->kode,3,3);
                $asd=substr($qwe,-1);
                if($asd=='0')$aset=substr($qwe,0,2);
                else $aset=$qwe;

                $no+=1;
                echo"<tr class=rowcontent>
                    <td>".$bar1->kode."</td>
                    <td>".$bar1->kodeorg."</td>
                    <td>".$bar1->tipe."</td>
                    <td>".$bar1->nama."</td>
                    <td>".tanggalnormal($bar1->tanggalmulai)."</td>
                    <td>".tanggalnormal($bar1->tanggalselesai)."</td>
                    <td>".$bar1->namakaryawan."</td>
                    <td>";
                    if($bar1->posting==0 and $bar1->updateby==$_SESSION['standard']['userid']){
                        echo"<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kodeorg."','".$aset."','".$bar1->tipe."','".$bar1->nama."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalselesai)."','update','".$bar1->kode."');\">
                        <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"hapus('".$bar1->kode."');\">
                        <img src=images/nxbtn.png class=resicon  title='Detail' onclick=\"detailForm('".$bar1->kodeorg."','".$aset."','".$bar1->tipe."','".$bar1->nama."','".tanggalnormal($bar1->tanggalmulai)."','".tanggalnormal($bar1->tanggalselesai)."','detail','".$bar1->kode."');\">
                        <img src=images/skyblue/posting.png class=resicon  title='posting data' onclick=\"postIni('".$bar1->kode."');\">
                        <img onclick=\"masterPDF('project','".$bar1->kode.",".$bar1->updateby."','','vhc_slave_project_pdf',event);\" title=\"Print\" class=\"resicon\" src=\"images/pdf.jpg\">";
                    }else{
                        if($bar1->posting==1){
                            echo"<img src=images/skyblue/posted.png class=resicon>";
                        }
                        else {    
                            echo"<img src=images/skyblue/posting.png>";
                            }                       
                        echo"<img onclick=\"masterPDF('project','".$bar1->kode.",".$bar1->updateby."','','vhc_slave_project_pdf',event);\" title=\"Print\" class=\"resicon\" src=\"images/pdf.jpg\">";
                    }
                    echo"</td></tr>";
            }
        }
    }
    break;
    case'detail':
   
    $sDet="select distinct * from ".$dbname.".project_dt  where kodeproject='".$kode."'";
    $qDet=mysql_query($sDet) or die(mysql_error($conn));
    $row=mysql_num_rows($qDet);
    if($row==0)
    {
        $frmdt.="<tr class=rowcontent><td colspan=5>".$_SESSION['lang']['dataempty']."</td></tr>";
    }
    else
    {
        while($rDet=  mysql_fetch_assoc($qDet))
        {
        $frmdt.="<tr class=rowcontent><td>".$rDet['kodeproject']."</td>";
        $frmdt.="<td>".$rDet['namakegiatan']."</td>";
        $frmdt.="<td>".tanggalnormal($rDet['tanggalmulai'])."</td>";
        $frmdt.="<td>".tanggalnormal($rDet['tanggalselesai'])."</td>";
        $frmdt.="<td>
                <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editDet('".tanggalnormal($rDet['tanggalmulai'])."','".tanggalnormal($rDet['tanggalselesai'])."','updatedet','".$rDet['kodeproject']."','".$rDet['kegiatan']."','".$rDet['namakegiatan']."');\">
                <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"hapusData('".$rDet['kegiatan']."');\">
                </td></tr>";
        }
    }
    echo $frmdt;
    break;
    case'insertDetail':
        $tglMul=tanggalsystem($_POST['tglMul']);
        $tglakh=tanggalsystem($_POST['tglSmp']);


        $sCek="SELECT datediff('".$tglakh."', '".$tglMul."') as selisih";
        $hasil = mysql_query($sCek);
        $data = mysql_fetch_array($hasil);
        if($data['selisih']<0)
        {
            exit("Error:Tanggal Selesai Lebih Besar dari Tanggal Mulai");
        }
    $sInser="insert into ".$dbname.".project_dt (kodeproject, namakegiatan, tanggalmulai, tanggalselesai) 
             values ('".$kode."','".$_POST['nmKeg']."','".tanggalsystem($_POST['tglMul'])."','".tanggalsystem($_POST['tglSmp'])."')";
    if(!mysql_query($sInser))
    {
        die(mysql_error($conn));
    }
    break;
    case'updatedet':
         $tglMul=tanggalsystem($_POST['tglMul']);
        $tglakh=tanggalsystem($_POST['tglSmp']);


        $sCek="SELECT datediff('".$tglakh."', '".$tglMul."') as selisih";
        $hasil = mysql_query($sCek);
        $data = mysql_fetch_array($hasil);
        if($data['selisih']<0)
        {
            exit("Error:Tanggal Selesai Lebih Kecil dari Tanggal Mulai");
        }
    $sUpdate="update ".$dbname.".project_dt set namakegiatan='".$_POST['nmKeg']."',
              tanggalmulai='".tanggalsystem($_POST['tglMul'])."', tanggalselesai='".tanggalsystem($_POST['tglSmp'])."'
              where kegiatan='".$_POST['index']."'";
    if(!mysql_query($sUpdate))
    {
        die(mysql_error($conn));
    }
    break;
    case'hpsDetail':
    $sdel="delete from ".$dbname.".project_dt where kegiatan='".$_POST['index']."'";
    if(!mysql_query($sdel))
    {
        die(mysql_error($conn));
    }
    break;
    case'postingData':
        $sCari="select distinct updateby from ".$dbname.".project where kode='".$_POST['kode']."'";
        $qCari=mysql_query($sCari) or die(mysql_error($conn));
        $rCari=mysql_fetch_assoc($qCari);
        if($optLokasi[$rCari['updateby']]!=$_SESSION['empl']['lokasitugas'])
        {
            exit("Error:Anda Tidak Memiliki Autorisasi");
        }
        $sPost="update ".$dbname.".project set updateby='".$_SESSION['standard']['userid']."',posting='1' where kode='".$_POST['kode']."'";
        //exit("Error:".$sPost);
        if(!mysql_query($sPost))
        {
            die(mysql_error($conn));
        }
        
    break;
    default:
    break;					
}


?>

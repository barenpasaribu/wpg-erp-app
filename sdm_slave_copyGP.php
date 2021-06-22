<?php

require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('config/connection.php');
$tahun1=$_POST['tahun1'];
$tahun2=$_POST['tahun2'];
$str="select a.*,b.lokasitugas,b.tanggalkeluar 
      from ".$dbname.".sdm_5gajipokok a  left join 
      ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
      where a.tahun=".$tahun1." and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'
      and (b.tanggalkeluar='0000-00-00' or b.tanggalkeluar>'".$tahun2."-01-01' or b.tanggalkeluar is null)
      order by karyawanid";
if($res=mysql_query($str))
{
    if(mysql_num_rows($res)<1)
    {
        exit("Error: No data on source");
    }
    else
    {
        $str="delete from ".$dbname.".sdm_5gajipokok where karyawanid in(
              select karyawanid from ".$dbname.".datakaryawan 
              where lokasitugas='".$_SESSION['empl']['lokasitugas']."')
              and tahun=".$tahun2;
       mysql_query($str);
       while($bar=mysql_fetch_object($res))
       {
           $str1="insert into ".$dbname.".sdm_5gajipokok (tahun,karyawanid,idkomponen,jumlah)
                  values(".$tahun2.",".$bar->karyawanid.",".$bar->idkomponen.",".$bar->jumlah.");";
           if(mysql_query($str1))
           {
               
           }
           else
           {
                 exit("Error: ".mysql_error($conn));  
           }
       }
    }  
        
    
}
else
{
    exit("Error: ".mysql_error($conn));
}   
?>
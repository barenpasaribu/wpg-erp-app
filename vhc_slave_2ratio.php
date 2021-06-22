<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_bar.php');
if(isset($_GET['unit'])){
    $param=$_GET;
    $border=1;
}else{
 $param=$_POST;   
 $border=0;
}
$waktu=$param['tahun'];
$kodeorg=$param['unit'];
#ambil jenis-jenis VHC
$str="select jenisvhc,namajenisvhc from ".$dbname.".vhc_5jenisvhc";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $nama[$bar->jenisvhc]=$bar->namajenisvhc;
}

    
echo"<link rel=stylesheet tyle=text href='style/generic.css'>
          <script language=javascript src='js/generic.js'></script>";

#ambil Biaya real      
$str="select sum(jlhbbm) as jlh,kodevhc,left(tanggal,7) as periode from ".$dbname.".vhc_runht where
       tanggal like '".$waktu."%'
      group by kodevhc,left(tanggal,7)";
$res=mysql_query($str);
  while($bar=mysql_fetch_object($res))
  {
       $real[$bar->periode][$bar->kodevhc]=$bar->jlh;
  }

  #ambil hm/km real
  $str="select sum(a.jumlah) as km, b.kodevhc,left(b.tanggal,7) as periode from ".$dbname.".vhc_rundt a
            left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
            and tanggal like '".$waktu."%' group by kodevhc,left(b.tanggal,7)";
$res=mysql_query($str);

  while($bar=mysql_fetch_object($res))
  {
       $realHM[$bar->periode][$bar->kodevhc]=$bar->km;
  }  
          
#budget
   $str="SELECT a.kodevhc,
   sum(fis01) as fis01,
   sum(fis02) as fis02,
   sum(fis03) as fis03,
   sum(fis04) as fis04,
   sum(fis05) as fis05,
   sum(fis06) as fis06,
   sum(fis07) as fis07,
   sum(fis08) as fis08,
   sum(fis09) as fis09,
   sum(fis10) as fis10,
   sum(fis11) as fis11,
   sum(fis12) as fis12
    FROM ".$dbname.".bgt_budget_detail a
   where a.kodevhc is not null and tipebudget='TRK' and tahunbudget='".$waktu."'
   and kodebarang like '351%'    
   group by kodevhc";

$res=mysql_query($str);   
  while($bar=mysql_fetch_object($res))
  {
       $bgtfis[$waktu."-01"][$bar->kodevhc]=$bar->fis01;
       $bgtfis[$waktu."-02"][$bar->kodevhc]=$bar->fis02;
       $bgtfis[$waktu."-03"][$bar->kodevhc]=$bar->fis03;
       $bgtfis[$waktu."-04"][$bar->kodevhc]=$bar->fis04;
       $bgtfis[$waktu."-05"][$bar->kodevhc]=$bar->fis05;
       $bgtfis[$waktu."-06"][$bar->kodevhc]=$bar->fis06;
       $bgtfis[$waktu."-07"][$bar->kodevhc]=$bar->fis07;
       $bgtfis[$waktu."-08"][$bar->kodevhc]=$bar->fis08;
       $bgtfis[$waktu."-09"][$bar->kodevhc]=$bar->fis09;
       $bgtfis[$waktu."-10"][$bar->kodevhc]=$bar->fis10;
       $bgtfis[$waktu."-11"][$bar->kodevhc]=$bar->fis11;
       $bgtfis[$waktu."-12"][$bar->kodevhc]=$bar->fis12;
  }           
  #ambil  budget fisik kendaraan
     $str="SELECT a.kodevhc,
   sum(jam01) as jam01,
   sum(jam02) as jam02,
   sum(jam03) as jam03,
   sum(jam04) as jam04,
   sum(jam05) as jam05,
   sum(jam06) as jam06,
   sum(jam07) as jam07,
   sum(jam08) as jam08,
   sum(jam09) as jam09,
   sum(jam10) as jam10,
   sum(jam11) as jam11,
   sum(jam12) as jam12
    FROM ".$dbname.".bgt_vhc_jam a  where tahunbudget='".$waktu."'
   group by a.kodevhc";
  $res=mysql_query($str);   
  while($bar=mysql_fetch_object($res))
  {
       $bgtjam[$waktu."-01"][$bar->kodevhc]=$bar->jam01;
       $bgtjam[$waktu."-02"][$bar->kodevhc]=$bar->jam02;
       $bgtjam[$waktu."-03"][$bar->kodevhc]=$bar->jam03;
       $bgtjam[$waktu."-04"][$bar->kodevhc]=$bar->jam04;
       $bgtjam[$waktu."-05"][$bar->kodevhc]=$bar->jam05;
       $bgtjam[$waktu."-06"][$bar->kodevhc]=$bar->jam06;
       $bgtjam[$waktu."-07"][$bar->kodevhc]=$bar->jam07;
       $bgtjam[$waktu."-08"][$bar->kodevhc]=$bar->jam08;
       $bgtjam[$waktu."-09"][$bar->kodevhc]=$bar->jam09;
       $bgtjam[$waktu."-10"][$bar->kodevhc]=$bar->jam10;
       $bgtjam[$waktu."-11"][$bar->kodevhc]=$bar->jam11;
       $bgtjam[$waktu."-12"][$bar->kodevhc]=$bar->jam12;
  } 

      $str="select kodevhc,jenisvhc from ".$dbname.".vhc_5master where kodetraksi like '".$kodeorg."%'";

        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $kodevhc[]=$bar->kodevhc;
            $jenisvhc[$bar->kodevhc]=$bar->jenisvhc;
        }
        
      $tab.="Penggunaan BBM  Sat./Litre  Kendaraan-Alat Berat-Mesin ".$kodeorg." Periode:".substr($waktu,0,4)."                   
                <table class=sortable cellspacing=1 border=".$border.">
               <thead><tr class=rowheader>
               <td rowspan=3>".$_SESSION['lang']['urut']."</td>
               <td rowspan=3>".$_SESSION['lang']['kodevhc']."</td>
                <td rowspan=3>".$_SESSION['lang']['jenis']."</td>
               <td colspan=6 align=center>Jan ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Feb ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Mar ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Apr ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Mei ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Jun ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Jul ".substr($waktu,0,4)."</td>    
               <td colspan=6 align=center>Aug ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Sep ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Okt ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Nop ".substr($waktu,0,4)."</td>
               <td colspan=6 align=center>Des ".substr($waktu,0,4)."</td>    
               <td colspan=6 align=center>Total ".substr($waktu,0,4)."</td>
               </tr>
               <tr class=rowheader>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
                 <td colspan=3 align=center>Realisasi</td><td colspan=3 align=center>Budget</td>
               </tr>
               <tr class=rowheader>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
                 <td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td><td align=center>Ltr</td><td align=center>Hm/Km</td><td align=center>Sat/Ltr.</td>
               </tr>
               </thead>
               <tbody>";       
     $no=0;
      foreach($kodevhc as $key=>$val)
      {
          $no+=1;
          $treal=0;
          $trealHM=0;
          $tbgtfis=0;
          $tbgtjam=0;
          
          $tab.="<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$val."</td>
                <td>".$jenisvhc[$val]."</td>";
          for($kk=1;$kk<=12;$kk++){       
              if($kk<10)
                  $zz=$waktu."-0".$kk;
              else
                  $zz=$waktu."-".$kk;
              $color='bgcolor=green';
              if(@($realHM[$zz][$val]/$real[$zz][$val])<@($bgtjam[$zz][$val]/$bgtfis[$zz][$val]))
                  $color='bgcolor=red';
              
              $tab.="<td align=right>".number_format($real[$zz][$val],2)."</td><td align=right>".number_format($realHM[$zz][$val],2)."</td><td ".$color."  align=right>".@number_format($realHM[$zz][$val]/$real[$zz][$val],2)."</td><td align=right>".number_format($bgtfis[$zz][$val],2)."</td><td align=right>".number_format($bgtjam[$zz][$val],2)."</td><td align=right bgcolor=#dedede>".@number_format($bgtjam[$zz][$val]/$bgtfis[$zz][$val],2)."</td>";
            $treal+=$real[$zz][$val];
            $trealHM+=$realHM[$zz][$val];
            $tbgtfis+=$bgtfis[$zz][$val];
            $tbgtjam+=$bgtjam[$zz][$val];
               }
         #total
               $color='bgcolor=green';
               if(@($trealHM/$treal)<@($tbgtjam/$tbgtfis)){
                   $color='bgcolor=red';
               }
           $tab.="<td align=right>".number_format($treal,2)."</td><td align=right>".number_format($trealHM,2)."</td><td ".$color."  align=right>".@number_format($trealHM/$treal,2)."</td><td align=right>".number_format($tbgtfis,2)."</td><td align=right>".number_format($tbgtjam,2)."</td><td align=right bgcolor=#dedede>".@number_format($tbgtjam/$tbgtfis,2)."</td>";    
           $tab.="</tr>";
      }
      
     $tab.= "</tbody><tfoot>
                </tfoot></table>"; 
 if(isset($_GET['unit'])){
            $nop_="Ratio_BBM";
            if(strlen($tab)>0)
            {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        @unlink('tempExcel/'.$file);
                    }
                }	
               closedir($handle);
            }
             $handle=fopen("tempExcel/".$nop_.".xls",'w');
             if(!fwrite($handle,$tab))
             {
              echo "<script language=javascript1.2>
                    parent.window.alert('Can't convert to excel format');
                    </script>";
               exit;
             }
             else
             {
              echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls';
                    </script>";
             }     
    }
 }
 else
 {
     echo $tab;
 }
?>